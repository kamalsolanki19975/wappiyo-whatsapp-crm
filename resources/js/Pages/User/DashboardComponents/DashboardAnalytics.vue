<script setup>
import { computed } from 'vue';
import { useTheme } from '@/Composables/useTheme';

const props = defineProps({
    period: {
        type: Array,
        default: () => [],
    },
    inbound: {
        type: Array,
        default: () => [],
    },
    outbound: {
        type: Array,
        default: () => [],
    },
});

const { isDark } = useTheme();

const totalInbound = computed(() => {
    return props.inbound.reduce((acc, curr) => acc + (Number(curr) || 0), 0);
});

const totalOutbound = computed(() => {
    return props.outbound.reduce((acc, curr) => acc + (Number(curr) || 0), 0);
});

const totalTraffic = computed(() => {
    return totalInbound.value + totalOutbound.value;
});

const series = computed(() => [
    {
        name: 'Inbound Chats',
        data: props.inbound || [],
    },
    {
        name: 'Outbound Chats',
        data: props.outbound || [],
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
        colors: ['#6C5CE7', '#06B6D4'],
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
                gradientToColors: undefined,
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
            categories: props.period || [],
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
                formatter: (val) => Math.round(val),
            },
            min: 0,
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
    <div class="rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-5 sm:p-6 shadow-card transition-all">
        <!-- Header & Metrics -->
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between border-b border-slate-100 dark:border-zinc-800/80 pb-4">
            <div>
                <div class="flex items-center gap-2">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">
                        {{ $t('Conversation Activity') }}
                    </h2>
                    <span class="inline-flex items-center rounded-full bg-purple-50 dark:bg-purple-950/50 px-2 py-0.5 text-[10px] font-semibold text-[#6C5CE7] dark:text-purple-300 ring-1 ring-inset ring-purple-600/20">
                        7-Day Window
                    </span>
                </div>
                <p class="mt-0.5 text-xs text-slate-500 dark:text-zinc-400">
                    {{ $t('Real-time breakdown of incoming inquiries versus outbound responses') }}
                </p>
            </div>

            <!-- Volume Badges -->
            <div class="flex items-center gap-3">
                <div class="rounded-xl border border-slate-200/80 dark:border-zinc-800 bg-slate-50/70 dark:bg-zinc-900/60 px-3 py-1.5 text-right">
                    <div class="text-[10px] font-semibold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                        {{ $t('Total Volume') }}
                    </div>
                    <div class="text-sm font-extrabold text-slate-900 dark:text-white">
                        {{ totalTraffic }}
                    </div>
                </div>

                <div class="hidden sm:flex items-center gap-2">
                    <div class="flex items-center gap-1.5 text-xs text-slate-600 dark:text-zinc-400">
                        <span class="h-2.5 w-2.5 rounded-full bg-[#6C5CE7]"></span>
                        <span>{{ totalInbound }} In</span>
                    </div>
                    <div class="flex items-center gap-1.5 text-xs text-slate-600 dark:text-zinc-400">
                        <span class="h-2.5 w-2.5 rounded-full bg-[#06B6D4]"></span>
                        <span>{{ totalOutbound }} Out</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Apex Chart -->
        <div class="mt-4 min-h-[300px]">
            <apexchart
                type="area"
                height="320"
                :options="chartOptions"
                :series="series"
            />
        </div>
    </div>
</template>
