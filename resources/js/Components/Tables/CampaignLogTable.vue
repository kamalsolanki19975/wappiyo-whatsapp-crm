<script setup>
import { ref } from 'vue';
import debounce from 'lodash/debounce';
import { router } from '@inertiajs/vue3';
import Modal from '@/Components/Modal.vue';
import Badge from '@/Components/UI/Badge.vue';
import Button from '@/Components/UI/Button.vue';
import Avatar from '@/Components/UI/Avatar.vue';
import Pagination from '@/Components/Pagination.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({})
    },
    uuid: {
        type: String,
        required: true,
    }
});

const params = ref({
    search: props.filters?.search || null,
});

const logs = ref(null);
const messageStatus = ref(null);
const isOpenModal = ref(false);
const isSearching = ref(false);

const clearSearch = () => {
    params.value.search = null;
    runSearch();
};

const search = debounce(() => {
    isSearching.value = true;
    runSearch();
}, 600);

const runSearch = () => {
    router.visit('/campaigns/' + props.uuid, {
        method: 'get',
        data: params.value,
        preserveState: true,
        onFinish: () => {
            isSearching.value = false;
        }
    });
};

const openModal = (status, value) => {
    messageStatus.value = status;
    logs.value = value;
    isOpenModal.value = true;
};

const getStatus = (metadata) => {
    try {
        const parsed = typeof metadata === 'string' ? JSON.parse(metadata) : metadata;
        return parsed?.status || 'sent';
    } catch (_) {
        return 'sent';
    }
};

const getErrorDetails = (val) => {
    try {
        return typeof val === 'string' ? JSON.parse(val) : val;
    } catch (_) {
        return { data: { error: { message: 'Delivery failed' } } };
    }
};

const getStatusVariant = (item) => {
    if (item.status === 'failed') return 'danger';
    const s = String(item.chat?.status || '').toLowerCase();
    if (s === 'read') return 'cyan';
    if (s === 'delivered') return 'success';
    if (s === 'sent' || s === 'accepted') return 'primary';
    return 'neutral';
};
</script>

