<?php

namespace App\Http\Controllers\User;

use App\Exports\CampaignDetailsExport;
use App\Exports\CampaignFailedMessagesExport;
use App\Http\Controllers\Controller as BaseController;
use App\Http\Requests\StoreCampaign;
use App\Http\Resources\CampaignResource;
use App\Http\Resources\CampaignLogResource;
use App\Models\Campaign;
use App\Models\CampaignLog;
use App\Models\ContactGroup;
use App\Models\Organization;
use App\Models\Template;
use App\Services\CampaignRecoveryService;
use App\Services\CampaignService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Maatwebsite\Excel\Facades\Excel;

class CampaignController extends BaseController
{
    private $campaignService;
    private $campaignRecoveryService;

    public function __construct(CampaignService $campaignService, CampaignRecoveryService $campaignRecoveryService)
    {
        $this->campaignService = $campaignService;
        $this->campaignRecoveryService = $campaignRecoveryService;
    }

    public function index(Request $request, $uuid = null)
    {
        $organizationId = session()->get('current_organization');

        if ($uuid == null) {
            $searchTerm = $request->query('search');
            $settings = Organization::where('id', $organizationId)->first();
            $rows = CampaignResource::collection(
                Campaign::with(['template', 'campaignLogs'])
                    ->where('organization_id', $organizationId)
                    ->where('deleted_at', null)
                    ->where(function ($query) use ($searchTerm) {
                        $query->where('name', 'like', '%' . $searchTerm . '%')
                              ->orWhereHas('template', function ($templateQuery) use ($searchTerm) {
                                  $templateQuery->where('name', 'like', '%' . $searchTerm . '%');
                              });
                    })
                    ->latest()
                    ->paginate(10)
            );

            return Inertia::render('User/Campaign/Index', [
                'title' => __('Campaigns'),
                'allowCreate' => true,
                'rows' => $rows,
                'filters' => request()->all(['search']),
                'settings' => $settings
            ]);
        } elseif ($uuid == 'create') {
            $data['settings'] = Organization::where('id', $organizationId)->first();
            $data['templates'] = Template::where('organization_id', $organizationId)
                ->where('deleted_at', null)
                ->where('status', 'APPROVED')
                ->get();

            $data['contactGroups'] = ContactGroup::where('organization_id', $organizationId)
                ->where('deleted_at', null)
                ->get();

            $data['title'] = __('Create campaign');

            return Inertia::render('User/Campaign/Create', $data);
        } else {
            $campaign = Campaign::with('contactGroup', 'template')
                ->where('uuid', $uuid)
                ->where('organization_id', $organizationId)
                ->firstOrFail();

            $counts = $campaign->getCounts();
            $campaignData = $campaign->toArray();
            $campaignData['total_message_count'] = (int) ($counts->total_message_count ?? 0);
            $campaignData['total_sent_count'] = (int) ($counts->total_sent_count ?? 0);
            $campaignData['total_delivered_count'] = (int) ($counts->total_delivered_count ?? 0);
            $campaignData['total_failed_count'] = (int) ($counts->total_failed_count ?? 0);
            $campaignData['total_read_count'] = (int) ($counts->total_read_count ?? 0);
            $campaignData['total_pending_count'] = (int) ($counts->total_pending_count ?? 0);
            $campaignData['total_retry_pending_count'] = (int) ($counts->total_retry_pending_count ?? 0);
            $campaignData['total_retried_count'] = (int) ($counts->total_retried_count ?? 0);

            $tab = $request->query('tab', 'overview');
            $filters = $request->all(['search', 'tab', 'retry_status', 'is_excluded']);

            // 1. General Recipient Logs
            $searchTerm = $request->query('search');
            $rows = CampaignLog::with('contact', 'chat.logs', 'retries')
                ->where('campaign_id', $campaign->id)
                ->where(function ($query) use ($searchTerm) {
                    if ($searchTerm) {
                        $query->whereHas('contact', function ($contactQuery) use ($searchTerm) {
                            $contactQuery->where('first_name', 'like', '%' . $searchTerm . '%')
                                         ->orWhere('last_name', 'like', '%' . $searchTerm . '%')
                                         ->orWhere('phone', 'like', '%' . $searchTerm . '%');
                        });
                    }
                })
                ->orderBy('id', 'desc')
                ->paginate(15)
                ->withQueryString();

            // 2. Failed Recipient Logs & Analysis
            $failedLogsQuery = $this->campaignRecoveryService->getFailedLogsQuery($campaign->id, $filters);
            $failedPaginated = $failedLogsQuery->paginate(15)->withQueryString();

            // Transform each failed row to append failure analysis & retry history
            $failedPaginated->getCollection()->transform(function ($item) {
                $analysis = $this->campaignRecoveryService->analyzeFailure($item);
                $item->failure_analysis = $analysis;
                return $item;
            });

            // 3. Failed Message Summary Metrics for the recovery banner
            $allFailedQuery = $this->campaignRecoveryService->getFailedLogsQuery($campaign->id);
            $allFailedLogs = $allFailedQuery->get();

            $retryableCount = 0;
            $nonRetryableCount = 0;
            $queuedCount = 0;
            $recoveredCount = 0;

            foreach ($allFailedLogs as $fl) {
                if (in_array($fl->retry_status, ['queued', 'retrying'])) {
                    $queuedCount++;
                }
                if ($fl->retry_status === 'success') {
                    $recoveredCount++;
                }

                $analysis = $this->campaignRecoveryService->analyzeFailure($fl);
                if ($analysis['is_retryable']) {
                    $retryableCount++;
                } else {
                    $nonRetryableCount++;
                }
            }

            $failedStats = [
                'total_failed' => $campaignData['total_failed_count'],
                'retryable_count' => $retryableCount,
                'non_retryable_count' => $nonRetryableCount,
                'queued_count' => $queuedCount,
                'recovered_count' => $recoveredCount,
            ];

            return Inertia::render('User/Campaign/View', [
                'title' => __('View campaign') . ' - ' . $campaign->name,
                'campaign' => $campaignData,
                'rows' => $rows,
                'failedRows' => $failedPaginated,
                'failedStats' => $failedStats,
                'activeTab' => $tab,
                'filters' => $filters,
            ]);
        }
    }

