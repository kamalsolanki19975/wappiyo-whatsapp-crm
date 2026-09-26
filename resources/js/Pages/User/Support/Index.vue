<template>
    <AppLayout>
        <Head :title="title" />

        <div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto space-y-6">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200/80 dark:border-white/10 pb-6">
                <div>
                    <div class="flex items-center gap-2.5 mb-1">
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-primary/10 text-primary dark:bg-primary/20 shadow-sm">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </span>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                            {{ $t('Customer Support & Tickets') }}
                        </h1>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        {{ $t('Track issues, collaborate with support reps, and monitor SLA resolutions.') }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <Link
                        href="/chats?status=open"
                        class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl border border-slate-200/80 dark:border-white/10 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-white/5 text-xs font-semibold shadow-xs transition"
                    >
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>{{ $t('WhatsApp Live Chats') }}</span>
                    </Link>

                    <Link
                        v-if="allowCreate"
                        href="/support/create"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold shadow-sm shadow-purple-600/30 hover:bg-primary/90 transition"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        <span>{{ $t('New Ticket') }}</span>
                    </Link>
                </div>
            </div>

            <!-- KPI Summary Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <!-- Total -->
                <button
                    type="button"
                    @click="filterStatus('all')"
                    class="p-4 rounded-2xl border transition text-left cursor-pointer"
                    :class="activeStatus === 'all'
                        ? 'bg-primary/5 border-primary dark:bg-primary/10 shadow-xs ring-1 ring-primary'
                        : 'bg-white dark:bg-slate-900 border-slate-200/80 dark:border-white/10 hover:border-primary/40'"
                >
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        {{ $t('Total Tickets') }}
                    </div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">
                        {{ counts?.total || 0 }}
                    </div>
                </button>

                <!-- Open -->
                <button
                    type="button"
                    @click="filterStatus('open')"
                    class="p-4 rounded-2xl border transition text-left cursor-pointer"
                    :class="activeStatus === 'open'
                        ? 'bg-emerald-500/10 border-emerald-500 dark:bg-emerald-500/15 shadow-xs ring-1 ring-emerald-500'
                        : 'bg-white dark:bg-slate-900 border-slate-200/80 dark:border-white/10 hover:border-emerald-500/40'"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                            {{ $t('Open') }}
                        </span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    </div>
                    <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
                        {{ counts?.open || 0 }}
                    </div>
                </button>

                <!-- Pending -->
                <button
                    type="button"
                    @click="filterStatus('pending')"
                    class="p-4 rounded-2xl border transition text-left cursor-pointer"
                    :class="activeStatus === 'pending'
                        ? 'bg-amber-500/10 border-amber-500 dark:bg-amber-500/15 shadow-xs ring-1 ring-amber-500'
                        : 'bg-white dark:bg-slate-900 border-slate-200/80 dark:border-white/10 hover:border-amber-500/40'"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-amber-600 dark:text-amber-400">
                            {{ $t('Pending') }}
                        </span>
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                    </div>
                    <div class="text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">
                        {{ counts?.pending || 0 }}
                    </div>
                </button>

                <!-- Resolved & Closed -->
                <button
                    type="button"
                    @click="filterStatus('resolved')"
                    class="p-4 rounded-2xl border transition text-left cursor-pointer"
                    :class="activeStatus === 'resolved'
                        ? 'bg-purple-500/10 border-purple-500 dark:bg-purple-500/15 shadow-xs ring-1 ring-purple-500'
                        : 'bg-white dark:bg-slate-900 border-slate-200/80 dark:border-white/10 hover:border-purple-500/40'"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400">
                            {{ $t('Resolved') }}
                        </span>
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                    </div>
                    <div class="text-2xl font-black text-purple-600 dark:text-purple-400 mt-1">
                        {{ counts?.resolved || 0 }}
                    </div>
                </button>
            </div>

            <!-- Filters Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <!-- Status Pills -->
                <div class="flex items-center p-1 rounded-xl bg-slate-100 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 text-xs font-semibold overflow-x-auto">
                    <button
                        v-for="st in statusTabs"
                        :key="st.key"
                        type="button"
                        @click="filterStatus(st.key)"
                        class="px-3 py-1.5 rounded-lg transition whitespace-nowrap"
                        :class="activeStatus === st.key
                            ? 'bg-white dark:bg-slate-800 text-primary dark:text-white shadow-xs font-bold'
                            : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    >
                        {{ st.label }}
                    </button>
                </div>

                <!-- Search Input -->
                <div class="flex items-center gap-2.5">
                    <div class="relative w-full sm:w-64">
                        <input
                            v-model="searchQuery"
                            @input="handleSearch"
                            type="text"
                            :placeholder="$t('Search by reference, subject...')"
                            class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-200/80 dark:border-white/10 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                        />
                        <svg class="w-4 h-4 absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <button
                            v-if="searchQuery"
                            type="button"
                            @click="clearSearch"
                            class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                        >
                            &times;
                        </button>
                    </div>
                </div>
            </div>

            <!-- Ticket Table Component -->
            <TicketTable :rows="props.rows" />
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import AppLayout from '../Layout/App.vue';
import TicketTable from '@/Components/Tables/TicketTable.vue';

const props = defineProps({
    title: {
        type: String,
        default: 'Support',
    },
    allowCreate: {
        type: Boolean,
        default: true,
    },
    rows: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    counts: {
        type: Object,
        default: () => ({
            total: 0,
            open: 0,
            pending: 0,
            resolved: 0,
            closed: 0,
        }),
    },
});

const activeStatus = ref(props.filters?.status || 'all');
const searchQuery = ref(props.filters?.search || '');

const statusTabs = [
    { key: 'all', label: 'All Tickets' },
    { key: 'open', label: 'Open' },
    { key: 'pending', label: 'Pending' },
    { key: 'resolved', label: 'Resolved' },
    { key: 'closed', label: 'Closed' },
];

function filterStatus(status) {
    activeStatus.value = status;
    router.visit('/support', {
        method: 'get',
        data: {
            status: status === 'all' ? null : status,
            search: searchQuery.value || null,
        },
        preserveState: true,
        preserveScroll: true,
    });
}

const handleSearch = debounce(() => {
    router.visit('/support', {
        method: 'get',
        data: {
            status: activeStatus.value === 'all' ? null : activeStatus.value,
            search: searchQuery.value || null,
        },
        preserveState: true,
        preserveScroll: true,
    });
}, 400);

function clearSearch() {
    searchQuery.value = '';
    handleSearch();
}
</script>