<template>
    <AppLayout>
        <div class="h-full overflow-y-auto bg-slate-50/70 dark:bg-[#09090B] p-4 sm:p-6 lg:p-8 space-y-6 transition-colors">
            
            <!-- Page Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        {{ $t('Campaigns & Broadcasts') }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-zinc-400 mt-1">
                        {{ $t('Create, schedule, and analyze broadcast marketing campaigns sent over WhatsApp.') }}
                    </p>
                </div>

                <div class="flex items-center gap-2">
                    <Link href="/campaigns/create">
                        <Button variant="primary" size="sm" class="gap-1.5 shadow-sm">
                            <template #icon>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            </template>
                            <span>{{ $t('Create Campaign') }}</span>
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Aggregate KPI Summary Bar -->
            <div v-if="campaignsList.length > 0" class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">
                <!-- Total Campaigns -->
                <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                    <div class="flex items-center justify-between text-xs text-slate-400 dark:text-zinc-500 font-semibold uppercase tracking-wider mb-1">
                        <span>{{ $t('Campaigns') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-purple-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
                    </div>
                    <div class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white">
                        {{ totalCampaignsCount }}
                    </div>
                    <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-0.5">
                        {{ $t('Broadcast pipelines') }}
                    </p>
                </div>

                <!-- Total Targeted Audience -->
                <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                    <div class="flex items-center justify-between text-xs text-slate-400 dark:text-zinc-500 font-semibold uppercase tracking-wider mb-1">
                        <span>{{ $t('Total Audience') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <div class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white">
                        {{ totalAudienceCount }}
                    </div>
                    <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-0.5">
                        {{ $t('Recipients targeted') }}
                    </p>
                </div>

                <!-- Total Delivered -->
                <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                    <div class="flex items-center justify-between text-xs text-slate-400 dark:text-zinc-500 font-semibold uppercase tracking-wider mb-1">
                        <span>{{ $t('Delivered') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <div class="text-xl sm:text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">
                        {{ totalDeliveredCount }}
                    </div>
                    <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-0.5">
                        {{ $t('WhatsApp deliveries confirmed') }}
                    </p>
                </div>

                <!-- Total Read -->
                <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                    <div class="flex items-center justify-between text-xs text-slate-400 dark:text-zinc-500 font-semibold uppercase tracking-wider mb-1">
                        <span>{{ $t('Read Rate') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-cyan-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 7 17l-5-5"/><path d="m22 10-7.5 7.5L13 16"/></svg>
                    </div>
                    <div class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white">
                        {{ totalReadCount }}
                    </div>
                    <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-0.5">
                        {{ totalDeliveredCount > 0 ? ((totalReadCount / totalDeliveredCount) * 100).toFixed(1) + '% read rate' : 'Read receipts tracked' }}
                    </p>
                </div>
            </div>

            <!-- Campaigns Table Component -->
            <CampaignTable :rows="props.rows" :filters="props.filters"/>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import { Link } from "@inertiajs/vue3";
import { computed } from "vue";
import CampaignTable from '@/Components/Tables/CampaignTable.vue';
import Button from '@/Components/UI/Button.vue';

const props = defineProps(['rows', 'filters', 'settings', 'allowCreate', 'title']);

const campaignsList = computed(() => props.rows?.data || []);

const totalCampaignsCount = computed(() => {
    return props.rows?.meta?.total || campaignsList.value.length || 0;
});

const totalAudienceCount = computed(() => {
    return campaignsList.value.reduce((acc, c) => acc + (c.contacts_count || c.contact_group_count || 0), 0);
});

const totalDeliveredCount = computed(() => {
    return campaignsList.value.reduce((acc, c) => acc + (c.delivery_count || 0), 0);
});

const totalReadCount = computed(() => {
    return campaignsList.value.reduce((acc, c) => acc + (c.read_count || 0), 0);
});
</script>