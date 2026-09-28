<?php

namespace App\Http\Controllers\Admin;

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
     * Display platform-wide admin reporting dashboard.
     */
    public function index(Request $request)
    {
        $preset = $request->query('range', '30d');
        $customStart = $request->query('start_date');
        $customEnd = $request->query('end_date');

        $companyTz = \App\Helpers\DateTimeHelper::getCompanyTimezone();
        [$startDate, $endDate, $rangeLabel, $activePreset] = $this->reportingService->calculateDateRange($preset, $customStart, $customEnd, $companyTz);
        $filters = $request->all(['search', 'status']);

        $reportData = $this->reportingService->getAdminOverviewReport($startDate, $endDate, $filters);

        return Inertia::render('Admin/Report/Index', [
            'title' => __('Platform Reporting & System Analytics'),
            'preset' => $activePreset,
            'rangeLabel' => $rangeLabel,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'filters' => $filters,
            'report' => $reportData,
        ]);
    }

    /**
     * Stream CSV export of platform usage and organizations.
     */
    public function export(Request $request): StreamedResponse
    {
        $preset = $request->query('range', '30d');
        $customStart = $request->query('start_date');
        $customEnd = $request->query('end_date');

        $companyTz = \App\Helpers\DateTimeHelper::getCompanyTimezone();
        [$startDate, $endDate] = $this->reportingService->calculateDateRange($preset, $customStart, $customEnd, $companyTz);
        $filters = $request->all(['search', 'status']);

        $reportData = $this->reportingService->getAdminOverviewReport($startDate, $endDate, $filters);

        $fileName = 'wappiyo-admin-platform-report-' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($reportData) {
            $handle = fopen('php://output', 'w');
            fputs($handle, "\xEF\xBB\xBF");

            // Section 1: Summary
            fputcsv($handle, ['Platform Metric', 'Value']);
            foreach ($reportData['summary'] as $key => $val) {
                fputcsv($handle, [ucwords(str_replace('_', ' ', $key)), $val]);
            }

            fputcsv($handle, []);
            fputcsv($handle, ['Organization Name', 'Status', 'Plan', 'Contacts', 'Chats', 'Campaigns', 'Registered At']);
            foreach ($reportData['organizations'] as $org) {
                fputcsv($handle, [
                    $org['name'],
                    $org['status'],
                    $org['plan'],
                    $org['contacts_count'],
                    $org['chats_count'],
                    $org['campaigns_count'],
                    $org['created_at'],
                ]);
            }

            fclose($handle);
        }, 200, $headers);
    }
}
