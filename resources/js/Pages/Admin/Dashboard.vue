<script setup>
import { computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import AppLayout from "./Layout/App.vue";
import { useTheme } from "@/Composables/useTheme";
import Button from "@/Components/UI/Button.vue";
import Badge from "@/Components/UI/Badge.vue";
import EmptyState from "@/Components/UI/EmptyState.vue";
import { formatDate } from "@/Utils/dateTime";

const user = computed(() => usePage().props.auth?.user || {});
const { isDark } = useTheme();

const props = defineProps({
    title: { type: String },
    payments: { type: Object, default: () => ({ data: [] }) },
    totalRevenue: { type: String, default: '0' },
    userCount: { type: Number, default: 0 },
    openTickets: { type: Number, default: 0 },
    totalMessages: { type: Number, default: 0 },
    totalLeads: { type: Number, default: 0 },
    newLeads: { type: Number, default: 0 },
    recentLeads: { type: Array, default: () => [] },
    period: { type: [Object, Array], default: () => [] },
    newUsers: { type: [Object, Array], default: () => [] },
    revenue: { type: [Object, Array], default: () => [] },
});

const todayDate = computed(() => {
    return formatDate(new Date(), { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric' });
});

const periodList = computed(() => {
    if (!props.period) return [];
    return Array.isArray(props.period) ? props.period : Object.values(props.period);
});

const newUsersList = computed(() => {
    if (!props.newUsers) return [];
    return Array.isArray(props.newUsers) ? props.newUsers : Object.values(props.newUsers);
});

const revenueList = computed(() => {
    if (!props.revenue) return [];
    return Array.isArray(props.revenue) ? props.revenue : Object.values(props.revenue);
});

const paymentsList = computed(() => {
    if (!props.payments) return [];
    if (Array.isArray(props.payments)) return props.payments;
    if (props.payments.data && Array.isArray(props.payments.data)) return props.payments.data;
    return Object.values(props.payments);
});

const series = computed(() => [
    {
        name: 'New Customers',
        data: newUsersList.value,
    },
    {
        name: 'Revenue',
        data: revenueList.value,
    },
]);

const chartOptions = computed(() => {
    const dark = isDark.value;
    return {
        chart: {
            height: 320,
            type: 'area',
            fontFamily: 'Outfit, Plus Jakarta Sans, sans-serif',
            background: 'transparent',
            toolbar: {
                show: false,
            },
            zoom: {
                enabled: false,
            },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 600,
            },
        },
        colors: ['#8B5CF6', '#22C55E'],
        dataLabels: {
            enabled: false,
        },
        stroke: {
            curve: 'smooth',
            width: 2.5,
        },
        fill: {
            type: 'gradient',
            gradient: {
                shade: dark ? 'dark' : 'light',
                type: 'vertical',
                shadeIntensity: 0.5,
                inverseColors: true,
                opacityFrom: dark ? 0.45 : 0.35,
                opacityTo: 0.02,
                stops: [0, 95, 100],
            },
        },
        grid: {
            borderColor: dark ? '#27272A' : '#F1F5F9',
            strokeDashArray: 4,
            padding: {
                top: 0,
                right: 15,
                bottom: 0,
                left: 10,
            },
        },
        xaxis: {
            type: 'datetime',
            categories: periodList.value,
            labels: {
                style: {
                    colors: dark ? '#71717A' : '#94A3B8',
                    fontSize: '11px',
                    fontWeight: 500,
                },
                format: 'dd MMM',
            },
            axisBorder: {
                show: false,
            },
            axisTicks: {
                show: false,
            },
        },
        yaxis: {
            labels: {
                style: {
                    colors: dark ? '#71717A' : '#94A3B8',
                    fontSize: '11px',
                    fontWeight: 500,
                },
            },
            forceNiceScale: true,
        },
        legend: {
            position: 'top',
            horizontalAlign: 'right',
            offsetY: -5,
            labels: {
                colors: dark ? '#D4D4D8' : '#334155',
            },
            markers: {
                width: 10,
                height: 10,
                radius: 4,
            },
        },
        tooltip: {
            theme: dark ? 'dark' : 'light',
            x: {
                format: 'dd MMM yyyy',
            },
            style: {
                fontSize: '12px',
                fontFamily: 'Outfit, Plus Jakarta Sans, sans-serif',
            },
        },
    };
});
</script>

<template>
    <AppLayout>
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full transition-colors duration-200">
            <!-- Hero Banner -->
            <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-5 sm:p-7 shadow-card transition-all">
                <div class="pointer-events-none absolute -top-24 -right-24 h-72 w-72 rounded-full bg-gradient-to-br from-emerald-500/15 to-transparent blur-3xl dark:from-emerald-500/25" />

                <div class="relative z-10 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-zinc-400 mb-1.5">
                            <span class="inline-flex items-center gap-1.5 font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="4" rx="2" ry="2"/><line x1="16" x2="16" y1="2" y2="6"/><line x1="8" x2="8" y1="2" y2="6"/><line x1="3" x2="21" y1="10" y2="10"/></svg>
                                {{ todayDate }}
                            </span>
                            <span class="text-slate-300 dark:text-zinc-700">•</span>
                            <Badge variant="primary" size="sm">Admin Portal</Badge>
                        </div>
                        <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            {{ $t('Welcome back') }}, {{ user?.first_name || 'Admin' }} 👋
                        </h1>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-zinc-400 mt-1">
                            {{ $t('Platform metrics, subscription billing logs, and support overview.') }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2.5 shrink-0">
                        <Link href="/admin/organizations/create">
                            <Button variant="secondary" size="md">
                                <template #icon>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v8h4"/><path d="M18 9h2a2 2 0 0 1 2 2v11h-4"/></svg>
                                </template>
                                {{ $t('Add Organization') }}
                            </Button>
                        </Link>
                        <Link href="/admin/users/create">
                            <Button variant="primary" size="md" glow>
                                <template #icon>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                                </template>
                                {{ $t('Add User') }}
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4">
                <!-- 1. Total Revenue -->
                <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-5 shadow-card hover:shadow-elevated transition-all duration-200 hover:-translate-y-0.5">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                                {{ $t('Total Revenue') }}
                            </span>
                            <h3 class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                                {{ props.totalRevenue }}
                            </h3>
                        </div>
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-[#22C55E] ring-1 ring-emerald-500/20 group-hover:scale-105 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between border-t border-slate-100 dark:border-zinc-800/80 pt-3 text-xs">
                        <span class="text-slate-400 dark:text-zinc-500">{{ $t('Subscription receipts') }}</span>
                        <Link href="/admin/payment-logs" class="font-semibold text-[#22C55E] hover:underline inline-flex items-center gap-1">
                            <span>{{ $t('View all') }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                        </Link>
                    </div>
                </div>

                <!-- 2. Active Users -->
                <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-5 shadow-card hover:shadow-elevated transition-all duration-200 hover:-translate-y-0.5">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                                {{ $t('Active Users') }}
                            </span>
                            <h3 class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                                {{ props.userCount }}
                            </h3>
                        </div>
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-cyan-50 dark:bg-cyan-950/40 text-[#06B6D4] ring-1 ring-cyan-500/20 group-hover:scale-105 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between border-t border-slate-100 dark:border-zinc-800/80 pt-3 text-xs">
                        <span class="text-slate-400 dark:text-zinc-500">{{ $t('Registered accounts') }}</span>
                        <Link href="/admin/users" class="font-semibold text-[#06B6D4] hover:underline inline-flex items-center gap-1">
                            <span>{{ $t('View users') }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                        </Link>
                    </div>
                </div>

                <!-- 3. Website Leads -->
                <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-5 shadow-card hover:shadow-elevated transition-all duration-200 hover:-translate-y-0.5">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                                {{ $t('Website Leads') }}
                            </span>
                            <div class="flex items-baseline gap-2 mt-2">
                                <h3 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                                    {{ props.totalLeads }}
                                </h3>
                                <span v-if="props.newLeads > 0" class="px-1.5 py-0.5 rounded-md bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-400 text-[10px] font-bold">
                                    {{ props.newLeads }} new
                                </span>
                            </div>
                        </div>
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-teal-50 dark:bg-teal-950/40 text-teal-600 ring-1 ring-teal-500/20 group-hover:scale-105 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/><path d="m14 2 4 4-7 7H7v-4l7-7z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between border-t border-slate-100 dark:border-zinc-800/80 pt-3 text-xs">
                        <span class="text-slate-400 dark:text-zinc-500">{{ $t('Inquiries & Demos') }}</span>
                        <Link href="/admin/leads" class="font-semibold text-teal-600 hover:underline inline-flex items-center gap-1">
                            <span>{{ $t('Manage leads') }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                        </Link>
                    </div>
                </div>

                <!-- 4. Open Tickets -->
                <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-5 shadow-card hover:shadow-elevated transition-all duration-200 hover:-translate-y-0.5">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                                {{ $t('Open Tickets') }}
                            </span>
                            <h3 class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                                {{ props.openTickets }}
                            </h3>
                        </div>
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-orange-50 dark:bg-orange-950/40 text-[#F97316] ring-1 ring-orange-500/20 group-hover:scale-105 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between border-t border-slate-100 dark:border-zinc-800/80 pt-3 text-xs">
                        <span class="text-slate-400 dark:text-zinc-500">{{ $t('Support desk') }}</span>
                        <Link href="/admin/support" class="font-semibold text-[#F97316] hover:underline inline-flex items-center gap-1">
                            <span>{{ $t('View desk') }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                        </Link>
                    </div>
                </div>

                <!-- 5. Total Messages -->
                <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-5 shadow-card hover:shadow-elevated transition-all duration-200 hover:-translate-y-0.5">
                    <div class="flex items-start justify-between">
                        <div>
                            <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                                {{ $t('Total Messages') }}
                            </span>
                            <h3 class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                                {{ props.totalMessages }}
                            </h3>
                        </div>
                        <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 ring-1 ring-emerald-500/20 group-hover:scale-105 transition-transform">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </div>
                    </div>
                    <div class="mt-4 flex items-center justify-between border-t border-slate-100 dark:border-zinc-800/80 pt-3 text-xs">
                        <span class="text-slate-400 dark:text-zinc-500">{{ $t('System wide volume') }}</span>
                        <span class="font-semibold text-emerald-600 dark:text-emerald-400">Platform Traffic</span>
                    </div>
                </div>
            </div>

            <!-- Chart & Recent Transactions Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Analytics Chart -->
                <div class="lg:col-span-2 rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-5 sm:p-6 shadow-card transition-all">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-zinc-800/80 pb-4 mb-4">
                        <div>
                            <h2 class="text-base font-bold text-slate-900 dark:text-white">
                                {{ $t('Platform Growth & Revenue') }}
                            </h2>
                            <p class="text-xs text-slate-500 dark:text-zinc-400">
                                {{ $t('Daily new customer signups and recurring subscription revenue') }}
                            </p>
                        </div>
                    </div>

                    <div class="min-h-[300px]">
                        <apexchart type="area" height="320" :options="chartOptions" :series="series" />
                    </div>
                </div>

                <!-- Recent Transactions -->
                <div class="rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-5 sm:p-6 shadow-card transition-all flex flex-col">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-zinc-800/80 pb-4 mb-4">
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                {{ $t('Recent Transactions') }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-zinc-400">
                                {{ $t('Latest customer payment logs') }}
                            </p>
                        </div>
                        <Link href="/admin/payment-logs" class="text-xs font-semibold text-[#6C5CE7] dark:text-purple-400 hover:underline">
                            {{ $t('View all') }}
                        </Link>
                    </div>

                    <div class="flex-1">
                        <div v-if="paymentsList.length === 0" class="py-8">
                            <EmptyState
                                title="No Transactions"
                                description="No recent payments recorded in this period."
                            />
                        </div>

                        <div v-else class="space-y-2.5">
                            <div
                                v-for="(item, index) in paymentsList"
                                :key="index"
                                class="flex items-center justify-between p-3 rounded-xl border border-slate-100 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-900/40 hover:bg-slate-100/80 dark:hover:bg-zinc-800/60 transition-colors"
                            >
                                <div class="min-w-0">
                                    <h4 class="text-xs font-semibold text-slate-800 dark:text-zinc-200 truncate capitalize">
                                        {{ item.organization?.name || 'Workspace' }}
                                    </h4>
                                    <p class="text-[11px] text-slate-400 dark:text-zinc-500">
                                        {{ item.created_at ? formatDate(item.created_at) : 'Confirmed' }}
                                    </p>
                                </div>
                                <div class="shrink-0">
                                    <Badge variant="success" size="sm">
                                        {{ item.amount }}
                                    </Badge>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>