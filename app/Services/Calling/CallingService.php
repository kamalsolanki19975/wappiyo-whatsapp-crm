<?php

namespace App\Services\Calling;

use App\Events\CallEvent;
use App\Models\Call;
use App\Models\ChatLog;
use App\Models\Contact;
use App\Models\Organization;
use App\Models\Team;
use App\Models\User;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Propaganistas\LaravelPhone\PhoneNumber;

class CallingService
{
    private int $organizationId;
    private CallingProviderInterface $provider;
    private ?array $whatsappConfig = null;

    public function __construct(int $organizationId, ?CallingProviderInterface $provider = null)
    {
        $this->organizationId = $organizationId;

        if ($provider) {
            $this->provider = $provider;
        } else {
            $this->initializeProvider();
        }
    }

    private function initializeProvider(): void
    {
        $org = Organization::find($this->organizationId);
        $metadata = $org ? json_decode($org->metadata ?? '{}', true) : [];

        $this->whatsappConfig = $metadata['whatsapp'] ?? [];

        $accessToken = $this->whatsappConfig['access_token'] ?? '';
        $apiVersion = config('graph.api_version', 'v20.0');
        $appId = $this->whatsappConfig['app_id'] ?? null;
        $phoneNumberId = $this->whatsappConfig['phone_number_id'] ?? null;
        $wabaId = $this->whatsappConfig['waba_id'] ?? null;

        $this->provider = new MetaWhatsAppCallingProvider(
            $accessToken,
            $apiVersion,
            $appId,
            $phoneNumberId,
            $wabaId,
            $this->organizationId
        );
    }

    public function getProvider(): CallingProviderInterface
    {
        return $this->provider;
    }

    public function setProvider(CallingProviderInterface $provider): void
    {
        $this->provider = $provider;
    }

    /**
     * Check if calling credentials are configured for this organization.
     */
    public function isCallingConfigured(): bool
    {
        $org = Organization::find($this->organizationId);
        $metadata = $org ? json_decode($org->metadata ?? '{}', true) : [];
        $whatsapp = $metadata['whatsapp'] ?? [];

        return !empty($whatsapp['access_token']) && !empty($whatsapp['phone_number_id']);
    }

    /**
     * Verify whether the business has permission to call a customer.
     */
    public function checkPermission(string $phone): array
    {
        $cleanPhone = preg_replace('/[^0-9]/', '', $phone);
        return $this->provider->checkCallPermission($cleanPhone);
    }

    /**
     * Check Meta phone number calling status.
     */
    public function getCallingStatus(): array
    {
        return $this->provider->getCallingStatus();
    }

