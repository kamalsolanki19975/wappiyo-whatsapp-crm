<?php

namespace App\Exports;

use App\Models\Campaign;
use App\Models\CampaignLog;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class CampaignDetailsExport implements FromCollection, WithHeadings
{
    protected $uuid;

    public function __construct($uuid)
    {
        $this->uuid = $uuid;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $orgId = session('current_organization');
        $query = Campaign::with('template')->where('uuid', $this->uuid);
        if ($orgId) {
            $query->where('organization_id', $orgId);
        }
        $campaign = $query->firstOrFail();

        $campaignLogs = CampaignLog::with('contact', 'chat.logs')
            ->where('campaign_id', $campaign->id)
            ->orderBy('id')
            ->get();

        $logs = $campaignLogs->map(function ($log) use ($campaign) {
            $row = [
                'campaign_name' => $campaign->name,
                'template_name' => $campaign->template ? $campaign->template->name : '',
                'first_name' => $log->contact ? $log->contact->first_name : '',
                'last_name' => $log->contact ? $log->contact->last_name : '',
                'phone' => $log->contact ? $log->contact->formatted_phone_number : '',
                'updated_at' => $log->updated_at,
                'status' => ($log->status == 'success' && $log->chat) ? $log->chat->status : $log->status
            ];

            return array_map([$this, 'sanitizeCellValue'], $row);
        });

        return $logs;
    }

    private function sanitizeCellValue($value)
    {
        if (is_string($value) && preg_match('/^[=\+\-@\t\r]/', $value)) {
            return "'" . $value;
        }
        return $value;
    }

    public function headings(): array
    {
        // Define your headers here
        return [
            'Campaign Name',
            'Template',
            'First Name',
            'Last Name',
            'Phone Number',
            'Last Updated',
            'Status'
        ];
    }
}
