<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller as BaseController;
use App\Models\Call;
use App\Models\Contact;
use App\Models\Organization;
use App\Models\Setting;
use App\Models\Team;
use App\Services\Calling\CallingService;
use App\Services\Calling\CallStatusProcessor;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Inertia\Inertia;

class CallController extends BaseController
{
    private function getOrganizationId(): int
    {
        $orgId = session()->get('current_organization');
        if (!$orgId) {
            abort(403, 'No active organization selected.');
        }
        return (int) $orgId;
    }

    private function getCallingService(): CallingService
    {
        return new CallingService($this->getOrganizationId());
    }

    /**
     * Display Call History and Communication Hub.
     */
    public function index(Request $request)
    {
        $orgId = $this->getOrganizationId();
        $user = Auth::user();
        $callingService = $this->getCallingService();

        $rows = $callingService->getCallHistory($request, $user, 15);

        if ($request->expectsJson()) {
            return response()->json($rows);
        }

        $analytics = $callingService->getAnalytics(
            $request->input('date_from'),
            $request->input('date_to')
        );

        $agents = Team::with('user')
            ->where('organization_id', $orgId)
            ->get()
            ->map(function ($team) {
                return [
                    'id' => $team->user_id,
                    'name' => $team->user ? $team->user->full_name : 'User #' . $team->user_id,
                    'role' => $team->role,
                ];
            });

        $dispositions = [
            'Interested',
            'Not Interested',
            'Follow-up Required',
            'No Answer',
            'Busy',
            'Wrong Number',
            'Converted',
            'Callback Requested',
            'Other',
        ];

        $pusherSettings = [
            'key' => Setting::where('key', 'pusher_app_key')->value('value'),
            'cluster' => Setting::where('key', 'pusher_app_cluster')->value('value'),
        ];

        return Inertia::render('User/Calls/Index', [
            'title' => 'WhatsApp Calls',
            'rows' => $rows,
            'filters' => $request->all(),
            'analytics' => $analytics,
            'isCallingConfigured' => $callingService->isCallingConfigured(),
            'callingStatus' => $callingService->getCallingStatus(),
            'agents' => $agents,
            'dispositions' => $dispositions,
            'organizationId' => $orgId,
            'pusherSettings' => $pusherSettings,
        ]);
    }

