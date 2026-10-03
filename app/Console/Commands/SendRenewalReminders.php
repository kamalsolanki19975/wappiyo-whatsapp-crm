<?php

namespace App\Console\Commands;

use App\Mail\SubscriptionRenewalMail;
use App\Models\Notification;
use App\Models\Subscription;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class SendRenewalReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'wappiyo:send-renewal-reminders {--force : Send reminder regardless of milestone tracking}';

    /**
     * The console command description.
     */
    protected $description = 'Automatically send subscription renewal notifications and emails to customers at 30, 15, 7, 3, and 1 days before expiry';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting subscription renewal reminder inspection...');

        // Threshold milestones in days
        $milestones = [30, 15, 7, 3, 1];
        $now = now();
        $processedCount = 0;
        $remindersDispatched = 0;

        $subscriptions = Subscription::with(['organization', 'plan'])
            ->whereIn('status', ['active', 'trial'])
            ->whereNotNull('valid_until')
            ->get();

        foreach ($subscriptions as $subscription) {
            $processedCount++;
            $org = $subscription->organization;
            if (!$org) continue;

            $validUntil = Carbon::parse($subscription->getRawOriginal('valid_until') ?: $subscription->valid_until);
            $daysRemaining = (int) $now->copy()->startOfDay()->diffInDays($validUntil->copy()->startOfDay(), false);

            // Skip expired subscriptions
            if ($daysRemaining < 0) {
                continue;
            }

            // Find matching milestone
            $matchedMilestone = null;
            foreach ($milestones as $m) {
                // If within 1 day of milestone
                if ($daysRemaining === $m) {
                    $matchedMilestone = $m;
                    break;
                }
            }

            // If forced or matched milestone
            if ($matchedMilestone !== null || ($this->option('force') && $daysRemaining <= 30)) {
                $milestoneKey = $matchedMilestone ?? $daysRemaining;
                
                // Track sent milestones to prevent duplicates
                $paymentDetails = json_decode($subscription->payment_details, true) ?: [];
                $sentMilestones = $paymentDetails['renewal_reminders_sent'] ?? [];

                $milestoneIdentifier = $validUntil->format('Y-m-d') . '_' . $milestoneKey . 'd';

                if (!$this->option('force') && in_array($milestoneIdentifier, $sentMilestones)) {
                    continue; // Already sent for this cycle and milestone
                }

                // Find organization owner
                $ownerTeam = Team::where('organization_id', $org->id)->where('role', 'owner')->first();
                $ownerUser = $ownerTeam ? User::find($ownerTeam->user_id) : null;
                $planName = $subscription->plan ? $subscription->plan->name : ($subscription->status === 'trial' ? 'Free Trial' : 'Subscription');
                $formattedDate = $validUntil->format('M d, Y');

                // 1. In-app Notification for Organization Owners
                $targetUsers = Team::where('organization_id', $org->id)
                    ->whereIn('role', ['owner', 'manager'])
                    ->pluck('user_id');

                foreach ($targetUsers as $userId) {
                    Notification::create([
                        'uuid' => (string) Str::uuid(),
                        'user_id' => $userId,
                        'title' => __('Subscription Renewal Approaching'),
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

                // 2. Dispatch Email to Owner if email exists
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
                        Log::warning("Failed to dispatch renewal email to {$ownerUser->email}: " . $e->getMessage());
                    }
                }

                // Update milestone tracking
                $sentMilestones[] = $milestoneIdentifier;
                $paymentDetails['renewal_reminders_sent'] = array_unique($sentMilestones);
                $paymentDetails['last_renewal_reminder_at'] = $now->toIso8601String();
                $subscription->payment_details = json_encode($paymentDetails);
                $subscription->save();

                $remindersDispatched++;
                $this->line("Dispatched reminder to {$org->name} ({$daysRemaining} days remaining)");
            }
        }

        $this->info("Completed. Inspected {$processedCount} subscriptions; dispatched {$remindersDispatched} renewal reminders.");

        return Command::SUCCESS;
    }
}
