<template>
    <div class="rounded-3xl border border-slate-200/80 dark:border-white/10 bg-white dark:bg-slate-900 p-6 shadow-sm flex flex-col justify-between">
        <!-- Header -->
        <div class="border-b border-slate-100 dark:border-white/5 pb-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">
                        {{ $t('Template Distribution') }}
                    </h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary dark:bg-primary/20">
                        {{ templates.length }}
                    </span>
                </div>
                <Link
                    href="/templates"
                    class="text-xs font-semibold text-primary hover:underline flex items-center gap-1"
                >
                    <span>{{ $t('Manage') }}</span>
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </Link>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                {{ $t('Utilization frequency of WhatsApp approved message templates in campaigns') }}
            </p>
        </div>

        <!-- Template List -->
        <div class="mt-4 flex-1">
            <div v-if="templates.length > 0" class="space-y-3">
                <div
                    v-for="tpl in topTemplates"
                    :key="tpl.id"
                    class="p-3 rounded-2xl bg-slate-50/80 dark:bg-white/5 border border-slate-100 dark:border-white/5 hover:border-primary/30 transition flex items-center justify-between gap-3"
                >
                    <!-- Info -->
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center gap-2">
                            <span class="font-bold text-xs text-slate-900 dark:text-white truncate">
                                {{ tpl.name }}
                            </span>
                            <span
                                class="px-1.5 py-0.2 rounded-md text-[9px] font-bold uppercase tracking-wider shrink-0"
                                :class="getCategoryBadgeClass(tpl.category)"
                            >
                                {{ tpl.category }}
                            </span>
                        </div>
                        <div class="flex items-center gap-3 text-[11px] text-slate-400 mt-1">
                            <span>{{ $t('Updated') }}: {{ tpl.updated_at }}</span>
                            <span class="inline-flex items-center gap-1 font-medium" :class="getStatusClass(tpl.status)">
                                <span class="w-1.5 h-1.5 rounded-full" :class="getStatusDotClass(tpl.status)"></span>
                                {{ tpl.status || 'Active' }}
                            </span>
                        </div>
                    </div>

                    <!-- Usage Frequency Count -->
                    <div class="text-right shrink-0">
                        <div class="text-sm font-black text-slate-900 dark:text-white">
                            {{ tpl.campaigns_count }}
                        </div>
                        <div class="text-[10px] text-slate-400">
                            {{ $t('Campaigns') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="py-8 flex flex-col items-center justify-center text-center p-4"
            >
                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-400 flex items-center justify-center mb-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                </div>
                <h4 class="text-xs font-bold text-slate-900 dark:text-white mb-0.5">
                    {{ $t('No templates found') }}
                </h4>
                <p class="text-[11px] text-slate-400 max-w-[200px]">
                    {{ $t('Sync or create templates to view marketing and utility usage metrics.') }}
                </p>
            </div>
        </div>

        <!-- Footer / Category Summary -->
        <div v-if="templates.length > 0" class="mt-4 pt-3 border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-[11px] text-slate-500 dark:text-slate-400">
            <span>{{ $t('Top template utilized in') }} {{ topTemplates[0]?.campaigns_count || 0 }} {{ $t('broadcasts') }}</span>
            <Link href="/templates/create" class="text-primary font-bold hover:underline">
                + {{ $t('New Template') }}
            </Link>
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    templates: {
        type: Array,
        default: () => [],
    },
});

const topTemplates = computed(() => {
    return props.templates.slice(0, 5);
});

function getCategoryBadgeClass(category) {
    switch (category?.toUpperCase()) {
        case 'MARKETING':
            return 'bg-pink-500/10 text-pink-600 dark:text-pink-400';
        case 'UTILITY':
            return 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-400';
        case 'AUTHENTICATION':
            return 'bg-violet-500/10 text-violet-600 dark:text-violet-400';
        default:
            return 'bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300';
    }
}

function getStatusClass(status) {
    switch (status?.toLowerCase()) {
        case 'approved':
            return 'text-emerald-600 dark:text-emerald-400';
        case 'pending':
            return 'text-amber-500';
        case 'rejected':
            return 'text-rose-500';
        default:
            return 'text-slate-500';
    }
}

function getStatusDotClass(status) {
    switch (status?.toLowerCase()) {
        case 'approved':
            return 'bg-emerald-500';
        case 'pending':
            return 'bg-amber-500';
        case 'rejected':
            return 'bg-rose-500';
        default:
            return 'bg-slate-400';
    }
}
</script>
