<?php

namespace App\Http\Controllers\User;

use Carbon\Carbon;
use Carbon\CarbonPeriod;
use DB;
use App\Http\Controllers\Controller as BaseController;
use App\Models\AutoReply;
use App\Models\Campaign;
use App\Models\CampaignLog;
use App\Models\Chat;
use App\Models\Contact;
use App\Models\Team;
use App\Models\Template;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends BaseController
{
    public function index(Request $request)
    {
        $organizationId = session()->get('current_organization');
        $range = $request->query('range', '7d');

        [$startDate, $endDate, $prevStartDate, $prevEndDate, $rangeLabel] = $this->calculateDates(
            $range,
            $request->query('start_date'),
            $request->query('end_date')
        );

        // 1. KPI Metrics (Current vs Previous Period)
        $currentMessages = Chat::where('organization_id', $organizationId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->count();

        $prevMessages = Chat::where('organization_id', $organizationId)
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->whereNull('deleted_at')
            ->count();

        $currentConversations = Chat::where('organization_id', $organizationId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->distinct('contact_id')
            ->count('contact_id');

        $prevConversations = Chat::where('organization_id', $organizationId)
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->whereNull('deleted_at')
            ->distinct('contact_id')
            ->count('contact_id');

        $currentContacts = Contact::where('organization_id', $organizationId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->count();

        $prevContacts = Contact::where('organization_id', $organizationId)
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->whereNull('deleted_at')
            ->count();

        $currentCampaigns = Campaign::where('organization_id', $organizationId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $prevCampaigns = Campaign::where('organization_id', $organizationId)
            ->whereBetween('created_at', [$prevStartDate, $prevEndDate])
            ->count();

        $kpis = [
            'messages' => [
                'value' => $currentMessages,
                'delta' => $this->calculateDelta($currentMessages, $prevMessages),
            ],
            'conversations' => [
                'value' => $currentConversations,
                'delta' => $this->calculateDelta($currentConversations, $prevConversations),
            ],
            'contacts' => [
                'value' => $currentContacts,
                'delta' => $this->calculateDelta($currentContacts, $prevContacts),
            ],
            'campaigns' => [
                'value' => $currentCampaigns,
                'delta' => $this->calculateDelta($currentCampaigns, $prevCampaigns),
            ],
        ];

        // 2. Messaging Delivery Performance
        $inboundCount = Chat::where('organization_id', $organizationId)
            ->where('type', 'inbound')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->count();

        $outboundCount = Chat::where('organization_id', $organizationId)
            ->where('type', 'outbound')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->count();

        $deliveredCount = Chat::where('organization_id', $organizationId)
            ->where('type', 'outbound')
            ->whereIn('status', ['delivered', 'read'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->count();

        $readCount = Chat::where('organization_id', $organizationId)
            ->where('type', 'outbound')
            ->where(function ($query) {
                $query->where('status', 'read')
                    ->orWhere('is_read', 1);
            })
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->count();

        $failedCount = Chat::where('organization_id', $organizationId)
            ->where('type', 'outbound')
            ->where('status', 'failed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->count();

        $deliveryRate = $outboundCount > 0 ? round(($deliveredCount / $outboundCount) * 100, 1) : 0;
        $readRate = $deliveredCount > 0 ? round(($readCount / $deliveredCount) * 100, 1) : 0;
        $failedRate = $outboundCount > 0 ? round(($failedCount / $outboundCount) * 100, 1) : 0;

        $deliveryStats = [
            'inbound' => $inboundCount,
            'outbound' => $outboundCount,
            'delivered' => $deliveredCount,
            'read' => $readCount,
            'failed' => $failedCount,
            'delivery_rate' => $deliveryRate,
            'read_rate' => $readRate,
            'failed_rate' => $failedRate,
        ];

        // 3. Continuous Time-Series Chart Data (Daily Buckets)
        $chartSeries = $this->buildDailyTimeSeries($organizationId, $startDate, $endDate);

        // 4. Campaign Analytics & Top Campaigns
        $campaigns = Campaign::where('organization_id', $organizationId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['template', 'contactGroup'])
            ->latest('id')
            ->limit(10)
            ->get()
            ->map(function ($camp) {
                $logs = CampaignLog::where('campaign_id', $camp->id)->select('status', DB::raw('count(*) as count'))->groupBy('status')->pluck('count', 'status');
                $total = $logs->sum();
                $success = $logs['success'] ?? 0;
                $failed = $logs['failed'] ?? 0;
                $successRate = $total > 0 ? round(($success / $total) * 100, 1) : 0;

                return [
                    'id' => $camp->id,
                    'uuid' => $camp->uuid,
                    'name' => $camp->name,
                    'status' => $camp->status,
                    'template_name' => $camp->template->name ?? '—',
                    'contact_group_name' => $camp->contactGroup->name ?? '—',
                    'recipients_count' => $total,
                    'success_count' => $success,
                    'failed_count' => $failed,
                    'success_rate' => $successRate,
                    'created_at' => Carbon::parse($camp->created_at)->format('M d, Y'),
                ];
            });

        // 5. Template Usage Breakdown
        $templates = Template::where('organization_id', $organizationId)
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($tpl) use ($organizationId) {
                $campaignCount = Campaign::where('organization_id', $organizationId)
                    ->where('template_id', $tpl->id)
                    ->count();

                return [
                    'id' => $tpl->id,
                    'uuid' => $tpl->uuid,
                    'name' => $tpl->name,
                    'category' => $tpl->category ?? 'MARKETING',
                    'status' => $tpl->status,
                    'campaigns_count' => $campaignCount,
                    'updated_at' => Carbon::parse($tpl->updated_at)->format('M d, Y'),
                ];
            })
            ->sortByDesc('campaigns_count')
            ->values();

        // 6. Automation Summary
        $automations = AutoReply::where('organization_id', $organizationId)
            ->whereNull('deleted_at')
            ->get();

        $activeAutomations = 0;
        $draftAutomations = 0;
        $inactiveAutomations = 0;

        foreach ($automations as $auto) {
            $meta = json_decode($auto->metadata, true) ?: [];
            $st = $meta['status'] ?? 'active';
            if ($st === 'draft') $draftAutomations++;
            elseif ($st === 'inactive') $inactiveAutomations++;
            else $activeAutomations++;
        }

        $automationStats = [
            'total' => $automations->count(),
            'active' => $activeAutomations,
            'draft' => $draftAutomations,
            'inactive' => $inactiveAutomations,
        ];

        // 7. Team Activity
        $teams = Team::where('organization_id', $organizationId)
            ->with('user')
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($team) use ($organizationId, $startDate, $endDate) {
                $chatsHandled = Chat::where('organization_id', $organizationId)
                    ->where('user_id', $team->user_id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->whereNull('deleted_at')
                    ->count();

                return [
                    'user_id' => $team->user_id,
                    'name' => $team->user ? ($team->user->first_name . ' ' . $team->user->last_name) : 'Agent',
                    'email' => $team->user->email ?? '—',
                    'role' => $team->role ?? 'member',
                    'chats_handled' => $chatsHandled,
                ];
            })
            ->sortByDesc('chats_handled')
            ->values();

        return Inertia::render('User/Analytics/Index', [
            'title' => __('Analytics & Reporting'),
            'range' => $range,
            'rangeLabel' => $rangeLabel,
            'startDate' => $startDate->format('Y-m-d'),
            'endDate' => $endDate->format('Y-m-d'),
            'kpis' => $kpis,
            'deliveryStats' => $deliveryStats,
            'chartSeries' => $chartSeries,
            'campaigns' => $campaigns,
            'templates' => $templates,
            'automationStats' => $automationStats,
            'teams' => $teams,
        ]);
    }

    public function export(Request $request)
    {
        $organizationId = session()->get('current_organization');
        $range = $request->query('range', '30d');

        [$startDate, $endDate] = $this->calculateDates(
            $range,
            $request->query('start_date'),
            $request->query('end_date')
        );

        $chartSeries = $this->buildDailyTimeSeries($organizationId, $startDate, $endDate);

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="wappiyo-analytics-' . $range . '-' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($chartSeries) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date', 'Inbound Messages', 'Outbound Messages', 'Total Messages', 'New Contacts']);

            for ($i = 0; $i < count($chartSeries['categories']); $i++) {
                $date = $chartSeries['categories'][$i];
                $inbound = $chartSeries['inbound'][$i] ?? 0;
                $outbound = $chartSeries['outbound'][$i] ?? 0;
                $contacts = $chartSeries['contacts'][$i] ?? 0;
                $total = $inbound + $outbound;

                fputcsv($file, [$date, $inbound, $outbound, $total, $contacts]);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    private function calculateDates(string $range, ?string $customStart, ?string $customEnd): array
    {
        $now = Carbon::now();

        switch ($range) {
            case 'today':
                $start = $now->copy()->startOfDay();
                $end = $now->copy()->endOfDay();
                $prevStart = $start->copy()->subDay();
                $prevEnd = $end->copy()->subDay();
                $label = 'Today';
                break;

            case 'yesterday':
                $start = $now->copy()->subDay()->startOfDay();
                $end = $now->copy()->subDay()->endOfDay();
                $prevStart = $start->copy()->subDay();
                $prevEnd = $end->copy()->subDay();
                $label = 'Yesterday';
                break;

            case '30d':
                $start = $now->copy()->subDays(29)->startOfDay();
                $end = $now->copy()->endOfDay();
                $prevStart = $start->copy()->subDays(30);
                $prevEnd = $start->copy()->subSecond();
                $label = 'Last 30 Days';
                break;

            case '90d':
                $start = $now->copy()->subDays(89)->startOfDay();
                $end = $now->copy()->endOfDay();
                $prevStart = $start->copy()->subDays(90);
                $prevEnd = $start->copy()->subSecond();
                $label = 'Last 90 Days';
                break;

            case 'custom':
                $start = $customStart ? Carbon::parse($customStart)->startOfDay() : $now->copy()->subDays(6)->startOfDay();
                $end = $customEnd ? Carbon::parse($customEnd)->endOfDay() : $now->copy()->endOfDay();
                $diffDays = $start->diffInDays($end) + 1;
                $prevStart = $start->copy()->subDays($diffDays);
                $prevEnd = $start->copy()->subSecond();
                $label = $start->format('M d') . ' – ' . $end->format('M d, Y');
                break;

            case '7d':
            default:
                $start = $now->copy()->subDays(6)->startOfDay();
                $end = $now->copy()->endOfDay();
                $prevStart = $start->copy()->subDays(7);
                $prevEnd = $start->copy()->subSecond();
                $label = 'Last 7 Days';
                break;
        }

        return [$start, $end, $prevStart, $prevEnd, $label];
    }

    private function calculateDelta($current, $previous): ?float
    {
        if ($previous == 0) {
            return $current > 0 ? 100.0 : 0.0;
        }

        return round((($current - $previous) / $previous) * 100, 1);
    }

    private function buildDailyTimeSeries($organizationId, Carbon $startDate, Carbon $endDate): array
    {
        // Fetch raw grouped chats
        $inboundRows = Chat::where('organization_id', $organizationId)
            ->where('type', 'inbound')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');

        $outboundRows = Chat::where('organization_id', $organizationId)
            ->where('type', 'outbound')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');

        $contactRows = Contact::where('organization_id', $organizationId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->groupBy('date')
            ->pluck('count', 'date');

        $period = CarbonPeriod::create($startDate, $endDate);
        $categories = [];
        $inbound = [];
        $outbound = [];
        $contacts = [];

        foreach ($period as $date) {
            $dateKey = $date->format('Y-m-d');
            $categories[] = $date->format('Y-m-d\TH:i:s.000\Z');
            $inbound[] = (int)($inboundRows[$dateKey] ?? 0);
            $outbound[] = (int)($outboundRows[$dateKey] ?? 0);
            $contacts[] = (int)($contactRows[$dateKey] ?? 0);
        }

        return [
            'categories' => $categories,
            'inbound' => $inbound,
            'outbound' => $outbound,
            'contacts' => $contacts,
        ];
    }
}
