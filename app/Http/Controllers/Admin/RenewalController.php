<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller as BaseController;
use App\Mail\SubscriptionRenewalMail;
use App\Models\Notification;
use App\Models\Organization;
use App\Models\Subscription;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Inertia\Inertia;

class RenewalController extends BaseController
{
    /**
     * Display the Admin Customers with Renewal Due list.
     */
    public function index(Request $request)
    {
        $currentUser = Auth::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $filter = $request->query('filter', 'all');
        $search = $request->query('search');
        $now = now();

        $query = Subscription::with(['organization', 'plan'])
            ->whereNotNull('valid_until');

        // Apply Search
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('organization', function ($oq) use ($search) {
                    $oq->where('name', 'like', "%{$search}%");
                })->orWhereHas('plan', function ($pq) use ($search) {
                    $pq->where('name', 'like', "%{$search}%");
                });
            });
        }

        $subscriptions = $query->orderBy('valid_until', 'asc')->get();

        // Calculate days remaining and map
        $records = $subscriptions->map(function ($sub) use ($now) {
            $rawDate = $sub->getRawOriginal('valid_until') ?: $sub->valid_until;
            $validUntil = Carbon::parse($rawDate);
            $daysRemaining = (int) $now->diffInDays($validUntil, false);

            $paymentDetails = json_decode($sub->payment_details, true) ?: [];
            $lastSentAt = $paymentDetails['last_renewal_reminder_at'] ?? null;

            // Find owner user
            $ownerTeam = Team::where('organization_id', $sub->organization_id)->where('role', 'owner')->first();
            $owner = $ownerTeam ? User::find($ownerTeam->user_id) : null;

            return [
                'id' => $sub->id,
                'uuid' => $sub->uuid,
                'organization_id' => $sub->organization_id,
                'organization_name' => $sub->organization ? $sub->organization->name : 'Unknown',
                'owner_name' => $owner ? ($owner->first_name . ' ' . ($owner->last_name ?: '')) : 'N/A',
                'owner_email' => $owner ? $owner->email : 'N/A',
                'plan_name' => $sub->plan ? $sub->plan->name : ($sub->status === 'trial' ? 'Free Trial' : 'Custom Plan'),
                'amount' => $sub->plan ? ('$' . number_format($sub->plan->price, 2)) : '$0.00',
                'status' => $sub->status,
                'valid_until' => $validUntil->format('M d, Y'),
                'raw_valid_until' => $validUntil->format('Y-m-d'),
                'days_remaining' => $daysRemaining,
                'last_reminder_at' => $lastSentAt ? Carbon::parse($lastSentAt)->diffForHumans() : __('Never'),
                'reminder_status' => !empty($paymentDetails['renewal_reminders_sent']) ? __('Sent') : __('Pending'),
            ];
        });

        // Filter presets
        if ($filter === 'due_today') {
            $records = $records->filter(fn ($r) => $r['days_remaining'] === 0)->values();
        } elseif ($filter === '1_7d') {
            $records = $records->filter(fn ($r) => $r['days_remaining'] >= 1 && $r['days_remaining'] <= 7)->values();
        } elseif ($filter === '8_30d') {
            $records = $records->filter(fn ($r) => $r['days_remaining'] >= 8 && $r['days_remaining'] <= 30)->values();
        } elseif ($filter === 'overdue') {
            $records = $records->filter(fn ($r) => $r['days_remaining'] < 0)->values();
        } elseif ($filter === 'trial') {
            $records = $records->filter(fn ($r) => $r['status'] === 'trial')->values();
        } elseif ($filter === 'active') {
            $records = $records->filter(fn ($r) => $r['status'] === 'active')->values();
        }

        // Summary counts
        $allRecords = $subscriptions->map(function ($sub) use ($now) {
            $rawDate = $sub->getRawOriginal('valid_until') ?: $sub->valid_until;
            $validUntil = Carbon::parse($rawDate);
            return (int) $now->diffInDays($validUntil, false);
        });

        $counts = [
            'all' => $allRecords->count(),
            'due_today' => $allRecords->filter(fn ($d) => $d === 0)->count(),
            'due_1_7d' => $allRecords->filter(fn ($d) => $d >= 1 && $d <= 7)->count(),
            'due_8_30d' => $allRecords->filter(fn ($d) => $d >= 8 && $d <= 30)->count(),
            'overdue' => $allRecords->filter(fn ($d) => $d < 0)->count(),
        ];

        return Inertia::render('Admin/Subscription/RenewalDue', [
            'rows' => $records,
            'counts' => $counts,
            'filters' => [
                'filter' => $filter,
                'search' => $search,
            ],
        ]);
    }

    /**
     * Manually trigger a renewal reminder to a specific customer organization.
     */
    public function sendManualReminder(Request $request, $id)
    {
        $currentUser = Auth::user();
        if (!$currentUser || $currentUser->role !== 'admin') {
            abort(403, 'Unauthorized.');
        }

        $subscription = Subscription::with(['organization', 'plan'])->findOrFail($id);
        $org = $subscription->organization;

        if (!$org) {
            return back()->with('status', [
                'type' => 'error',
                'message' => __('Organization not found.')
            ]);
        }

        $rawDate = $subscription->getRawOriginal('valid_until') ?: $subscription->valid_until;
        $validUntil = Carbon::parse($rawDate);
        $now = now();
        $daysRemaining = (int) $now->diffInDays($validUntil, false);
        $planName = $subscription->plan ? $subscription->plan->name : 'Subscription';
        $formattedDate = $validUntil->format('M d, Y');

        // 1. In-app notification
        $targetUsers = Team::where('organization_id', $org->id)
            ->whereIn('role', ['owner', 'manager'])
            ->pluck('user_id');

        foreach ($targetUsers as $userId) {
            Notification::create([
                'uuid' => (string) Str::uuid(),
                'user_id' => $userId,
                'title' => __('Subscription Renewal Reminder'),
                'comment' => __('Your :plan subscription for :org renews on :date (:days days remaining). Check billing settings.', [
                    'plan' => $planName,
                    'org' => $org->name,
                    'date' => $formattedDate,
                    'days' => $daysRemaining,
                ]),
                'url' => '/billing',
                'seen' => false,
                'created_at' => $now,
                'updated_at' => $now,
            ]);
        }

        // 2. Email notification
        $ownerTeam = Team::where('organization_id', $org->id)->where('role', 'owner')->first();
        $ownerUser = $ownerTeam ? User::find($ownerTeam->user_id) : null;

        if ($ownerUser && !empty($ownerUser->email)) {
            try {
                Mail::to($ownerUser->email)->send(new SubscriptionRenewalMail(
                    $ownerUser->first_name ?: $org->name,
                    $planName,
                    $formattedDate,
                    $daysRemaining,
                    '/billing'
                ));
            } catch (\Throwable $e) {
                Log::warning("Failed to dispatch manual renewal email: " . $e->getMessage());
            }
        }

        // Update tracking
        $paymentDetails = json_decode($subscription->payment_details, true) ?: [];
        $sentMilestones = $paymentDetails['renewal_reminders_sent'] ?? [];
        $sentMilestones[] = 'manual_' . $now->format('Y-m-d_H:i');
        $paymentDetails['renewal_reminders_sent'] = array_unique($sentMilestones);
        $paymentDetails['last_renewal_reminder_at'] = $now->toIso8601String();
        $subscription->payment_details = json_encode($paymentDetails);
        if ($request->wantsJson() || $request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => __('Renewal reminder successfully sent to :client!', ['client' => $org->name])
            ]);
        }

        return back()->with('status', [
            'type' => 'success',
            'message' => __('Renewal reminder successfully sent to :client!', ['client' => $org->name])
        ]);
    }
}
