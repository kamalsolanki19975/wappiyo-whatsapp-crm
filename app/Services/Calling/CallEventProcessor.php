<?php

namespace App\Services\Calling;

use App\Events\CallEvent;
use App\Models\Call;
use App\Models\ChatLog;
use App\Models\Contact;
use App\Models\Organization;
use App\Models\ProcessedWebhookEvent;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Propaganistas\LaravelPhone\PhoneNumber;

class CallEventProcessor
{
    /**
     * Process an incoming Meta calling webhook change payload.
     *
     * @param array $change The entry[0]['changes'][0] array where field == 'calls'
     * @param Organization $organization
     * @return array
     */
    public static function process(array $change, Organization $organization): array
    {
        $value = $change['value'] ?? [];
        $eventId = self::extractEventId($value);

        // 1. Idempotency Check
        if ($eventId && self::isEventAlreadyProcessed($organization->id, $eventId)) {
            Log::info("Idempotent skip: WhatsApp call event {$eventId} already processed for org {$organization->id}.");
            return [
                'success' => true,
                'status' => 'skipped',
                'action' => 'duplicate_ignored',
                'reason' => 'duplicate_event',
                'event_id' => $eventId,
            ];
        }

        // 2. Extract Event Details
        $providerCallId = $value['call_id'] ?? $value['id'] ?? null;
        $event = $value['event'] ?? $value['status'] ?? 'unknown';
        $direction = strtolower($value['direction'] ?? 'inbound');
        $rawFrom = $value['from'] ?? null;
        $rawTo = $value['to'] ?? null;
        $timestamp = isset($value['timestamp']) ? Carbon::createFromTimestamp($value['timestamp']) : now();
        $reason = $value['reason'] ?? $value['failure_reason'] ?? null;
        $failureCode = $value['code'] ?? $value['failure_code'] ?? null;
        $duration = isset($value['duration']) ? (int) $value['duration'] : null;

        // Determine customer phone based on direction
        $customerPhone = $direction === 'inbound' ? $rawFrom : $rawTo;
        $normalizedPhone = self::normalizePhone($customerPhone);

        // 3. Resolve or Create Contact
        $contact = null;
        if (!empty($normalizedPhone)) {
            $digitsOnly = preg_replace('/[^\d]/', '', $normalizedPhone);
            $contact = Contact::where('organization_id', $organization->id)
                ->where(function ($q) use ($normalizedPhone, $digitsOnly, $customerPhone) {
                    $q->where('phone', $normalizedPhone)
                      ->orWhere('phone', $digitsOnly);
                    if ($customerPhone && $customerPhone !== $normalizedPhone && $customerPhone !== $digitsOnly) {
                        $q->orWhere('phone', $customerPhone);
                    }
                })
                ->whereNull('deleted_at')
                ->first();

            if (!$contact && $direction === 'inbound') {
                // Inbound call from unknown contact -> auto-create Contact so activity is preserved
                $profileName = $value['profile']['name'] ?? $value['contacts'][0]['profile']['name'] ?? 'WhatsApp Caller';
                $contact = Contact::create([
                    'uuid' => (string) Str::uuid(),
                    'first_name' => $profileName,
                    'last_name' => null,
                    'email' => null,
                    'phone' => $normalizedPhone,
                    'organization_id' => $organization->id,
                    'created_by' => 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // 4. Map Status
        $internalStatus = CallStatusProcessor::map(null, $event);

        // 5. Find or Create Call Record
        $call = null;
        if (!empty($providerCallId)) {
            $call = Call::where('organization_id', $organization->id)
                ->where('provider_call_id', $providerCallId)
                ->first();
        }

        if (!$call && $direction === 'inbound') {
            // New inbound call
            $call = new Call();
            $call->uuid = (string) Str::uuid();
            $call->organization_id = $organization->id;
            $call->contact_id = $contact?->id;
            $call->provider = 'meta';
            $call->provider_call_id = $providerCallId;
            $call->direction = 'inbound';
            $call->customer_phone = $normalizedPhone ?: ($customerPhone ?: 'Unknown');
            $call->status = $internalStatus;
            $call->started_at = $timestamp;
            $call->metadata = $value;
            $call->save();
        } elseif ($call) {
            // Verify state transition is legal
            if (CallStatusProcessor::canTransition($call->status, $internalStatus)) {
                $call->status = $internalStatus;
            }

            if ($internalStatus === CallStatusProcessor::STATUS_CONNECTED && empty($call->connected_at)) {
                $call->connected_at = $timestamp;
            }

            if (CallStatusProcessor::isFinal($internalStatus)) {
                $call->ended_at = $timestamp;

                if ($duration !== null) {
                    $call->duration = $duration;
                } elseif ($call->connected_at) {
                    $call->duration = max(0, $call->ended_at->diffInSeconds($call->connected_at));
                }

                if ($reason) {
                    $call->failure_reason = $reason;
                }
                if ($failureCode) {
                    $call->failure_code = (string) $failureCode;
                }
            }

            // Merge metadata
            $existingMeta = is_array($call->metadata) ? $call->metadata : [];
            $call->metadata = array_merge($existingMeta, ['last_event' => $value]);
            $call->save();
        }

        // 6. Record CRM Activity Timeline in ChatLog if call is active or terminal
        if ($call && $contact) {
            self::recordChatLogActivity($call, $contact);
        }

        // 7. Broadcast Real-time Event
        if ($call) {
            try {
                broadcast(new CallEvent($call, $internalStatus, $organization->id, [
                    'event' => $event,
                    'direction' => $direction,
                ]));
            } catch (\Throwable $e) {
                Log::debug('CallEvent webhook broadcast notice: ' . $e->getMessage());
            }
        }

        // 8. Mark Event as Processed for Idempotency
        if ($eventId) {
            self::markEventProcessed($organization->id, $eventId, 'calls', $value);
        }

        return [
            'success' => true,
            'status' => 'processed',
            'action' => 'processed',
            'call_id' => $call?->id,
            'call_uuid' => $call?->uuid,
            'internal_status' => $internalStatus,
            'event_id' => $eventId,
        ];
    }

    /**
     * Record or update ChatLog activity for contact conversation feed.
     */
    public static function recordChatLogActivity(Call $call, Contact $contact): void
    {
        // Check if chat log already exists for this call
        $existingLog = ChatLog::where('contact_id', $contact->id)
            ->where('entity_type', 'call')
            ->where('entity_id', $call->id)
            ->whereNull('deleted_at')
            ->first();

        if (!$existingLog) {
            ChatLog::insert([
                'contact_id' => $contact->id,
                'entity_type' => 'call',
                'entity_id' => $call->id,
                'created_at' => now(),
            ]);

            // Update contact latest activity timestamp
            $contact->update([
                'latest_chat_created_at' => now(),
            ]);
        }
    }

    /**
     * Normalize phone number to standard E164 format.
     */
    public static function normalizePhone(?string $phone): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $phone = trim($phone);
        if (substr($phone, 0, 1) !== '+') {
            $phone = '+' . $phone;
        }

        try {
            $phoneNumber = new PhoneNumber($phone);
            return $phoneNumber->formatE164();
        } catch (\Throwable $e) {
            return preg_replace('/[^\+0-9]/', '', $phone);
        }
    }

    /**
     * Extract a unique event identifier from the webhook payload.
     */
    private static function extractEventId(array $value): ?string
    {
        if (!empty($value['id'])) {
            return (string) $value['id'];
        }

        if (!empty($value['event_id'])) {
            return (string) $value['event_id'];
        }

        if (!empty($value['call_id']) && !empty($value['event'])) {
            return $value['call_id'] . '_' . $value['event'] . '_' . ($value['timestamp'] ?? time());
        }

        return null;
    }

    private static function isEventAlreadyProcessed(int $organizationId, string $eventId): bool
    {
        return ProcessedWebhookEvent::where('organization_id', $organizationId)
            ->where('event_id', $eventId)
            ->exists();
    }

    private static function markEventProcessed(int $organizationId, string $eventId, string $type, array $payload): void
    {
        try {
            ProcessedWebhookEvent::create([
                'organization_id' => $organizationId,
                'event_id' => $eventId,
                'event_type' => $type,
                'provider' => 'meta',
                'payload' => $payload,
                'processed_at' => now(),
                'created_at' => now(),
            ]);
        } catch (\Throwable $e) {
            // Silently ignore unique race conditions
        }
    }
}
