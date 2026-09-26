<script setup>
import { ref, computed } from 'vue';
import { Link, router, useForm } from "@inertiajs/vue3";
import debounce from 'lodash/debounce';
import AlertModal from '@/Components/AlertModal.vue';
import { useAlertModal } from '@/Composables/useAlertModal';
import Badge from '@/Components/UI/Badge.vue';
import Button from '@/Components/UI/Button.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownItemGroup from '@/Components/DropdownItemGroup.vue';
import DropdownItem from '@/Components/DropdownItem.vue';
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
    }
});

const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();
const form = useForm({});
const isSearching = ref(false);
const activeStatusFilter = ref('all');

const params = ref({
    search: props.filters?.search || null,
});

const deleteAction = (uuid) => {
    form.delete('/campaigns/' + uuid, {
        preserveState: true,
    });
};

const clearSearch = () => {
    params.value.search = null;
    runSearch();
};

const search = debounce(() => {
    isSearching.value = true;
    runSearch();
}, 600);

const runSearch = () => {
    router.visit('/campaigns', {
        method: 'get',
        data: params.value,
        preserveState: true,
        onFinish: () => {
            isSearching.value = false;
        }
    });
};

const calculatePercent = (num, denom) => {
    if (!denom || denom === 0 || !num) return 0;
    return Math.min(100, Math.round((num / denom) * 100));
};

const formatPercentageRate = (numerator, contactsCount, groupCount) => {
    const total = contactsCount > 0 ? contactsCount : groupCount;
    if (!total || total === 0 || !numerator) return '0%';
    return ((numerator / total) * 100).toFixed(1) + '%';
};

const formatStats = (numerator, contactsCount, groupCount) => {
    const total = contactsCount > 0 ? contactsCount : groupCount;
    return `${numerator || 0} / ${total || 0}`;
};

const getStatusVariant = (status) => {
    const s = String(status || '').toLowerCase();
    if (s === 'completed' || s === 'success') return 'success';
    if (s === 'scheduled') return 'cyan';
    if (s === 'processing' || s === 'running') return 'primary';
    if (s === 'failed') return 'danger';
    return 'neutral';
};

const filteredRows = computed(() => {
    if (!props.rows?.data) return [];
    if (activeStatusFilter.value === 'all') return props.rows.data;

    return props.rows.data.filter((item) => {
        const s = String(item.status || '').toLowerCase();
        if (activeStatusFilter.value === 'completed') return s === 'completed' || s === 'success';
        if (activeStatusFilter.value === 'scheduled') return s === 'scheduled';
        if (activeStatusFilter.value === 'processing') return s === 'processing' || s === 'running';
        if (activeStatusFilter.value === 'failed') return s === 'failed';
        return true;
    });
});
</script>

