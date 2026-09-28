<?php

namespace App\Exports;

use App\Models\Campaign;
use App\Models\CampaignLog;
use App\Services\CampaignRecoveryService;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CampaignFailedMessagesExport implements FromCollection, WithHeadings
{
    protected $uuid;

    public function __construct(string $uuid)
    {
        $this->uuid = $uuid;
    }

    public function collection()
    {
        $campaign = Campaign::with('template')
            ->where('uuid', $this->uuid)
            ->where('organization_id', session('current_organization'))
            ->firstOrFail();

        $recoveryService = new CampaignRecoveryService();

        $failedLogs = $recoveryService->getFailedLogsQuery($campaign->id)->get();

        return $failedLogs->map(function ($log) use ($campaign, $recoveryService) {
            $analysis = $recoveryService->analyzeFailure($log);

            return [
                'campaign_name' => $campaign->name,
                'template_name' => $campaign->template->name ?? '—',
                'recipient_name' => ($log->contact->first_name ?? '') . ' ' . ($log->contact->last_name ?? ''),
                'phone' => $log->contact->formatted_phone_number ?? ($log->contact->phone ?? '—'),
                'status' => $log->status === 'success' ? ($log->chat->status ?? 'failed') : $log->status,
                'failure_reason' => $analysis['failure_reason'] ?? 'Unknown Error',
                'error_code' => $analysis['error_code'] ?? '—',
                'error_message' => $analysis['error_message'] ?? '—',
                'retry_eligible' => $analysis['is_retryable'] ? 'Yes' : 'No',
                'retry_attempts' => $log->retry_count ?? 0,
                'last_attempt' => $log->last_retried_at ?? $log->updated_at,
                'retry_status' => $log->retry_status ?? 'none',
            ];
        });
    }

    public function headings(): array
    {
        return [
            'Campaign Name',
            'Template',
            'Recipient Name',
            'Phone Number',
            'Delivery Status',
            'Failure Reason',
            'Error Code',
            'Error Details',
            'Retry Eligible',
            'Retry Attempts',
            'Last Attempt At',
            'Retry Status',
        ];
    }
}
