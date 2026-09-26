<template>
    <SettingLayout :aimodule="aimodule" :fbmodule="fbmodule">
        <div class="space-y-6">
            <!-- Top Action Banner -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 bg-gradient-to-r from-primary/5 via-violet-500/5 to-transparent p-5 rounded-2xl border border-primary/20">
                <div>
                    <h2 class="text-xl font-bold text-slate-900 dark:text-white">
                        {{ $t('Automations') }}
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ $t('Automate conversations, customer journeys, and repetitive workflows.') }}
                    </p>
                </div>

                <div class="flex items-center gap-2.5">
                    <button
                        @click="isCreateModalOpen = true"
                        class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-primary to-violet-600 hover:from-primary/90 hover:to-violet-700 text-white text-xs font-bold shadow-md shadow-primary/20 transition-all duration-200"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>{{ $t('Create Automation') }}</span>
                    </button>
                </div>
            </div>

            <!-- KPI Metric Summary Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
                <!-- Total Automations -->
                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-white/10 shadow-sm flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 text-primary flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-slate-900 dark:text-white">{{ kpiStats.total }}</div>
                        <div class="text-xs text-slate-500 font-medium">{{ $t('Total Automations') }}</div>
                    </div>
                </div>

                <!-- Active Workflows -->
                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-white/10 shadow-sm flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-emerald-600 dark:text-emerald-400">{{ kpiStats.active }}</div>
                        <div class="text-xs text-slate-500 font-medium">{{ $t('Active') }}</div>
                    </div>
                </div>

                <!-- Draft Workflows -->
                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-white/10 shadow-sm flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-600 dark:text-amber-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-amber-600 dark:text-amber-400">{{ kpiStats.draft }}</div>
                        <div class="text-xs text-slate-500 font-medium">{{ $t('Drafts') }}</div>
                    </div>
                </div>

                <!-- Inactive Workflows -->
                <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-white/10 shadow-sm flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-500/10 text-slate-600 dark:text-slate-400 flex items-center justify-center shrink-0">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 9v6m4-6v6m7-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="text-xl font-bold text-slate-700 dark:text-slate-300">{{ kpiStats.inactive }}</div>
                        <div class="text-xs text-slate-500 font-medium">{{ $t('Inactive') }}</div>
                    </div>
                </div>
            </div>

            <!-- Search, Filters, and View Switcher -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <!-- Search Input -->
                <div class="relative w-full sm:w-80">
                    <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                    </span>
                    <input
                        v-model="searchTerm"
                        @input="handleSearch"
                        type="text"
                        :placeholder="$t('Search by name or trigger phrase...')"
                        class="w-full pl-9 pr-9 py-2 rounded-xl border border-slate-200/80 dark:border-white/10 bg-white dark:bg-slate-900 text-slate-900 dark:text-white placeholder:text-slate-400 text-xs focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                    />
                    <button
                        v-if="searchTerm"
                        @click="clearSearch"
                        type="button"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-slate-600"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <!-- Filters & View Switcher -->
                <div class="flex items-center gap-2">
                    <!-- Status Filter Tabs -->
                    <div class="flex items-center p-1 rounded-xl bg-slate-100 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 text-xs font-medium">
                        <button
                            v-for="tab in ['all', 'active', 'draft', 'inactive']"
                            :key="tab"
                            @click="selectedStatus = tab"
                            class="px-3 py-1 rounded-lg capitalize transition"
                            :class="selectedStatus === tab
                                ? 'bg-white dark:bg-slate-800 text-primary dark:text-white font-bold shadow-xs'
                                : 'text-slate-500 hover:text-slate-800 dark:hover:text-white'"
                        >
                            {{ tab === 'all' ? $t('All') : $t(tab) }}
                        </button>
                    </div>

                    <!-- View Switcher -->
                    <div class="flex items-center p-1 rounded-xl bg-slate-100 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 text-slate-500">
                        <button
                            @click="viewMode = 'grid'"
                            :title="$t('Cards View')"
                            class="p-1.5 rounded-lg transition"
                            :class="viewMode === 'grid' ? 'bg-white dark:bg-slate-800 text-primary dark:text-white shadow-xs' : 'hover:text-slate-900 dark:hover:text-white'"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </button>
                        <button
                            @click="viewMode = 'table'"
                            :title="$t('Table View')"
                            class="p-1.5 rounded-lg transition"
                            :class="viewMode === 'table' ? 'bg-white dark:bg-slate-800 text-primary dark:text-white shadow-xs' : 'hover:text-slate-900 dark:hover:text-white'"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Cards Grid View -->
            <div v-if="filteredRows.length > 0 && viewMode === 'grid'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                <AutomationCard
                    v-for="item in filteredRows"
                    :key="item.uuid"
                    :automation="item"
                    @delete="confirmDelete"
                />
            </div>

            <!-- Table View -->
            <div v-else-if="filteredRows.length > 0 && viewMode === 'table'" class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-white/10 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse text-xs">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-white/10 bg-slate-50/50 dark:bg-white/5 text-slate-500 font-semibold uppercase tracking-wider">
                                <th class="py-3 px-4">{{ $t('Status') }}</th>
                                <th class="py-3 px-4">{{ $t('Name') }}</th>
                                <th class="py-3 px-4">{{ $t('Trigger Phrase') }}</th>
                                <th class="py-3 px-4">{{ $t('Match Criteria') }}</th>
                                <th class="py-3 px-4">{{ $t('Response') }}</th>
                                <th class="py-3 px-4">{{ $t('Updated') }}</th>
                                <th class="py-3 px-4 text-right">{{ $t('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            <tr
                                v-for="item in filteredRows"
                                :key="item.uuid"
                                class="hover:bg-slate-50/60 dark:hover:bg-white/5 transition"
                            >
                                <!-- Status -->
                                <td class="py-3 px-4">
                                    <span
                                        class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[11px] font-semibold"
                                        :class="getItemStatusBadgeClass(item)"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full" :class="getItemStatusDotClass(item)"></span>
                                        {{ getItemStatusLabel(item) }}
                                    </span>
                                </td>

                                <!-- Name -->
                                <td class="py-3 px-4">
                                    <Link
                                        :href="`/automation/builder/${item.uuid}`"
                                        class="font-bold text-slate-900 dark:text-white hover:text-primary transition"
                                    >
                                        {{ item.name }}
                                    </Link>
                                </td>

                                <!-- Trigger -->
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-md bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 font-mono text-[11px] border border-emerald-500/20 max-w-[150px] inline-block truncate">
                                        {{ item.trigger }}
                                    </span>
                                </td>

                                <!-- Criteria -->
                                <td class="py-3 px-4 text-slate-600 dark:text-slate-400 capitalize">
                                    {{ item.match_criteria }}
                                </td>

                                <!-- Response Type -->
                                <td class="py-3 px-4">
                                    <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 capitalize">
                                        {{ getItemResponseType(item) }}
                                    </span>
                                </td>

                                <!-- Updated -->
                                <td class="py-3 px-4 text-slate-400">
                                    {{ item.updated_at }}
                                </td>

                                <!-- Actions -->
                                <td class="py-3 px-4 text-right">
                                    <div class="flex items-center justify-end gap-1.5">
                                        <Link
                                            :href="`/automation/builder/${item.uuid}`"
                                            class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg bg-primary/10 hover:bg-primary text-primary hover:text-white text-xs font-semibold transition"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                                            </svg>
                                            <span>{{ $t('Builder') }}</span>
                                        </Link>

                                        <Link
                                            :href="`/automation/basic/${item.uuid}/edit`"
                                            class="p-1 text-slate-400 hover:text-slate-700 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-white/5 transition"
                                            :title="$t('Edit')"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </Link>

                                        <button
                                            @click="confirmDelete(item.uuid)"
                                            class="p-1 text-rose-500 hover:text-rose-700 rounded-lg hover:bg-rose-50 dark:hover:bg-rose-950/20 transition"
                                            :title="$t('Delete')"
                                        >
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-white/10 p-12 text-center shadow-sm"
            >
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-primary/20 to-violet-500/20 text-primary mx-auto flex items-center justify-center mb-4">
                    <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M13 10V3L4 14h7v7l9-11h-7z" />
                    </svg>
                </div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">
                    {{ searchTerm || selectedStatus !== 'all' ? $t('No matching automations') : $t('No automations yet') }}
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 max-w-sm mx-auto mb-6">
                    {{ searchTerm || selectedStatus !== 'all'
                        ? $t('Try clearing your search keyword or switching your status filter.')
                        : $t('Automate customer conversations, inbound triggers, and follow-ups with intelligent workflows.') }}
                </p>
                <div class="flex items-center justify-center gap-3">
                    <button
                        v-if="searchTerm || selectedStatus !== 'all'"
                        @click="clearFilters"
                        class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 rounded-xl transition"
                    >
                        {{ $t('Clear Filters') }}
                    </button>
                    <button
                        @click="isCreateModalOpen = true"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-primary to-violet-600 text-white text-xs font-bold shadow-md shadow-primary/20 hover:from-primary/90 hover:to-violet-700 transition"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>{{ $t('Create Automation') }}</span>
                    </button>
                </div>
            </div>

            <!-- Pagination -->
            <Pagination
                v-if="props.rows && props.rows.meta"
                :pagination="props.rows.meta"
            />
        </div>

        <!-- Create Automation Modal -->
        <CreateAutomationModal
            :isOpen="isCreateModalOpen"
            @close="isCreateModalOpen = false"
        />

        <!-- Alert Modal for Delete -->
        <AlertModal
            v-model="isOpenAlert"
            @confirm="() => confirmAlert(handleDeleteAction)"
            :label="$t('Delete Automation')"
            :description="$t('Are you sure you want to delete this automation? This action cannot be undone.')"
        />
    </SettingLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router, useForm } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import SettingLayout from './../Layout.vue';
import AutomationCard from '@/Components/Automation/AutomationCard.vue';
import CreateAutomationModal from '@/Components/Automation/CreateAutomationModal.vue';
import Pagination from '@/Components/Pagination.vue';
import AlertModal from '@/Components/AlertModal.vue';
import { useAlertModal } from '@/Composables/useAlertModal';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    aimodule: {
        type: Boolean,
        default: false,
    },
    fbmodule: {
        type: Boolean,
        default: false,
    },
});

