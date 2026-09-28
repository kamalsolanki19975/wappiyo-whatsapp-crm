<?php

namespace App\Jobs;

use App\Models\Campaign;
use App\Models\CampaignLog;
use App\Models\CampaignRetry;
use App\Models\Organization;
use App\Services\WhatsappService;
use App\Traits\TemplateTrait;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RetryCampaignJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, TemplateTrait;

    protected $campaignId;
    protected $campaignLogIds;
    protected $organizationId;
    protected $userId;
    protected $whatsappService;

    /**
     * Create a new job instance.
     *
     * @param int $campaignId
     * @param array $campaignLogIds
     * @param int $organizationId
     * @param int|null $userId
     */
    public function __construct(int $campaignId, array $campaignLogIds, int $organizationId, ?int $userId = null)
    {
        $this->campaignId = $campaignId;
        $this->campaignLogIds = $campaignLogIds;
        $this->organizationId = $organizationId;
        $this->userId = $userId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $campaign = Campaign::find($this->campaignId);
        if (!$campaign) {
            Log::warning("RetryCampaignJob: Campaign {$this->campaignId} not found.");
            return;
        }

        $this->initializeWhatsappService();

        $logs = CampaignLog::with('contact')
            ->where('campaign_id', $this->campaignId)
            ->whereIn('id', $this->campaignLogIds)
            ->get();

        foreach ($logs as $campaignLog) {
            $this->processSingleRetry($campaign, $campaignLog);
        }
    }

    /**
     * Process an individual message retry attempt with idempotency.
     *
     * @param Campaign $campaign
     * @param CampaignLog $campaignLog
     */
    protected function processSingleRetry(Campaign $campaign, CampaignLog $campaignLog): void
    {
        DB::transaction(function () use ($campaign, $campaignLog) {
            $lockedLog = CampaignLog::where('id', $campaignLog->id)
                ->where('retry_status', 'queued')
                ->lockForUpdate()
                ->first();

            if (!$lockedLog) {
                // Already processed or state changed concurrently
                return;
            }

            $lockedLog->retry_status = 'retrying';
            $lockedLog->save();

            $retryRecord = CampaignRetry::where('campaign_log_id', $lockedLog->id)
                ->where('status', 'queued')
                ->latest('id')
                ->first();

            if ($retryRecord) {
                $retryRecord->status = 'processing';
                $retryRecord->save();
            }

            try {
                $templateRequest = $this->buildTemplateRequest($campaign->id, $lockedLog->contact);
                $responseObject = $this->whatsappService->sendTemplateMessage(
                    $lockedLog->contact->uuid,
                    $templateRequest,
                    $campaign->id
                );

                if ($responseObject->success === true) {
                    $chatId = $responseObject->data->chat->id ?? null;
                    $lockedLog->chat_id = $chatId;
                    $lockedLog->status = 'success';
                    $lockedLog->retry_status = 'success';
                    $lockedLog->last_retried_at = now();
                    $lockedLog->save();

                    if ($retryRecord) {
                        $retryRecord->status = 'success';
                        $retryRecord->metadata = json_encode($responseObject);
                        $retryRecord->save();
                    }
                } else {
                    $errorCode = null;
                    $errorMessage = $responseObject->message ?? 'Retry attempt failed';

                    if (isset($responseObject->data->error)) {
                        $errorCode = $responseObject->data->error->code ?? null;
                        $errorMessage = $responseObject->data->error->message ?? $errorMessage;
                    }

                    $lockedLog->status = 'failed';
                    $lockedLog->retry_status = 'failed';
                    $lockedLog->metadata = json_encode($responseObject);
                    $lockedLog->last_retried_at = now();
                    $lockedLog->save();

                    if ($retryRecord) {
                        $retryRecord->status = 'failed';
                        $retryRecord->error_code = $errorCode;
                        $retryRecord->error_message = $errorMessage;
                        $retryRecord->metadata = json_encode($responseObject);
                        $retryRecord->save();
                    }
                }
            } catch (\Exception $e) {
                Log::error("RetryCampaignJob single error for log {$lockedLog->id}: " . $e->getMessage());

                $lockedLog->status = 'failed';
                $lockedLog->retry_status = 'failed';
                $lockedLog->last_retried_at = now();
                $lockedLog->save();

                if ($retryRecord) {
                    $retryRecord->status = 'failed';
                    $retryRecord->error_message = $e->getMessage();
                    $retryRecord->save();
                }
            }
        });
    }

    /**
     * Initialize WhatsApp Cloud API service using tenant credentials.
     */
    protected function initializeWhatsappService(): void
    {
        $organization = Organization::find($this->organizationId);
        $metadata = json_decode($organization->metadata ?? '{}', true);

        $accessToken = $metadata['whatsapp']['access_token'] ?? null;
        $apiVersion = config('graph.api_version', 'v20.0');
        $appId = $metadata['whatsapp']['app_id'] ?? null;
        $phoneNumberId = $metadata['whatsapp']['phone_number_id'] ?? null;
        $wabaId = $metadata['whatsapp']['waba_id'] ?? null;

        $this->whatsappService = new WhatsappService(
            $accessToken,
            $apiVersion,
            $appId,
            $phoneNumberId,
            $wabaId,
            $this->organizationId
        );
    }
}
