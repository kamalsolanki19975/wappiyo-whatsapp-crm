<template>
    <div class="rounded-3xl border border-slate-200/80 dark:border-white/10 bg-white dark:bg-slate-900 p-6 shadow-sm flex flex-col justify-between">
        <!-- Header -->
        <div class="border-b border-slate-100 dark:border-white/5 pb-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">
                        {{ $t('Automation Health') }}
                    </h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary dark:bg-primary/20">
                        {{ automationStats.total || 0 }} {{ $t('Flows') }}
                    </span>
                </div>
                <Link
                    href="/automation/basic"
                    class="text-xs font-semibold text-primary hover:underline flex items-center gap-1"
                >
                    <span>{{ $t('Workflows') }}</span>
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </Link>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                {{ $t('Status overview of auto-replies, event triggers, and chatbot automation flows') }}
            </p>
        </div>

        <!-- Metric Badges Grid -->
        <div class="grid grid-cols-3 gap-3 my-5">
            <!-- Active -->
            <div class="p-3.5 rounded-2xl bg-emerald-500/5 border border-emerald-500/20 text-center">
                <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400">
                    {{ automationStats.active || 0 }}
                </div>
                <div class="text-[11px] font-bold text-slate-700 dark:text-slate-300 mt-0.5">
                    {{ $t('Active') }}
                </div>
            </div>

            <!-- Inactive -->
            <div class="p-3.5 rounded-2xl bg-slate-500/5 border border-slate-500/20 text-center">
                <div class="text-2xl font-black text-slate-600 dark:text-slate-400">
                    {{ automationStats.inactive || 0 }}
                </div>
                <div class="text-[11px] font-bold text-slate-700 dark:text-slate-300 mt-0.5">
                    {{ $t('Paused') }}
                </div>
            </div>

            <!-- Draft -->
            <div class="p-3.5 rounded-2xl bg-amber-500/5 border border-amber-500/20 text-center">
                <div class="text-2xl font-black text-amber-500">
                    {{ automationStats.draft || 0 }}
                </div>
                <div class="text-[11px] font-bold text-slate-700 dark:text-slate-300 mt-0.5">
                    {{ $t('Drafts') }}
                </div>
            </div>
        </div>

        <!-- Status Progress Breakdown -->
        <div class="space-y-3">
            <div>
                <div class="flex justify-between text-xs font-medium text-slate-600 dark:text-slate-300 mb-1">
                    <span class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                        <span>{{ $t('Operational Active Flows') }}</span>
                    </span>
                    <span class="font-bold text-slate-900 dark:text-white">{{ activeRate }}%</span>
                </div>
                <div class="h-2 w-full rounded-full bg-slate-100 dark:bg-white/5 overflow-hidden">
                    <div class="h-full bg-emerald-500 rounded-full transition-all duration-500" :style="{ width: `${activeRate}%` }"></div>
                </div>
            </div>
        </div>

        <!-- Footer / CTA -->
        <div class="mt-5 pt-3 border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-xs">
            <span class="text-[11px] text-slate-400">{{ $t('Automated lead qualification & routing') }}</span>
            <Link href="/automation/basic/create" class="font-bold text-primary hover:underline text-[11px]">
                + {{ $t('New Workflow') }}
            </Link>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    automationStats: {
        type: Object,
        default: () => ({
            total: 0,
            active: 0,
            draft: 0,
            inactive: 0,
        }),
    },
});

const activeRate = computed(() => {
    if (!props.automationStats.total || props.automationStats.total === 0) return 0;
    return Math.round((props.automationStats.active / props.automationStats.total) * 100);
});
</script>