const isCreateModalOpen = ref(false);
const viewMode = ref('grid');
const selectedStatus = ref('all');
const searchTerm = ref(props.filters.search || '');

const { isOpenAlert, openAlert, confirmAlert } = useAlertModal();
const deleteTargetUuid = ref(null);

const confirmDelete = (uuid) => {
    deleteTargetUuid.value = uuid;
    openAlert(uuid);
};

const handleDeleteAction = (uuid) => {
    const form = useForm({});
    form.delete('/automation/basic/' + (uuid || deleteTargetUuid.value), {
        preserveScroll: true,
    });
};

const handleSearch = debounce(() => {
    router.visit('/automation/basic', {
        method: 'get',
        data: { search: searchTerm.value },
        preserveState: true,
        preserveScroll: true,
    });
}, 500);

const clearSearch = () => {
    searchTerm.value = '';
    handleSearch();
};

const clearFilters = () => {
    searchTerm.value = '';
    selectedStatus.value = 'all';
    handleSearch();
};

// Filtered rows computed
const allItems = computed(() => {
    return props.rows?.data || [];
});

const filteredRows = computed(() => {
    let list = allItems.value;
    if (selectedStatus.value !== 'all') {
        list = list.filter((item) => {
            const meta = parseMetadata(item.metadata);
            const status = meta.status || 'active';
            return status === selectedStatus.value;
        });
    }
    return list;
});

