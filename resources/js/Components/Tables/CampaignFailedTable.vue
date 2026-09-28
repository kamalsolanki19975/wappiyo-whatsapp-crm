<script setup>
import { ref, computed } from 'vue';
import { router, Link } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import Badge from '@/Components/UI/Badge.vue';
import Button from '@/Components/UI/Button.vue';
import Avatar from '@/Components/UI/Avatar.vue';
import Modal from '@/Components/Modal.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    campaign: {
        type: Object,
        required: true,
    },
    rows: {
        type: Object,
        required: true,
    },
    stats: {
        type: Object,
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({}),
    }
});

const selectedIds = ref([]);
const isAllSelected = ref(false);
const isRetrying = ref(false);
const activeLog = ref(null);
const isInspectModalOpen = ref(false);
const isConfirmModalOpen = ref(false);
const confirmActionType = ref('selected'); // 'selected' or 'all_eligible'

const searchParam = ref(props.filters?.search || '');
const retryStatusFilter = ref(props.filters?.retry_status || 'all');

const isSearching = ref(false);

const runFilter = debounce(() => {
    isSearching.value = true;
    router.visit(`/campaigns/${props.campaign.uuid}`, {
        method: 'get',
        data: {
            tab: 'failed',
            search: searchParam.value || null,
            retry_status: retryStatusFilter.value !== 'all' ? retryStatusFilter.value : null,
        },
        preserveState: true,
        preserveScroll: true,
        onFinish: () => {
            isSearching.value = false;
        }
    });
}, 500);

const toggleSelectAll = () => {
    if (isAllSelected.value) {
        selectedIds.value = [];
        isAllSelected.value = false;
    } else {
        selectedIds.value = (props.rows.data || []).map(r => r.id);
        isAllSelected.value = true;
    }
};

const toggleSelectRow = (id) => {
    const idx = selectedIds.value.indexOf(id);
    if (idx > -1) {
        selectedIds.value.splice(idx, 1);
        isAllSelected.value = false;
    } else {
        selectedIds.value.push(id);
        if (selectedIds.value.length === (props.rows.data || []).length) {
            isAllSelected.value = true;
        }
    }
};

const openInspectModal = (row) => {
    activeLog.value = row;
    isInspectModalOpen.value = true;
};

const promptRetrySelected = () => {
    confirmActionType.value = 'selected';
    isConfirmModalOpen.value = true;
};

const promptRetryAllEligible = () => {
    confirmActionType.value = 'all_eligible';
    isConfirmModalOpen.value = true;
};

const executeRetry = () => {
    isRetrying.value = true;
    isConfirmModalOpen.value = false;

    const payload = confirmActionType.value === 'all_eligible'
        ? { all_eligible: true }
        : { log_ids: selectedIds.value };

    router.post(`/campaigns/${props.campaign.uuid}/retry`, payload, {
        preserveScroll: true,
        onFinish: () => {
            isRetrying.value = false;
            selectedIds.value = [];
            isAllSelected.value = false;
        }
    });
};

const retrySingleLog = (logId) => {
    isRetrying.value = true;
    router.post(`/campaigns/${props.campaign.uuid}/retry`, {
        log_ids: [logId]
    }, {
        preserveScroll: true,
        onFinish: () => {
            isRetrying.value = false;
        }
    });
};

const toggleExcludeLog = (logId, isCurrentlyExcluded) => {
    router.post(`/campaigns/${props.campaign.uuid}/exclude`, {
        log_id: logId,
        is_excluded: !isCurrentlyExcluded
    }, {
        preserveScroll: true
    });
};

const getReasonBadgeVariant = (category) => {
    switch (category) {
        case 'Rate Limit Hit': return 'warning';
        case 'Meta Service Outage': return 'primary';
        case 'Network Timeout': return 'cyan';
        case 'Number Not on WhatsApp': return 'danger';
        case 'Template Error': return 'danger';
        case 'Session Window Closed': return 'warning';
        default: return 'neutral';
    }
};

const getStatusBadgeVariant = (row) => {
    if (row.retry_status === 'queued') return 'primary';
    if (row.retry_status === 'retrying') return 'cyan';
    if (row.retry_status === 'success') return 'success';
    return 'danger';
};
</script>

