<template>
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200/80 dark:border-white/10 pb-6">
        <!-- Title & Subtitle -->
        <div>
            <div class="flex items-center gap-2.5 mb-1">
                <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-primary/10 text-primary dark:bg-primary/20 shadow-sm">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <line x1="18" y1="20" x2="18" y2="10" stroke-width="2" stroke-linecap="round" />
                        <line x1="12" y1="20" x2="12" y2="4" stroke-width="2" stroke-linecap="round" />
                        <line x1="6" y1="20" x2="6" y2="14" stroke-width="2" stroke-linecap="round" />
                    </svg>
                </span>
                <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                    {{ $t('Analytics & Reporting') }}
                </h1>
            </div>
            <p class="text-sm text-slate-500 dark:text-slate-400">
                {{ $t('Understand your WhatsApp conversations, communication performance, and team engagement.') }}
            </p>
        </div>

        <!-- Date Range Controls & Actions -->
        <div class="flex flex-wrap items-center gap-2.5">
            <!-- Range Selector Tabs -->
            <div class="flex items-center p-1 rounded-xl bg-slate-100 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 text-xs font-semibold">
                <button
                    v-for="opt in rangeOptions"
                    :key="opt.value"
                    type="button"
                    @click="selectRange(opt.value)"
                    class="px-3 py-1.5 rounded-lg transition-all duration-150"
                    :class="range === opt.value
                        ? 'bg-white dark:bg-slate-800 text-primary dark:text-white shadow-xs font-bold'
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                >
                    {{ opt.label }}
                </button>

                <!-- Custom Range Trigger -->
                <button
                    type="button"
                    @click="isCustomModalOpen = true"
                    class="px-3 py-1.5 rounded-lg transition-all duration-150"
                    :class="range === 'custom'
                        ? 'bg-white dark:bg-slate-800 text-primary dark:text-white shadow-xs font-bold'
                        : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                >
                    {{ range === 'custom' ? rangeLabel : $t('Custom') }}
                </button>
            </div>

            <!-- Export CSV Button -->
            <a
                :href="exportUrl"
                class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200/80 dark:border-white/10 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-white/5 text-xs font-semibold shadow-xs transition"
                :title="$t('Export CSV Report')"
            >
                <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                </svg>
                <span>{{ $t('Export CSV') }}</span>
            </a>
        </div>
    </div>

    <!-- Custom Date Modal -->
    <div
        v-if="isCustomModalOpen"
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
        @click.self="isCustomModalOpen = false"
    >
        <div class="relative w-full max-w-sm bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-white/10 p-6 select-none">
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">
                {{ $t('Select Custom Date Range') }}
            </h3>
            <p class="text-xs text-slate-500 mb-4">
                {{ $t('Filter analytics between two specific calendar dates.') }}
            </p>

            <div class="space-y-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        {{ $t('Start Date') }}
                    </label>
                    <input
                        v-model="customStartDate"
                        type="date"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                    />
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1">
                        {{ $t('End Date') }}
                    </label>
                    <input
                        v-model="customEndDate"
                        type="date"
                        class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                    />
                </div>
            </div>

            <div class="pt-5 mt-5 border-t border-slate-100 dark:border-white/10 flex items-center justify-end gap-2.5">
                <button
                    type="button"
                    @click="isCustomModalOpen = false"
                    class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-white/5 rounded-xl transition"
                >
                    {{ $t('Cancel') }}
                </button>

                <button
                    type="button"
                    @click="applyCustomRange"
                    class="px-5 py-2 text-xs font-bold text-white bg-gradient-to-r from-primary to-violet-600 hover:from-primary/90 rounded-xl transition shadow-md shadow-primary/20"
                >
                    {{ $t('Apply Range') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    range: { type: String, default: '7d' },
    rangeLabel: { type: String, default: 'Last 7 Days' },
    startDate: { type: String, default: '' },
    endDate: { type: String, default: '' },
});

const isCustomModalOpen = ref(false);
const customStartDate = ref(props.startDate);
const customEndDate = ref(props.endDate);

const rangeOptions = [
    { value: 'today', label: trans('Today') },
    { value: 'yesterday', label: trans('Yesterday') },
    { value: '7d', label: trans('7 Days') },
    { value: '30d', label: trans('30 Days') },
    { value: '90d', label: trans('90 Days') },
];

const selectRange = (r) => {
    router.visit('/analytics', {
        method: 'get',
        data: { range: r },
        preserveState: true,
        preserveScroll: true,
    });
};

const applyCustomRange = () => {
    if (!customStartDate.value || !customEndDate.value) return;
    isCustomModalOpen.value = false;
    router.visit('/analytics', {
        method: 'get',
        data: {
            range: 'custom',
            start_date: customStartDate.value,
            end_date: customEndDate.value,
        },
        preserveState: true,
        preserveScroll: true,
    });
};

const exportUrl = computed(() => {
    let url = `/analytics/export?range=${props.range}`;
    if (props.range === 'custom') {
        url += `&start_date=${props.startDate}&end_date=${props.endDate}`;
    }
    return url;
});
</script>