// KPI summary computed from actual real data
const kpiStats = computed(() => {
    const list = allItems.value;
    let active = 0;
    let draft = 0;
    let inactive = 0;

    list.forEach((item) => {
        const meta = parseMetadata(item.metadata);
        const status = meta.status || 'active';
        if (status === 'draft') draft++;
        else if (status === 'inactive') inactive++;
        else active++;
    });

    return {
        total: props.rows?.total ?? list.length,
        active,
        draft,
        inactive,
    };
});

const parseMetadata = (raw) => {
    try {
        return typeof raw === 'string' ? JSON.parse(raw) : (raw || {});
    } catch (e) {
        return {};
    }
};

const getItemStatusLabel = (item) => {
    const meta = parseMetadata(item.metadata);
    const status = meta.status || 'active';
    if (status === 'draft') return trans('Draft');
    if (status === 'inactive') return trans('Inactive');
    return trans('Active');
};

const getItemStatusBadgeClass = (item) => {
    const meta = parseMetadata(item.metadata);
    const status = meta.status || 'active';
    if (status === 'draft') return 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20';
    if (status === 'inactive') return 'bg-slate-500/10 text-slate-700 dark:text-slate-400 border border-slate-500/20';
    return 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20';
};

const getItemStatusDotClass = (item) => {
    const meta = parseMetadata(item.metadata);
    const status = meta.status || 'active';
    if (status === 'draft') return 'bg-amber-500';
    if (status === 'inactive') return 'bg-slate-400';
    return 'bg-emerald-500 animate-pulse';
};

const getItemResponseType = (item) => {
    const meta = parseMetadata(item.metadata);
    return meta.type || 'text';
};
</script>