    /**
     * Initiate an outbound call to a contact.
     */
    public function store(Request $request)
    {
        $request->validate([
            'contact_uuid' => 'nullable|string',
            'contact_id' => 'nullable|integer',
            'phone' => 'nullable|string',
            'sdp' => 'nullable|string',
            'session' => 'nullable|array',
        ]);

        $orgId = $this->getOrganizationId();
        $user = Auth::user();
        $callingService = $this->getCallingService();

        // 1. Resolve Contact
        $contact = null;
        if ($request->filled('contact_uuid')) {
            $contact = Contact::where('organization_id', $orgId)
                ->where('uuid', $request->input('contact_uuid'))
                ->whereNull('deleted_at')
                ->first();
        } elseif ($request->filled('contact_id')) {
            $contact = Contact::where('organization_id', $orgId)
                ->where('id', $request->input('contact_id'))
                ->whereNull('deleted_at')
                ->first();
        } elseif ($request->filled('phone')) {
            $contact = Contact::where('organization_id', $orgId)
                ->where('phone', $request->input('phone'))
                ->whereNull('deleted_at')
                ->first();

            if (!$contact) {
                // Quick create contact for new outbound phone
                $contact = Contact::create([
                    'first_name' => $request->input('name') ?: 'Contact',
                    'phone' => $request->input('phone'),
                    'organization_id' => $orgId,
                    'created_by' => $user->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        if (!$contact) {
            return response()->json([
                'success' => false,
                'message' => 'Valid contact or phone number required to place call.',
            ], 422);
        }

        try {
            $options = [];
            if ($request->filled('sdp')) {
                $options['sdp'] = $request->input('sdp');
            }
            if ($request->filled('session')) {
                $options['session'] = $request->input('session');
            }

            $call = $callingService->initiateCall($user, $contact, $options);

            return response()->json([
                'success' => true,
                'call' => $call,
                'message' => $call->status === CallStatusProcessor::STATUS_FAILED
                    ? ($call->failure_reason ?: 'Call failed.')
                    : 'Call initiated successfully via WhatsApp.',
            ]);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Get specific call details.
     */
    public function show(Request $request, string $uuid)
    {
        $orgId = $this->getOrganizationId();
        $user = Auth::user();

        $call = Call::with(['contact', 'agent'])
            ->where('organization_id', $orgId)
            ->where('uuid', $uuid)
            ->firstOrFail();

        $team = Team::where('organization_id', $orgId)
            ->where('user_id', $user->id)
            ->first();
        $role = $team ? $team->role : ($user->role === 'admin' ? 'admin' : 'owner');

        if ($role === 'agent' && $call->user_id && (int) $call->user_id !== (int) $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to view this call record.',
            ], 403);
        }

        return response()->json([
            'success' => true,
            'call' => $call,
        ]);
    }

    /**
     * Terminate an active call.
     */
    public function end(Request $request, string $uuid)
    {
        $user = Auth::user();
        $callingService = $this->getCallingService();

        try {
            $call = $callingService->endCall($uuid, $user);

            return response()->json([
                'success' => true,
                'call' => $call,
                'message' => 'Call terminated.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Call record not found.',
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update call notes.
     */
    public function updateNotes(Request $request, string $uuid)
    {
        $request->validate([
            'notes' => 'required|string',
        ]);

        $user = Auth::user();
        $callingService = $this->getCallingService();

        try {
            $call = $callingService->updateNotes($uuid, $request->input('notes'), $user);

            return response()->json([
                'success' => true,
                'call' => $call,
                'message' => 'Call notes updated.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Call record not found.',
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Update call disposition.
     */
    public function updateDisposition(Request $request, string $uuid)
    {
        $request->validate([
            'disposition' => 'required|string|max:128',
        ]);

        $user = Auth::user();
        $callingService = $this->getCallingService();

        try {
            $call = $callingService->updateDisposition($uuid, $request->input('disposition'), $user);

            return response()->json([
                'success' => true,
                'call' => $call,
                'message' => 'Call disposition saved.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Call record not found.',
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Schedule follow-up for a call.
     */
    public function scheduleFollowUp(Request $request, string $uuid)
    {
        $request->validate([
            'follow_up_at' => 'required|date',
            'reminder_notes' => 'nullable|string',
        ]);

        $user = Auth::user();
        $callingService = $this->getCallingService();

        try {
            $call = $callingService->scheduleFollowUp(
                $uuid,
                $request->input('follow_up_at'),
                $request->input('reminder_notes'),
                $user
            );

            return response()->json([
                'success' => true,
                'call' => $call,
                'message' => 'Follow-up scheduled.',
            ]);
        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Call record not found.',
            ], 404);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 400);
        }
    }

    /**
     * Check Meta call permission for a customer phone.
     */
    public function checkPermission(Request $request, string $phone)
    {
        $callingService = $this->getCallingService();
        $result = $callingService->checkPermission($phone);

        return response()->json($result);
    }

    /**
     * Return live analytics metrics.
     */
    public function analytics(Request $request)
    {
        $callingService = $this->getCallingService();
        $metrics = $callingService->getAnalytics(
            $request->input('date_from'),
            $request->input('date_to')
        );

        return response()->json($metrics);
    }

    /**
     * Export Call History to CSV.
     */
    public function export(Request $request)
    {
        $orgId = $this->getOrganizationId();
        $user = Auth::user();

        // RBAC: Agents are not authorized to export organization call history
        $team = Team::where('organization_id', $orgId)
            ->where('user_id', $user->id)
            ->first();
        $role = $team ? $team->role : ($user->role === 'admin' ? 'admin' : 'owner');

        if ($role === 'agent') {
            abort(403, 'Unauthorized: Agents do not have permission to export call history.');
        }

        $callingService = $this->getCallingService();

        // Get all matching calls without pagination for export (up to 5000 max)
        $calls = $callingService->getCallHistory($request, $user, 5000);

        $filename = 'calls_export_' . date('Y-m-d_H-i') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $sanitize = function ($val): string {
            $val = (string) ($val ?? '');
            if ($val !== '' && in_array($val[0], ['=', '+', '-', '@', "\t", "\r"])) {
                return "'" . $val;
            }
            return $val;
        };

        $callback = function () use ($calls, $sanitize) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'Call ID',
                'Contact Name',
                'Phone Number',
                'Agent',
                'Direction',
                'Status',
                'Duration (seconds)',
                'Formatted Duration',
                'Started At',
                'Connected At',
                'Ended At',
                'Disposition',
                'Notes',
                'Follow-up Date',
            ]);

            foreach ($calls as $call) {
                fputcsv($handle, [
                    $sanitize($call->uuid),
                    $sanitize($call->contact ? $call->contact->full_name : 'Unknown Contact'),
                    $sanitize($call->formatted_phone_number ?: $call->customer_phone),
                    $sanitize($call->agent ? $call->agent->full_name : 'Unassigned'),
                    $sanitize(ucfirst($call->direction)),
                    $sanitize(ucfirst($call->status)),
                    $call->duration,
                    $sanitize($call->formatted_duration),
                    $sanitize($call->started_at),
                    $sanitize($call->connected_at),
                    $sanitize($call->ended_at),
                    $sanitize($call->disposition ?: 'None'),
                    $sanitize($call->notes ?: ''),
                    $sanitize($call->follow_up_at ?: ''),
                ]);
            }

            fclose($handle);
        };

        return response()->stream($callback, 200, $headers);
    }
}
