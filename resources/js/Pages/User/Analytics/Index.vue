<template>
    <AppLayout>
        <Head :title="title" />

        <div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto space-y-6">
            <!-- Header with Range Selector & CSV Export -->
            <AnalyticsHeader
                :range="range"
                :range-label="rangeLabel"
                :start-date="startDate"
                :end-date="endDate"
            />

            <!-- Primary KPI Cards with Period Comparison -->
            <AnalyticsKpis :kpis="kpis" />

            <!-- Row 1: Message Volume Area Chart (2/3) + Delivery & Read Rates Card (1/3) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                <div class="lg:col-span-8">
                    <MessageVolumeChart
                        :chart-series="chartSeries"
                        :range-label="rangeLabel"
                    />
                </div>
                <div class="lg:col-span-4">
                    <DeliveryPerformanceCard :delivery-stats="deliveryStats" />
                </div>
            </div>

            <!-- Row 2: Contact Acquisition Chart (1/2) + Automation Health (1/2) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                <div class="lg:col-span-7">
                    <ContactGrowthChart
                        :chart-series="chartSeries"
                        :range-label="rangeLabel"
                    />
                </div>
                <div class="lg:col-span-5">
                    <AutomationPerformanceCard :automation-stats="automationStats" />
                </div>
            </div>

            <!-- Row 3: Campaign Performance Table (Full Width) -->
            <CampaignPerformanceTable :campaigns="campaigns" />

            <!-- Row 4: Template Distribution (1/2) + Team Activity Table (1/2) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                <div class="lg:col-span-6">
                    <TemplateUsageCard :templates="templates" />
                </div>
                <div class="lg:col-span-6">
                    <TeamActivityTable :teams="teams" />
                </div>
            </div>

            <!-- Data Integrity & Security Footnote -->
            <div class="p-4 rounded-2xl bg-slate-50 dark:bg-white/[0.02] border border-slate-200/60 dark:border-white/5 flex flex-col sm:flex-row items-center justify-between gap-3 text-xs text-slate-500 dark:text-slate-400">
                <div class="flex items-center gap-2">
                    <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    <span>
                        {{ $t('Real-time data: All metrics are derived from verified WhatsApp message logs and tenant records.') }}
                    </span>
                </div>
                <div class="text-[11px] text-slate-400">
                    {{ $t('Range') }}: {{ startDate }} &rarr; {{ endDate }}
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
import AppLayout from '../Layout/App.vue';
import AnalyticsHeader from './Components/AnalyticsHeader.vue';
import AnalyticsKpis from './Components/AnalyticsKpis.vue';
import MessageVolumeChart from './Components/MessageVolumeChart.vue';
import DeliveryPerformanceCard from './Components/DeliveryPerformanceCard.vue';
import ContactGrowthChart from './Components/ContactGrowthChart.vue';
import CampaignPerformanceTable from './Components/CampaignPerformanceTable.vue';
import TemplateUsageCard from './Components/TemplateUsageCard.vue';
import AutomationPerformanceCard from './Components/AutomationPerformanceCard.vue';
import TeamActivityTable from './Components/TeamActivityTable.vue';

defineProps({
    title: {
        type: String,
        default: 'Analytics & Reporting',
    },
    range: {
        type: String,
        default: '7d',
    },
    rangeLabel: {
        type: String,
        default: 'Last 7 Days',
    },
    startDate: {
        type: String,
        required: true,
    },
    endDate: {
        type: String,
        required: true,
    },
    kpis: {
        type: Object,
        required: true,
    },
    deliveryStats: {
        type: Object,
        required: true,
    },
    chartSeries: {
        type: Object,
        required: true,
    },
    campaigns: {
        type: Array,
        default: () => [],
    },
    templates: {
        type: Array,
        default: () => [],
    },
    automationStats: {
        type: Object,
        default: () => ({}),
    },
    teams: {
        type: Array,
        default: () => [],
    },
});
</script>
