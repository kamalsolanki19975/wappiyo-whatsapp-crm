<template>
    <AppLayout>
        <div class="h-full overflow-y-auto bg-slate-50/70 dark:bg-[#09090B] p-4 sm:p-6 lg:p-8 space-y-6 transition-colors">
            
            <!-- Header Bar -->
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-[#6C5CE7] to-[#8B5CF6] flex items-center justify-center text-white shadow-md shadow-purple-600/20 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                            {{ $t('Client-Wise Platform Reporting & Analytics') }}
                        </h1>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">
                            {{ $t('Multi-tenant client usage overview, messaging volume, WhatsApp calling, and subscription renewals') }}
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
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ $t('Total Clients') }}</span>
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

            <!-- Client-Wise Usage Table (Requirements 45 & 46) -->
            <div class="p-5 rounded-3xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 dark:border-zinc-800/80 pb-3">
                    <div>
                        <h2 class="text-sm font-bold text-slate-900 dark:text-white">
                            {{ $t('Client-Wise Usage & Subscription Reporting') }}
                        </h2>
                        <p class="text-xs text-slate-400 mt-0.5">
                            {{ $t('Granular multi-tenant breakdown of messaging, WhatsApp voice calling, campaigns, contacts, and renewals') }}
                        </p>
                    </div>

                    <!-- Search & Filter -->
                    <div class="flex items-center gap-2">
                        <div class="relative">
                            <input
                                type="text"
                                v-model="searchQuery"
                                :placeholder="$t('Search client or plan...')"
                                class="w-48 sm:w-60 text-xs bg-slate-50 dark:bg-zinc-800/60 border border-slate-200 dark:border-zinc-700/60 rounded-xl pl-8 pr-3 py-1.5 text-slate-700 dark:text-zinc-200 focus:ring-2 focus:ring-[#6C5CE7]"
                            />
                            <svg class="w-3.5 h-3.5 text-slate-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        </div>
                    </div>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs whitespace-nowrap">
                        <thead class="bg-slate-50 dark:bg-zinc-800/40 text-slate-400 uppercase font-bold tracking-wider">
                            <tr>
                                <th class="p-3">{{ $t('Client') }}</th>
                                <th class="p-3">{{ $t('Plan') }}</th>
                                <th class="p-3 text-right">{{ $t('Messages') }}</th>
                                <th class="p-3 text-right">{{ $t('Delivered') }}</th>
                                <th class="p-3 text-right">{{ $t('Failed') }}</th>
                                <th class="p-3 text-right">{{ $t('Calls') }}</th>
                                <th class="p-3 text-right">{{ $t('Call Mins') }}</th>
                                <th class="p-3 text-right">{{ $t('Campaigns') }}</th>
                                <th class="p-3 text-right">{{ $t('Contacts') }}</th>
                                <th class="p-3 text-right">{{ $t('Users') }}</th>
                                <th class="p-3 text-center">{{ $t('Renewal') }}</th>
                                <th class="p-3 text-center">{{ $t('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-zinc-800">
                            <tr
                                v-for="org in filteredOrganizations"
                                :key="org.id"
                                class="hover:bg-slate-50/60 dark:hover:bg-zinc-800/40 transition-colors group cursor-pointer"
                                @click="openClientDetail(org)"
                            >
                                <td class="p-3">
                                    <div class="font-bold text-slate-900 dark:text-white group-hover:text-[#6C5CE7] transition-colors flex items-center gap-1.5">
                                        <span>{{ org.name }}</span>
                                        <svg class="w-3 h-3 text-slate-300 dark:text-zinc-600 group-hover:text-[#6C5CE7] opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"/></svg>
                                    </div>
                                    <span class="text-[10px] text-slate-400 capitalize">{{ org.status }}</span>
                                </td>
                                <td class="p-3 font-semibold text-purple-600 dark:text-purple-400">
                                    <span class="px-2 py-0.5 rounded-lg bg-purple-50 dark:bg-purple-900/30 border border-purple-200 dark:border-purple-800 text-[11px]">
                                        {{ org.plan }}
                                    </span>
                                </td>
                                <td class="p-3 text-right font-bold text-[#6C5CE7]">{{ formatNumber(org.chats_count) }}</td>
                                <td class="p-3 text-right font-semibold text-emerald-600 dark:text-emerald-400">{{ formatNumber(org.delivered_count) }}</td>
                                <td class="p-3 text-right font-semibold text-rose-500">{{ formatNumber(org.failed_count) }}</td>
                                <td class="p-3 text-right font-bold text-cyan-600 dark:text-cyan-400">{{ formatNumber(org.calls_count) }}</td>
                                <td class="p-3 text-right font-mono text-slate-600 dark:text-zinc-300">{{ org.call_minutes }}m</td>
                                <td class="p-3 text-right font-bold text-indigo-600 dark:text-indigo-400">{{ formatNumber(org.campaigns_count) }}</td>
                                <td class="p-3 text-right font-bold text-slate-700 dark:text-zinc-200">{{ formatNumber(org.contacts_count) }}</td>
                                <td class="p-3 text-right font-semibold text-slate-600 dark:text-zinc-400">{{ formatNumber(org.users_count) }}</td>
                                <td class="p-3 text-center">
                                    <div class="text-[11px] font-semibold text-slate-700 dark:text-zinc-300">{{ org.renewal_date }}</div>
                                    <span v-if="org.days_until_renewal !== null" :class="[
                                        'text-[10px] font-bold',
                                        org.days_until_renewal <= 7 ? 'text-rose-500' : (org.days_until_renewal <= 30 ? 'text-amber-500' : 'text-slate-400')
                                    ]">
                                        {{ org.days_until_renewal > 0 ? `${org.days_until_renewal}d left` : (org.days_until_renewal === 0 ? 'Today' : `${Math.abs(org.days_until_renewal)}d overdue`) }}
                                    </span>
                                </td>
                                <td class="p-3 text-center" @click.stop>
                                    <button
                                        type="button"
                                        @click="openClientDetail(org)"
                                        class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-slate-700 dark:text-zinc-200 transition-colors"
                                    >
                                        {{ $t('Details') }}
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="!filteredOrganizations.length">
                                <td colspan="12" class="p-8 text-center text-slate-400">
                                    {{ $t('No matching clients found for the selected criteria.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Client Detail Modal / Drawer (Requirement 47) -->
            <div
                v-if="selectedClient"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs animate-in fade-in duration-200"
                @click.self="selectedClient = null"
            >
                <div class="bg-white dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 rounded-3xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl p-6 space-y-6">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-zinc-800 pb-4">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-[#6C5CE7]/10 dark:bg-[#6C5CE7]/20 text-[#6C5CE7] flex items-center justify-center font-bold text-base">
                                {{ selectedClient.name.charAt(0).toUpperCase() }}
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ selectedClient.name }}</h3>
                                <p class="text-xs text-slate-400">Client ID #{{ selectedClient.id }} &bull; Registered {{ selectedClient.created_at }}</p>
                            </div>
                        </div>
                        <button
                            type="button"
                            @click="selectedClient = null"
                            class="p-2 rounded-xl text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors"
                        >
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>

                    <!-- Client Key Metrics Grid -->
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-zinc-800/50 border border-slate-100 dark:border-zinc-800">
                            <span class="text-[10px] font-bold uppercase text-slate-400">Plan</span>
                            <p class="text-sm font-bold text-purple-600 dark:text-purple-400 mt-0.5">{{ selectedClient.plan }}</p>
                        </div>
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-zinc-800/50 border border-slate-100 dark:border-zinc-800">
                            <span class="text-[10px] font-bold uppercase text-slate-400">Renewal</span>
                            <p class="text-sm font-bold text-slate-900 dark:text-white mt-0.5">{{ selectedClient.renewal_date }}</p>
                        </div>
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-zinc-800/50 border border-slate-100 dark:border-zinc-800">
                            <span class="text-[10px] font-bold uppercase text-slate-400">Total Users</span>
                            <p class="text-sm font-bold text-slate-900 dark:text-white mt-0.5">{{ selectedClient.users_count }}</p>
                        </div>
                        <div class="p-3 rounded-2xl bg-slate-50 dark:bg-zinc-800/50 border border-slate-100 dark:border-zinc-800">
                            <span class="text-[10px] font-bold uppercase text-slate-400">Contacts</span>
                            <p class="text-sm font-bold text-slate-900 dark:text-white mt-0.5">{{ formatNumber(selectedClient.contacts_count) }}</p>
                        </div>
                    </div>

                    <!-- Messaging & Calling Breakdown (Requirements 48 & 49) -->
                    <div class="space-y-4">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400">Communication Activity</h4>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Messaging Box -->
                            <div class="p-4 rounded-2xl bg-purple-50/50 dark:bg-purple-950/20 border border-purple-100 dark:border-purple-900/30 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-purple-700 dark:text-purple-300">WhatsApp Messaging</span>
                                    <span class="text-xs font-extrabold text-[#6C5CE7]">{{ formatNumber(selectedClient.chats_count) }}</span>
                                </div>
                                <div class="space-y-1.5 text-xs">
                                    <div class="flex justify-between text-slate-600 dark:text-zinc-400">
                                        <span>Delivered Messages:</span>
                                        <span class="font-bold text-emerald-600">{{ formatNumber(selectedClient.delivered_count) }}</span>
                                    </div>
                                    <div class="flex justify-between text-slate-600 dark:text-zinc-400">
                                        <span>Failed Messages:</span>
                                        <span class="font-bold text-rose-500">{{ formatNumber(selectedClient.failed_count) }}</span>
                                    </div>
                                    <div class="flex justify-between text-slate-600 dark:text-zinc-400">
                                        <span>Campaigns Launched:</span>
                                        <span class="font-bold text-indigo-600 dark:text-indigo-400">{{ selectedClient.campaigns_count }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Calling Box -->
                            <div class="p-4 rounded-2xl bg-cyan-50/50 dark:bg-cyan-950/20 border border-cyan-100 dark:border-cyan-900/30 space-y-2.5">
                                <div class="flex items-center justify-between">
                                    <span class="text-xs font-bold text-cyan-700 dark:text-cyan-300">WhatsApp Calling</span>
                                    <span class="text-xs font-extrabold text-cyan-600">{{ formatNumber(selectedClient.calls_count) }}</span>
                                </div>
                                <div class="space-y-1.5 text-xs">
                                    <div class="flex justify-between text-slate-600 dark:text-zinc-400">
                                        <span>Total Call Minutes:</span>
                                        <span class="font-bold text-cyan-600">{{ selectedClient.call_minutes }} mins</span>
                                    </div>
                                    <div class="flex justify-between text-slate-600 dark:text-zinc-400">
                                        <span>Average Duration:</span>
                                        <span class="font-bold text-slate-700 dark:text-zinc-300">{{ selectedClient.avg_call_duration }}s</span>
                                    </div>
                                    <div class="flex justify-between text-slate-600 dark:text-zinc-400">
                                        <span>Support Tickets:</span>
                                        <span class="font-bold text-slate-700 dark:text-zinc-300">{{ selectedClient.tickets_count }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-zinc-800">
                        <button
                            type="button"
                            @click="selectedClient = null"
                            class="px-4 py-2 rounded-xl text-xs font-bold bg-slate-100 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 hover:bg-slate-200 dark:hover:bg-zinc-700 transition-colors"
                        >
                            {{ $t('Close') }}
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import AppLayout from '../Layout/App.vue';

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
const searchQuery = ref('');
const selectedClient = ref(null);

const exportUrl = computed(() => {
    let url = `/admin/reports/export?range=${selectedPreset.value}`;
    if (selectedPreset.value === 'custom') {
        url += `&start_date=${customStartDate.value}&end_date=${customEndDate.value}`;
    }
    return url;
});

const filteredOrganizations = computed(() => {
    const orgs = props.report?.organizations || [];
    if (!searchQuery.value.trim()) return orgs;
    const q = searchQuery.value.toLowerCase().trim();
    return orgs.filter(o =>
        (o.name && o.name.toLowerCase().includes(q)) ||
        (o.plan && o.plan.toLowerCase().includes(q)) ||
        (o.status && o.status.toLowerCase().includes(q))
    );
});

const openClientDetail = (org) => {
    selectedClient.value = org;
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