    public function store(StoreCampaign $request)
    {
        $this->campaignService->store($request);

        return Redirect::route('campaigns')->with(
            'status', [
                'type' => 'success', 
                'message' => __('Campaign created successfully!')
            ]
        );
    }

    public function export($uuid = null)
    {
        return Excel::download(new CampaignDetailsExport($uuid), 'campaign.csv');
    }

    public function exportFailed($uuid)
    {
        return Excel::download(new CampaignFailedMessagesExport($uuid), 'campaign-failed-messages.csv');
    }

    public function retry(Request $request, $uuid)
    {
        $organizationId = session()->get('current_organization');
        $campaign = Campaign::where('uuid', $uuid)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        $logIds = $request->input('log_ids', []);
        $allEligible = $request->boolean('all_eligible', false);

        $result = $this->campaignRecoveryService->queueRetry(
            $campaign,
            is_array($logIds) ? $logIds : [$logIds],
            $allEligible,
            auth()->id()
        );

        return Redirect::back()->with(
            'status', [
                'type' => $result['success'] ? 'success' : 'danger',
                'message' => $result['message'],
            ]
        );
    }

    public function exclude(Request $request, $uuid)
    {
        $organizationId = session()->get('current_organization');
        $campaign = Campaign::where('uuid', $uuid)
            ->where('organization_id', $organizationId)
            ->firstOrFail();

        $request->validate([
            'log_id' => 'required|integer',
            'is_excluded' => 'nullable|boolean',
        ]);

        $this->campaignRecoveryService->setLogExcluded(
            $campaign,
            $request->input('log_id'),
            $request->boolean('is_excluded', true)
        );

        return Redirect::back()->with(
            'status', [
                'type' => 'success',
                'message' => $request->boolean('is_excluded', true) 
                    ? __('Recipient excluded from campaign retries.') 
                    : __('Recipient re-included for campaign retries.')
            ]
        );
    }

    public function delete($uuid)
    {
        $this->campaignService->destroy($uuid);

        return Redirect::back()->with(
            'status', [
                'type' => 'success', 
                'message' => __('Row deleted successfully!')
            ]
        );
    }
}