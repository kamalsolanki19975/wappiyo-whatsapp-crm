<template>
    <div class="rounded-3xl border border-slate-200/80 dark:border-white/10 bg-white dark:bg-slate-900 p-6 shadow-sm">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 dark:border-white/5 pb-4">
            <div>
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">
                        {{ $t('Message Traffic & Volume') }}
                    </h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary dark:bg-primary/20">
                        {{ rangeLabel }}
                    </span>
                </div>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                    {{ $t('Daily volume breakdown of incoming customer inquiries vs outbound agent/system messages') }}
                </p>
            </div>

            <!-- Totals & Legend -->
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300">
                    <span class="w-3 h-3 rounded-full bg-[#6C5CE7]"></span>
                    <span>{{ formatNumber(totalInbound) }} {{ $t('Inbound') }}</span>
                </div>
                <div class="flex items-center gap-2 text-xs font-semibold text-slate-700 dark:text-slate-300">
                    <span class="w-3 h-3 rounded-full bg-[#06B6D4]"></span>
                    <span>{{ formatNumber(totalOutbound) }} {{ $t('Outbound') }}</span>
                </div>
            </div>
        </div>

        <!-- Chart Area -->
        <div class="mt-4 min-h-[320px]">
            <apexchart
                v-if="hasData"
                type="area"
                height="320"
                :options="chartOptions"
                :series="series"
            />
            <div
                v-else
                class="h-80 flex flex-col items-center justify-center text-center p-6"
            >
                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-white/5 text-slate-400 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1">
                    {{ $t('No messaging activity in this period') }}
                </h4>
                <p class="text-xs text-slate-400 max-w-xs">
                    {{ $t('Customer inquiries and outbound messages will appear on this timeline.') }}
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
        default: () => ({ categories: [], inbound: [], outbound: [] }),
    },
    rangeLabel: {
        type: String,
        default: 'Last 7 Days',
    },
});

const { isDark } = useTheme();

const totalInbound = computed(() => {
    return (props.chartSeries?.inbound || []).reduce((a, b) => a + Number(b), 0);
});

const totalOutbound = computed(() => {
    return (props.chartSeries?.outbound || []).reduce((a, b) => a + Number(b), 0);
});

const hasData = computed(() => {
    return (props.chartSeries?.categories || []).length > 0;
});

const series = computed(() => [
    {
        name: trans('Inbound Messages'),
        data: props.chartSeries?.inbound || [],
    },
    {
        name: trans('Outbound Messages'),
        data: props.chartSeries?.outbound || [],
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
            toolbar: { show: false },
            zoom: { enabled: false },
            animations: {
                enabled: true,
                easing: 'easeinout',
                speed: 500,
            },
        },
        colors: ['#6C5CE7', '#06B6D4'],
        dataLabels: { enabled: false },
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
                opacityFrom: dark ? 0.45 : 0.35,
                opacityTo: 0.02,
                stops: [0, 95, 100],
            },
        },
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
            style: {
                fontSize: '12px',
                fontFamily: 'Outfit, Plus Jakarta Sans, sans-serif',
            },
        },
    };
});

const formatNumber = (num) => {
    return Number(num || 0).toLocaleString();
};
</script>
