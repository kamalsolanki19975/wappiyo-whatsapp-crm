<template>
    <AppLayout>
        <div class="h-full overflow-y-auto bg-slate-50/70 dark:bg-[#09090B] p-4 sm:p-6 lg:p-8 space-y-6 transition-colors">
            
            <!-- Sticky Header Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link
                        href="/campaigns"
                        class="p-2 -ml-1 text-slate-500 hover:text-slate-800 dark:hover:text-zinc-200 rounded-xl hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors"
                        title="Back to campaigns"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                    </Link>

                    <div>
                        <div class="flex flex-wrap items-center gap-2">
                            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                                {{ props.campaign.name }}
                            </h1>
                            <Badge :variant="campaignStatusVariant" size="sm">
                                {{ props.campaign.status || 'Active' }}
                            </Badge>
                        </div>
                        <p class="text-xs text-slate-400 dark:text-zinc-500 font-mono mt-0.5">
                            Ref: {{ props.campaign.uuid }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <a
                        :href="'/campaigns/export/' + props.campaign.uuid"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 hover:bg-slate-50 dark:hover:bg-zinc-700 text-slate-700 dark:text-zinc-200 shadow-xs transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#6C5CE7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        <span>{{ $t('Export CSV') }}</span>
                    </a>

                    <Link href="/campaigns">
                        <Button variant="secondary" size="xs">
                            {{ $t('Back to Campaigns') }}
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- KPI Metric Cards Ribbon -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
                <!-- Total Messages -->
                <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ $t('Total Audience') }}</span>
                    <p class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white mt-1">
                        {{ props.campaign.total_message_count || 0 }}
                    </p>
                    <span class="text-[10px] text-slate-400">{{ $t('Recipients queued') }}</span>
                </div>

                <!-- Total Sent -->
                <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ $t('Sent') }}</span>
                    <p class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white mt-1">
                        {{ props.campaign.total_sent_count || 0 }}
                    </p>
                    <span class="text-[10px] text-slate-400">{{ $t('Dispatched to Meta') }}</span>
                </div>

                <!-- Total Delivered -->
                <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">{{ $t('Delivered') }}</span>
                    <p class="text-xl sm:text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mt-1">
                        {{ props.campaign.total_delivered_count || 0 }}
                    </p>
                    <span class="text-[10px] text-emerald-600/80 dark:text-emerald-400/80 font-semibold">
                        {{ deliveryRatePercent }}% {{ $t('success') }}
                    </span>
                </div>

                <!-- Total Read -->
                <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-[#6C5CE7] dark:text-purple-400">{{ $t('Read Receipts') }}</span>
                    <p class="text-xl sm:text-2xl font-extrabold text-[#6C5CE7] dark:text-purple-400 mt-1">
                        {{ props.campaign.total_read_count || 0 }}
                    </p>
                    <span class="text-[10px] text-purple-600/80 dark:text-purple-400/80 font-semibold">
                        {{ readRatePercent }}% {{ $t('read rate') }}
                    </span>
                </div>

                <!-- Total Failed -->
                <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                    <span class="text-[11px] font-bold uppercase tracking-wider text-rose-500">{{ $t('Failed') }}</span>
                    <p class="text-xl sm:text-2xl font-extrabold text-rose-500 mt-1">
                        {{ props.campaign.total_failed_count || 0 }}
                    </p>
                    <span class="text-[10px] text-rose-500/80">{{ $t('Errors / Invalid numbers') }}</span>
                </div>
            </div>

            <!-- Two-Column Analytics Workspace -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- LEFT COLUMN: Recipient Logs Table (2 Cols on lg) -->
                <div class="lg:col-span-2 space-y-4">
                    <div class="p-4 sm:p-5 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-zinc-800/80 pb-3">
                            <h2 class="text-sm font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                                {{ $t('Recipient Delivery Logs') }}
                            </h2>
                        </div>

                        <!-- Recipient Log Table Component -->
                        <CampaignLogTable
                            :rows="props.rows"
                            :filters="props.filters"
                            :uuid="props.campaign.uuid"
                        />
                    </div>
                </div>

                <!-- RIGHT COLUMN: Campaign Setup & Message Preview (1 Col on lg) -->
                <div class="space-y-6">
                    <!-- Campaign Overview Metadata Card -->
                    <div class="rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-5 shadow-xs space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                            {{ $t('Campaign Overview') }}
                        </h3>

                        <dl class="divide-y divide-slate-100 dark:divide-zinc-800/60 text-xs sm:text-sm">
                            <div class="py-2.5 flex justify-between gap-4">
                                <dt class="text-slate-500 dark:text-zinc-400">{{ $t('Template') }}</dt>
                                <dd class="font-bold text-slate-900 dark:text-white text-right">{{ props.campaign?.template?.name || '—' }}</dd>
                            </div>
                            <div class="py-2.5 flex justify-between gap-4">
                                <dt class="text-slate-500 dark:text-zinc-400">{{ $t('Target Audience') }}</dt>
                                <dd class="font-semibold text-purple-600 dark:text-purple-400 text-right">
                                    {{ props.campaign.contact_group_id === '0' || props.campaign.contact_group_id === 0 ? $t('All Contacts') : (props.campaign?.contact_group?.name || 'Segment') }}
                                </dd>
                            </div>
                            <div class="py-2.5 flex justify-between gap-4">
                                <dt class="text-slate-500 dark:text-zinc-400">{{ $t('Broadcast Time') }}</dt>
                                <dd class="text-slate-700 dark:text-zinc-300 text-right font-mono text-xs">{{ props.campaign.scheduled_at || 'Immediate' }}</dd>
                            </div>
                        </dl>
                    </div>

                    <!-- WhatsApp Message Preview Card -->
                    <div class="rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-5 shadow-xs space-y-3">
                        <div class="flex items-center justify-between text-xs font-semibold text-slate-500 dark:text-zinc-400">
                            <span class="flex items-center gap-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                <span>{{ $t('Sent WhatsApp Template') }}</span>
                            </span>
                        </div>

                        <div class="rounded-2xl p-4 chat-bg border border-slate-200/80 dark:border-zinc-800 flex justify-center">
                            <WhatsappTemplate
                                v-if="parsedMetadata"
                                :parameters="parsedMetadata"
                                :placeholder="false"
                                :visible="true"
                            />
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AppLayout>
</template>

<script setup>
import { computed } from 'vue';
import AppLayout from "./../Layout/App.vue";
import CampaignLogTable from '@/Components/Tables/CampaignLogTable.vue';
import WhatsappTemplate from '@/Components/WhatsappTemplate.vue';
import Button from '@/Components/UI/Button.vue';
import Badge from '@/Components/UI/Badge.vue';
import { Link } from "@inertiajs/vue3";

const props = defineProps(['campaign', 'rows', 'filters', 'title']);

const campaignStatusVariant = computed(() => {
    const s = String(props.campaign?.status || '').toLowerCase();
    if (s === 'completed' || s === 'success') return 'success';
    if (s === 'scheduled') return 'cyan';
    if (s === 'processing' || s === 'running') return 'primary';
    if (s === 'failed') return 'danger';
    return 'neutral';
});

const deliveryRatePercent = computed(() => {
    const total = props.campaign.total_message_count || 0;
    const delivered = props.campaign.total_delivered_count || 0;
    if (!total || total === 0) return 0;
    return Math.min(100, Math.round((delivered / total) * 100));
});

const readRatePercent = computed(() => {
    const delivered = props.campaign.total_delivered_count || props.campaign.total_message_count || 0;
    const read = props.campaign.total_read_count || 0;
    if (!delivered || delivered === 0) return 0;
    return Math.min(100, Math.round((read / delivered) * 100));
});

const parsedMetadata = computed(() => {
    try {
        if (!props.campaign?.metadata) return null;
        return typeof props.campaign.metadata === 'string'
            ? JSON.parse(props.campaign.metadata)
            : props.campaign.metadata;
    } catch (_) {
        return null;
    }
});
</script>