<template>
    <div class="space-y-4">
        <!-- Search Field -->
        <div class="flex items-center justify-between gap-3">
            <div class="relative w-full max-w-sm">
                <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 dark:text-zinc-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                </div>

                <input
                    v-model="params.search"
                    @input="search"
                    type="text"
                    :placeholder="$t('Search recipients by name or phone...')"
                    class="w-full pl-9 pr-8 py-2 text-xs sm:text-sm bg-white dark:bg-zinc-900/80 text-slate-800 dark:text-zinc-200 border border-slate-200 dark:border-zinc-800 rounded-xl focus:outline-none focus:border-[#6C5CE7] focus:ring-1 focus:ring-[#6C5CE7] placeholder-slate-400 dark:placeholder-zinc-500 transition-colors shadow-xs"
                />

                <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center">
                    <button
                        v-if="params.search && !isSearching"
                        type="button"
                        @click="clearSearch"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                    <svg v-if="isSearching" class="animate-spin h-3.5 w-3.5 text-[#6C5CE7]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </div>
            </div>

            <span class="text-xs text-slate-400 dark:text-zinc-500 font-medium">
                {{ rows.meta?.total || rows.data?.length || 0 }} {{ $t('total logs') }}
            </span>
        </div>

        <!-- Desktop Recipient Logs Table -->
        <div class="overflow-hidden rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs transition-colors">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-zinc-800/80 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500 bg-slate-50/50 dark:bg-zinc-900/40">
                        <th class="py-3 pl-4 pr-3">{{ $t('Recipient') }}</th>
                        <th class="py-3 px-3">{{ $t('Phone') }}</th>
                        <th class="py-3 px-3">{{ $t('Status') }}</th>
                        <th class="py-3 px-3 hidden sm:table-cell">{{ $t('Delivery Time') }}</th>
                        <th class="py-3 pl-3 pr-4 text-right">{{ $t('Details') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-800/60 text-xs sm:text-sm">
                    <tr
                        v-for="(item, index) in rows.data"
                        :key="index"
                        class="hover:bg-slate-50/70 dark:hover:bg-zinc-800/40 transition-colors"
                    >
                        <!-- Recipient Name & Avatar -->
                        <td class="py-3 pl-4 pr-3">
                            <div class="flex items-center gap-2.5">
                                <Avatar
                                    :src="item.contact?.avatar || null"
                                    :name="item.contact?.full_name || 'Contact'"
                                    size="sm"
                                />
                                <span class="font-semibold text-slate-900 dark:text-white truncate max-w-[140px] sm:max-w-xs">
                                    {{ item.contact?.full_name || '—' }}
                                </span>
                            </div>
                        </td>

                        <!-- Phone -->
                        <td class="py-3 px-3 font-mono text-xs text-slate-600 dark:text-zinc-400">
                            {{ item.contact?.phone || '—' }}
                        </td>

                        <!-- Status Badge -->
                        <td class="py-3 px-3">
                            <Badge :variant="getStatusVariant(item)" size="sm">
                                {{ item.status === 'success' ? (item.chat?.status || 'Sent') : $t('Failed') }}
                            </Badge>
                        </td>

                        <!-- Delivery Time -->
                        <td class="py-3 px-3 text-xs text-slate-500 dark:text-zinc-400 hidden sm:table-cell">
                            {{ item.status === 'success' ? (item.chat?.created_at || item.created_at) : item.created_at }}
                        </td>

                        <!-- Action: More Info -->
                        <td class="py-3 pl-3 pr-4 text-right">
                            <button
                                type="button"
                                @click="openModal(item.status, item.status === 'success' ? item.chat?.logs : item.metadata)"
                                class="inline-flex items-center gap-1 text-xs font-semibold text-[#6C5CE7] hover:text-[#5B4BC4] dark:text-purple-400 hover:underline"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                                <span>{{ $t('Inspect') }}</span>
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div v-if="!rows.data || rows.data.length === 0" class="p-8 text-center text-xs text-slate-400 dark:text-zinc-500">
                {{ $t('No recipient delivery logs recorded yet.') }}
            </div>
        </div>

        <!-- Pagination -->
        <div v-if="props.rows?.meta" class="pt-2">
            <Pagination :pagination="props.rows.meta" />
        </div>

        <!-- Message Delivery Timeline / Error Modal -->
        <Modal :label="$t('Message Delivery Details')" :isOpen="isOpenModal" @close="isOpenModal = false">
            <div class="space-y-4">
                <!-- SUCCESS: Delivery Milestones Timeline -->
                <div v-if="messageStatus === 'success'" class="space-y-3">
                    <p class="text-xs text-slate-500 dark:text-zinc-400">
                        {{ $t('WhatsApp webhook delivery lifecycle events recorded for this recipient:') }}
                    </p>

                    <div v-if="logs && logs.length > 0" class="space-y-2 border-l-2 border-purple-200 dark:border-purple-800 ml-2 pl-3">
                        <div
                            v-for="(log, idx) in logs"
                            :key="idx"
                            class="relative text-xs space-y-0.5"
                        >
                            <span class="absolute -left-[19px] top-1 h-2.5 w-2.5 rounded-full bg-[#6C5CE7] ring-4 ring-white dark:ring-[#111113]"></span>
                            <div class="flex items-center gap-1.5 font-bold capitalize text-slate-800 dark:text-zinc-200">
                                <span>{{ $t(getStatus(log.metadata)) }}</span>
                            </div>
                            <p class="text-[11px] text-slate-400 dark:text-zinc-500 font-mono">{{ log.created_at }}</p>
                        </div>
                    </div>
                    <div v-else class="text-xs text-slate-400 italic">
                        {{ $t('No webhook callbacks recorded yet.') }}
                    </div>
                </div>

                <!-- FAILED: Error Details -->
                <div v-else-if="messageStatus === 'failed'" class="space-y-3">
                    <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-800/40 text-rose-800 dark:text-rose-300 text-xs space-y-1">
                        <div class="font-bold flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>{{ $t('WhatsApp API Error') }}</span>
                        </div>
                        <p class="font-mono text-[11px]">
                            {{ getErrorDetails(logs)?.data?.error?.message || getErrorDetails(logs)?.message || 'Delivery failed' }}
                        </p>
                        <p v-if="getErrorDetails(logs)?.data?.error?.error_data?.details" class="text-[11px] opacity-80 pt-1">
                            {{ getErrorDetails(logs).data.error.error_data.details }}
                        </p>
                    </div>
                </div>

                <!-- Footer -->
                <div class="flex justify-end pt-2">
                    <Button variant="secondary" size="sm" @click="isOpenModal = false">
                        {{ $t('Close') }}
                    </Button>
                </div>
            </div>
        </Modal>
    </div>
</template>