<template>
    <div class="space-y-4">
        <!-- Search and Filter Bar -->
        <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-between gap-3">
            <!-- Search Field -->
            <div class="relative flex-1 max-w-md">
                <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 dark:text-zinc-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                </div>

                <input
                    v-model="params.search"
                    @input="search"
                    type="text"
                    :placeholder="$t('Search campaigns by name or template...')"
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

            <!-- Status Filter Pills -->
            <div class="flex items-center gap-1 overflow-x-auto p-1 rounded-xl bg-slate-100 dark:bg-zinc-800/80 text-xs font-semibold shrink-0">
                <button
                    type="button"
                    @click="activeStatusFilter = 'all'"
                    :class="[
                        'px-3 py-1.5 rounded-lg transition-all duration-150',
                        activeStatusFilter === 'all'
                            ? 'bg-white dark:bg-zinc-900 text-slate-900 dark:text-white shadow-xs font-bold'
                            : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-200'
                    ]"
                >
                    {{ $t('All') }}
                </button>
                <button
                    type="button"
                    @click="activeStatusFilter = 'completed'"
                    :class="[
                        'px-3 py-1.5 rounded-lg transition-all duration-150',
                        activeStatusFilter === 'completed'
                            ? 'bg-white dark:bg-zinc-900 text-emerald-600 dark:text-emerald-400 shadow-xs font-bold'
                            : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-200'
                    ]"
                >
                    {{ $t('Completed') }}
                </button>
                <button
                    type="button"
                    @click="activeStatusFilter = 'scheduled'"
                    :class="[
                        'px-3 py-1.5 rounded-lg transition-all duration-150',
                        activeStatusFilter === 'scheduled'
                            ? 'bg-white dark:bg-zinc-900 text-cyan-600 dark:text-cyan-400 shadow-xs font-bold'
                            : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-200'
                    ]"
                >
                    {{ $t('Scheduled') }}
                </button>
                <button
                    type="button"
                    @click="activeStatusFilter = 'failed'"
                    :class="[
                        'px-3 py-1.5 rounded-lg transition-all duration-150',
                        activeStatusFilter === 'failed'
                            ? 'bg-white dark:bg-zinc-900 text-rose-600 dark:text-rose-400 shadow-xs font-bold'
                            : 'text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-200'
                    ]"
                >
                    {{ $t('Failed') }}
                </button>
            </div>
        </div>

        <!-- Desktop Campaigns Table -->
        <div class="hidden md:block overflow-hidden rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs transition-colors">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="border-b border-slate-100 dark:border-zinc-800/80 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500 bg-slate-50/50 dark:bg-zinc-900/40">
                        <th class="py-3.5 pl-5 pr-4">{{ $t('Campaign & Template') }}</th>
                        <th class="py-3.5 px-4">{{ $t('Audience') }}</th>
                        <th class="py-3.5 px-4">{{ $t('Delivery') }}</th>
                        <th class="py-3.5 px-4">{{ $t('Read Rate') }}</th>
                        <th class="py-3.5 px-4">{{ $t('Status') }}</th>
                        <th class="py-3.5 pl-4 pr-5 text-right">{{ $t('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-zinc-800/60 text-xs sm:text-sm">
                    <tr
                        v-for="(item, index) in filteredRows"
                        :key="index"
                        class="hover:bg-slate-50/70 dark:hover:bg-zinc-800/40 transition-colors group"
                    >
                        <!-- Campaign & Template -->
                        <td class="py-3.5 pl-5 pr-4">
                            <Link :href="'/campaigns/' + item.uuid" class="block">
                                <h3 class="font-bold text-slate-900 dark:text-white group-hover:text-[#6C5CE7] transition-colors truncate max-w-xs">
                                    {{ item.name }}
                                </h3>
                                <div class="flex items-center gap-1.5 mt-0.5 text-xs text-slate-400 dark:text-zinc-500">
                                    <span class="inline-flex items-center gap-1 font-mono text-[11px]">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-[#6C5CE7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                                        {{ item.template?.name || 'Template' }}
                                    </span>
                                </div>
                            </Link>
                        </td>

                        <!-- Audience -->
                        <td class="py-3.5 px-4">
                            <span class="font-bold text-slate-800 dark:text-zinc-200">
                                {{ item.contacts_count > 0 ? item.contacts_count : item.contact_group_count }}
                            </span>
                            <span class="text-slate-400 dark:text-zinc-500 text-xs ml-1">{{ $t('recipients') }}</span>
                        </td>

                        <!-- Delivery -->
                        <td class="py-3.5 px-4">
                            <div v-if="String(item.status).toLowerCase() === 'scheduled'" class="text-xs text-slate-400">
                                {{ $t('Pending schedule') }}
                            </div>
                            <div v-else class="space-y-1 max-w-[140px]">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-semibold text-slate-700 dark:text-zinc-300">
                                        {{ formatPercentageRate(item.delivery_count, item.contacts_count, item.contact_group_count) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">
                                        {{ formatStats(item.delivery_count, item.contacts_count, item.contact_group_count) }}
                                    </span>
                                </div>
                                <div class="h-1.5 w-full rounded-full bg-slate-100 dark:bg-zinc-800 overflow-hidden">
                                    <div
                                        class="h-full rounded-full bg-emerald-500 transition-all duration-300"
                                        :style="{ width: calculatePercent(item.delivery_count, item.contacts_count || item.contact_group_count) + '%' }"
                                    />
                                </div>
                            </div>
                        </td>

                        <!-- Read Rate -->
                        <td class="py-3.5 px-4">
                            <div v-if="String(item.status).toLowerCase() === 'scheduled'" class="text-xs text-slate-400">
                                {{ $t('Pending schedule') }}
                            </div>
                            <div v-else class="space-y-1 max-w-[140px]">
                                <div class="flex items-center justify-between text-xs">
                                    <span class="font-semibold text-slate-700 dark:text-zinc-300">
                                        {{ formatPercentageRate(item.read_count, item.contacts_count, item.contact_group_count) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">
                                        {{ formatStats(item.read_count, item.contacts_count, item.contact_group_count) }}
                                    </span>
                                </div>
                                <div class="h-1.5 w-full rounded-full bg-slate-100 dark:bg-zinc-800 overflow-hidden">
                                    <div
                                        class="h-full rounded-full bg-[#6C5CE7] transition-all duration-300"
                                        :style="{ width: calculatePercent(item.read_count, item.contacts_count || item.contact_group_count) + '%' }"
                                    />
                                </div>
                            </div>
                        </td>

                        <!-- Status Badge -->
                        <td class="py-3.5 px-4">
                            <Badge :variant="getStatusVariant(item.status)" size="sm">
                                {{ item.status }}
                            </Badge>
                        </td>

                        <!-- Actions Dropdown -->
                        <td class="py-3.5 pl-4 pr-5 text-right">
                            <div class="flex items-center justify-end gap-1.5">
                                <Link :href="'/campaigns/' + item.uuid">
                                    <Button variant="ghost" size="xs">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-500 hover:text-[#6C5CE7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </Button>
                                </Link>

                                <Dropdown align="right" width="w-36">
                                    <button
                                        type="button"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                                    </button>
                                    <template #items>
                                        <DropdownItemGroup>
                                            <DropdownItem :href="'/campaigns/' + item.uuid">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                                <span>{{ $t('Analytics') }}</span>
                                            </DropdownItem>
                                            <DropdownItem as="button" @click="openAlert(item.uuid)" class="text-rose-600 dark:text-rose-400">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                                <span>{{ $t('Delete') }}</span>
                                            </DropdownItem>
                                        </DropdownItemGroup>
                                    </template>
                                </Dropdown>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mobile Card Transformation -->
        <div class="md:hidden space-y-3">
            <div
                v-for="(item, index) in filteredRows"
                :key="index"
                class="rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-4 shadow-xs space-y-3"
            >
                <div class="flex items-start justify-between gap-2">
                    <Link :href="'/campaigns/' + item.uuid" class="min-w-0 flex-1">
                        <h3 class="font-bold text-slate-900 dark:text-white truncate text-sm">
                            {{ item.name }}
                        </h3>
                        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-0.5">
                            {{ item.template?.name || 'Template' }}
                        </p>
                    </Link>
                    <Badge :variant="getStatusVariant(item.status)" size="sm">
                        {{ item.status }}
                    </Badge>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-3 gap-2 py-2 border-y border-slate-100 dark:border-zinc-800/80 text-center">
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase font-semibold">{{ $t('Audience') }}</span>
                        <p class="text-xs font-bold text-slate-800 dark:text-zinc-200">
                            {{ item.contacts_count > 0 ? item.contacts_count : item.contact_group_count }}
                        </p>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase font-semibold">{{ $t('Delivered') }}</span>
                        <p class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                            {{ formatPercentageRate(item.delivery_count, item.contacts_count, item.contact_group_count) }}
                        </p>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase font-semibold">{{ $t('Read') }}</span>
                        <p class="text-xs font-bold text-[#6C5CE7] dark:text-purple-400">
                            {{ formatPercentageRate(item.read_count, item.contacts_count, item.contact_group_count) }}
                        </p>
                    </div>
                </div>

                <!-- Card Actions -->
                <div class="flex items-center justify-between gap-2 pt-1">
                    <Link :href="'/campaigns/' + item.uuid" class="flex-1">
                        <Button variant="secondary" size="xs" class="w-full justify-center">
                            {{ $t('View Details') }}
                        </Button>
                    </Link>
                    <Button variant="danger" size="xs" @click="openAlert(item.uuid)">
                        {{ $t('Delete') }}
                    </Button>
                </div>
            </div>
        </div>

        <!-- Empty State -->
        <div v-if="filteredRows.length === 0" class="rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-8 text-center">
            <EmptyState
                :title="$t('No Campaigns Found')"
                :description="$t('Start reaching your audience with broadcast WhatsApp messaging campaigns.')"
                actionText="Create Campaign"
                actionUrl="/campaigns/create"
            />
        </div>

        <!-- Pagination -->
        <div v-if="props.rows?.meta" class="pt-2">
            <Pagination :pagination="props.rows.meta" />
        </div>

        <!-- Delete Alert Confirmation Modal -->
        <AlertModal 
            v-model="isOpenAlert" 
            @confirm="() => confirmAlert(deleteAction)"
            :label="$t('Delete Campaign')" 
            :description="$t('Are you sure you want to delete this campaign? Existing sent messages will remain in contact chat histories.')"
        />
    </div>
</template>