    /**
     * Initiate an outbound call from an agent to a contact.
     */
    public function initiateCall(User $user, Contact $contact, array $options = []): Call
    {
        // 1. Validate Organization & Configuration
        if (!$this->isCallingConfigured()) {
            throw new \RuntimeException('WhatsApp Calling is not configured for this organization. Please verify your WhatsApp settings.');
        }

        // 2. Validate Subscription Calling Limits
        if (SubscriptionService::isSubscriptionFeatureLimitReached($this->organizationId, 'calling_limit')) {
            throw new \RuntimeException('Your organization has reached the WhatsApp call limit on its current subscription plan.');
        }

        // 3. Validate Contact belongs to Organization
        if ((int) $contact->organization_id !== $this->organizationId) {
            throw new \InvalidArgumentException('Unauthorized access to contact.');
        }

        // 3. Normalize Phone
        $customerPhone = CallEventProcessor::normalizePhone($contact->phone);
        if (empty($customerPhone)) {
            throw new \InvalidArgumentException('Contact does not have a valid phone number for calling.');
        }

        // 4. Double Call Protection / Active Call Check
        $activeCall = Call::where('organization_id', $this->organizationId)
            ->where('contact_id', $contact->id)
            ->whereIn('status', [
                CallStatusProcessor::STATUS_INITIATING,
                CallStatusProcessor::STATUS_RINGING,
                CallStatusProcessor::STATUS_CONNECTING,
                CallStatusProcessor::STATUS_CONNECTED,
            ])
            ->where('created_at', '>=', now()->subMinutes(5))
            ->first();

        if ($activeCall) {
            // Idempotent return if same user clicked within 15 seconds
            $createdAt = $activeCall->getRawOriginal('created_at') ?: $activeCall->created_at;
            if ((int) $activeCall->user_id === (int) $user->id && Carbon::parse($createdAt)->diffInSeconds(now()) < 15) {
                return $activeCall->fresh(['contact', 'agent']);
            }
            throw new \RuntimeException('There is already an active call in progress for this contact.');
        }

        // 5. Resolve Agent Team ID
        $team = Team::where('organization_id', $this->organizationId)
            ->where('user_id', $user->id)
            ->first();

        // 6. Create Pending Call Record
        $call = Call::create([
            'organization_id' => $this->organizationId,
            'contact_id' => $contact->id,
            'user_id' => $user->id,
            'team_id' => $team?->id,
            'whatsapp_account_id' => $this->whatsappConfig['waba_id'] ?? null,
            'phone_number_id' => $this->whatsappConfig['phone_number_id'] ?? null,
            'customer_phone' => $customerPhone,
            'direction' => 'outbound',
            'provider' => 'meta',
            'status' => CallStatusProcessor::STATUS_INITIATING,
            'started_at' => now(),
            'metadata' => [
                'initiated_by_user_id' => $user->id,
                'initiated_by_name' => $user->full_name,
            ],
        ]);

        // 7. Record ChatLog Activity for CRM Timeline
        CallEventProcessor::recordChatLogActivity($call, $contact);

        // 8. Invoke Provider
        $response = $this->provider->initiateCall($call, $options);

        if ($response['success']) {
            $call->update([
                'provider_call_id' => $response['provider_call_id'] ?? $call->provider_call_id,
                'status' => $response['status'] ?? CallStatusProcessor::STATUS_RINGING,
                'metadata' => array_merge($call->metadata ?? [], ['meta_response' => $response['raw'] ?? []]),
            ]);
        } else {
            $call->update([
                'status' => CallStatusProcessor::STATUS_FAILED,
                'failure_reason' => $response['error'] ?? 'Call failed to initiate with WhatsApp provider.',
                'ended_at' => now(),
            ]);
        }

        // 8. Broadcast Real-Time Event
        $this->safeBroadcast(new CallEvent($call, $call->status, $this->organizationId, [
            'direction' => 'outbound',
            'agent' => [
                'id' => $user->id,
                'name' => $user->full_name,
            ],
        ]), true);

        return $call->fresh(['contact', 'agent']);
    }

    /**
     * Terminate an active call.
     */
    public function endCall(string $uuid, User $user): Call
    {
        $call = Call::where('organization_id', $this->organizationId)
            ->where('uuid', $uuid)
            ->firstOrFail();

        $this->authorizeCallAccess($call, $user);

        // If not already in final state, terminate with provider
        if (!CallStatusProcessor::isFinal($call->status)) {
            $this->provider->terminateCall($call);

            $endedAt = now();
            $rawStarted = $call->getRawOriginal('connected_at') ?: $call->getRawOriginal('started_at');
            $duration = $rawStarted ? max(0, $endedAt->diffInSeconds(Carbon::parse($rawStarted))) : 0;

            $call->update([
                'status' => CallStatusProcessor::STATUS_COMPLETED,
                'ended_at' => $endedAt,
                'duration' => $duration,
            ]);

            // Broadcast real-time call ended
            $this->safeBroadcast(new CallEvent($call, CallStatusProcessor::STATUS_COMPLETED, $this->organizationId, [
                'action' => 'terminated_by_agent',
                'agent_id' => $user->id,
            ]), true);
        }

        return $call->fresh(['contact', 'agent']);
    }

    /**
     * Update call notes.
     */
    public function updateNotes(string $uuid, string $notes, User $user): Call
    {
        $call = Call::where('organization_id', $this->organizationId)
            ->where('uuid', $uuid)
            ->firstOrFail();

        $this->authorizeCallAccess($call, $user);

        $call->update([
            'notes' => clean($notes),
        ]);

        $this->safeBroadcast(new CallEvent($call, 'notes_updated', $this->organizationId), true);

        return $call->fresh(['contact', 'agent']);
    }

    /**
     * Update call disposition outcome.
     */
    public function updateDisposition(string $uuid, string $disposition, User $user): Call
    {
        $call = Call::where('organization_id', $this->organizationId)
            ->where('uuid', $uuid)
            ->firstOrFail();

        $this->authorizeCallAccess($call, $user);

        $call->update([
            'disposition' => clean($disposition),
        ]);

        $this->safeBroadcast(new CallEvent($call, 'disposition_updated', $this->organizationId), true);

        return $call->fresh(['contact', 'agent']);
    }

