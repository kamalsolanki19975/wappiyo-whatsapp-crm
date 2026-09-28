<template>
    <AppLayout>
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full transition-colors duration-200">
            <!-- Header Banner -->
            <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-5 sm:p-7 shadow-card transition-all">
                <div class="pointer-events-none absolute -top-24 -right-24 h-72 w-72 rounded-full bg-gradient-to-br from-emerald-500/15 to-transparent blur-3xl dark:from-emerald-500/25" />

                <div class="relative z-10 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-zinc-400 mb-1.5">
                            <span class="inline-flex items-center gap-1.5 font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/><path d="m14 2 4 4-7 7H7v-4l7-7z"/></svg>
                                {{ $t('Lead Capture & CRM') }}
                            </span>
                            <span class="text-slate-300 dark:text-zinc-700">•</span>
                            <Badge variant="primary" size="sm">{{ metrics?.new || 0 }} {{ $t('New Leads') }}</Badge>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            {{ $t('Website Inquiries & Leads') }}
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-zinc-400 mt-1">
                            {{ $t('Review, qualify, assign, and convert website visitors into active CRM contacts.') }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2.5">
                        <a
                            href="/contact"
                            target="_blank"
                            class="inline-flex items-center gap-2 px-3.5 py-2 rounded-xl text-xs font-semibold bg-slate-100 hover:bg-slate-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-slate-700 dark:text-zinc-200 transition-colors"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                            <span>{{ $t('View Contact Form') }}</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Metrics Strip -->
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
                <button
                    @click="applyStatusFilter('all')"
                    :class="[
                        'p-4 rounded-xl border text-left transition-all',
                        currentStatus === 'all'
                            ? 'bg-emerald-50/80 dark:bg-emerald-950/30 border-emerald-500/50 ring-2 ring-emerald-500/20'
                            : 'bg-white dark:bg-[#111113] border-slate-200/80 dark:border-zinc-800 hover:border-slate-300 dark:hover:border-zinc-700'
                    ]"
                >
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400 block">{{ $t('Total Leads') }}</span>
                    <span class="text-2xl font-black text-slate-900 dark:text-white mt-1 block">{{ metrics?.total || 0 }}</span>
                </button>

                <button
                    @click="applyStatusFilter('new')"
                    :class="[
                        'p-4 rounded-xl border text-left transition-all',
                        currentStatus === 'new'
                            ? 'bg-blue-50/80 dark:bg-blue-950/30 border-blue-500/50 ring-2 ring-blue-500/20'
                            : 'bg-white dark:bg-[#111113] border-slate-200/80 dark:border-zinc-800 hover:border-slate-300 dark:hover:border-zinc-700'
                    ]"
                >
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-blue-600 dark:text-blue-400 block">{{ $t('New') }}</span>
                    <span class="text-2xl font-black text-slate-900 dark:text-white mt-1 block">{{ metrics?.new || 0 }}</span>
                </button>

                <button
                    @click="applyStatusFilter('contacted')"
                    :class="[
                        'p-4 rounded-xl border text-left transition-all',
                        currentStatus === 'contacted'
                            ? 'bg-amber-50/80 dark:bg-amber-950/30 border-amber-500/50 ring-2 ring-amber-500/20'
                            : 'bg-white dark:bg-[#111113] border-slate-200/80 dark:border-zinc-800 hover:border-slate-300 dark:hover:border-zinc-700'
                    ]"
                >
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-amber-600 dark:text-amber-400 block">{{ $t('Contacted') }}</span>
                    <span class="text-2xl font-black text-slate-900 dark:text-white mt-1 block">{{ metrics?.contacted || 0 }}</span>
                </button>

                <button
                    @click="applyStatusFilter('qualified')"
                    :class="[
                        'p-4 rounded-xl border text-left transition-all',
                        currentStatus === 'qualified'
                            ? 'bg-purple-50/80 dark:bg-purple-950/30 border-purple-500/50 ring-2 ring-purple-500/20'
                            : 'bg-white dark:bg-[#111113] border-slate-200/80 dark:border-zinc-800 hover:border-slate-300 dark:hover:border-zinc-700'
                    ]"
                >
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-purple-600 dark:text-purple-400 block">{{ $t('Qualified') }}</span>
                    <span class="text-2xl font-black text-slate-900 dark:text-white mt-1 block">{{ metrics?.qualified || 0 }}</span>
                </button>

                <button
                    @click="applyStatusFilter('converted')"
                    :class="[
                        'p-4 rounded-xl border text-left transition-all',
                        currentStatus === 'converted'
                            ? 'bg-emerald-50/80 dark:bg-emerald-950/30 border-emerald-500/50 ring-2 ring-emerald-500/20'
                            : 'bg-white dark:bg-[#111113] border-slate-200/80 dark:border-zinc-800 hover:border-slate-300 dark:hover:border-zinc-700'
                    ]"
                >
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 block">{{ $t('Converted') }}</span>
                    <span class="text-2xl font-black text-slate-900 dark:text-white mt-1 block">{{ metrics?.converted || 0 }}</span>
                </button>

                <button
                    @click="applyStatusFilter('lost')"
                    :class="[
                        'p-4 rounded-xl border text-left transition-all',
                        currentStatus === 'lost'
                            ? 'bg-rose-50/80 dark:bg-rose-950/30 border-rose-500/50 ring-2 ring-rose-500/20'
                            : 'bg-white dark:bg-[#111113] border-slate-200/80 dark:border-zinc-800 hover:border-slate-300 dark:hover:border-zinc-700'
                    ]"
                >
                    <span class="text-[11px] font-semibold uppercase tracking-wider text-rose-600 dark:text-rose-400 block">{{ $t('Lost') }}</span>
                    <span class="text-2xl font-black text-slate-900 dark:text-white mt-1 block">{{ metrics?.lost || 0 }}</span>
                </button>
            </div>

            <!-- Filters & Search Bar -->
            <div class="rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-4 shadow-sm">
                <div class="grid grid-cols-1 md:grid-cols-12 gap-3 items-center">
                    <!-- Search Input -->
                    <div class="md:col-span-6 relative">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                        <input
                            type="text"
                            v-model="searchQuery"
                            @keyup.enter="handleSearch"
                            :placeholder="$t('Search by name, email, phone, company, or subject...')"
                            class="w-full pl-9 pr-4 py-2 rounded-xl bg-slate-50 dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-xs text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        />
                    </div>

                    <!-- Status Dropdown -->
                    <div class="md:col-span-3">
                        <select
                            v-model="selectedStatus"
                            @change="handleFilter"
                            class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                            <option value="all">{{ $t('All Statuses') }}</option>
                            <option value="new">{{ $t('New') }}</option>
                            <option value="contacted">{{ $t('Contacted') }}</option>
                            <option value="qualified">{{ $t('Qualified') }}</option>
                            <option value="converted">{{ $t('Converted') }}</option>
                            <option value="lost">{{ $t('Lost') }}</option>
                        </select>
                    </div>

                    <!-- Form Type Dropdown -->
                    <div class="md:col-span-3 flex items-center gap-2">
                        <select
                            v-model="selectedFormType"
                            @change="handleFilter"
                            class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                            <option value="all">{{ $t('All Form Types') }}</option>
                            <option value="contact">{{ $t('Contact Us') }}</option>
                            <option value="demo">{{ $t('Request Demo') }}</option>
                            <option value="sales">{{ $t('Contact Sales') }}</option>
                            <option value="pricing">{{ $t('Pricing Inquiry') }}</option>
                        </select>

                        <button
                            v-if="hasActiveFilters"
                            @click="clearFilters"
                            title="Reset filters"
                            class="p-2 rounded-xl bg-slate-100 hover:bg-slate-200 dark:bg-zinc-800 dark:hover:bg-zinc-700 text-slate-600 dark:text-zinc-300 transition-colors shrink-0"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Leads Table / List -->
            <div class="rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] shadow-card overflow-hidden">
                <div v-if="leadsList.length === 0" class="p-12 text-center">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 mx-auto flex items-center justify-center mb-3">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/><path d="m14 2 4 4-7 7H7v-4l7-7z"/></svg>
                    </div>
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">{{ $t('No leads found') }}</h3>
                    <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1 max-w-sm mx-auto">
                        {{ $t('Leads submitted through your public website contact and demo forms will appear here in real time.') }}
                    </p>
                </div>

                <div v-else class="overflow-x-auto">
                    <table class="w-full text-left text-xs">
                        <thead class="bg-slate-50 dark:bg-[#18181B] border-b border-slate-200/80 dark:border-zinc-800 text-slate-500 dark:text-zinc-400 font-semibold uppercase tracking-wider text-[10px]">
                            <tr>
                                <th class="px-5 py-3.5">{{ $t('Lead') }}</th>
                                <th class="px-5 py-3.5">{{ $t('Phone / Channel') }}</th>
                                <th class="px-5 py-3.5">{{ $t('Form & Source') }}</th>
                                <th class="px-5 py-3.5">{{ $t('Status') }}</th>
                                <th class="px-5 py-3.5">{{ $t('Assigned To') }}</th>
                                <th class="px-5 py-3.5">{{ $t('Created') }}</th>
                                <th class="px-5 py-3.5 text-right">{{ $t('Action') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-zinc-800/80">
                            <tr
                                v-for="lead in leadsList"
                                :key="lead.uuid"
                                class="hover:bg-slate-50/60 dark:hover:bg-zinc-800/40 transition-colors"
                            >
                                <!-- Lead Info -->
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-emerald-600 to-teal-500 text-white font-bold flex items-center justify-center text-xs shrink-0">
                                            {{ lead.name?.charAt(0)?.toUpperCase() || 'L' }}
                                        </div>
                                        <div class="min-w-0">
                                            <Link :href="`/admin/leads/${lead.uuid}`" class="font-bold text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 truncate block">
                                                {{ lead.name }}
                                            </Link>
                                            <div class="text-[11px] text-slate-500 dark:text-zinc-400 truncate flex items-center gap-1.5 mt-0.5">
                                                <span>{{ lead.email }}</span>
                                                <span v-if="lead.company" class="text-slate-300 dark:text-zinc-700">•</span>
                                                <span v-if="lead.company" class="text-slate-700 dark:text-zinc-300 font-medium truncate">{{ lead.company }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Phone / Channel -->
                                <td class="px-5 py-4">
                                    <span v-if="lead.phone" class="font-mono text-slate-700 dark:text-zinc-300 block">
                                        {{ lead.phone }}
                                    </span>
                                    <span v-else class="text-slate-400 dark:text-zinc-600 italic">
                                        {{ $t('No phone') }}
                                    </span>
                                    <span v-if="lead.utm_source" class="inline-flex items-center gap-1 mt-1 px-1.5 py-0.5 rounded bg-slate-100 dark:bg-zinc-800 text-[10px] text-slate-600 dark:text-zinc-400 font-mono">
                                        UTM: {{ lead.utm_source }}
                                    </span>
                                </td>

                                <!-- Form & Source -->
                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-1 font-semibold text-slate-900 dark:text-white">
                                        {{ formatFormType(lead.form_type) }}
                                    </span>
                                    <span class="block text-[11px] text-slate-400 dark:text-zinc-500 truncate mt-0.5 max-w-[180px]">
                                        {{ lead.source || 'Website' }}
                                    </span>
                                </td>

                                <!-- Status Badge -->
                                <td class="px-5 py-4">
                                    <span
                                        :class="[
                                            'inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider',
                                            statusClasses[lead.status] || 'bg-slate-100 text-slate-700 dark:bg-zinc-800 dark:text-zinc-300'
                                        ]"
                                    >
                                        <span class="w-1.5 h-1.5 rounded-full" :class="statusDotClasses[lead.status]"></span>
                                        {{ lead.status }}
                                    </span>
                                </td>

                                <!-- Assigned To -->
                                <td class="px-5 py-4">
                                    <span v-if="lead.assigned_admin" class="inline-flex items-center gap-1.5 text-xs text-slate-700 dark:text-zinc-300">
                                        <span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-zinc-700 text-slate-700 dark:text-zinc-200 flex items-center justify-center text-[10px] font-bold">
                                            {{ lead.assigned_admin.first_name?.charAt(0) }}
                                        </span>
                                        <span>{{ lead.assigned_admin.first_name }} {{ lead.assigned_admin.last_name }}</span>
                                    </span>
                                    <span v-else class="text-slate-400 dark:text-zinc-600 text-xs italic">
                                        {{ $t('Unassigned') }}
                                    </span>
                                </td>

                                <!-- Created Date -->
                                <td class="px-5 py-4 text-slate-500 dark:text-zinc-400 text-xs whitespace-nowrap">
                                    {{ formatDate(lead.created_at) }}
                                </td>

                                <!-- Action -->
                                <td class="px-5 py-4 text-right">
                                    <Link
                                        :href="`/admin/leads/${lead.uuid}`"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-slate-100 hover:bg-emerald-50 dark:bg-zinc-800 dark:hover:bg-emerald-950/40 text-slate-700 dark:text-zinc-300 hover:text-emerald-600 dark:hover:text-emerald-400 font-semibold text-xs transition-colors"
                                    >
                                        <span>{{ $t('Review') }}</span>
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                                    </Link>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div v-if="leads?.links && leads.links.length > 3" class="px-5 py-3.5 border-t border-slate-100 dark:border-zinc-800/80 flex items-center justify-between">
                    <span class="text-xs text-slate-500 dark:text-zinc-400">
                        {{ $t('Showing') }} {{ leads.from || 0 }} - {{ leads.to || 0 }} {{ $t('of') }} {{ leads.total || 0 }}
                    </span>
                    <div class="flex items-center gap-1">
                        <Component
                            :is="link.url ? Link : 'span'"
                            v-for="(link, i) in leads.links"
                            :key="i"
                            :href="link.url"
                            v-html="link.label"
                            :class="[
                                'px-2.5 py-1 rounded-lg text-xs font-medium transition-colors',
                                link.active
                                    ? 'bg-emerald-600 text-white font-bold'
                                    : link.url
                                        ? 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800'
                                        : 'text-slate-300 dark:text-zinc-700 cursor-not-allowed'
                            ]"
                        />
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '../Layout/App.vue';
import Badge from '@/Components/UI/Badge.vue';
import { formatDateTime } from '@/Utils/dateTime';

const props = defineProps({
    title: String,
    leads: Object,
    filters: Object,
    metrics: Object,
    adminUsers: Array,
});

const searchQuery = ref(props.filters?.search || '');
const selectedStatus = ref(props.filters?.status || 'all');
const selectedFormType = ref(props.filters?.form_type || 'all');

const currentStatus = computed(() => props.filters?.status || 'all');

const leadsList = computed(() => {
    return props.leads?.data || [];
});

const hasActiveFilters = computed(() => {
    return !!(searchQuery.value || (selectedStatus.value && selectedStatus.value !== 'all') || (selectedFormType.value && selectedFormType.value !== 'all'));
});

const statusClasses = {
    new: 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800',
    contacted: 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800',
    qualified: 'bg-purple-50 text-purple-700 border border-purple-200 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800',
    converted: 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800',
    lost: 'bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800',
};

const statusDotClasses = {
    new: 'bg-blue-500',
    contacted: 'bg-amber-500',
    qualified: 'bg-purple-500',
    converted: 'bg-emerald-500',
    lost: 'bg-rose-500',
};

const formatFormType = (type) => {
    if (type === 'demo') return 'Demo Request';
    if (type === 'sales') return 'Sales Inquiry';
    if (type === 'pricing') return 'Pricing Inquiry';
    return 'Contact Us';
};

const formatDate = (dateString) => {
    return formatDateTime(dateString);
};

const handleSearch = () => {
    router.get('/admin/leads', {
        search: searchQuery.value || undefined,
        status: selectedStatus.value !== 'all' ? selectedStatus.value : undefined,
        form_type: selectedFormType.value !== 'all' ? selectedFormType.value : undefined,
    }, { preserveState: true });
};

const handleFilter = () => {
    router.get('/admin/leads', {
        search: searchQuery.value || undefined,
        status: selectedStatus.value !== 'all' ? selectedStatus.value : undefined,
        form_type: selectedFormType.value !== 'all' ? selectedFormType.value : undefined,
    }, { preserveState: true });
};

const applyStatusFilter = (status) => {
    selectedStatus.value = status;
    router.get('/admin/leads', {
        search: searchQuery.value || undefined,
        status: status !== 'all' ? status : undefined,
        form_type: selectedFormType.value !== 'all' ? selectedFormType.value : undefined,
    }, { preserveState: true });
};

const clearFilters = () => {
    searchQuery.value = '';
    selectedStatus.value = 'all';
    selectedFormType.value = 'all';
    router.get('/admin/leads', {}, { preserveState: true });
};
</script>
