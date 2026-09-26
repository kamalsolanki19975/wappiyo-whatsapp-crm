<template>
    <div class="rounded-3xl border border-slate-200/80 dark:border-white/10 bg-white dark:bg-slate-900 p-6 shadow-sm">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 dark:border-white/5 pb-4">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">
                        {{ $t('Campaign Broadcast Performance') }}
                    </h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary dark:bg-primary/20">
                        {{ campaigns.length }} {{ $t('Campaigns') }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    {{ $t('Real-time delivery verification across broadcasts dispatched in this time window') }}
                </p>
            </div>

            <!-- Search Filter -->
            <div class="flex items-center gap-3">
                <div class="relative">
                    <input
                        v-model="searchQuery"
                        type="text"
                        :placeholder="$t('Filter campaigns...')"
                        class="w-48 sm:w-60 pl-8 pr-3 py-1.5 rounded-xl border border-slate-200/80 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                    />
                    <svg class="w-4 h-4 absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <Link
                    href="/campaigns"
                    class="text-xs font-semibold text-primary hover:underline flex items-center gap-1 shrink-0"
                >
                    <span>{{ $t('View All') }}</span>
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </Link>
            </div>
        </div>

        <!-- Table Container -->
        <div class="mt-4 overflow-x-auto">
            <table v-if="filteredCampaigns.length > 0" class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-white/5 text-slate-400 font-bold uppercase tracking-wider text-[11px]">
                        <th class="py-3 px-3">{{ $t('Campaign') }}</th>
                        <th class="py-3 px-3">{{ $t('Template & Group') }}</th>
                        <th class="py-3 px-3 text-right">{{ $t('Recipients') }}</th>
                        <th class="py-3 px-3 text-right">{{ $t('Delivered') }}</th>
                        <th class="py-3 px-3 text-right">{{ $t('Failed') }}</th>
                        <th class="py-3 px-3 text-center">{{ $t('Success Rate') }}</th>
                        <th class="py-3 px-3 text-right">{{ $t('Date') }}</th>
                        <th class="py-3 px-2 text-right">{{ $t('Action') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                    <tr
                        v-for="camp in filteredCampaigns"
                        :key="camp.id"
                        class="hover:bg-slate-50/80 dark:hover:bg-white/5 transition-colors group"
                    >
                        <!-- Name & Status -->
                        <td class="py-3.5 px-3">
                            <div class="font-bold text-slate-900 dark:text-white group-hover:text-primary transition-colors">
                                {{ camp.name }}
                            </div>
                            <div class="mt-0.5">
                                <span
                                    class="inline-block px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                    :class="getStatusBadgeClass(camp.status)"
                                >
                                    {{ camp.status }}
                                </span>
                            </div>
                        </td>

                        <!-- Template & Contact Group -->
                        <td class="py-3.5 px-3 text-slate-600 dark:text-slate-300">
                            <div class="flex items-center gap-1.5">
                                <span class="text-primary font-medium">{{ camp.template_name }}</span>
                            </div>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                {{ $t('Audience') }}: {{ camp.contact_group_name }}
                            </div>
                        </td>

                        <!-- Total Recipients -->
                        <td class="py-3.5 px-3 text-right font-semibold text-slate-900 dark:text-white">
                            {{ formatNumber(camp.recipients_count) }}
                        </td>

                        <!-- Success -->
                        <td class="py-3.5 px-3 text-right font-semibold text-emerald-600 dark:text-emerald-400">
                            {{ formatNumber(camp.success_count) }}
                        </td>

                        <!-- Failed -->
                        <td class="py-3.5 px-3 text-right font-semibold" :class="camp.failed_count > 0 ? 'text-rose-500' : 'text-slate-400'">
                            {{ formatNumber(camp.failed_count) }}
                        </td>

                        <!-- Success Rate Bar -->
                        <td class="py-3.5 px-3 text-center">
                            <div class="inline-flex flex-col items-center">
                                <span class="font-bold" :class="camp.success_rate >= 80 ? 'text-emerald-600 dark:text-emerald-400' : (camp.success_rate >= 50 ? 'text-amber-500' : 'text-slate-500')">
                                    {{ camp.success_rate }}%
                                </span>
                                <div class="w-16 h-1.5 bg-slate-100 dark:bg-white/10 rounded-full overflow-hidden mt-1">
                                    <div
                                        class="h-full rounded-full transition-all duration-300"
                                        :class="camp.success_rate >= 80 ? 'bg-emerald-500' : (camp.success_rate >= 50 ? 'bg-amber-500' : 'bg-rose-500')"
                                        :style="{ width: `${camp.success_rate}%` }"
                                    ></div>
                                </div>
                            </div>
                        </td>

                        <!-- Date -->
                        <td class="py-3.5 px-3 text-right text-slate-500 dark:text-slate-400 whitespace-nowrap text-[11px]">
                            {{ camp.created_at }}
                        </td>

                        <!-- Action -->
                        <td class="py-3.5 px-2 text-right">
                            <Link
                                :href="'/campaigns/' + camp.uuid"
                                class="inline-flex items-center justify-center w-7 h-7 rounded-lg text-slate-400 hover:text-primary hover:bg-primary/10 transition"
                                :title="$t('View Campaign Logs')"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14" />
                                </svg>
                            </Link>
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- Empty State -->
            <div
                v-else
                class="py-12 flex flex-col items-center justify-center text-center p-6"
            >
                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-white/5 text-slate-400 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1">
                    {{ searchQuery ? $t('No matching campaigns found') : $t('No campaigns executed in this period') }}
                </h4>
                <p class="text-xs text-slate-400 max-w-sm mb-4">
                    {{ searchQuery ? $t('Try searching with a different campaign name or template.') : $t('Broadcast campaigns will automatically display delivery statistics here once sent.') }}
                </p>
                <Link
                    v-if="!searchQuery"
                    href="/campaigns/create"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-primary text-white text-xs font-semibold shadow-sm hover:bg-primary/90 transition"
                >
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>{{ $t('Create Broadcast') }}</span>
                </Link>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    campaigns: {
        type: Array,
        default: () => [],
    },
});

const searchQuery = ref('');

const filteredCampaigns = computed(() => {
    if (!searchQuery.value.trim()) return props.campaigns;
    const q = searchQuery.value.toLowerCase().trim();
    return props.campaigns.filter((c) =>
        c.name.toLowerCase().includes(q) ||
        (c.template_name && c.template_name.toLowerCase().includes(q)) ||
        (c.contact_group_name && c.contact_group_name.toLowerCase().includes(q))
    );
});

function formatNumber(num) {
    if (num === null || num === undefined) return '0';
    return Number(num).toLocaleString();
}

function getStatusBadgeClass(status) {
    switch (status?.toLowerCase()) {
        case 'completed':
            return 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400';
        case 'processing':
        case 'sending':
            return 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 animate-pulse';
        case 'scheduled':
            return 'bg-amber-500/10 text-amber-600 dark:text-amber-400';
        case 'failed':
            return 'bg-rose-500/10 text-rose-500';
        default:
            return 'bg-slate-100 dark:bg-white/10 text-slate-500';
    }
}
</script>
