<template>
    <AppLayout>
        <div class="h-full overflow-y-auto bg-slate-50/70 dark:bg-[#09090B] p-4 sm:p-6 lg:p-8 space-y-6 transition-colors">
            
            <!-- Sticky Header Bar -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-[#6C5CE7] to-[#8B5CF6] flex items-center justify-center text-white shadow-md shadow-purple-600/20 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            {{ $t('Platform Reporting & System Analytics') }}
                        </h1>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">
                            {{ $t('Multi-tenant platform overview, system usage, billing revenue, and queue health') }}
                        </p>
                    </div>
                </div>

                <!-- Controls: Date Range & Export -->
                <div class="flex flex-wrap items-center gap-2.5">
                    <select
                        v-model="selectedPreset"
                        @change="handlePresetChange"
                        class="text-xs font-semibold bg-white dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 rounded-xl px-3 py-2 text-slate-700 dark:text-zinc-200 shadow-xs focus:ring-2 focus:ring-[#6C5CE7] cursor-pointer"
                    >
                        <option value="today">{{ $t('Today') }}</option>
                        <option value="yesterday">{{ $t('Yesterday') }}</option>
                        <option value="7d">{{ $t('Last 7 Days') }}</option>
                        <option value="30d">{{ $t('Last 30 Days') }}</option>
                        <option value="this_month">{{ $t('This Month') }}</option>
                        <option value="last_month">{{ $t('Last Month') }}</option>
                        <option value="custom">{{ $t('Custom Range') }}</option>
                    </select>

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

                    <a
                        :href="exportUrl"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold bg-white dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 hover:bg-slate-50 dark:hover:bg-zinc-800 text-slate-700 dark:text-zinc-200 shadow-xs transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#6C5CE7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        <span>{{ $t('Export CSV') }}</span>
                    </a>
                </div>
            </div>

            <!-- Platform KPI Ribbon -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3 sm:gap-4">
                <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ $t('Total Organizations') }}</span>
                    <p class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white mt-1">
                        {{ formatNumber(props.report?.summary?.total_organizations) }}
                    </p>
                    <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-semibold">{{ props.report?.summary?.active_organizations }} {{ $t('active') }}</span>
                </div>

                <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-purple-500">{{ $t('Active Subscriptions') }}</span>
                    <p class="text-xl sm:text-2xl font-extrabold text-purple-600 dark:text-purple-400 mt-1">
                        {{ formatNumber(props.report?.summary?.active_subscriptions) }}
                    </p>
                    <span class="text-[10px] text-slate-400">{{ props.report?.summary?.expired_subscriptions }} {{ $t('expired') }}</span>
                </div>

                <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#6C5CE7]">{{ $t('Platform Messages') }}</span>
                    <p class="text-xl sm:text-2xl font-extrabold text-[#6C5CE7] dark:text-purple-400 mt-1">
                        {{ formatNumber(props.report?.summary?.platform_messages) }}
                    </p>
                    <span class="text-[10px] text-emerald-600 font-semibold">{{ props.report?.summary?.platform_delivery_rate }}% {{ $t('delivered') }}</span>
                </div>

                <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-cyan-500">{{ $t('Platform Campaigns') }}</span>
                    <p class="text-xl sm:text-2xl font-extrabold text-cyan-600 dark:text-cyan-400 mt-1">
                        {{ formatNumber(props.report?.summary?.platform_campaigns) }}
                    </p>
                    <span class="text-[10px] text-slate-400">{{ $t('Broadcast executions') }}</span>
                </div>

                <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600">{{ $t('Period Revenue') }}</span>
                    <p class="text-xl sm:text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">
                        ${{ formatNumber(props.report?.summary?.period_revenue) }}
                    </p>
                    <span class="text-[10px] text-slate-400">${{ formatNumber(props.report?.summary?.total_revenue) }} {{ $t('all-time') }}</span>
                </div>

                <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-rose-500">{{ $t('Failed Queue Jobs') }}</span>
                    <p class="text-xl sm:text-2xl font-extrabold text-rose-500 mt-1">
                        {{ formatNumber(props.report?.summary?.failed_jobs) }}
                    </p>
                    <span class="text-[10px] text-rose-500/80">{{ $t('Queue health monitor') }}</span>
                </div>
            </div>

            <!-- Organizations Usage Table -->
            <div class="p-5 rounded-3xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-zinc-800/80 pb-3">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                            {{ $t('Organization Usage & Tenant Activity') }}
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ $t('Real-time database records of multi-tenant capacity and message volume') }}
                        </p>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-zinc-800/40 text-slate-400 uppercase font-bold tracking-wider">
                            <tr>
                                <th class="p-3">{{ $t('Organization') }}</th>
                                <th class="p-3">{{ $t('Status') }}</th>
                                <th class="p-3">{{ $t('Plan') }}</th>
                                <th class="p-3 text-center">{{ $t('Contacts') }}</th>
                                <th class="p-3 text-center">{{ $t('Messages') }}</th>
                                <th class="p-3 text-center">{{ $t('Campaigns') }}</th>
                                <th class="p-3 text-right">{{ $t('Registered') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                            <tr v-for="org in props.report?.organizations || []" :key="org.id" class="hover:bg-slate-50/50 dark:hover:bg-zinc-800/30">
                                <td class="p-3 font-bold text-slate-900 dark:text-white">{{ org.name }}</td>
                                <td class="p-3">
                                    <Badge :variant="org.status === 'active' ? 'success' : 'danger'" size="xs">
                                        {{ org.status }}
                                    </Badge>
                                </td>
                                <td class="p-3 font-semibold text-purple-600 dark:text-purple-400">{{ org.plan }}</td>
                                <td class="p-3 text-center font-bold">{{ formatNumber(org.contacts_count) }}</td>
                                <td class="p-3 text-center font-bold text-[#6C5CE7]">{{ formatNumber(org.chats_count) }}</td>
                                <td class="p-3 text-center font-bold text-cyan-600">{{ formatNumber(org.campaigns_count) }}</td>
                                <td class="p-3 text-right font-mono text-slate-400">{{ org.created_at }}</td>
                            </tr>
                            <tr v-if="!props.report?.organizations?.length">
                                <td colspan="7" class="p-8 text-center text-slate-400">
                                    {{ $t('No organizations found') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '../Layout/App.vue';
import Badge from '@/Components/UI/Badge.vue';

const props = defineProps({
    title: String,
    preset: {
        type: String,
        default: '30d'
    },
    rangeLabel: String,
    startDate: String,
    endDate: String,
    filters: Object,
    report: Object,
});

const selectedPreset = ref(props.preset || '30d');
const customStartDate = ref(props.startDate || '');
const customEndDate = ref(props.endDate || '');

const exportUrl = computed(() => {
    let url = `/admin/reports/export?range=${selectedPreset.value}`;
    if (selectedPreset.value === 'custom') {
        url += `&start_date=${customStartDate.value}&end_date=${customEndDate.value}`;
    }
    return url;
});

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
    router.visit('/admin/reports', {
        method: 'get',
        data: {
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
</script>