    /**
     * Schedule follow up reminder for a call.
     */
    public function scheduleFollowUp(string $uuid, string $followUpAt, ?string $reminderNotes, User $user): Call
    {
        $call = Call::where('organization_id', $this->organizationId)
            ->where('uuid', $uuid)
            ->firstOrFail();

        $this->authorizeCallAccess($call, $user);

        $call->update([
            'follow_up_at' => Carbon::parse($followUpAt),
            'notes' => $reminderNotes ? ($call->notes . "\n[Follow-up Note]: " . clean($reminderNotes)) : $call->notes,
        ]);

        $this->safeBroadcast(new CallEvent($call, 'follow_up_scheduled', $this->organizationId), true);

        return $call->fresh(['contact', 'agent']);
    }

    /**
     * Verify whether a user has permission to manage a specific call.
     */
    private function authorizeCallAccess(Call $call, User $user): void
    {
        $team = Team::where('organization_id', $this->organizationId)
            ->where('user_id', $user->id)
            ->first();

        $role = $team ? $team->role : ($user->role === 'admin' ? 'admin' : 'owner');

        if ($role === 'agent' && $call->user_id && (int) $call->user_id !== (int) $user->id) {
            throw new \RuntimeException('Unauthorized: Agents may only manage their own calls.');
        }
    }

    /**
     * Safely dispatch real-time broadcast without crashing if broadcaster has no active client socket.
     */
    private function safeBroadcast($event, bool $toOthers = false): void
    {
        try {
            if ($toOthers) {
                broadcast($event)->toOthers();
            } else {
                broadcast($event);
            }
        } catch (\Throwable $e) {
            Log::debug('Calling broadcast notice: ' . $e->getMessage());
        }
    }

