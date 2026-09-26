<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import AlertModal from '@/Components/AlertModal.vue';
import { Link, router } from "@inertiajs/vue3";
import { useAlertModal } from '@/Composables/useAlertModal';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';
import Table from '@/Components/Table.vue';
import TableHeader from '@/Components/TableHeader.vue';
import TableHeaderRow from '@/Components/TableHeaderRow.vue';
import TableHeaderRowItem from '@/Components/TableHeaderRowItem.vue';
import TableBody from '@/Components/TableBody.vue';
import TableBodyRow from '@/Components/TableBodyRow.vue';
import TableBodyRowItem from '@/Components/TableBodyRowItem.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownItemGroup from '@/Components/DropdownItemGroup.vue';
import DropdownItem from '@/Components/DropdownItem.vue';
import Button from '@/Components/UI/Button.vue';
import Badge from '@/Components/UI/Badge.vue';
import Pagination from '@/Components/Pagination.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';
import TemplateStatusBadge from '@/Components/Template/TemplateStatusBadge.vue';
import TemplatePreviewModal from '@/Components/Template/TemplatePreviewModal.vue';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    },
    stats: {
        type: Object,
        default: () => null,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const emit = defineEmits(['delete', 'sync']);

// Modal state
const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();
const isPreviewModalOpen = ref(false);
const previewTemplateItem = ref(null);

// Filters & View State
const search = ref(props.filters?.search || '');
const activeStatus = ref(props.filters?.status || 'ALL');
const activeCategory = ref(props.filters?.category || 'ALL');
const viewMode = ref('table'); // 'table' | 'cards'

// Real Metrics from backend or calculated from current page
const kpiStats = computed(() => {
    if (props.stats) {
        return props.stats;
    }
    const data = props.rows?.data || [];
    return {
        total: data.length,
        approved: data.filter(t => t.status === 'APPROVED').length,
        pending: data.filter(t => t.status === 'PENDING').length,
        rejected: data.filter(t => t.status === 'REJECTED').length,
    };
});

// Debounced search
let searchTimeout = null;
const handleSearchInput = () => {
    clearTimeout(searchTimeout);
    searchTimeout = setTimeout(() => {
        applyFilters();
    }, 350);
};

const setStatusFilter = (status) => {
    activeStatus.value = status;
    applyFilters();
};

const setCategoryFilter = (cat) => {
    activeCategory.value = cat;
    applyFilters();
};

const clearAllFilters = () => {
    search.value = '';
    activeStatus.value = 'ALL';
    activeCategory.value = 'ALL';
    applyFilters();
};

const hasActiveFilters = computed(() => {
    return Boolean(search.value || (activeStatus.value && activeStatus.value !== 'ALL') || (activeCategory.value && activeCategory.value !== 'ALL'));
});

const applyFilters = () => {
    router.get(
        '/templates',
        {
            search: search.value || undefined,
            status: activeStatus.value !== 'ALL' ? activeStatus.value : undefined,
            category: activeCategory.value !== 'ALL' ? activeCategory.value : undefined,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true,
        }
    );
};

// Open Quick Preview Modal
const openPreview = (item) => {
    previewTemplateItem.value = item;
    isPreviewModalOpen.value = true;
};

// Duplicate template: navigates to create with duplicate query
const duplicateTemplate = (item) => {
    router.visit(`/templates/create?duplicate=${item.uuid}`);
};

// Delete Action
const deleteAction = async (key) => {
    try {
        const response = await axios.delete(`/templates/${key}`);

        if (response.status === 200 && response.data.success) {
            const idx = props.rows.data.findIndex((i) => i.uuid === key);
            if (idx !== -1) {
                props.rows.data.splice(idx, 1);
            }
            toast.success(response.data.message || 'Template deleted successfully', {
                autoClose: 3000,
            });
        } else {
            toast.error(response.data.message || 'Failed to delete template', {
                autoClose: 3000,
            });
        }
    } catch (error) {
        toast.error('Something went wrong while deleting the template', {
            autoClose: 3000,
        });
    }
};

const isLastRow = (index) => {
    return index === (props.rows.data?.length || 0) - 1;
};

const capitalizeFirstLetter = (string) => {
    if (!string) return '';
    return string.charAt(0).toUpperCase() + string.slice(1).toLowerCase();
};

const findBodyText = (metadata) => {
    if (!metadata) return '—';
    try {
        const parsed = typeof metadata === 'string' ? JSON.parse(metadata) : metadata;
        const body = parsed?.components?.find(element => element.type === 'BODY');
        return body?.text || '—';
    } catch (e) {
        return '—';
    }
};

const getHeaderFormat = (metadata) => {
    if (!metadata) return 'NONE';
    try {
        const parsed = typeof metadata === 'string' ? JSON.parse(metadata) : metadata;
        const header = parsed?.components?.find(element => element.type === 'HEADER');
        return header?.format || 'NONE';
    } catch (e) {
        return 'NONE';
    }
};

const getButtonsCount = (metadata) => {
    if (!metadata) return 0;
    try {
        const parsed = typeof metadata === 'string' ? JSON.parse(metadata) : metadata;
        const btns = parsed?.components?.find(element => element.type === 'BUTTONS');
        return btns?.buttons?.length || 0;
    } catch (e) {
        return 0;
    }
};
</script>

<template>
    <div class="space-y-6">
        <!-- KPI SUMMARY METRICS -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Total Templates -->
            <div 
                @click="setStatusFilter('ALL')"
                class="group cursor-pointer relative overflow-hidden rounded-2xl p-4 sm:p-5 transition-all duration-200 border bg-white dark:bg-[#111113] hover:shadow-md"
                :class="activeStatus === 'ALL' ? 'border-[#6C5CE7] ring-2 ring-[#6C5CE7]/20 shadow-sm' : 'border-slate-200/80 dark:border-zinc-800/80'"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                        {{ $t('Total Templates') }}
                    </span>
                    <span class="p-2 rounded-xl bg-purple-500/10 text-[#6C5CE7] dark:text-purple-400">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-black text-slate-900 dark:text-white">
                        {{ kpiStats.total }}
                    </span>
                    <span class="text-xs text-slate-500 dark:text-zinc-400 font-medium">
                        {{ $t('in organization') }}
                    </span>
                </div>
            </div>

            <!-- Approved -->
            <div 
                @click="setStatusFilter('APPROVED')"
                class="group cursor-pointer relative overflow-hidden rounded-2xl p-4 sm:p-5 transition-all duration-200 border bg-white dark:bg-[#111113] hover:shadow-md"
                :class="activeStatus === 'APPROVED' ? 'border-emerald-500 ring-2 ring-emerald-500/20 shadow-sm' : 'border-slate-200/80 dark:border-zinc-800/80'"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                        {{ $t('Approved') }}
                    </span>
                    <span class="p-2 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-black text-emerald-600 dark:text-emerald-400">
                        {{ kpiStats.approved }}
                    </span>
                    <span class="text-xs text-slate-500 dark:text-zinc-400 font-medium">
                        {{ $t('ready to broadcast') }}
                    </span>
                </div>
            </div>

            <!-- Pending -->
            <div 
                @click="setStatusFilter('PENDING')"
                class="group cursor-pointer relative overflow-hidden rounded-2xl p-4 sm:p-5 transition-all duration-200 border bg-white dark:bg-[#111113] hover:shadow-md"
                :class="activeStatus === 'PENDING' ? 'border-amber-500 ring-2 ring-amber-500/20 shadow-sm' : 'border-slate-200/80 dark:border-zinc-800/80'"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                        {{ $t('Pending Review') }}
                    </span>
                    <span class="p-2 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-black text-amber-600 dark:text-amber-400">
                        {{ kpiStats.pending }}
                    </span>
                    <span class="text-xs text-slate-500 dark:text-zinc-400 font-medium">
                        {{ $t('awaiting Meta review') }}
                    </span>
                </div>
            </div>

            <!-- Rejected -->
            <div 
                @click="setStatusFilter('REJECTED')"
                class="group cursor-pointer relative overflow-hidden rounded-2xl p-4 sm:p-5 transition-all duration-200 border bg-white dark:bg-[#111113] hover:shadow-md"
                :class="activeStatus === 'REJECTED' ? 'border-rose-500 ring-2 ring-rose-500/20 shadow-sm' : 'border-slate-200/80 dark:border-zinc-800/80'"
            >
                <div class="flex items-center justify-between">
                    <span class="text-xs font-semibold uppercase tracking-wider text-rose-600 dark:text-rose-400">
                        {{ $t('Rejected') }}
                    </span>
                    <span class="p-2 rounded-xl bg-rose-500/10 text-rose-600 dark:text-rose-400">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                    </span>
                </div>
                <div class="mt-3 flex items-baseline gap-2">
                    <span class="text-2xl sm:text-3xl font-black text-rose-600 dark:text-rose-400">
                        {{ kpiStats.rejected }}
                    </span>
                    <span class="text-xs text-slate-500 dark:text-zinc-400 font-medium">
                        {{ $t('requires changes') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- FILTER & SEARCH TOOLBAR -->
        <div class="bg-white dark:bg-[#111113] rounded-2xl border border-slate-200/80 dark:border-zinc-800/80 p-4 shadow-sm space-y-4">
            <div class="flex flex-col md:flex-row items-stretch md:items-center justify-between gap-3">
                <!-- Search Box -->
                <div class="relative flex-1 max-w-md">
                    <span class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </span>
                    <input
                        type="text"
                        v-model="search"
                        @input="handleSearchInput"
                        :placeholder="$t('Search templates by name, body, category...')"
                        class="w-full pl-10 pr-9 py-2 rounded-xl text-sm border border-slate-200 dark:border-zinc-700/80 bg-slate-50/50 dark:bg-zinc-800/50 text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-[#6C5CE7]/30 focus:border-[#6C5CE7] transition-all"
                    />
                    <button
                        v-if="search"
                        type="button"
                        @click="search = ''; applyFilters()"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200"
                    >
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Right Controls: Category Dropdown & View Mode Switcher -->
                <div class="flex items-center gap-2.5 overflow-x-auto pb-1 md:pb-0">
                    <!-- Category Selector -->
                    <div class="relative">
                        <select
                            v-model="activeCategory"
                            @change="applyFilters"
                            class="text-xs font-semibold py-2 pl-3 pr-8 rounded-xl border border-slate-200 dark:border-zinc-700/80 bg-white dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-[#6C5CE7]/30"
                        >
                            <option value="ALL">{{ $t('All Categories') }}</option>
                            <option value="MARKETING">{{ $t('Marketing') }}</option>
                            <option value="UTILITY">{{ $t('Utility') }}</option>
                            <option value="AUTHENTICATION">{{ $t('Authentication') }}</option>
                        </select>
                    </div>

                    <!-- View Switcher (Table / Card Grid) -->
                    <div class="flex items-center p-1 rounded-xl bg-slate-100 dark:bg-zinc-800 border border-slate-200/80 dark:border-zinc-700/80">
                        <button
                            type="button"
                            @click="viewMode = 'table'"
                            :title="$t('Table View')"
                            class="p-1.5 rounded-lg transition-colors"
                            :class="viewMode === 'table' ? 'bg-white dark:bg-zinc-700 text-[#6C5CE7] dark:text-purple-400 shadow-sm' : 'text-slate-400 hover:text-slate-600 dark:hover:text-zinc-300'"
                        >
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                        </button>
                        <button
                            type="button"
                            @click="viewMode = 'cards'"
                            :title="$t('Card Grid View')"
                            class="p-1.5 rounded-lg transition-colors"
                            :class="viewMode === 'cards' ? 'bg-white dark:bg-zinc-700 text-[#6C5CE7] dark:text-purple-400 shadow-sm' : 'text-slate-400 hover:text-slate-600 dark:hover:text-zinc-300'"
                        >
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="7" x="3" y="3" rx="1"/><rect width="7" height="7" x="14" y="3" rx="1"/><rect width="7" height="7" x="14" y="14" rx="1"/><rect width="7" height="7" x="3" y="14" rx="1"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Status Segmented Tabs -->
            <div class="flex items-center gap-1.5 overflow-x-auto pb-1 pt-1 border-t border-slate-100 dark:border-zinc-800/80">
                <button
                    type="button"
                    @click="setStatusFilter('ALL')"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap"
                    :class="activeStatus === 'ALL'
                        ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-500/20'
                        : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800'"
                >
                    {{ $t('All') }} ({{ kpiStats.total }})
                </button>
                <button
                    type="button"
                    @click="setStatusFilter('APPROVED')"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap flex items-center gap-1.5"
                    :class="activeStatus === 'APPROVED'
                        ? 'bg-emerald-600 text-white shadow-sm shadow-emerald-500/20'
                        : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800'"
                >
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                    {{ $t('Approved') }} ({{ kpiStats.approved }})
                </button>
                <button
                    type="button"
                    @click="setStatusFilter('PENDING')"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap flex items-center gap-1.5"
                    :class="activeStatus === 'PENDING'
                        ? 'bg-amber-500 text-white shadow-sm shadow-amber-500/20'
                        : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800'"
                >
                    <span class="w-1.5 h-1.5 rounded-full bg-amber-300"></span>
                    {{ $t('Pending') }} ({{ kpiStats.pending }})
                </button>
                <button
                    type="button"
                    @click="setStatusFilter('REJECTED')"
                    class="px-3 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap flex items-center gap-1.5"
                    :class="activeStatus === 'REJECTED'
                        ? 'bg-rose-600 text-white shadow-sm shadow-rose-500/20'
                        : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800'"
                >
                    <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                    {{ $t('Rejected') }} ({{ kpiStats.rejected }})
                </button>

                <!-- Clear Active Filters Chip -->
                <button
                    v-if="hasActiveFilters"
                    type="button"
                    @click="clearAllFilters"
                    class="ml-auto text-xs font-medium text-rose-500 hover:text-rose-600 dark:text-rose-400 hover:underline px-2 py-1"
                >
                    {{ $t('Reset Filters') }}
                </button>
            </div>
        </div>

        <!-- TEMPLATE LIST / GRID CONTENT -->
        <div v-if="rows.data && rows.data.length > 0">
            <!-- 1. TABLE VIEW -->
            <div v-if="viewMode === 'table'" class="bg-white dark:bg-[#111113] rounded-2xl border border-slate-200/80 dark:border-zinc-800/80 shadow-sm overflow-hidden">
                <Table :rows="rows">
                    <TableHeader>
                        <TableHeaderRow>
                            <TableHeaderRowItem :position="'first'">{{ $t('Template Name') }}</TableHeaderRowItem>
                            <TableHeaderRowItem>{{ $t('Category') }}</TableHeaderRowItem>
                            <TableHeaderRowItem>{{ $t('Language') }}</TableHeaderRowItem>
                            <TableHeaderRowItem class="hidden md:table-cell">{{ $t('Message Preview') }}</TableHeaderRowItem>
                            <TableHeaderRowItem>{{ $t('Status') }}</TableHeaderRowItem>
                            <TableHeaderRowItem class="hidden lg:table-cell">{{ $t('Updated') }}</TableHeaderRowItem>
                            <TableHeaderRowItem :position="'last'" class="text-right">{{ $t('Actions') }}</TableHeaderRowItem>
                        </TableHeaderRow>
                    </TableHeader>
                    <TableBody>
                        <TableBodyRow 
                            v-for="(item, index) in rows.data" 
                            :key="item.uuid || index" 
                            :class="[!isLastRow(index) ? 'border-b border-slate-100 dark:border-zinc-800/60' : '', 'hover:bg-slate-50/60 dark:hover:bg-zinc-800/30 transition-colors cursor-pointer']"
                            @click="openPreview(item)"
                        >
                            <!-- Template Name -->
                            <TableBodyRowItem :position="'first'">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-[#6C5CE7] dark:text-purple-400 flex items-center justify-center shrink-0">
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-mono font-bold text-xs sm:text-sm text-slate-900 dark:text-white truncate">
                                            {{ item.name }}
                                        </div>
                                        <div class="flex items-center gap-2 text-[11px] text-slate-400 dark:text-zinc-500">
                                            <span v-if="getHeaderFormat(item.metadata) !== 'NONE'" class="capitalize">
                                                {{ getHeaderFormat(item.metadata).toLowerCase() }} header
                                            </span>
                                            <span v-if="getButtonsCount(item.metadata) > 0">• {{ getButtonsCount(item.metadata) }} buttons</span>
                                        </div>
                                    </div>
                                </div>
                            </TableBodyRowItem>

                            <!-- Category -->
                            <TableBodyRowItem>
                                <span 
                                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold tracking-wide"
                                    :class="item.category === 'MARKETING'
                                        ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300'
                                        : item.category === 'UTILITY'
                                        ? 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-300'
                                        : 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'"
                                >
                                    {{ capitalizeFirstLetter(item.category) }}
                                </span>
                            </TableBodyRowItem>

                            <!-- Language -->
                            <TableBodyRowItem>
                                <span class="px-2 py-0.5 rounded bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-300 text-xs font-mono font-medium uppercase">
                                    {{ item.language || 'en' }}
                                </span>
                            </TableBodyRowItem>

                            <!-- Preview snippet -->
                            <TableBodyRowItem class="hidden md:table-cell max-w-xs">
                                <div class="py-1 px-2.5 bg-slate-50 dark:bg-zinc-800/60 rounded-lg border border-dashed border-slate-200 dark:border-zinc-700/60 text-xs text-slate-600 dark:text-zinc-300 truncate font-mono">
                                    {{ findBodyText(item.metadata) }}
                                </div>
                            </TableBodyRowItem>

                            <!-- Status -->
                            <TableBodyRowItem>
                                <TemplateStatusBadge :status="item.status" size="sm" />
                            </TableBodyRowItem>

                            <!-- Updated date -->
                            <TableBodyRowItem class="hidden lg:table-cell text-xs text-slate-500 dark:text-zinc-400">
                                {{ item.updated_at }}
                            </TableBodyRowItem>

                            <!-- Actions -->
                            <TableBodyRowItem :position="'last'" class="text-right" @click.stop>
                                <div class="flex items-center justify-end gap-1.5">
                                    <!-- Quick Preview Button -->
                                    <button
                                        type="button"
                                        @click="openPreview(item)"
                                        :title="$t('Preview Template')"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors"
                                    >
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <Dropdown :align="'right'">
                                        <button 
                                            type="button"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors"
                                        >
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M12 16a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2m0-6a2 2 0 0 1 2 2a2 2 0 0 1-2 2a2 2 0 0 1-2-2a2 2 0 0 1 2-2Z"/></svg>
                                        </button>
                                        <template #items>
                                            <DropdownItemGroup>
                                                <DropdownItem as="button" @click="openPreview(item)">
                                                    {{ $t('View / Preview') }}
                                                </DropdownItem>
                                                <DropdownItem :href="'/templates/' + item.uuid">
                                                    {{ $t('Edit Template') }}
                                                </DropdownItem>
                                                <DropdownItem as="button" @click="duplicateTemplate(item)">
                                                    {{ $t('Duplicate') }}
                                                </DropdownItem>
                                                <DropdownItem as="button" @click="openAlert(item.uuid)" class="text-rose-600 dark:text-rose-400">
                                                    {{ $t('Delete Template') }}
                                                </DropdownItem>
                                            </DropdownItemGroup>
                                        </template>
                                    </Dropdown>
                                </div>
                            </TableBodyRowItem>
                        </TableBodyRow>
                    </TableBody>
                </Table>
            </div>

            <!-- 2. CARD GRID VIEW -->
            <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <div 
                    v-for="(item, index) in rows.data" 
                    :key="item.uuid || index"
                    @click="openPreview(item)"
                    class="group cursor-pointer relative bg-white dark:bg-[#111113] rounded-2xl border border-slate-200/80 dark:border-zinc-800/80 p-5 shadow-sm hover:shadow-md transition-all duration-200 flex flex-col justify-between gap-4"
                >
                    <!-- Card Top Header -->
                    <div class="space-y-2.5">
                        <div class="flex items-center justify-between gap-2">
                            <span 
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"
                                :class="item.category === 'MARKETING'
                                    ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300'
                                    : item.category === 'UTILITY'
                                    ? 'bg-cyan-100 dark:bg-cyan-900/30 text-cyan-700 dark:text-cyan-300'
                                    : 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300'"
                            >
                                {{ item.category }}
                            </span>
                            <TemplateStatusBadge :status="item.status" size="xs" />
                        </div>

                        <!-- Template Title -->
                        <div class="flex items-baseline justify-between gap-2">
                            <h4 class="font-mono font-bold text-sm text-slate-900 dark:text-white truncate group-hover:text-[#6C5CE7] transition-colors">
                                {{ item.name }}
                            </h4>
                            <span class="px-1.5 py-0.5 rounded bg-slate-100 dark:bg-zinc-800 text-[10px] font-mono text-slate-500 uppercase shrink-0">
                                {{ item.language || 'en' }}
                            </span>
                        </div>

                        <!-- Mini Message Snippet Box -->
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-zinc-800/50 border border-slate-100 dark:border-zinc-800 text-xs text-slate-600 dark:text-zinc-300 line-clamp-3 leading-relaxed font-sans">
                            {{ findBodyText(item.metadata) }}
                        </div>
                    </div>

                    <!-- Card Bottom Meta & Actions -->
                    <div class="pt-3 border-t border-slate-100 dark:border-zinc-800/80 flex items-center justify-between text-xs text-slate-400 dark:text-zinc-500">
                        <span class="truncate">{{ item.updated_at }}</span>
                        
                        <div class="flex items-center gap-1" @click.stop>
                            <button
                                type="button"
                                @click="openPreview(item)"
                                :title="$t('Preview')"
                                class="p-1 rounded text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-800"
                            >
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                            <Link
                                :href="'/templates/' + item.uuid"
                                :title="$t('Edit')"
                                class="p-1 rounded text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-800"
                            >
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.121 2.121 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                            </Link>
                            <button
                                type="button"
                                @click="duplicateTemplate(item)"
                                :title="$t('Duplicate')"
                                class="p-1 rounded text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-800"
                            >
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="14" height="14" x="8" y="8" rx="2" ry="2"/><path d="M4 16c-1.1 0-2-.9-2-2V4c0-1.1.9-2 2-2h10c1.1 0 2 .9 2 2"/></svg>
                            </button>
                            <button
                                type="button"
                                @click="openAlert(item.uuid)"
                                :title="$t('Delete')"
                                class="p-1 rounded text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30"
                            >
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- PAGINATION -->
            <div class="mt-6 flex justify-center">
                <Pagination :links="rows.links || []" />
            </div>
        </div>

        <!-- EMPTY STATE (NO TEMPLATES OR FILTER EMPTY) -->
        <div v-else class="bg-white dark:bg-[#111113] rounded-2xl border border-slate-200/80 dark:border-zinc-800/80 p-8 shadow-sm">
            <EmptyState
                v-if="hasActiveFilters"
                :title="$t('No templates match your filters')"
                :description="$t('Try clearing your search query or switching status filters.')"
                icon="filter"
            >
                <template #actions>
                    <Button variant="secondary" size="sm" @click="clearAllFilters">
                        {{ $t('Clear All Filters') }}
                    </Button>
                </template>
            </EmptyState>

            <EmptyState
                v-else
                :title="$t('No WhatsApp Templates Yet')"
                :description="$t('Create your first WhatsApp message template to launch campaigns, send automated notifications, and engage leads.')"
                icon="message"
            >
                <template #actions>
                    <div class="flex items-center gap-3">
                        <Button variant="secondary" size="sm" @click="$emit('sync')">
                            <template #icon>
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/></svg>
                            </template>
                            {{ $t('Sync from Meta') }}
                        </Button>
                        <Link href="/templates/create">
                            <Button variant="primary" size="sm">
                                <template #icon>
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                </template>
                                {{ $t('Create Template') }}
                            </Button>
                        </Link>
                    </div>
                </template>
            </EmptyState>
        </div>

        <!-- QUICK PREVIEW MODAL -->
        <TemplatePreviewModal
            v-model="isPreviewModalOpen"
            :template="previewTemplateItem"
            @duplicate="duplicateTemplate"
        />

        <!-- DELETE CONFIRMATION ALERT MODAL -->
        <AlertModal 
            v-model="isOpenAlert" 
            @confirm="() => confirmAlert(deleteAction)"
            :label="$t('Delete Template?')" 
            :description="$t('Are you sure you want to delete this message template from WhatsApp? This action cannot be undone.')"
        />
    </div>
</template>