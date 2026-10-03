<?php

namespace App\Services;

use App\Models\AutoReply;
use App\Models\Campaign;
use App\Models\CampaignLog;
use App\Models\CampaignRetry;
use App\Models\Chat;
use App\Models\Contact;
use App\Models\ContactGroup;
use App\Models\Organization;
use App\Models\Subscription;
use App\Models\Team;
use App\Models\Template;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportingService
{
    /**
     * Calculate start and end date from a preset or custom range.
     */
    public function calculateDateRange(string $preset = '7d', ?string $customStart = null, ?string $customEnd = null, ?string $timezone = null): array
    {
        $tz = ($timezone && \App\Helpers\DateTimeHelper::isValidTimezone($timezone))
            ? $timezone
            : \App\Helpers\DateTimeHelper::getOrganizationTimezone();

        $now = Carbon::now($tz);

        switch ($preset) {
            case 'today':
                $start = Carbon::today($tz)->startOfDay();
                $end = Carbon::today($tz)->endOfDay();
                $label = __('Today');
                break;
            case 'yesterday':
                $start = Carbon::yesterday($tz)->startOfDay();
                $end = Carbon::yesterday($tz)->endOfDay();
                $label = __('Yesterday');
                break;
            case 'this_week':
                $start = $now->copy()->startOfWeek()->startOfDay();
                $end = $now->copy()->endOfWeek()->endOfDay();
                $label = __('This Week');
                break;
            case 'last_week':
                $start = $now->copy()->subWeek()->startOfWeek()->startOfDay();
                $end = $now->copy()->subWeek()->endOfWeek()->endOfDay();
                $label = __('Last Week');
                break;
            case '30d':
                $start = $now->copy()->subDays(29)->startOfDay();
                $end = $now->copy()->endOfDay();
                $label = __('Last 30 Days');
                break;
            case 'this_month':
                $start = $now->copy()->startOfMonth()->startOfDay();
                $end = $now->copy()->endOfMonth()->endOfDay();
                $label = __('This Month');
                break;
            case 'last_month':
                $start = $now->copy()->subMonth()->startOfMonth()->startOfDay();
                $end = $now->copy()->subMonth()->endOfMonth()->endOfDay();
                $label = __('Last Month');
                break;
            case 'custom':
                $start = $customStart ? Carbon::parse($customStart, $tz)->startOfDay() : $now->copy()->subDays(6)->startOfDay();
                $end = $customEnd ? Carbon::parse($customEnd, $tz)->endOfDay() : $now->copy()->endOfDay();
                $label = $start->format('M d, Y') . ' - ' . $end->format('M d, Y');
                break;
            case '7d':
            default:
                $preset = '7d';
                $start = $now->copy()->subDays(6)->startOfDay();
                $end = $now->copy()->endOfDay();
                $label = __('Last 7 Days');
                break;
        }

        return [$start, $end, $label, $preset];
    }

    /**
     * Customer: Overview Report
     */
    public function getOverviewReport(int $organizationId, Carbon $startDate, Carbon $endDate, array $filters = []): array
    {
        // Total Messages
        $totalMessages = Chat::where('organization_id', $organizationId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->count();

        $sentCount = Chat::where('organization_id', $organizationId)
            ->where('status', 'sent')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->count();

        $deliveredCount = Chat::where('organization_id', $organizationId)
            ->where('status', 'delivered')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->count();

        $readCount = Chat::where('organization_id', $organizationId)
            ->where('status', 'read')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->count();

        $failedCount = Chat::where('organization_id', $organizationId)
            ->where('status', 'failed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->count();

        $outboundCount = Chat::where('organization_id', $organizationId)
            ->where('type', 'outbound')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->count();

        $inboundCount = Chat::where('organization_id', $organizationId)
            ->where('type', 'inbound')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->count();

        $successfulSends = $sentCount + $deliveredCount + $readCount;
        $deliveryRate = $outboundCount > 0 ? round((($deliveredCount + $readCount) / $outboundCount) * 100, 1) : 0;
        $readRate = ($deliveredCount + $readCount) > 0 ? round(($readCount / ($deliveredCount + $readCount)) * 100, 1) : 0;
        $failureRate = $outboundCount > 0 ? round(($failedCount / $outboundCount) * 100, 1) : 0;

        // Conversations & Contacts
        $activeConversations = Chat::where('organization_id', $organizationId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->distinct('contact_id')
            ->count('contact_id');

        $newContacts = Contact::where('organization_id', $organizationId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->count();

        // Campaigns
        $campaignsCount = Campaign::where('organization_id', $organizationId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        $activeCampaigns = Campaign::where('organization_id', $organizationId)
            ->whereIn('status', ['running', 'processing', 'scheduled'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->count();

        // Daily Time Series for ApexCharts
        $dailyTrends = $this->buildDailyTimeSeries($organizationId, $startDate, $endDate);

        return [
            'kpis' => [
                'total_messages' => $totalMessages,
                'outbound_messages' => $outboundCount,
                'inbound_messages' => $inboundCount,
                'sent' => $sentCount,
                'delivered' => $deliveredCount,
                'read' => $readCount,
                'failed' => $failedCount,
                'delivery_rate' => $deliveryRate,
                'read_rate' => $readRate,
                'failure_rate' => $failureRate,
                'active_conversations' => $activeConversations,
                'new_contacts' => $newContacts,
                'campaigns_count' => $campaignsCount,
                'active_campaigns' => $activeCampaigns,
            ],
            'chart_daily' => $dailyTrends,
            'status_distribution' => [
                'delivered' => $deliveredCount,
                'read' => $readCount,
                'sent' => $sentCount,
                'failed' => $failedCount,
            ],
        ];
    }

    /**
     * Customer: Messaging Report
     */
    public function getMessagingReport(int $organizationId, Carbon $startDate, Carbon $endDate, array $filters = []): array
    {
        $overview = $this->getOverviewReport($organizationId, $startDate, $endDate, $filters);

        // Hourly activity distribution (0 - 23h)
        $hourlyData = DB::table('chats')
            ->selectRaw('HOUR(created_at) as hour, count(*) as count, type')
            ->where('organization_id', $organizationId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->groupBy(DB::raw('HOUR(created_at)'), 'type')
            ->get();

        $hourlyInbound = array_fill(0, 24, 0);
        $hourlyOutbound = array_fill(0, 24, 0);

        foreach ($hourlyData as $row) {
            $h = (int) $row->hour;
            if ($row->type === 'inbound') {
                $hourlyInbound[$h] = (int) $row->count;
            } else {
                $hourlyOutbound[$h] = (int) $row->count;
            }
        }

        // Recent 15 chat messages preview
        $recentMessages = Chat::with('contact')
            ->where('organization_id', $organizationId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->orderBy('id', 'desc')
            ->limit(15)
            ->get()
            ->map(function ($chat) {
                return [
                    'id' => $chat->id,
                    'uuid' => $chat->uuid,
                    'contact_name' => $chat->contact ? trim($chat->contact->first_name . ' ' . $chat->contact->last_name) : 'Contact #' . $chat->contact_id,
                    'contact_phone' => $chat->contact ? $chat->contact->phone : '—',
                    'type' => $chat->type,
                    'status' => $chat->status,
                    'created_at' => $chat->created_at ? $chat->created_at->format('Y-m-d H:i') : '',
                ];
            });

        return [
            'kpis' => $overview['kpis'],
            'chart_daily' => $overview['chart_daily'],
            'hourly_inbound' => $hourlyInbound,
            'hourly_outbound' => $hourlyOutbound,
            'recent_messages' => $recentMessages,
        ];
    }

    /**
     * Customer: Campaign Performance & Delivery Report
     */
    public function getCampaignReport(int $organizationId, Carbon $startDate, Carbon $endDate, array $filters = []): array
    {
        $campaignsQuery = Campaign::with(['template', 'contactGroup'])
            ->where('organization_id', $organizationId)
            ->whereBetween('created_at', [$startDate, $endDate]);

        if (!empty($filters['search'])) {
            $campaignsQuery->where('name', 'like', '%' . $filters['search'] . '%');
        }

        if (!empty($filters['status']) && $filters['status'] !== 'all') {
            $campaignsQuery->where('status', $filters['status']);
        }

        $campaigns = $campaignsQuery->orderBy('id', 'desc')->get();

        $totalCampaigns = $campaigns->count();
        $completedCampaigns = $campaigns->where('status', 'completed')->count();
        $runningCampaigns = $campaigns->whereIn('status', ['running', 'processing'])->count();
        $scheduledCampaigns = $campaigns->where('status', 'scheduled')->count();
        $failedCampaigns = $campaigns->where('status', 'failed')->count();

        $totalRecipients = 0;
        $totalSent = 0;
        $totalDelivered = 0;
        $totalRead = 0;
        $totalFailed = 0;
        $totalRetried = 0;

        $campaignList = $campaigns->map(function ($c) use (&$totalRecipients, &$totalSent, &$totalDelivered, &$totalRead, &$totalFailed, &$totalRetried) {
            $counts = $c->getCounts();
            $recipients = (int) ($counts->total_message_count ?? 0);
            $sent = (int) ($counts->total_sent_count ?? 0);
            $delivered = (int) ($counts->total_delivered_count ?? 0);
            $read = (int) ($counts->total_read_count ?? 0);
            $failed = (int) ($counts->total_failed_count ?? 0);
            $retried = (int) ($counts->total_retried_count ?? 0);

            $totalRecipients += $recipients;
            $totalSent += $sent;
            $totalDelivered += $delivered;
            $totalRead += $read;
            $totalFailed += $failed;
            $totalRetried += $retried;

            $deliveryRate = $recipients > 0 ? round(($delivered / $recipients) * 100, 1) : 0;
            $readRate = $delivered > 0 ? round(($read / $delivered) * 100, 1) : 0;
            $failureRate = $recipients > 0 ? round(($failed / $recipients) * 100, 1) : 0;

            return [
                'id' => $c->id,
                'uuid' => $c->uuid,
                'name' => $c->name,
                'status' => $c->status,
                'template_name' => $c->template ? $c->template->name : '—',
                'target_group' => $c->contact_group_id === '0' || $c->contact_group_id === 0 ? __('All Contacts') : ($c->contactGroup ? $c->contactGroup->name : 'Segment'),
                'recipients' => $recipients,
                'sent' => $sent,
                'delivered' => $delivered,
                'read' => $read,
                'failed' => $failed,
                'retried' => $retried,
                'delivery_rate' => $deliveryRate,
                'read_rate' => $readRate,
                'failure_rate' => $failureRate,
                'created_at' => $c->created_at ? $c->created_at->format('Y-m-d H:i') : '',
            ];
        });

        $overallDeliveryRate = $totalRecipients > 0 ? round(($totalDelivered / $totalRecipients) * 100, 1) : 0;
        $overallReadRate = $totalDelivered > 0 ? round(($totalRead / $totalDelivered) * 100, 1) : 0;
        $overallFailureRate = $totalRecipients > 0 ? round(($totalFailed / $totalRecipients) * 100, 1) : 0;

        return [
            'summary' => [
                'total_campaigns' => $totalCampaigns,
                'completed' => $completedCampaigns,
                'running' => $runningCampaigns,
                'scheduled' => $scheduledCampaigns,
                'failed' => $failedCampaigns,
                'total_recipients' => $totalRecipients,
                'total_sent' => $totalSent,
                'total_delivered' => $totalDelivered,
                'total_read' => $totalRead,
                'total_failed' => $totalFailed,
                'total_retried' => $totalRetried,
                'delivery_rate' => $overallDeliveryRate,
                'read_rate' => $overallReadRate,
                'failure_rate' => $overallFailureRate,
            ],
            'campaigns' => $campaignList,
        ];
    }

    /**
     * Customer: Message Failure & Recovery Report
     */
    public function getFailureRecoveryReport(int $organizationId, Carbon $startDate, Carbon $endDate, array $filters = []): array
    {
        // 1. Total Failed logs in date range
        $failedLogs = CampaignLog::with(['campaign', 'contact', 'retries'])
            ->whereHas('campaign', function ($q) use ($organizationId) {
                $q->where('organization_id', $organizationId);
            })
            ->where('status', 'failed')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->orderBy('id', 'desc')
            ->get();

        $totalFailed = $failedLogs->count();
        $totalRetried = $failedLogs->where('retry_count', '>', 0)->count();
        $recoveredCount = $failedLogs->where('retry_status', 'success')->count();
        $retryQueuedCount = $failedLogs->whereIn('retry_status', ['queued', 'retrying'])->count();
        $stillFailedCount = $totalFailed - $recoveredCount;

        $recoverySuccessRate = $totalRetried > 0 ? round(($recoveredCount / $totalRetried) * 100, 1) : 0;

        // Categorize failures
        $reasonCounts = [];
        $recoveryService = app(CampaignRecoveryService::class);

        $recentFailures = [];
        foreach ($failedLogs as $log) {
            $analysis = $recoveryService->analyzeFailure($log);
            $cat = $analysis['reason_category'] ?? 'General Error';
            $reasonCounts[$cat] = ($reasonCounts[$cat] ?? 0) + 1;

            if (count($recentFailures) < 25) {
                $recentFailures[] = [
                    'id' => $log->id,
                    'campaign_name' => $log->campaign ? $log->campaign->name : 'Campaign #' . $log->campaign_id,
                    'campaign_uuid' => $log->campaign ? $log->campaign->uuid : '',
                    'contact_name' => $log->contact ? trim($log->contact->first_name . ' ' . $log->contact->last_name) : 'Contact #' . $log->contact_id,
                    'contact_phone' => $log->contact ? $log->contact->phone : '—',
                    'failure_reason' => $analysis['failure_reason'],
                    'error_code' => $analysis['error_code'] ?: '—',
                    'is_retryable' => $analysis['is_retryable'],
                    'retry_count' => $log->retry_count,
                    'retry_status' => $log->retry_status,
                    'created_at' => $log->created_at ? $log->created_at->format('Y-m-d H:i') : '',
                ];
            }
        }

        arsort($reasonCounts);

        return [
            'summary' => [
                'total_failed' => $totalFailed,
                'total_retried' => $totalRetried,
                'recovered' => $recoveredCount,
                'retry_queued' => $retryQueuedCount,
                'still_failed' => $stillFailedCount,
                'recovery_rate' => $recoverySuccessRate,
            ],
            'categories' => $reasonCounts,
            'recent_failures' => $recentFailures,
        ];
    }

    /**
     * Customer: Contact Growth & Segmentation Report
     */
    public function getContactReport(int $organizationId, Carbon $startDate, Carbon $endDate, array $filters = []): array
    {
        $totalContacts = Contact::where('organization_id', $organizationId)->whereNull('deleted_at')->count();

        $newContacts = Contact::where('organization_id', $organizationId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->count();

        // Contacts by group
        $groups = ContactGroup::where('organization_id', $organizationId)
            ->withCount(['contacts' => function ($q) {
                $q->whereNull('deleted_at');
            }])
            ->get()
            ->map(function ($g) {
                return [
                    'id' => $g->id,
                    'name' => $g->name,
                    'count' => $g->contacts_count,
                ];
            });

        // Contacts engaged in conversations
        $engagedContacts = Chat::where('organization_id', $organizationId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->distinct('contact_id')
            ->count('contact_id');

        // Growth daily trend
        $period = CarbonPeriod::create($startDate, $endDate);
        $growthDates = [];
        $growthCounts = [];

        foreach ($period as $date) {
            $dStr = $date->format('Y-m-d');
            $growthDates[] = $date->format('M d');
            $c = Contact::where('organization_id', $organizationId)
                ->whereDate('created_at', $dStr)
                ->whereNull('deleted_at')
                ->count();
            $growthCounts[] = $c;
        }

        return [
            'summary' => [
                'total_contacts' => $totalContacts,
                'new_contacts' => $newContacts,
                'engaged_contacts' => $engagedContacts,
            ],
            'groups' => $groups,
            'chart_growth' => [
                'categories' => $growthDates,
                'series' => [
                    [
                        'name' => __('New Contacts'),
                        'data' => $growthCounts,
                    ],
                ],
            ],
        ];
    }

    /**
     * Customer: Conversation & Ticket Report
     */
    public function getConversationReport(int $organizationId, Carbon $startDate, Carbon $endDate, array $filters = []): array
    {
        $totalThreads = Chat::where('organization_id', $organizationId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->distinct('contact_id')
            ->count('contact_id');

        $inboundMessages = Chat::where('organization_id', $organizationId)
            ->where('type', 'inbound')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->count();

        $outboundMessages = Chat::where('organization_id', $organizationId)
            ->where('type', 'outbound')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->whereNull('deleted_at')
            ->count();

        // Support Tickets
        $ticketsQuery = DB::table('chat_tickets')
            ->whereBetween('created_at', [$startDate, $endDate]);

        $totalTickets = $ticketsQuery->count();
        $openTickets = (clone $ticketsQuery)->where('status', 'open')->count();
        $closedTickets = (clone $ticketsQuery)->where('status', 'closed')->count();
        $pendingTickets = (clone $ticketsQuery)->where('status', 'pending')->count();

        return [
            'summary' => [
                'total_threads' => $totalThreads,
                'inbound_messages' => $inboundMessages,
                'outbound_messages' => $outboundMessages,
                'total_tickets' => $totalTickets,
                'open_tickets' => $openTickets,
                'closed_tickets' => $closedTickets,
                'pending_tickets' => $pendingTickets,
            ],
        ];
    }

    /**
     * Customer: Team Performance Report
     */
    public function getTeamReport(int $organizationId, Carbon $startDate, Carbon $endDate, array $filters = []): array
    {
        $teamMembers = Team::where('organization_id', $organizationId)
            ->with('user')
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($team) use ($organizationId, $startDate, $endDate) {
                $messagesSent = Chat::where('organization_id', $organizationId)
                    ->where('user_id', $team->user_id)
                    ->where('type', 'outbound')
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->whereNull('deleted_at')
                    ->count();

                $conversationsHandled = Chat::where('organization_id', $organizationId)
                    ->where('user_id', $team->user_id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->whereNull('deleted_at')
                    ->distinct('contact_id')
                    ->count('contact_id');

                return [
                    'user_id' => $team->user_id,
                    'name' => $team->user ? trim($team->user->first_name . ' ' . $team->user->last_name) : 'Agent #' . $team->user_id,
                    'email' => $team->user ? $team->user->email : '—',
                    'role' => $team->role ?? 'member',
                    'messages_sent' => $messagesSent,
                    'conversations_handled' => $conversationsHandled,
                ];
            })
            ->sortByDesc('conversations_handled')
            ->values();

        return [
            'team_members' => $teamMembers,
        ];
    }

    /**
     * Customer: WhatsApp Template Usage Report
     */
    public function getTemplateReport(int $organizationId, Carbon $startDate, Carbon $endDate, array $filters = []): array
    {
        $templates = Template::where('organization_id', $organizationId)
            ->whereNull('deleted_at')
            ->get()
            ->map(function ($tmpl) use ($startDate, $endDate) {
                // Count campaigns using this template
                $campaigns = Campaign::where('template_id', $tmpl->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->get();

                $campaignsCount = $campaigns->count();
                $totalRecipients = 0;
                $totalDelivered = 0;
                $totalRead = 0;
                $totalFailed = 0;

                foreach ($campaigns as $c) {
                    $counts = $c->getCounts();
                    $totalRecipients += (int) ($counts->total_message_count ?? 0);
                    $totalDelivered += (int) ($counts->total_delivered_count ?? 0);
                    $totalRead += (int) ($counts->total_read_count ?? 0);
                    $totalFailed += (int) ($counts->total_failed_count ?? 0);
                }

                $deliveryRate = $totalRecipients > 0 ? round(($totalDelivered / $totalRecipients) * 100, 1) : 0;
                $readRate = $totalDelivered > 0 ? round(($totalRead / $totalDelivered) * 100, 1) : 0;
                $failureRate = $totalRecipients > 0 ? round(($totalFailed / $totalRecipients) * 100, 1) : 0;

                return [
                    'id' => $tmpl->id,
                    'uuid' => $tmpl->uuid,
                    'name' => $tmpl->name,
                    'category' => $tmpl->category ?: 'Marketing',
                    'language' => $tmpl->language ?: 'en_US',
                    'status' => $tmpl->status ?: 'APPROVED',
                    'campaigns_count' => $campaignsCount,
                    'total_recipients' => $totalRecipients,
                    'delivered' => $totalDelivered,
                    'read' => $totalRead,
                    'failed' => $totalFailed,
                    'delivery_rate' => $deliveryRate,
                    'read_rate' => $readRate,
                    'failure_rate' => $failureRate,
                ];
            })
            ->sortByDesc('total_recipients')
            ->values();

        return [
            'templates' => $templates,
        ];
    }

    /**
     * Admin: Global Platform Reporting
     */
    public function getAdminOverviewReport(Carbon $startDate, Carbon $endDate, array $filters = []): array
    {
        // 1. Organization statistics
        $totalOrgs = Organization::withTrashed()->count();
        $newOrgs = Organization::whereBetween('created_at', [$startDate, $endDate])->count();
        $activeOrgs = Organization::whereNull('deleted_at')->count();

        // 2. Subscriptions
        $totalSubscriptions = Subscription::count();
        $activeSubscriptions = Subscription::where('status', 'active')->count();
        $expiredSubscriptions = Subscription::where('status', 'expired')->count();

        // 3. Platform Usage
        $platformMessages = Chat::whereBetween('created_at', [$startDate, $endDate])->whereNull('deleted_at')->count();
        $platformDelivered = Chat::where('status', 'delivered')->whereBetween('created_at', [$startDate, $endDate])->whereNull('deleted_at')->count();
        $platformRead = Chat::where('status', 'read')->whereBetween('created_at', [$startDate, $endDate])->whereNull('deleted_at')->count();
        $platformFailed = Chat::where('status', 'failed')->whereBetween('created_at', [$startDate, $endDate])->whereNull('deleted_at')->count();

        $platformDeliveryRate = $platformMessages > 0 ? round((($platformDelivered + $platformRead) / $platformMessages) * 100, 1) : 0;
        $platformFailureRate = $platformMessages > 0 ? round(($platformFailed / $platformMessages) * 100, 1) : 0;

        $platformCampaigns = Campaign::whereBetween('created_at', [$startDate, $endDate])->count();

        // 4. System Health
        $failedJobs = DB::table('failed_jobs')->count();

        // 5. Revenue
        $totalRevenue = DB::table('billing_transactions')->where('entity_type', 'payment')->sum('amount');
        $periodRevenue = DB::table('billing_transactions')->where('entity_type', 'payment')->whereBetween('created_at', [$startDate, $endDate])->sum('amount');

        // 6. Organization activity breakdown with comprehensive Client-Wise metrics (Requirement 45 & 46)
        $orgBreakdown = Organization::with(['subscription.plan'])
            ->orderBy('id', 'desc')
            ->limit(50)
            ->get()
            ->map(function ($org) use ($startDate, $endDate) {
                $chatsQuery = Chat::where('organization_id', $org->id)
                    ->whereBetween('created_at', [$startDate, $endDate])
                    ->whereNull('deleted_at');
                $chatsCount = (clone $chatsQuery)->count();
                $deliveredCount = (clone $chatsQuery)->whereIn('status', ['delivered', 'read'])->count();
                $failedCount = (clone $chatsQuery)->where('status', 'failed')->count();

                // Calling metrics
                $callsQuery = \App\Models\Call::where('organization_id', $org->id)
                    ->whereBetween('created_at', [$startDate, $endDate]);
                $callsCount = (clone $callsQuery)->count();
                $callSeconds = (clone $callsQuery)->sum('duration') ?: 0;
                $callMinutes = round($callSeconds / 60, 1);
                $avgDuration = $callsCount > 0 ? round($callSeconds / $callsCount) : 0;

                $contactsCount = Contact::where('organization_id', $org->id)->whereNull('deleted_at')->count();
                $campaignsCount = Campaign::where('organization_id', $org->id)->whereBetween('created_at', [$startDate, $endDate])->count();
                $usersCount = Team::where('organization_id', $org->id)->count();
                $ticketsCount = \App\Models\Ticket::where('organization_id', $org->id)->whereBetween('created_at', [$startDate, $endDate])->count();

                $rawValidUntil = $org->subscription ? ($org->subscription->getRawOriginal('valid_until') ?: $org->subscription->valid_until) : null;
                $renewalDate = $rawValidUntil ? Carbon::parse($rawValidUntil)->format('M d, Y') : __('N/A');
                $daysUntilRenewal = $rawValidUntil ? (int) now()->diffInDays(Carbon::parse($rawValidUntil), false) : null;

                return [
                    'id' => $org->id,
                    'name' => $org->name,
                    'status' => $org->status ?? 'active',
                    'plan' => $org->subscription && $org->subscription->plan ? $org->subscription->plan->name : ($org->subscription?->status === 'trial' ? 'Free Trial' : 'No Plan'),
                    'subscription_status' => $org->subscription?->status ?? 'none',
                    'renewal_date' => $renewalDate,
                    'days_until_renewal' => $daysUntilRenewal,
                    'contacts_count' => $contactsCount,
                    'chats_count' => $chatsCount,
                    'delivered_count' => $deliveredCount,
                    'failed_count' => $failedCount,
                    'calls_count' => $callsCount,
                    'call_minutes' => $callMinutes,
                    'avg_call_duration' => $avgDuration,
                    'campaigns_count' => $campaignsCount,
                    'users_count' => $usersCount,
                    'tickets_count' => $ticketsCount,
                    'created_at' => $org->created_at ? $org->created_at->format('Y-m-d') : '',
                ];
            });

        return [
            'summary' => [
                'total_organizations' => $totalOrgs,
                'new_organizations' => $newOrgs,
                'active_organizations' => $activeOrgs,
                'total_subscriptions' => $totalSubscriptions,
                'active_subscriptions' => $activeSubscriptions,
                'expired_subscriptions' => $expiredSubscriptions,
                'platform_messages' => $platformMessages,
                'platform_delivery_rate' => $platformDeliveryRate,
                'platform_failure_rate' => $platformFailureRate,
                'platform_campaigns' => $platformCampaigns,
                'failed_jobs' => $failedJobs,
                'total_revenue' => $totalRevenue ?: 0,
                'period_revenue' => $periodRevenue ?: 0,
            ],
            'organizations' => $orgBreakdown,
        ];
    }

    /**
     * Build daily time series for ApexCharts.
     */
    protected function buildDailyTimeSeries(int $organizationId, Carbon $startDate, Carbon $endDate): array
    {
        $period = CarbonPeriod::create($startDate, $endDate);
        $categories = [];
        $sentSeries = [];
        $deliveredSeries = [];
        $readSeries = [];
        $failedSeries = [];

        foreach ($period as $date) {
            $dStr = $date->format('Y-m-d');
            $categories[] = $date->format('M d');

            $sentSeries[] = Chat::where('organization_id', $organizationId)
                ->where('status', 'sent')
                ->whereDate('created_at', $dStr)
                ->whereNull('deleted_at')
                ->count();

            $deliveredSeries[] = Chat::where('organization_id', $organizationId)
                ->where('status', 'delivered')
                ->whereDate('created_at', $dStr)
                ->whereNull('deleted_at')
                ->count();

            $readSeries[] = Chat::where('organization_id', $organizationId)
                ->where('status', 'read')
                ->whereDate('created_at', $dStr)
                ->whereNull('deleted_at')
                ->count();

            $failedSeries[] = Chat::where('organization_id', $organizationId)
                ->where('status', 'failed')
                ->whereDate('created_at', $dStr)
                ->whereNull('deleted_at')
                ->count();
        }

        return [
            'categories' => $categories,
            'series' => [
                ['name' => __('Delivered'), 'data' => $deliveredSeries, 'color' => '#10B981'],
                ['name' => __('Read'), 'data' => $readSeries, 'color' => '#6C5CE7'],
                ['name' => __('Sent'), 'data' => $sentSeries, 'color' => '#3B82F6'],
                ['name' => __('Failed'), 'data' => $failedSeries, 'color' => '#EF4444'],
            ],
        ];
    }

    /**
     * Export reports to CSV.
     */
    public function exportCsv(string $type, int $organizationId, Carbon $startDate, Carbon $endDate, array $filters = []): StreamedResponse
    {
        $fileName = 'wappiyo-' . $type . '-report-' . $startDate->format('Ymd') . '-' . $endDate->format('Ymd') . '.csv';

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        return response()->stream(function () use ($type, $organizationId, $startDate, $endDate, $filters) {
            $handle = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel
            fputs($handle, "\xEF\xBB\xBF");

            switch ($type) {
                case 'campaigns':
                    fputcsv($handle, ['Campaign Name', 'Template', 'Target Group', 'Status', 'Recipients', 'Sent', 'Delivered', 'Read', 'Failed', 'Delivery %', 'Read %', 'Created At']);
                    $report = $this->getCampaignReport($organizationId, $startDate, $endDate, $filters);
                    foreach ($report['campaigns'] as $c) {
                        fputcsv($handle, [
                            $c['name'],
                            $c['template_name'],
                            $c['target_group'],
                            $c['status'],
                            $c['recipients'],
                            $c['sent'],
                            $c['delivered'],
                            $c['read'],
                            $c['failed'],
                            $c['delivery_rate'] . '%',
                            $c['read_rate'] . '%',
                            $c['created_at'],
                        ]);
                    }
                    break;

                case 'failures':
                    fputcsv($handle, ['Campaign', 'Contact Name', 'Contact Phone', 'Failure Reason', 'Error Code', 'Retryable', 'Retry Attempts', 'Retry Status', 'Failed At']);
                    $report = $this->getFailureRecoveryReport($organizationId, $startDate, $endDate, $filters);
                    foreach ($report['recent_failures'] as $f) {
                        fputcsv($handle, [
                            $f['campaign_name'],
                            $f['contact_name'],
                            $f['contact_phone'],
                            $f['failure_reason'],
                            $f['error_code'],
                            $f['is_retryable'] ? 'Yes' : 'No',
                            $f['retry_count'],
                            $f['retry_status'] ?: 'None',
                            $f['created_at'],
                        ]);
                    }
                    break;

                case 'templates':
                    fputcsv($handle, ['Template Name', 'Category', 'Language', 'Status', 'Campaigns Count', 'Recipients', 'Delivered', 'Read', 'Failed', 'Delivery %', 'Read %']);
                    $report = $this->getTemplateReport($organizationId, $startDate, $endDate, $filters);
                    foreach ($report['templates'] as $t) {
                        fputcsv($handle, [
                            $t['name'],
                            $t['category'],
                            $t['language'],
                            $t['status'],
                            $t['campaigns_count'],
                            $t['total_recipients'],
                            $t['delivered'],
                            $t['read'],
                            $t['failed'],
                            $t['delivery_rate'] . '%',
                            $t['read_rate'] . '%',
                        ]);
                    }
                    break;

                case 'team':
                    fputcsv($handle, ['Agent Name', 'Email', 'Role', 'Messages Sent', 'Conversations Handled']);
                    $report = $this->getTeamReport($organizationId, $startDate, $endDate, $filters);
                    foreach ($report['team_members'] as $m) {
                        fputcsv($handle, [
                            $m['name'],
                            $m['email'],
                            $m['role'],
                            $m['messages_sent'],
                            $m['conversations_handled'],
                        ]);
                    }
                    break;

                case 'overview':
                default:
                    fputcsv($handle, ['Metric', 'Value']);
                    $report = $this->getOverviewReport($organizationId, $startDate, $endDate, $filters);
                    foreach ($report['kpis'] as $key => $val) {
                        fputcsv($handle, [ucwords(str_replace('_', ' ', $key)), $val]);
                    }
                    break;
            }

            fclose($handle);
        }, 200, $headers);
    }
}
