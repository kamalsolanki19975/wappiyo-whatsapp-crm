<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller as BaseController;
use App\Services\ReportingService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends BaseController
{
    protected ReportingService $reportingService;

    public function __construct(ReportingService $reportingService)
    {
        $this->reportingService = $reportingService;
    }

    /**
     * Display reporting center dashboard with active report module.
     */
    public function index(Request $request)
    {
        $organizationId = session('current_organization');
        if (!$organizationId) {
            return redirect()->route('dashboard');
        }

        $type = $request->query('type', 'overview');
        $validTypes = ['overview', 'messaging', 'campaigns', 'failures', 'contacts', 'conversations', 'teams', 'templates'];
        if (!in_array($type, $validTypes)) {
            $type = 'overview';
        }

        $preset = $request->query('range', '7d');
        $customStart = $request->query('start_date');
        $customEnd = $request->query('end_date');

        $orgTz = \App\Helpers\DateTimeHelper::getOrganizationTimezone($organizationId);
        [$startDate, $endDate, $rangeLabel, $activePreset] = $this->reportingService->calculateDateRange($preset, $customStart, $customEnd, $orgTz);

        $filters = $request->all(['search', 'status']);

        $reportData = [];
        switch ($type) {
            case 'messaging':
                $reportData = $this->reportingService->getMessagingReport($organizationId, $startDate, $endDate, $filters);
                break;
            case 'campaigns':
                $reportData = $this->reportingService->getCampaignReport($organizationId, $startDate, $endDate, $filters);
                break;
            case 'failures':
                $reportData = $this->reportingService->getFailureRecoveryReport($organizationId, $startDate, $endDate, $filters);
                break;
            case 'contacts':
                $reportData = $this->reportingService->getContactReport($organizationId, $startDate, $endDate, $filters);
                break;
            case 'conversations':
                $reportData = $this->reportingService->getConversationReport($organizationId, $startDate, $endDate, $filters);
                break;
            case 'teams':
                $reportData = $this->reportingService->getTeamReport($organizationId, $startDate, $endDate, $filters);
                break;
            case 'templates':
                $reportData = $this->reportingService->getTemplateReport($organizationId, $startDate, $endDate, $filters);
                break;
            case 'overview':
            default:
                $reportData = $this->reportingService->getOverviewReport($organizationId, $startDate, $endDate, $filters);
                break;
        }

        return Inertia::render('User/Report/Index', [
            'title' => __('Reports & Analytics Center'),
            'activeType' => $type,
            'preset' => $activePreset,
            'rangeLabel' => $rangeLabel,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'filters' => $filters,
            'report' => $reportData,
        ]);
    }

    /**
     * Export active report to CSV.
     */
    public function export(Request $request): StreamedResponse
    {
        $organizationId = session('current_organization');
        if (!$organizationId) {
            abort(403, 'Unauthorized tenant access');
        }

        $type = $request->query('type', 'overview');
        $preset = $request->query('range', '7d');
        $customStart = $request->query('start_date');
        $customEnd = $request->query('end_date');

        $orgTz = \App\Helpers\DateTimeHelper::getOrganizationTimezone($organizationId);
        [$startDate, $endDate] = $this->reportingService->calculateDateRange($preset, $customStart, $customEnd, $orgTz);
        $filters = $request->all(['search', 'status']);

        return $this->reportingService->exportCsv($type, $organizationId, $startDate, $endDate, $filters);
    }
}