<template>
    <div class="space-y-4">
        <!-- Recovery Overview Banner -->
        <div class="p-4 sm:p-5 rounded-2xl bg-gradient-to-tr from-rose-500/10 via-purple-500/5 to-transparent border border-rose-200/80 dark:border-rose-900/40 space-y-3">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-rose-500 text-white flex items-center justify-center font-bold text-xs shadow-md shadow-rose-500/30 shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                            {{ $t('Failed Message Recovery & Resend Engine') }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">
                            {{ $t('Identify undelivered recipients, analyze Meta error codes, and safely resend eligible messages.') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap">
                    <a
                        :href="`/campaigns/export-failed/${props.campaign.uuid}`"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-semibold bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 text-slate-700 dark:text-zinc-200 hover:bg-slate-50 dark:hover:bg-zinc-700 shadow-xs transition-colors"
                        title="Download CSV report of failed messages"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-rose-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        <span>{{ $t('Export Failed CSV') }}</span>
                    </a>

                    <!-- Prominent Retry All Eligible Button -->
                    <button
                        type="button"
                        @click="promptRetryAllEligible"
                        :disabled="isRetrying || (props.stats.retryable_count || 0) === 0"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] hover:from-[#5B4CD7] hover:to-[#7A4BE6] shadow-md shadow-purple-600/20 active:scale-[0.98] transition-all disabled:opacity-50 cursor-pointer"
                    >
                        <svg v-if="isRetrying" class="animate-spin w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12a9 9 0 0 1 9-9 9.75 9.75 0 0 1 6.74 2.74L21 8"/><path d="M21 3v5h-5"/><path d="M21 12a9 9 0 0 1-9 9 9.75 9.75 0 0 1-6.74-2.74L3 16"/><path d="M8 16H3v5"/></svg>
                        <span>{{ $t('Retry All Eligible') }} ({{ props.stats.retryable_count || 0 }})</span>
                    </button>
                </div>
            </div>

            <!-- Failure Breakdown Ribbon -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2 pt-2 border-t border-rose-200/50 dark:border-rose-900/30 text-xs">
                <div class="p-2 rounded-xl bg-white/60 dark:bg-zinc-900/60 border border-slate-200/50 dark:border-zinc-800">
                    <span class="text-slate-400 text-[10px] block font-bold uppercase">{{ $t('Total Failures') }}</span>
                    <span class="font-extrabold text-rose-500 text-sm">{{ props.stats.total_failed || 0 }}</span>
                </div>
                <div class="p-2 rounded-xl bg-white/60 dark:bg-zinc-900/60 border border-slate-200/50 dark:border-zinc-800">
                    <span class="text-slate-400 text-[10px] block font-bold uppercase">{{ $t('Retry Eligible') }}</span>
                    <span class="font-extrabold text-emerald-600 dark:text-emerald-400 text-sm">{{ props.stats.retryable_count || 0 }}</span>
                </div>
                <div class="p-2 rounded-xl bg-white/60 dark:bg-zinc-900/60 border border-slate-200/50 dark:border-zinc-800">
                    <span class="text-slate-400 text-[10px] block font-bold uppercase">{{ $t('Non-Retryable') }}</span>
                    <span class="font-extrabold text-slate-600 dark:text-zinc-400 text-sm">{{ props.stats.non_retryable_count || 0 }}</span>
                </div>
                <div class="p-2 rounded-xl bg-white/60 dark:bg-zinc-900/60 border border-slate-200/50 dark:border-zinc-800">
                    <span class="text-slate-400 text-[10px] block font-bold uppercase">{{ $t('Retry In Queue') }}</span>
                    <span class="font-extrabold text-[#6C5CE7] text-sm">{{ props.stats.queued_count || 0 }}</span>
                </div>
            </div>
        </div>

        <!-- Filter & Search Toolbar -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2 flex-1 max-w-md">
                <div class="relative w-full">
                    <input
                        type="text"
                        v-model="searchParam"
                        @input="runFilter"
                        placeholder="Search failed recipient name or phone..."
                        class="w-full pl-9 pr-4 py-2 rounded-xl bg-white dark:bg-zinc-900 text-xs text-slate-800 dark:text-white border border-slate-200 dark:border-zinc-800 focus:outline-none focus:ring-2 focus:ring-[#6C5CE7]"
                    />
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </div>
            </div>

            <!-- Bulk Selected Action Bar (Visible when rows are checked) -->
            <div v-if="selectedIds.length > 0" class="flex items-center gap-2 animate-fade-in">
                <span class="text-xs font-semibold text-slate-600 dark:text-zinc-400">
                    {{ selectedIds.length }} {{ $t('selected') }}
                </span>
                <button
                    type="button"
                    @click="promptRetrySelected"
                    :disabled="isRetrying"
                    class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-xs shadow-sm transition-all"
                >
                    {{ $t('Retry Selected') }} ({{ selectedIds.length }})
                </button>
            </div>
        </div>

        <!-- FAILED RECIPIENTS DATA TABLE -->
        <div class="overflow-x-auto rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-zinc-800/80 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500 bg-slate-50/50 dark:bg-zinc-900/40">
                        <th class="py-3 pl-4 pr-2 w-8">
                            <input
                                type="checkbox"
                                :checked="isAllSelected"
                                @change="toggleSelectAll"
                                class="rounded border-slate-300 dark:border-zinc-700 text-[#6C5CE7] focus:ring-[#6C5CE7]"
                            />
                        </th>
                        <th class="py-3 px-3">{{ $t('Recipient') }}</th>
                        <th class="py-3 px-3">{{ $t('Phone') }}</th>
                        <th class="py-3 px-3">{{ $t('Failure Reason') }}</th>
                        <th class="py-3 px-3">{{ $t('Eligibility') }}</th>
                        <th class="py-3 px-3 text-center">{{ $t('Attempts') }}</th>
                        <th class="py-3 px-3 hidden md:table-cell">{{ $t('Last Attempt') }}</th>
                        <th class="py-3 pl-3 pr-4 text-right">{{ $t('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-800/60">
                    <tr
                        v-for="item in props.rows.data"
                        :key="item.id"
                        class="hover:bg-slate-50/70 dark:hover:bg-zinc-800/40 transition-colors"
                        :class="selectedIds.includes(item.id) ? 'bg-purple-50/40 dark:bg-purple-950/20' : ''"
                    >
                        <!-- Checkbox -->
                        <td class="py-3 pl-4 pr-2">
                            <input
                                type="checkbox"
                                :checked="selectedIds.includes(item.id)"
                                @change="toggleSelectRow(item.id)"
                                class="rounded border-slate-300 dark:border-zinc-700 text-[#6C5CE7] focus:ring-[#6C5CE7]"
                            />
                        </td>

                        <!-- Recipient Contact -->
                        <td class="py-3 px-3">
                            <div class="flex items-center gap-2.5">
                                <Avatar
                                    :src="item.contact?.avatar || null"
                                    :name="item.contact?.first_name || 'Recipient'"
                                    size="sm"
                                />
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white truncate max-w-[140px]">
                                        {{ (item.contact?.first_name || '') + ' ' + (item.contact?.last_name || '') || '—' }}
                                    </div>
                                    <span v-if="item.is_excluded" class="text-[10px] text-amber-500 font-semibold">
                                        {{ $t('Excluded from retries') }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Phone -->
                        <td class="py-3 px-3 font-mono text-slate-600 dark:text-zinc-400">
                            {{ item.contact?.phone || '—' }}
                        </td>

                        <!-- Failure Reason & Code -->
                        <td class="py-3 px-3">
                            <div>
                                <Badge :variant="getReasonBadgeVariant(item.failure_analysis?.failure_reason)" size="sm">
                                    {{ item.failure_analysis?.failure_reason || 'Failure' }}
                                </Badge>
                                <div class="text-[10px] text-slate-400 font-mono mt-0.5">
                                    Code: {{ item.failure_analysis?.error_code || 'ERR' }}
                                </div>
                            </div>
                        </td>

                        <!-- Eligibility -->
                        <td class="py-3 px-3">
                            <div class="flex items-center gap-1.5">
                                <span
                                    class="w-2 h-2 rounded-full shrink-0"
                                    :class="item.failure_analysis?.is_retryable ? 'bg-emerald-500 animate-pulse' : 'bg-rose-400'"
                                ></span>
                                <span
                                    class="text-[11px] font-semibold"
                                    :class="item.failure_analysis?.is_retryable ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400 dark:text-zinc-500'"
                                >
                                    {{ item.failure_analysis?.is_retryable ? $t('Retryable') : $t('Non-Retryable') }}
                                </span>
                            </div>
                        </td>

                        <!-- Attempt Count -->
                        <td class="py-3 px-3 text-center">
                            <span class="inline-flex items-center justify-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300">
                                {{ (item.retry_count || 0) + 1 }}
                            </span>
                        </td>

                        <!-- Last Attempt -->
                        <td class="py-3 px-3 text-slate-500 dark:text-zinc-400 text-[11px] hidden md:table-cell">
                            {{ item.last_retried_at || item.updated_at }}
                        </td>

                        <!-- Actions -->
                        <td class="py-3 pl-3 pr-4 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <!-- Inspect Drawer -->
                                <button
                                    type="button"
                                    @click="openInspectModal(item)"
                                    class="text-xs font-semibold text-[#6C5CE7] hover:underline"
                                    title="View complete error details & retry history"
                                >
                                    {{ $t('Inspect') }}
                                </button>

                                <!-- Individual Retry Button -->
                                <button
                                    v-if="item.failure_analysis?.is_retryable"
                                    type="button"
                                    @click="retrySingleLog(item.id)"
                                    :disabled="isRetrying || item.retry_status === 'queued'"
                                    class="px-2.5 py-1 rounded-lg bg-purple-50 dark:bg-purple-950 text-[#6C5CE7] hover:bg-purple-100 dark:hover:bg-purple-900 border border-purple-200 dark:border-purple-800 text-[11px] font-bold transition-all disabled:opacity-50"
                                >
                                    {{ item.retry_status === 'queued' ? $t('Queued') : $t('Retry') }}
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="!props.rows.data || props.rows.data.length === 0" class="p-12 text-center text-xs text-slate-400 dark:text-zinc-500 space-y-1">
                <p class="font-bold text-slate-700 dark:text-zinc-300">{{ $t('No failed messages matching this criteria') }}</p>
                <p>{{ $t('All recipients in this campaign either delivered successfully or match your search.') }}</p>
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="props.rows?.meta" class="pt-2">
            <Pagination :pagination="props.rows.meta" />
        </div>

        <!-- CONFIRMATION MODAL BEFORE BULK RETRY -->
        <Modal :label="$t('Confirm Campaign Retry')" :isOpen="isConfirmModalOpen" @close="isConfirmModalOpen = false">
            <div class="space-y-4 text-xs">
                <div class="p-3.5 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-800 text-amber-800 dark:text-amber-300 space-y-1">
                    <h4 class="font-bold flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        <span>{{ $t('Duplicate Send Protection Active') }}</span>
                    </h4>
                    <p class="leading-relaxed">
                        {{ $t('Only failed, non-delivered recipients will be dispatched. Messages already delivered or currently processing are automatically skipped.') }}
                    </p>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 space-y-2">
                    <div class="flex justify-between">
                        <span class="text-slate-500">{{ $t('Action Target:') }}</span>
                        <span class="font-bold text-slate-900 dark:text-white">
                            {{ confirmActionType === 'all_eligible' ? $t('All Eligible Failed Recipients') : `${selectedIds.length} Selected Recipients` }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-500">{{ $t('Template:') }}</span>
                        <span class="font-semibold text-purple-600 dark:text-purple-400">{{ props.campaign.template?.name }}</span>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <Button variant="secondary" size="sm" @click="isConfirmModalOpen = false">
                        {{ $t('Cancel') }}
                    </Button>
                    <Button variant="primary" size="sm" @click="executeRetry">
                        {{ $t('Confirm & Queue Resend') }}
                    </Button>
                </div>
            </div>
        </Modal>

        <!-- INSPECT FAILED MESSAGE DETAILS MODAL -->
        <Modal :label="$t('Failed Message Audit & History')" :isOpen="isInspectModalOpen" @close="isInspectModalOpen = false">
            <div v-if="activeLog" class="space-y-4 text-xs">
                <!-- Recipient Info Card -->
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 space-y-1.5">
                    <div class="flex items-center justify-between">
                        <div class="font-bold text-slate-900 dark:text-white text-sm">
                            {{ (activeLog.contact?.first_name || '') + ' ' + (activeLog.contact?.last_name || '') }}
                        </div>
                        <span class="font-mono text-slate-500">{{ activeLog.contact?.phone }}</span>
                    </div>
                    <p class="text-slate-400 text-[11px]">
                        Campaign: {{ props.campaign.name }} &bull; Initial Dispatch: {{ activeLog.created_at }}
                    </p>
                </div>

                <!-- Error Breakdown -->
                <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/40 text-rose-800 dark:text-rose-300 space-y-2">
                    <div class="flex items-center justify-between font-bold">
                        <span>{{ activeLog.failure_analysis?.failure_reason }}</span>
                        <span class="font-mono">Code: {{ activeLog.failure_analysis?.error_code }}</span>
                    </div>
                    <p class="font-mono text-[11px] leading-relaxed">
                        {{ activeLog.failure_analysis?.error_message }}
                    </p>
                    <p v-if="activeLog.failure_analysis?.error_details" class="text-[10px] opacity-80 border-t border-rose-200/60 dark:border-rose-800/40 pt-1">
                        {{ activeLog.failure_analysis?.error_details }}
                    </p>
                </div>

                <!-- Eligibility Advice -->
                <div class="p-3 rounded-xl border" :class="activeLog.failure_analysis?.is_retryable ? 'bg-emerald-50 dark:bg-emerald-950/30 border-emerald-200 text-emerald-800 dark:text-emerald-300' : 'bg-slate-100 dark:bg-zinc-800 border-slate-200 text-slate-600 dark:text-zinc-400'">
                    <strong>{{ activeLog.failure_analysis?.is_retryable ? $t('Safe to Retry') : $t('Permanent Failure') }}:</strong>
                    <span class="ml-1">{{ activeLog.failure_analysis?.eligibility_reason }}</span>
                </div>

                <!-- Retry History Timeline -->
                <div class="space-y-2 pt-2">
                    <h4 class="font-bold text-slate-700 dark:text-zinc-300 uppercase tracking-wider text-[10px]">
                        {{ $t('Attempt History Timeline') }}
                    </h4>

                    <div v-if="activeLog.retries && activeLog.retries.length > 0" class="border-l-2 border-purple-300 dark:border-purple-800 ml-2 pl-3 space-y-2.5">
                        <div
                            v-for="retry in activeLog.retries"
                            :key="retry.id"
                            class="relative text-xs space-y-0.5"
                        >
                            <span class="absolute -left-[19px] top-1 h-2.5 w-2.5 rounded-full bg-[#6C5CE7]"></span>
                            <div class="flex items-center justify-between font-bold text-slate-800 dark:text-zinc-200">
                                <span>Attempt #{{ retry.attempt_number }} &bull; {{ retry.status }}</span>
                                <span class="font-mono text-[10px] text-slate-400">{{ retry.created_at }}</span>
                            </div>
                            <p v-if="retry.error_message" class="text-[11px] text-rose-500 font-mono">
                                {{ retry.error_message }}
                            </p>
                        </div>
                    </div>
                    <p v-else class="text-[11px] text-slate-400 italic">
                        {{ $t('No retries have been performed on this message yet.') }}
                    </p>
                </div>

                <!-- Modal Actions -->
                <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-zinc-800">
                    <button
                        type="button"
                        @click="toggleExcludeLog(activeLog.id, activeLog.is_excluded)"
                        class="text-xs text-slate-500 hover:text-slate-800 dark:hover:text-zinc-200 underline"
                    >
                        {{ activeLog.is_excluded ? $t('Include back in retries') : $t('Exclude from future retries') }}
                    </button>

                    <div class="flex items-center gap-2">
                        <Button variant="secondary" size="sm" @click="isInspectModalOpen = false">
                            {{ $t('Close') }}
                        </Button>
                        <Button
                            v-if="activeLog.failure_analysis?.is_retryable"
                            variant="primary"
                            size="sm"
                            @click="retrySingleLog(activeLog.id); isInspectModalOpen = false"
                        >
                            {{ $t('Retry Message Now') }}
                        </Button>
                    </div>
                </div>
            </div>
        </Modal>
    </div>
</template>