    /**
     * Query Call History with search, multi-faceted filtering, and server-side pagination.
     */
    public function getCallHistory(Request $request, User $user, int $perPage = 15)
    {
        $query = Call::with(['contact', 'agent'])
            ->where('organization_id', $this->organizationId)
            ->latest('created_at');

        // RBAC: Check user's role in this organization
        $team = Team::where('organization_id', $this->organizationId)
            ->where('user_id', $user->id)
            ->first();

        $role = $team ? $team->role : ($user->role === 'admin' ? 'admin' : 'owner');

        // If restricted agent, only view their own calls
        if ($role === 'agent') {
            $query->where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhereNull('user_id');
            });
        }

        // Filter: Search (Contact name, phone, email, agent name, provider call ID)
        if ($request->filled('search')) {
            $searchTerm = trim($request->input('search'));
            $query->where(function ($q) use ($searchTerm) {
                $q->where('customer_phone', 'like', "%{$searchTerm}%")
                  ->orWhere('provider_call_id', 'like', "%{$searchTerm}%")
                  ->orWhere('disposition', 'like', "%{$searchTerm}%")
                  ->orWhere('notes', 'like', "%{$searchTerm}%")
                  ->orWhereHas('contact', function ($cq) use ($searchTerm) {
                      $cq->where('first_name', 'like', "%{$searchTerm}%")
                         ->orWhere('last_name', 'like', "%{$searchTerm}%")
                         ->orWhere('phone', 'like', "%{$searchTerm}%")
                         ->orWhere('email', 'like', "%{$searchTerm}%");
                  })
                  ->orWhereHas('agent', function ($uq) use ($searchTerm) {
                      $uq->where('first_name', 'like', "%{$searchTerm}%")
                         ->orWhere('last_name', 'like', "%{$searchTerm}%");
                  });
            });
        }

        // Filter: Direction (inbound / outbound)
        if ($request->filled('direction') && $request->input('direction') !== 'all') {
            $query->where('direction', $request->input('direction'));
        }

        // Filter: Status
        if ($request->filled('status') && $request->input('status') !== 'all') {
            $query->where('status', $request->input('status'));
        }

        // Filter: Disposition
        if ($request->filled('disposition') && $request->input('disposition') !== 'all') {
            $query->where('disposition', $request->input('disposition'));
        }

        // Filter: Specific Contact UUID
        if ($request->filled('contact_uuid')) {
            $contact = Contact::where('organization_id', $this->organizationId)
                ->where('uuid', $request->input('contact_uuid'))
                ->first();

            if ($contact) {
                $query->where('contact_id', $contact->id);
            }
        }

        // Filter: Agent
        if ($request->filled('agent_id') && $request->input('agent_id') !== 'all') {
            $query->where('user_id', $request->input('agent_id'));
        }

        // Filter: Date Range
        if ($request->filled('date_from')) {
            $query->where('created_at', '>=', Carbon::parse($request->input('date_from'))->startOfDay());
        }
        if ($request->filled('date_to')) {
            $query->where('created_at', '<=', Carbon::parse($request->input('date_to'))->endOfDay());
        }

        return $query->paginate($perPage);
    }

    /**
     * Compute comprehensive calling analytics for this organization.
     */
    public function getAnalytics(?string $startDate = null, ?string $endDate = null): array
    {
        $baseQuery = Call::where('organization_id', $this->organizationId);

        if ($startDate) {
            $baseQuery->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }
        if ($endDate) {
            $baseQuery->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        $totalCalls = (clone $baseQuery)->count();
        $inboundCalls = (clone $baseQuery)->where('direction', 'inbound')->count();
        $outboundCalls = (clone $baseQuery)->where('direction', 'outbound')->count();
        $connectedCalls = (clone $baseQuery)->where('status', CallStatusProcessor::STATUS_COMPLETED)->count();
        $missedCalls = (clone $baseQuery)->where('status', CallStatusProcessor::STATUS_MISSED)->count();
        $failedCalls = (clone $baseQuery)->where('status', CallStatusProcessor::STATUS_FAILED)->count();
        $rejectedCalls = (clone $baseQuery)->where('status', CallStatusProcessor::STATUS_REJECTED)->count();
        $totalTalkTime = (clone $baseQuery)->sum('duration');

        $avgDuration = $connectedCalls > 0 ? (int) round($totalTalkTime / $connectedCalls) : 0;
        $connectionRate = $totalCalls > 0 ? round(($connectedCalls / $totalCalls) * 100, 1) : 0.0;
        $missedRate = $totalCalls > 0 ? round(($missedCalls / $totalCalls) * 100, 1) : 0.0;

        // Calls today
        $callsToday = Call::where('organization_id', $this->organizationId)
            ->whereDate('created_at', Carbon::today())
            ->count();

        $connectedToday = Call::where('organization_id', $this->organizationId)
            ->whereDate('created_at', Carbon::today())
            ->where('status', CallStatusProcessor::STATUS_COMPLETED)
            ->count();

        // Calls per agent (with date filter)
        $agentQuery = Call::with('agent')
            ->where('organization_id', $this->organizationId)
            ->whereNotNull('user_id');

        if ($startDate) {
            $agentQuery->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }
        if ($endDate) {
            $agentQuery->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        $callsPerAgent = $agentQuery
            ->select('user_id', DB::raw('count(*) as count'), DB::raw('sum(duration) as total_duration'))
            ->groupBy('user_id')
            ->get()
            ->map(function ($item) {
                return [
                    'agent_id' => $item->user_id,
                    'agent_name' => $item->agent ? $item->agent->full_name : 'Agent #' . $item->user_id,
                    'calls_count' => (int) $item->count,
                    'talk_time' => (int) $item->total_duration,
                ];
            });

        // Top Dispositions (with date filter)
        $dispQuery = Call::where('organization_id', $this->organizationId)
            ->whereNotNull('disposition');

        if ($startDate) {
            $dispQuery->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }
        if ($endDate) {
            $dispQuery->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        $dispositionStats = $dispQuery
            ->select('disposition', DB::raw('count(*) as count'))
            ->groupBy('disposition')
            ->orderByDesc('count')
            ->take(8)
            ->get();

        return [
            'total_calls' => $totalCalls,
            'inbound_calls' => $inboundCalls,
            'outbound_calls' => $outboundCalls,
            'connected_calls' => $connectedCalls,
            'missed_calls' => $missedCalls,
            'failed_calls' => $failedCalls,
            'rejected_calls' => $rejectedCalls,
            'total_talk_time' => (int) $totalTalkTime,
            'formatted_total_talk_time' => self::formatDuration((int) $totalTalkTime),
            'avg_duration' => $avgDuration,
            'formatted_avg_duration' => self::formatDuration($avgDuration),
            'connection_rate' => $connectionRate,
            'missed_rate' => $missedRate,
            'calls_today' => $callsToday,
            'connected_today' => $connectedToday,
            'calls_per_agent' => $callsPerAgent,
            'dispositions' => $dispositionStats,
        ];
    }

    public static function formatDuration(int $seconds): string
    {
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%dh %02dm %02ds', $hours, $minutes, $remainingSeconds);
        }

        return sprintf('%02dm %02ds', $minutes, $remainingSeconds);
    }
}
