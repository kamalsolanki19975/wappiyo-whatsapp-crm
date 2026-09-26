<template>
    <div class="rounded-3xl border border-slate-200/80 dark:border-white/10 bg-white dark:bg-slate-900 p-6 shadow-sm">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 dark:border-white/5 pb-4">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">
                        {{ $t('Contact Growth & Acquisition') }}
                    </h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-cyan-500/10 text-cyan-600 dark:text-cyan-400">
                        {{ rangeLabel }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    {{ $t('New customer contacts added across inbox interactions, imports, and API integrations') }}
                </p>
            </div>

            <!-- Total Badge -->
            <div class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300">
                <span class="w-3 h-3 rounded-full bg-[#06B6D4]"></span>
                <span>{{ formatNumber(totalNewContacts) }} {{ $t('New Contacts') }}</span>
            </div>
        </div>

        <!-- Chart Area -->
        <div class="mt-4 min-h-[260px]">
            <apexchart
                v-if="hasData"
                type="bar"
                height="260"
                :options="chartOptions"
                :series="series"
            />
            <div
                v-else
                class="h-64 flex flex-col items-center justify-center text-center p-6"
            >
                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-white/5 text-slate-400 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1">
                    {{ $t('No new contacts in this window') }}
                </h4>
                <p class="text-xs text-slate-400 max-w-xs">
                    {{ $t('Contacts added through WhatsApp messages or CRM imports will appear here.') }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { useTheme } from '@/Composables/useTheme';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    chartSeries: {
        type: Object,
        default: () => ({ categories: [], contacts: [] }),
    },
    rangeLabel: {
        type: String,
        default: 'Last 7 Days',
    },
});

const { isDark } = useTheme();

const totalNewContacts = computed(() => {
    return (props.chartSeries?.contacts || []).reduce((a, b) => a + Number(b), 0);
});

const hasData = computed(() => {
    return (props.chartSeries?.categories || []).length > 0;
});

const series = computed(() => [
    {
        name: trans('New Contacts'),
        data: props.chartSeries?.contacts || [],
    },
]);

const chartOptions = computed(() => {
    const dark = isDark.value;
    return {
        chart: {
            height: 260,
            type: 'bar',
            fontFamily: 'Outfit, Plus Jakarta Sans, sans-serif',
            background: 'transparent',
            toolbar: { show: false },
            zoom: { enabled: false },
        },
        plotOptions: {
            bar: {
                borderRadius: 6,
                columnWidth: '40%',
            },
        },
        colors: ['#06B6D4'],
        dataLabels: { enabled: false },
        grid: {
            borderColor: dark ? '#27272A' : '#F1F5F9',
            strokeDashArray: 4,
            padding: { top: 0, right: 15, bottom: 0, left: 10 },
        },
        xaxis: {
            type: 'datetime',
            categories: props.chartSeries?.categories || [],
            labels: {
                style: {
                    colors: dark ? '#71717A' : '#94A3B8',
                    fontSize: '11px',
                    fontWeight: 500,
                },
                format: 'dd MMM',
            },
            axisBorder: { show: false },
            axisTicks: { show: false },
        },
        yaxis: {
            labels: {
                style: {
                    colors: dark ? '#71717A' : '#94A3B8',
                    fontSize: '11px',
                    fontWeight: 500,
                },
                formatter: (val) => Math.round(val),
            },
            min: 0,
            forceNiceScale: true,
        },
        legend: { show: false },
        tooltip: {
            theme: dark ? 'dark' : 'light',
            x: { format: 'dd MMM yyyy' },
        },
    };
});

const formatNumber = (num) => {
    return Number(num || 0).toLocaleString();
};
</script>
