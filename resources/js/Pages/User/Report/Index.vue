<template>
    <AppLayout>
        <div class="h-full overflow-y-auto bg-slate-50/70 dark:bg-[#09090B] p-4 sm:p-6 lg:p-8 space-y-6 transition-colors">
            
            <!-- Sticky Header Bar -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-[#6C5CE7] to-[#A29BFE] flex items-center justify-center text-white shadow-md shadow-purple-600/20 shrink-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        </div>
                        <div>
                            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                                {{ $t('Reports & Analytics Center') }}
                            </h1>
                            <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">
                                {{ $t('Actual database performance metrics, delivery rates, and recovery tracking') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Date Range Presets & CSV Export Action -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <!-- Preset Selector Dropdown -->
                    <div class="relative">
                        <select
                            v-model="selectedPreset"
                            @change="handlePresetChange"
                            class="text-xs font-semibold bg-white dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 rounded-xl px-3 py-2 text-slate-700 dark:text-zinc-200 shadow-xs focus:ring-2 focus:ring-[#6C5CE7] focus:outline-none cursor-pointer"
                        >
                            <option value="today">{{ $t('Today') }}</option>
                            <option value="yesterday">{{ $t('Yesterday') }}</option>
                            <option value="7d">{{ $t('Last 7 Days') }}</option>
                            <option value="30d">{{ $t('Last 30 Days') }}</option>
                            <option value="this_month">{{ $t('This Month') }}</option>
                            <option value="last_month">{{ $t('Last Month') }}</option>
                            <option value="custom">{{ $t('Custom Range') }}</option>
                        </select>
                    </div>

                    <!-- Custom Range Inputs (if selected) -->
                    <div v-if="selectedPreset === 'custom'" class="flex items-center gap-1.5">
                        <input
                            type="date"
                            v-model="customStartDate"
                            class="text-xs bg-white dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 rounded-xl px-2.5 py-1.5 text-slate-700 dark:text-zinc-200 shadow-xs focus:ring-2 focus:ring-[#6C5CE7]"
                        />
                        <span class="text-xs text-slate-400">to</span>
                        <input
                            type="date"
                            v-model="customEndDate"
                            class="text-xs bg-white dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 rounded-xl px-2.5 py-1.5 text-slate-700 dark:text-zinc-200 shadow-xs focus:ring-2 focus:ring-[#6C5CE7]"
                        />
                        <button
                            type="button"
                            @click="applyCustomDate"
                            class="px-2.5 py-1.5 rounded-xl text-xs font-bold bg-[#6C5CE7] text-white hover:bg-[#5b4bc4] transition-colors"
                        >
                            {{ $t('Apply') }}
                        </button>
                    </div>

                    <!-- Export CSV Link -->
                    <a
                        :href="exportUrl"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-white dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-700 dark:text-zinc-200 shadow-xs transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#6C5CE7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        <span>{{ $t('Export CSV') }}</span>
                    </a>
                </div>
            </div>

            <!-- Report Navigation Sub-Tabs -->
            <div class="flex items-center gap-1 overflow-x-auto border-b border-slate-200 dark:border-zinc-800 pb-px scrollbar-none">
                <button
                    v-for="tab in reportTabs"
                    :key="tab.key"
                    @click="switchReportType(tab.key)"
                    type="button"
                    :class="[
                        'flex items-center gap-2 px-3.5 py-2.5 text-xs font-semibold border-b-2 whitespace-nowrap transition-all rounded-t-lg',
                        activeTab === tab.key
                            ? 'border-[#6C5CE7] text-[#6C5CE7] dark:text-purple-400 bg-white dark:bg-[#111113]'
                            : 'border-transparent text-slate-500 hover:text-slate-800 dark:text-zinc-400 dark:hover:text-zinc-200 hover:bg-slate-100/50 dark:hover:bg-zinc-800/50'
                    ]"
                >
                    <component :is="tab.icon" class="w-4 h-4" />
                    <span>{{ tab.label }}</span>
                    <span
                        v-if="tab.badge"
                        class="px-1.5 py-0.5 rounded-full text-[10px] font-extrabold bg-[#6C5CE7]/10 text-[#6C5CE7] dark:bg-purple-500/20 dark:text-purple-400"
                    >
                        {{ tab.badge }}
                    </span>
                </button>
            </div>

            <!-- ========================================== -->
            <!-- TAB 1: OVERVIEW REPORT                     -->
            <!-- ========================================== -->
            <div v-if="activeTab === 'overview'" class="space-y-6">
                <!-- Top KPI Cards Ribbon -->
                <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 sm:gap-4">
                    <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ $t('Total Messages') }}</span>
                        <p class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white mt-1">
                            {{ formatNumber(props.report?.kpis?.total_messages) }}
                        </p>
                        <span class="text-[10px] text-slate-400">{{ formatNumber(props.report?.kpis?.outbound_messages) }} out / {{ formatNumber(props.report?.kpis?.inbound_messages) }} in</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">{{ $t('Delivered') }}</span>
                        <p class="text-xl sm:text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">
                            {{ formatNumber(props.report?.kpis?.delivered) }}
                        </p>
                        <span class="text-[10px] text-emerald-600/80 font-semibold">{{ props.report?.kpis?.delivery_rate }}% {{ $t('success') }}</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-[#6C5CE7] dark:text-purple-400">{{ $t('Read Receipts') }}</span>
                        <p class="text-xl sm:text-2xl font-extrabold text-[#6C5CE7] dark:text-purple-400 mt-1">
                            {{ formatNumber(props.report?.kpis?.read) }}
                        </p>
                        <span class="text-[10px] text-purple-600/80 font-semibold">{{ props.report?.kpis?.read_rate }}% {{ $t('read rate') }}</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-rose-500">{{ $t('Failed') }}</span>
                        <p class="text-xl sm:text-2xl font-extrabold text-rose-500 mt-1">
                            {{ formatNumber(props.report?.kpis?.failed) }}
                        </p>
                        <span class="text-[10px] text-rose-500/80 font-semibold">{{ props.report?.kpis?.failure_rate }}% {{ $t('failure') }}</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-cyan-500">{{ $t('Conversations') }}</span>
                        <p class="text-xl sm:text-2xl font-extrabold text-cyan-600 dark:text-cyan-400 mt-1">
                            {{ formatNumber(props.report?.kpis?.active_conversations) }}
                        </p>
                        <span class="text-[10px] text-cyan-600/80">{{ $t('Unique contacts') }}</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-amber-500">{{ $t('New Contacts') }}</span>
                        <p class="text-xl sm:text-2xl font-extrabold text-amber-600 dark:text-amber-400 mt-1">
                            {{ formatNumber(props.report?.kpis?.new_contacts) }}
                        </p>
                        <span class="text-[10px] text-amber-600/80">{{ $t('Added in period') }}</span>
                    </div>

                    <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-purple-500">{{ $t('Campaigns') }}</span>
                        <p class="text-xl sm:text-2xl font-extrabold text-purple-600 dark:text-purple-400 mt-1">
                            {{ formatNumber(props.report?.kpis?.campaigns_count) }}
                        </p>
                        <span class="text-[10px] text-purple-600/80">{{ props.report?.kpis?.active_campaigns }} {{ $t('in flight') }}</span>
                    </div>
                </div>

                <!-- Daily Traffic Area Chart -->
                <div class="p-5 rounded-3xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-zinc-800/80 pb-3">
                        <div>
                            <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                                {{ $t('Messaging Delivery & Status Trends') }}
                            </h2>
                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ $t('Daily volume breakdown for') }} {{ props.rangeLabel }}
                            </p>
                        </div>
                    </div>

                    <div class="min-h-[320px]">
                        <apexchart
                            v-if="hasOverviewChartData"
                            type="area"
                            height="320"
                            :options="overviewChartOptions"
                            :series="props.report?.chart_daily?.series || []"
                        />
                        <div v-else class="h-64 flex flex-col items-center justify-center text-center p-6 text-slate-400">
                            <p class="text-xs">{{ $t('No messaging data recorded during this time window.') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 2: MESSAGING REPORT                    -->
            <!-- ========================================== -->
            <div v-if="activeTab === 'messaging'" class="space-y-6">
                <!-- Hourly 24-hour Distribution Bar Chart -->
                <div class="p-5 rounded-3xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs space-y-4">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                            {{ $t('24-Hour Activity Distribution') }}
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ $t('Identify peak customer engagement times (0:00 to 23:00 UTC)') }}
                        </p>
                    </div>

                    <div class="min-h-[280px]">
                        <apexchart
                            type="bar"
                            height="280"
                            :options="hourlyChartOptions"
                            :series="hourlySeries"
                        />
                    </div>
                </div>

                <!-- Recent Messages Table -->
                <div class="p-5 rounded-3xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                        {{ $t('Recent Message Activity Logs') }}
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 dark:bg-zinc-800/40 text-slate-400 uppercase font-bold tracking-wider">
                                <tr>
                                    <th class="p-3">{{ $t('Contact') }}</th>
                                    <th class="p-3">{{ $t('Phone') }}</th>
                                    <th class="p-3">{{ $t('Direction') }}</th>
                                    <th class="p-3">{{ $t('Status') }}</th>
                                    <th class="p-3 text-right">{{ $t('Timestamp') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                                <tr v-for="m in props.report?.recent_messages || []" :key="m.id" class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30">
                                    <td class="p-3 font-semibold text-slate-900 dark:text-white">{{ m.contact_name }}</td>
                                    <td class="p-3 font-mono text-slate-500">{{ m.contact_phone }}</td>
                                    <td class="p-3">
                                        <Badge :variant="m.type === 'inbound' ? 'cyan' : 'primary'" size="sm">
                                            {{ m.type }}
                                        </Badge>
                                    </td>
                                    <td class="p-3">
                                        <Badge :variant="statusBadgeVariant(m.status)" size="sm">
                                            {{ m.status }}
                                        </Badge>
                                    </td>
                                    <td class="p-3 text-right font-mono text-slate-400">{{ m.created_at }}</td>
                                </tr>
                                <tr v-if="!props.report?.recent_messages?.length">
                                    <td colspan="5" class="p-8 text-center text-slate-400">
                                        {{ $t('No recent messages in selected period') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 3: CAMPAIGNS REPORT                    -->
            <!-- ========================================== -->
            <div v-if="activeTab === 'campaigns'" class="space-y-6">
                <!-- Summary Ribbon -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                    <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800">
                        <span class="text-[10px] font-bold uppercase text-slate-400">{{ $t('Total Campaigns') }}</span>
                        <p class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">{{ props.report?.summary?.total_campaigns || 0 }}</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800">
                        <span class="text-[10px] font-bold uppercase text-slate-400">{{ $t('Total Audience') }}</span>
                        <p class="text-xl font-extrabold text-slate-900 dark:text-white mt-1">{{ formatNumber(props.report?.summary?.total_recipients) }}</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800">
                        <span class="text-[10px] font-bold uppercase text-emerald-500">{{ $t('Delivered') }}</span>
                        <p class="text-xl font-extrabold text-emerald-600 mt-1">{{ formatNumber(props.report?.summary?.total_delivered) }}</p>
                        <span class="text-[10px] text-emerald-600 font-bold">{{ props.report?.summary?.delivery_rate }}%</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800">
                        <span class="text-[10px] font-bold uppercase text-purple-500">{{ $t('Read') }}</span>
                        <p class="text-xl font-extrabold text-purple-600 mt-1">{{ formatNumber(props.report?.summary?.total_read) }}</p>
                        <span class="text-[10px] text-purple-600 font-bold">{{ props.report?.summary?.read_rate }}%</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800">
                        <span class="text-[10px] font-bold uppercase text-rose-500">{{ $t('Failed') }}</span>
                        <p class="text-xl font-extrabold text-rose-600 mt-1">{{ formatNumber(props.report?.summary?.total_failed) }}</p>
                        <span class="text-[10px] text-rose-600 font-bold">{{ props.report?.summary?.failure_rate }}%</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800">
                        <span class="text-[10px] font-bold uppercase text-cyan-500">{{ $t('Retried') }}</span>
                        <p class="text-xl font-extrabold text-cyan-600 mt-1">{{ formatNumber(props.report?.summary?.total_retried) }}</p>
                    </div>
                </div>

                <!-- Campaign Performance Table -->
                <div class="p-5 rounded-3xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                        {{ $t('Campaign Performance Breakdown') }}
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 dark:bg-zinc-800/40 text-slate-400 uppercase font-bold tracking-wider">
                                <tr>
                                    <th class="p-3">{{ $t('Campaign') }}</th>
                                    <th class="p-3">{{ $t('Template') }}</th>
                                    <th class="p-3">{{ $t('Audience') }}</th>
                                    <th class="p-3 text-center">{{ $t('Delivered') }}</th>
                                    <th class="p-3 text-center">{{ $t('Read') }}</th>
                                    <th class="p-3 text-center">{{ $t('Failed') }}</th>
                                    <th class="p-3 text-center">{{ $t('Retried') }}</th>
                                    <th class="p-3 text-right">{{ $t('Action') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                                <tr v-for="c in props.report?.campaigns || []" :key="c.id" class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30">
                                    <td class="p-3">
                                        <div class="font-bold text-slate-900 dark:text-white">{{ c.name }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ c.created_at }}</div>
                                    </td>
                                    <td class="p-3 font-semibold text-purple-600 dark:text-purple-400">{{ c.template_name }}</td>
                                    <td class="p-3 font-bold">{{ c.recipients }}</td>
                                    <td class="p-3 text-center">
                                        <span class="font-bold text-emerald-600">{{ c.delivered }}</span>
                                        <span class="text-[10px] text-slate-400 block">({{ c.delivery_rate }}%)</span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="font-bold text-purple-600">{{ c.read }}</span>
                                        <span class="text-[10px] text-slate-400 block">({{ c.read_rate }}%)</span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="font-bold text-rose-500">{{ c.failed }}</span>
                                        <span class="text-[10px] text-slate-400 block">({{ c.failure_rate }}%)</span>
                                    </td>
                                    <td class="p-3 text-center font-bold text-cyan-600">{{ c.retried }}</td>
                                    <td class="p-3 text-right">
                                        <Link
                                            :href="'/campaigns/' + c.uuid"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-[#6C5CE7]/10 text-[#6C5CE7] hover:bg-[#6C5CE7]/20 transition-colors"
                                        >
                                            {{ $t('View / Recover') }}
                                        </Link>
                                    </td>
                                </tr>
                                <tr v-if="!props.report?.campaigns?.length">
                                    <td colspan="8" class="p-8 text-center text-slate-400">
                                        {{ $t('No campaigns executed in this date range') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 4: FAILURES & RECOVERY REPORT          -->
            <!-- ========================================== -->
            <div v-if="activeTab === 'failures'" class="space-y-6">
                <!-- Recovery KPIs Ribbon -->
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
                    <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800">
                        <span class="text-[10px] font-bold uppercase text-rose-500">{{ $t('Total Failures') }}</span>
                        <p class="text-2xl font-extrabold text-rose-600 mt-1">{{ props.report?.summary?.total_failed || 0 }}</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800">
                        <span class="text-[10px] font-bold uppercase text-amber-500">{{ $t('Retries Attempted') }}</span>
                        <p class="text-2xl font-extrabold text-amber-600 mt-1">{{ props.report?.summary?.total_retried || 0 }}</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800">
                        <span class="text-[10px] font-bold uppercase text-emerald-500">{{ $t('Successfully Recovered') }}</span>
                        <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ props.report?.summary?.recovered || 0 }}</p>
                        <span class="text-[10px] text-emerald-600 font-bold">{{ props.report?.summary?.recovery_rate }}% {{ $t('success rate') }}</span>
                    </div>
                    <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800">
                        <span class="text-[10px] font-bold uppercase text-slate-400">{{ $t('Still Failing') }}</span>
                        <p class="text-2xl font-extrabold text-slate-700 dark:text-zinc-300 mt-1">{{ props.report?.summary?.still_failed || 0 }}</p>
                    </div>
                    <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800">
                        <span class="text-[10px] font-bold uppercase text-cyan-500">{{ $t('Retry Queued') }}</span>
                        <p class="text-2xl font-extrabold text-cyan-600 mt-1">{{ props.report?.summary?.retry_queued || 0 }}</p>
                    </div>
                </div>

                <!-- Failure Categories Breakdown -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                    <div class="p-5 rounded-3xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            {{ $t('Top Failure Classifications') }}
                        </h3>
                        <div class="space-y-2">
                            <div
                                v-for="(cnt, cat) in props.report?.categories || {}"
                                :key="cat"
                                class="flex items-center justify-between p-2.5 rounded-xl bg-slate-50 dark:bg-zinc-800/40 text-xs"
                            >
                                <span class="font-semibold text-slate-700 dark:text-zinc-300 truncate mr-2">{{ cat }}</span>
                                <Badge variant="danger" size="xs">{{ cnt }}</Badge>
                            </div>
                            <div v-if="!Object.keys(props.report?.categories || {}).length" class="text-center py-6 text-slate-400 text-xs">
                                {{ $t('No failures recorded') }}
                            </div>
                        </div>
                    </div>

                    <!-- Recent Failed Recipients List -->
                    <div class="lg:col-span-2 p-5 rounded-3xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">
                            {{ $t('Recent Failed Recipients & Recovery Status') }}
                        </h3>

                        <div class="overflow-x-auto">
                            <table class="w-full text-left text-xs">
                                <thead class="bg-slate-50 dark:bg-zinc-800/40 text-slate-400 uppercase font-bold tracking-wider">
                                    <tr>
                                        <th class="p-2.5">{{ $t('Campaign') }}</th>
                                        <th class="p-2.5">{{ $t('Recipient') }}</th>
                                        <th class="p-2.5">{{ $t('Reason') }}</th>
                                        <th class="p-2.5 text-center">{{ $t('Retries') }}</th>
                                        <th class="p-2.5 text-right">{{ $t('Status') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                                    <tr v-for="f in props.report?.recent_failures || []" :key="f.id" class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30">
                                        <td class="p-2.5 font-bold text-slate-900 dark:text-white">
                                            <Link v-if="f.campaign_uuid" :href="'/campaigns/' + f.campaign_uuid" class="hover:underline text-[#6C5CE7]">
                                                {{ f.campaign_name }}
                                            </Link>
                                            <span v-else>{{ f.campaign_name }}</span>
                                        </td>
                                        <td class="p-2.5">
                                            <div>{{ f.contact_name }}</div>
                                            <div class="text-[10px] text-slate-400 font-mono">{{ f.contact_phone }}</div>
                                        </td>
                                        <td class="p-2.5 max-w-[200px]">
                                            <div class="truncate text-rose-500 font-medium" :title="f.failure_reason">{{ f.failure_reason }}</div>
                                            <div class="text-[10px] text-slate-400 font-mono">Code: {{ f.error_code }}</div>
                                        </td>
                                        <td class="p-2.5 text-center font-bold">{{ f.retry_count }}</td>
                                        <td class="p-2.5 text-right">
                                            <Badge :variant="f.retry_status === 'success' ? 'success' : (f.retry_status === 'queued' ? 'warning' : 'danger')" size="sm">
                                                {{ f.retry_status || 'failed' }}
                                            </Badge>
                                        </td>
                                    </tr>
                                    <tr v-if="!props.report?.recent_failures?.length">
                                        <td colspan="5" class="p-8 text-center text-slate-400">
                                            {{ $t('Zero failures recorded in this period') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 5: CONTACTS REPORT                     -->
            <!-- ========================================== -->
            <div v-if="activeTab === 'contacts'" class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-5 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800">
                        <span class="text-[11px] font-bold uppercase text-slate-400">{{ $t('Total Audience Size') }}</span>
                        <p class="text-3xl font-extrabold text-slate-900 dark:text-white mt-1">{{ formatNumber(props.report?.summary?.total_contacts) }}</p>
                    </div>
                    <div class="p-5 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800">
                        <span class="text-[11px] font-bold uppercase text-emerald-500">{{ $t('New Contacts Added') }}</span>
                        <p class="text-3xl font-extrabold text-emerald-600 mt-1">{{ formatNumber(props.report?.summary?.new_contacts) }}</p>
                    </div>
                    <div class="p-5 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800">
                        <span class="text-[11px] font-bold uppercase text-purple-500">{{ $t('Active Engaged Contacts') }}</span>
                        <p class="text-3xl font-extrabold text-purple-600 mt-1">{{ formatNumber(props.report?.summary?.engaged_contacts) }}</p>
                    </div>
                </div>

                <!-- Contact Groups Breakdown -->
                <div class="p-5 rounded-3xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                        {{ $t('Audience Segmentation by Group') }}
                    </h2>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
                        <div
                            v-for="g in props.report?.groups || []"
                            :key="g.id"
                            class="p-4 rounded-xl bg-slate-50 dark:bg-zinc-800/40 border border-slate-100 dark:border-zinc-800"
                        >
                            <span class="text-xs font-bold text-slate-700 dark:text-zinc-200 block truncate">{{ g.name }}</span>
                            <p class="text-lg font-extrabold text-purple-600 dark:text-purple-400 mt-1">{{ g.count }} {{ $t('contacts') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 6: CONVERSATIONS REPORT                -->
            <!-- ========================================== -->
            <div v-if="activeTab === 'conversations'" class="space-y-6">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="p-5 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800">
                        <span class="text-[11px] font-bold uppercase text-slate-400">{{ $t('Total Threads') }}</span>
                        <p class="text-2xl font-extrabold text-slate-900 dark:text-white mt-1">{{ formatNumber(props.report?.summary?.total_threads) }}</p>
                    </div>
                    <div class="p-5 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800">
                        <span class="text-[11px] font-bold uppercase text-emerald-500">{{ $t('Open Tickets') }}</span>
                        <p class="text-2xl font-extrabold text-emerald-600 mt-1">{{ formatNumber(props.report?.summary?.open_tickets) }}</p>
                    </div>
                    <div class="p-5 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800">
                        <span class="text-[11px] font-bold uppercase text-purple-500">{{ $t('Closed Tickets') }}</span>
                        <p class="text-2xl font-extrabold text-purple-600 mt-1">{{ formatNumber(props.report?.summary?.closed_tickets) }}</p>
                    </div>
                    <div class="p-5 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800">
                        <span class="text-[11px] font-bold uppercase text-amber-500">{{ $t('Pending Tickets') }}</span>
                        <p class="text-2xl font-extrabold text-amber-600 mt-1">{{ formatNumber(props.report?.summary?.pending_tickets) }}</p>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 7: TEAM REPORT                         -->
            <!-- ========================================== -->
            <div v-if="activeTab === 'teams'" class="space-y-6">
                <div class="p-5 rounded-3xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                        {{ $t('Team Member Workload & Engagement') }}
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 dark:bg-zinc-800/40 text-slate-400 uppercase font-bold tracking-wider">
                                <tr>
                                    <th class="p-3">{{ $t('Member') }}</th>
                                    <th class="p-3">{{ $t('Role') }}</th>
                                    <th class="p-3 text-center">{{ $t('Messages Dispatched') }}</th>
                                    <th class="p-3 text-right">{{ $t('Conversations Handled') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                                <tr v-for="m in props.report?.team_members || []" :key="m.user_id" class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30">
                                    <td class="p-3">
                                        <div class="font-bold text-slate-900 dark:text-white">{{ m.name }}</div>
                                        <div class="text-[10px] text-slate-400">{{ m.email }}</div>
                                    </td>
                                    <td class="p-3">
                                        <Badge variant="neutral" size="xs">{{ m.role }}</Badge>
                                    </td>
                                    <td class="p-3 text-center font-bold text-[#6C5CE7]">{{ m.messages_sent }}</td>
                                    <td class="p-3 text-right font-bold text-slate-900 dark:text-white">{{ m.conversations_handled }}</td>
                                </tr>
                                <tr v-if="!props.report?.team_members?.length">
                                    <td colspan="4" class="p-8 text-center text-slate-400">
                                        {{ $t('No team members found') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- ========================================== -->
            <!-- TAB 8: TEMPLATES REPORT                    -->
            <!-- ========================================== -->
            <div v-if="activeTab === 'templates'" class="space-y-6">
                <div class="p-5 rounded-3xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs space-y-4">
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                        {{ $t('WhatsApp Template Engagement & Delivery Rates') }}
                    </h2>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead class="bg-slate-50 dark:bg-zinc-800/40 text-slate-400 uppercase font-bold tracking-wider">
                                <tr>
                                    <th class="p-3">{{ $t('Template') }}</th>
                                    <th class="p-3">{{ $t('Category') }}</th>
                                    <th class="p-3 text-center">{{ $t('Campaigns') }}</th>
                                    <th class="p-3 text-center">{{ $t('Recipients') }}</th>
                                    <th class="p-3 text-center">{{ $t('Delivered') }}</th>
                                    <th class="p-3 text-center">{{ $t('Read') }}</th>
                                    <th class="p-3 text-right">{{ $t('Failed') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                                <tr v-for="t in props.report?.templates || []" :key="t.id" class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30">
                                    <td class="p-3">
                                        <div class="font-bold text-slate-900 dark:text-white">{{ t.name }}</div>
                                        <div class="text-[10px] text-slate-400 font-mono">{{ t.language }}</div>
                                    </td>
                                    <td class="p-3 font-semibold text-purple-600">{{ t.category }}</td>
                                    <td class="p-3 text-center font-bold">{{ t.campaigns_count }}</td>
                                    <td class="p-3 text-center font-bold">{{ t.total_recipients }}</td>
                                    <td class="p-3 text-center">
                                        <span class="font-bold text-emerald-600">{{ t.delivered }}</span>
                                        <span class="text-[10px] text-slate-400 block">({{ t.delivery_rate }}%)</span>
                                    </td>
                                    <td class="p-3 text-center">
                                        <span class="font-bold text-purple-600">{{ t.read }}</span>
                                        <span class="text-[10px] text-slate-400 block">({{ t.read_rate }}%)</span>
                                    </td>
                                    <td class="p-3 text-right">
                                        <span class="font-bold text-rose-500">{{ t.failed }}</span>
                                        <span class="text-[10px] text-slate-400 block">({{ t.failure_rate }}%)</span>
                                    </td>
                                </tr>
                                <tr v-if="!props.report?.templates?.length">
                                    <td colspan="7" class="p-8 text-center text-slate-400">
                                        {{ $t('No template activity recorded') }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed, h } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import AppLayout from '../Layout/App.vue';
import Badge from '@/Components/UI/Badge.vue';
import { useTheme } from '@/Composables/useTheme';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    title: String,
    activeType: {
        type: String,
        default: 'overview'
    },
    preset: {
        type: String,
        default: '7d'
    },
    rangeLabel: String,
    startDate: String,
    endDate: String,
    filters: Object,
    report: Object,
});

const { isDark } = useTheme();

const activeTab = ref(props.activeType || 'overview');
const selectedPreset = ref(props.preset || '7d');
const customStartDate = ref(props.startDate || '');
const customEndDate = ref(props.endDate || '');

// SVG Icons as functional components for tabs
const IconChart = {
    render: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
        h('rect', { width: '18', height: '18', x: '3', y: '3', rx: '2' }),
        h('line', { x1: '3', y1: '9', x2: '21', y2: '9' }),
        h('line', { x1: '9', y1: '21', x2: '9', y2: '9' })
    ])
};

const IconMessage = {
    render: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
        h('path', { d: 'M7.9 20A9 9 0 1 0 4 16.1L2 22Z' })
    ])
};

const IconCampaign = {
    render: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
        h('path', { d: 'm3 11 18-5v12L3 14v-3z' }),
        h('path', { d: 'M11.6 16.8a3 3 0 1 1-5.8-1.6' })
    ])
};

const IconAlert = {
    render: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
        h('circle', { cx: '12', cy: '12', r: '10' }),
        h('line', { x1: '12', y1: '8', x2: '12', y2: '12' }),
        h('line', { x1: '12', y1: '16', x2: '12.01', y2: '16' })
    ])
};

const IconUsers = {
    render: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
        h('path', { d: 'M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2' }),
        h('circle', { cx: '9', cy: '7', r: '4' }),
        h('path', { d: 'M22 21v-2a4 4 0 0 0-3-3.87' }),
        h('path', { d: 'M16 3.13a4 4 0 0 1 0 7.75' })
    ])
};

const IconChat = {
    render: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
        h('path', { d: 'M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z' })
    ])
};

const IconTeam = {
    render: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
        h('path', { d: 'M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10' }),
        h('path', { d: 'm9 12 2 2 4-4' })
    ])
};

const IconTemplate = {
    render: () => h('svg', { xmlns: 'http://www.w3.org/2000/svg', viewBox: '0 0 24 24', fill: 'none', stroke: 'currentColor', 'stroke-width': '2' }, [
        h('path', { d: 'M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z' }),
        h('polyline', { points: '14 2 14 8 20 8' })
    ])
};

const reportTabs = computed(() => [
    { key: 'overview', label: trans('Overview'), icon: IconChart },
    { key: 'messaging', label: trans('Messaging'), icon: IconMessage },
    { key: 'campaigns', label: trans('Campaigns'), icon: IconCampaign },
    { key: 'failures', label: trans('Failures & Recovery'), icon: IconAlert, badge: props.report?.summary?.total_failed ? String(props.report.summary.total_failed) : null },
    { key: 'contacts', label: trans('Contacts'), icon: IconUsers },
    { key: 'conversations', label: trans('Conversations'), icon: IconChat },
    { key: 'teams', label: trans('Team Performance'), icon: IconTeam },
    { key: 'templates', label: trans('WhatsApp Templates'), icon: IconTemplate },
]);

const exportUrl = computed(() => {
    let url = `/reports/export?type=${activeTab.value}&range=${selectedPreset.value}`;
    if (selectedPreset.value === 'custom') {
        url += `&start_date=${customStartDate.value}&end_date=${customEndDate.value}`;
    }
    return url;
});

const switchReportType = (tabKey) => {
    activeTab.value = tabKey;
    navigateToReport();
};

const handlePresetChange = () => {
    if (selectedPreset.value !== 'custom') {
        navigateToReport();
    }
};

const applyCustomDate = () => {
    if (customStartDate.value && customEndDate.value) {
        navigateToReport();
    }
};

const navigateToReport = () => {
    router.visit('/reports', {
        method: 'get',
        data: {
            type: activeTab.value,
            range: selectedPreset.value,
            start_date: selectedPreset.value === 'custom' ? customStartDate.value : null,
            end_date: selectedPreset.value === 'custom' ? customEndDate.value : null,
        },
        preserveState: true,
        preserveScroll: true,
    });
};

const formatNumber = (num) => {
    if (num === null || num === undefined) return '0';
    return Number(num).toLocaleString();
};

const statusBadgeVariant = (st) => {
    const s = String(st || '').toLowerCase();
    if (s === 'delivered' || s === 'read' || s === 'success') return 'success';
    if (s === 'sent') return 'primary';
    if (s === 'failed') return 'danger';
    return 'neutral';
};

const hasOverviewChartData = computed(() => {
    return (props.report?.chart_daily?.categories || []).length > 0;
});

const overviewChartOptions = computed(() => ({
    chart: {
        type: 'area',
        toolbar: { show: false },
        fontFamily: 'inherit',
        background: 'transparent',
    },
    theme: { mode: isDark.value ? 'dark' : 'light' },
    colors: ['#10B981', '#6C5CE7', '#3B82F6', '#EF4444'],
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: 2 },
    xaxis: {
        categories: props.report?.chart_daily?.categories || [],
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: { style: { colors: isDark.value ? '#94A3B8' : '#64748B' } },
    },
    yaxis: {
        labels: {
            style: { colors: isDark.value ? '#94A3B8' : '#64748B' },
            formatter: (v) => Math.round(v),
        }
    },
    grid: {
        borderColor: isDark.value ? 'rgba(255, 255, 255, 0.05)' : '#F1F5F9',
        strokeDashArray: 4,
    },
    tooltip: { theme: isDark.value ? 'dark' : 'light' },
    legend: {
        position: 'top',
        labels: { colors: isDark.value ? '#E2E8F0' : '#334155' }
    }
}));

const hourlySeries = computed(() => [
    { name: trans('Inbound Messages'), data: props.report?.hourly_inbound || [] },
    { name: trans('Outbound Messages'), data: props.report?.hourly_outbound || [] },
]);

const hourlyChartOptions = computed(() => ({
    chart: {
        type: 'bar',
        toolbar: { show: false },
        fontFamily: 'inherit',
        background: 'transparent',
    },
    theme: { mode: isDark.value ? 'dark' : 'light' },
    colors: ['#06B6D4', '#6C5CE7'],
    dataLabels: { enabled: false },
    plotOptions: { bar: { columnWidth: '55%', borderRadius: 3 } },
    xaxis: {
        categories: Array.from({ length: 24 }, (_, i) => `${i}:00`),
        labels: { style: { colors: isDark.value ? '#94A3B8' : '#64748B' } }
    },
    yaxis: {
        labels: { style: { colors: isDark.value ? '#94A3B8' : '#64748B' } }
    },
    grid: {
        borderColor: isDark.value ? 'rgba(255, 255, 255, 0.05)' : '#F1F5F9',
        strokeDashArray: 4,
    },
    legend: {
        position: 'top',
        labels: { colors: isDark.value ? '#E2E8F0' : '#334155' }
    }
}));
</script>
