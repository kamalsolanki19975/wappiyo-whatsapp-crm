<?php

namespace App\Services;

use App\Jobs\RetryCampaignJob;
use App\Models\Campaign;
use App\Models\CampaignLog;
use App\Models\CampaignRetry;
use App\Models\Organization;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CampaignRecoveryService
{
    /**
     * Maximum retry attempts allowed per message.
     */
    public const MAX_RETRY_ATTEMPTS = 5;

    /**
     * Analyze and classify a failed campaign log.
     *
     * @param CampaignLog $log
     * @return array
     */
    public function analyzeFailure(CampaignLog $log): array
    {
        $code = null;
        $message = null;
        $details = null;
        $category = 'Unknown Error';
        $isRetryable = false;
        $eligibilityReason = '';

        // 1. Check if failure data is in campaign_logs.metadata
        $metadata = null;
        if (!empty($log->metadata)) {
            $metadata = is_string($log->metadata) ? json_decode($log->metadata, true) : (array) $log->metadata;
        }

        // 2. Check if failure data is in chat status logs / chat metadata
        $chat = $log->chat;
        $chatStatusLogs = ($chat && $chat->logs) ? $chat->logs : [];

        // Parse from campaign log metadata first (initial send failure)
        if ($metadata) {
            if (isset($metadata['data']['error'])) {
                $err = $metadata['data']['error'];
                $code = $err['code'] ?? ($err['error_subcode'] ?? null);
                $message = $err['message'] ?? null;
                $details = $err['error_data']['details'] ?? ($err['type'] ?? null);
            } elseif (isset($metadata['error'])) {
                $err = $metadata['error'];
                $code = is_array($err) ? ($err['code'] ?? ($err['error_subcode'] ?? null)) : null;
                $message = is_array($err) ? ($err['message'] ?? null) : (string) $err;
                $details = is_array($err) ? ($err['error_data']['details'] ?? ($err['type'] ?? null)) : null;
            } elseif (isset($metadata['message'])) {
                $message = $metadata['message'];
            }
        }

        // If not found or if chat status is failed, check chat webhook logs
        if ((!$code && !$message) || ($chat && $chat->status === 'failed')) {
            if ($chat && !empty($chatStatusLogs)) {
                foreach ($chatStatusLogs as $statusLog) {
                    $statusMeta = is_string($statusLog->metadata) ? json_decode($statusLog->metadata, true) : (array) $statusLog->metadata;
                    if (isset($statusMeta['errors']) && is_array($statusMeta['errors'])) {
                        foreach ($statusMeta['errors'] as $errorItem) {
                            $code = $errorItem['code'] ?? $code;
                            $message = $errorItem['title'] ?? ($errorItem['message'] ?? $message);
                            $details = $errorItem['error_data']['details'] ?? $details;
                            break 2;
                        }
                    }
                }
            }
        }

        // Fallback message if null
        if (!$message) {
            $message = ($log->status === 'failed' || ($chat && $chat->status === 'failed'))
                ? __('Delivery failed')
                : __('Pending delivery confirmation');
        }

        $codeStr = (string) $code;
        $msgLower = strtolower($message . ' ' . $details);

        // Classification Logic based on official WhatsApp Cloud API specifications
        if ($codeStr === '131026' || str_contains($msgLower, 'not on whatsapp') || str_contains($msgLower, 'undeliverable')) {
            $category = 'Number Not on WhatsApp';
            $isRetryable = false;
            $eligibilityReason = __('Recipient phone number is invalid or not registered on WhatsApp.');
        } elseif ($codeStr === '130429' || $codeStr === '429' || str_contains($msgLower, 'rate limit') || str_contains($msgLower, 'throughput')) {
            $category = 'Rate Limit Hit';
            $isRetryable = true;
            $eligibilityReason = __('Temporary WhatsApp API throughput rate limit reached. Safe to retry.');
        } elseif (in_array($codeStr, ['131000', '131016', '131056']) || str_contains($msgLower, 'service temporarily unavailable') || str_contains($msgLower, 'server error')) {
            $category = 'Meta Service Outage';
            $isRetryable = true;
            $eligibilityReason = __('Temporary Meta service interruption. Safe to retry.');
        } elseif (str_contains($msgLower, 'timeout') || str_contains($msgLower, 'timed out') || str_contains($msgLower, 'connection refused') || str_contains($msgLower, 'connectexception')) {
            $category = 'Network Timeout';
            $isRetryable = true;
            $eligibilityReason = __('Network connection timeout during dispatch. Safe to retry.');
        } elseif (in_array($codeStr, ['132000', '132001', '132007']) || str_contains($msgLower, 'template paused') || str_contains($msgLower, 'template deleted')) {
            $category = 'Template Error';
            $isRetryable = false;
            $eligibilityReason = __('Message template has been paused or rejected by Meta.');
        } elseif ($codeStr === '131047' || str_contains($msgLower, '24 hours') || str_contains($msgLower, 're-engagement')) {
            $category = 'Session Window Closed';
            $isRetryable = false;
            $eligibilityReason = __('24-hour service window closed; template message required.');
        } elseif (in_array($codeStr, ['131052', '131053']) || str_contains($msgLower, 'media')) {
            $category = 'Media Processing Error';
            $isRetryable = true;
            $eligibilityReason = __('Temporary media upload or download error. Safe to retry.');
        } elseif ($log->status === 'failed') {
            $category = 'API Dispatch Failure';
            $isRetryable = true;
            $eligibilityReason = __('Temporary API dispatch error. Safe to retry.');
        } else {
            $category = 'General Failure';
            $isRetryable = true;
            $eligibilityReason = __('Delivery failed. Can be retried.');
        }

        // Check Retry Count Limit
        if ($log->retry_count >= self::MAX_RETRY_ATTEMPTS) {
            $isRetryable = false;
            $eligibilityReason = __('Maximum retry attempts (:max) reached.', ['max' => self::MAX_RETRY_ATTEMPTS]);
        }

        // Check if message was already delivered or read
        if ($chat && in_array($chat->status, ['delivered', 'read'])) {
            $isRetryable = false;
            $eligibilityReason = __('Message was already delivered or read.');
        }

        // Check if currently excluded
        if ($log->is_excluded) {
            $isRetryable = false;
            $eligibilityReason = __('Message was excluded from retries by user.');
        }

        return [
            'error_code' => $code ?: 'ERR',
            'error_message' => $message,
            'failure_reason' => $category,
            'reason_category' => $category,
            'error_details' => $details,
            'is_retryable' => $isRetryable,
            'eligibility_reason' => $eligibilityReason,
        ];
    }

    /**
     * Get a query for failed campaign logs.
     *
     * @param int $campaignId
     * @param array $filters
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function getFailedLogsQuery(int $campaignId, array $filters = [])
    {
        $query = CampaignLog::with(['contact', 'chat.logs', 'retries.user'])
            ->where('campaign_id', $campaignId)
            ->where(function ($q) {
                $q->where('status', 'failed')
                  ->orWhereHas('chat', function ($chatQ) {
                      $chatQ->where('status', 'failed');
                  })
                  ->orWhere('retry_status', 'failed');
            });

        // Search by contact name or phone
        if (!empty($filters['search'])) {
            $term = $filters['search'];
            $query->whereHas('contact', function ($contactQ) use ($term) {
                $contactQ->where('first_name', 'like', "%{$term}%")
                         ->orWhere('last_name', 'like', "%{$term}%")
                         ->orWhere('phone', 'like', "%{$term}%");
            });
        }

        // Filter by retry status
        if (!empty($filters['retry_status']) && $filters['retry_status'] !== 'all') {
            if ($filters['retry_status'] === 'not_retried') {
                $query->where('retry_count', 0);
            } elseif ($filters['retry_status'] === 'retried') {
                $query->where('retry_count', '>', 0);
            } elseif ($filters['retry_status'] === 'queued') {
                $query->whereIn('retry_status', ['queued', 'retrying']);
            }
        }

        // Exclude filter
        if (isset($filters['is_excluded']) && $filters['is_excluded'] !== 'all') {
            $query->where('is_excluded', (bool) $filters['is_excluded']);
        } else {
            // By default don't show excluded unless explicitly requested
            $query->where('is_excluded', false);
        }

        return $query->orderBy('id', 'desc');
    }

    /**
     * Queue retry for failed messages with idempotency & duplicate send protection.
     *
     * @param Campaign $campaign
     * @param array $logIds
     * @param bool $allEligible
     * @param int|null $userId
     * @return array
     */
    public function queueRetry(Campaign $campaign, array $logIds = [], bool $allEligible = false, ?int $userId = null): array
    {
        // 1. Verify WhatsApp credentials exist for the organization
        $org = Organization::find($campaign->organization_id);
        $metadata = json_decode($org->metadata ?? '{}', true);
        if (empty($metadata['whatsapp']['access_token']) || empty($metadata['whatsapp']['phone_number_id'])) {
            return [
                'success' => false,
                'message' => __('WhatsApp Cloud API is not configured for this organization.'),
                'queued_count' => 0,
            ];
        }

        // 2. Fetch candidates
        $query = CampaignLog::with(['contact', 'chat.logs', 'retries'])
            ->where('campaign_id', $campaign->id)
            ->where('is_excluded', false);

        if (!$allEligible && !empty($logIds)) {
            $query->whereIn('id', $logIds);
        } else {
            // Find all failed candidates
            $query->where(function ($q) {
                $q->where('status', 'failed')
                  ->orWhereHas('chat', function ($chatQ) {
                      $chatQ->where('status', 'failed');
                  })
                  ->orWhere('retry_status', 'failed');
            });
        }

        $candidates = $query->get();

        $eligibleLogs = [];
        $skippedCount = 0;

        foreach ($candidates as $log) {
            // DUPLICATE PROTECTION 1: Do not retry if already delivered or read
            if ($log->chat && in_array($log->chat->status, ['delivered', 'read'])) {
                $skippedCount++;
                continue;
            }

            // DUPLICATE PROTECTION 2: Do not retry if currently ongoing or already queued
            if ($log->status === 'ongoing' || in_array($log->retry_status, ['queued', 'retrying'])) {
                $skippedCount++;
                continue;
            }

            // Analyze eligibility
            $analysis = $this->analyzeFailure($log);

            // If user clicked "Retry All Eligible", enforce $analysis['is_retryable']
            if ($allEligible && !$analysis['is_retryable']) {
                $skippedCount++;
                continue;
            }

            // If individual retry, check hard limits (max attempts)
            if ($log->retry_count >= self::MAX_RETRY_ATTEMPTS) {
                $skippedCount++;
                continue;
            }

            $eligibleLogs[] = [
                'log' => $log,
                'analysis' => $analysis,
            ];
        }

        if (empty($eligibleLogs)) {
            return [
                'success' => false,
                'message' => __('No messages were eligible for retry (already delivered, in progress, or exceeded retry limits).'),
                'queued_count' => 0,
                'skipped_count' => $skippedCount,
            ];
        }

        $queuedLogIds = [];

        DB::transaction(function () use ($eligibleLogs, $campaign, $userId, &$queuedLogIds) {
            foreach ($eligibleLogs as $item) {
                /** @var CampaignLog $log */
                $log = $item['log'];
                $analysis = $item['analysis'];

                $attemptNumber = ($log->retry_count ?? 0) + 1;

                // Update campaign log status with row lock
                $lockedLog = CampaignLog::where('id', $log->id)->lockForUpdate()->first();
                if (!$lockedLog) continue;

                $lockedLog->retry_count = $attemptNumber;
                $lockedLog->last_retried_at = now();
                $lockedLog->retry_status = 'queued';
                $lockedLog->save();

                // Create CampaignRetry attempt record
                CampaignRetry::create([
                    'campaign_id' => $campaign->id,
                    'campaign_log_id' => $lockedLog->id,
                    'organization_id' => $campaign->organization_id,
                    'attempt_number' => $attemptNumber,
                    'status' => 'queued',
                    'error_code' => $analysis['error_code'] ?? null,
                    'failure_reason' => $analysis['failure_reason'] ?? null,
                    'error_message' => $analysis['error_message'] ?? null,
                    'retried_by' => $userId,
                ]);

                $queuedLogIds[] = $lockedLog->id;
            }
        });

        // Dispatch asynchronous retry job
        if (!empty($queuedLogIds)) {
            RetryCampaignJob::dispatch($campaign->id, $queuedLogIds, $campaign->organization_id, $userId);
        }

        return [
            'success' => true,
            'queued_count' => count($queuedLogIds),
            'skipped_count' => $skippedCount,
            'message' => __(':count failed message(s) queued for resend.', ['count' => count($queuedLogIds)]),
        ];
    }

    /**
     * Exclude or unexclude a failed log from retries.
     *
     * @param Campaign $campaign
     * @param int $logId
     * @param bool $excluded
     * @return bool
     */
    public function setLogExcluded(Campaign $campaign, int $logId, bool $excluded = true): bool
    {
        return CampaignLog::where('id', $logId)
            ->where('campaign_id', $campaign->id)
            ->update(['is_excluded' => $excluded]) > 0;
    }
}
