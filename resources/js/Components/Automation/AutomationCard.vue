<template>
    <div
        class="group relative bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-white/10 p-5 shadow-sm hover:shadow-md hover:border-primary/40 dark:hover:border-primary/50 transition-all duration-200 flex flex-col justify-between"
    >
        <!-- Top Row: Status & Actions -->
        <div>
            <div class="flex items-center justify-between gap-3 mb-3">
                <!-- Status Badge -->
                <div class="flex items-center gap-2">
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-semibold"
                        :class="statusBadgeClasses"
                    >
                        <span
                            class="w-1.5 h-1.5 rounded-full"
                            :class="statusDotClasses"
                        />
                        {{ statusLabel }}
                    </span>

                    <span class="text-xs px-2 py-0.5 rounded-md bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-400 capitalize">
                        {{ responseType }}
                    </span>
                </div>

                <!-- Dropdown Menu -->
                <div class="relative">
                    <button
                        @click.stop="isMenuOpen = !isMenuOpen"
                        class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-white/5 transition"
                    >
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                        </svg>
                    </button>

                    <!-- Dropdown Items -->
                    <div
                        v-if="isMenuOpen"
                        @click.outside="isMenuOpen = false"
                        class="absolute right-0 mt-1 w-44 bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-white/10 py-1.5 z-20 text-xs"
                    >
                        <Link
                            :href="`/automation/builder/${automation.uuid}`"
                            class="flex items-center gap-2 px-3 py-2 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-white/5 transition"
                        >
                            <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            <span>{{ $t('Open Builder') }}</span>
                        </Link>

                        <Link
                            :href="`/automation/basic/${automation.uuid}/edit`"
                            class="flex items-center gap-2 px-3 py-2 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-white/5 transition"
                        >
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <span>{{ $t('Basic Settings') }}</span>
                        </Link>

                        <button
                            @click="toggleStatus"
                            class="w-full text-left flex items-center gap-2 px-3 py-2 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-white/5 transition"
                        >
                            <svg class="w-4 h-4 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4" />
                            </svg>
                            <span>{{ status === 'active' ? $t('Deactivate') : $t('Activate') }}</span>
                        </button>

                        <button
                            @click="duplicateAutomation"
                            class="w-full text-left flex items-center gap-2 px-3 py-2 text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-white/5 transition"
                        >
                            <svg class="w-4 h-4 text-cyan-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                            <span>{{ $t('Duplicate') }}</span>
                        </button>

                        <div class="border-t border-slate-100 dark:border-white/10 my-1"></div>

                        <button
                            @click="$emit('delete', automation.uuid)"
                            class="w-full text-left flex items-center gap-2 px-3 py-2 text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/20 transition"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                            </svg>
                            <span>{{ $t('Delete') }}</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Title & Description -->
            <div class="mb-3">
                <h3 class="text-base font-bold text-slate-900 dark:text-white capitalize group-hover:text-primary transition line-clamp-1">
                    {{ automation.name }}
                </h3>
                <p v-if="description" class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-2">
                    {{ description }}
                </p>
            </div>

            <!-- Trigger Badge & Match criteria -->
            <div class="flex flex-wrap items-center gap-2 mb-4">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 text-xs font-mono font-medium max-w-[200px] truncate border border-emerald-500/20">
                    <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z" />
                    </svg>
                    <span class="truncate">{{ automation.trigger }}</span>
                </span>

                <span class="px-2 py-0.5 rounded-md bg-slate-100 dark:bg-white/5 text-slate-500 text-[11px]">
                    {{ matchCriteriaLabel }}
                </span>
            </div>

            <!-- Visual Pathway Preview -->
            <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/5 mb-4">
                <div class="text-[11px] font-semibold text-slate-400 dark:text-slate-500 mb-1.5 uppercase tracking-wider flex items-center justify-between">
                    <span>{{ $t('Flow Pathway') }}</span>
                    <span>{{ nodeCount }} {{ $t('nodes') }}</span>
                </div>
                <div class="flex items-center gap-1.5 text-xs text-slate-700 dark:text-slate-300 overflow-x-auto py-0.5">
                    <span class="px-2 py-0.5 rounded-md bg-emerald-500/15 text-emerald-700 dark:text-emerald-300 font-medium shrink-0">
                        {{ $t('Trigger') }}
                    </span>
                    <span class="text-slate-400">→</span>
                    <span v-if="hasCondition" class="px-2 py-0.5 rounded-md bg-amber-500/15 text-amber-700 dark:text-amber-300 font-medium shrink-0">
                        {{ $t('Condition') }}
                    </span>
                    <span v-if="hasCondition" class="text-slate-400">→</span>
                    <span class="px-2 py-0.5 rounded-md bg-violet-500/15 text-violet-700 dark:text-violet-300 font-medium shrink-0">
                        {{ responseType === 'template' ? $t('Template') : $t('Message') }}
                    </span>
                    <span class="text-slate-400">→</span>
                    <span class="px-2 py-0.5 rounded-md bg-slate-500/15 text-slate-700 dark:text-slate-400 font-medium shrink-0">
                        {{ $t('End') }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Footer / CTA -->
        <div class="pt-3 border-t border-slate-100 dark:border-white/5 flex items-center justify-between">
            <span class="text-[11px] text-slate-400">
                {{ $t('Updated') }} {{ automation.updated_at }}
            </span>

            <Link
                :href="`/automation/builder/${automation.uuid}`"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-primary/10 hover:bg-primary text-primary hover:text-white dark:bg-primary/20 dark:hover:bg-primary text-xs font-semibold transition-all duration-200"
            >
                <span>{{ $t('Open Builder') }}</span>
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                </svg>
            </Link>
        </div>
    </div>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    automation: {
        type: Object,
        required: true,
    },
});

defineEmits(['delete']);

const isMenuOpen = ref(false);

const metadata = computed(() => {
    try {
        return typeof props.automation.metadata === 'string'
            ? JSON.parse(props.automation.metadata)
            : (props.automation.metadata || {});
    } catch (e) {
        return {};
    }
});

const status = computed(() => {
    return metadata.value?.status || 'active';
});

const description = computed(() => {
    return metadata.value?.description || '';
});

const responseType = computed(() => {
    return metadata.value?.type || 'text';
});

const workflow = computed(() => {
    return metadata.value?.workflow || null;
});

const nodeCount = computed(() => {
    if (workflow.value?.nodes && Array.isArray(workflow.value.nodes)) {
        return workflow.value.nodes.length;
    }
    // Default fallback pipeline nodes (Trigger, Action, End)
    return 3;
});

const hasCondition = computed(() => {
    if (workflow.value?.nodes) {
        return workflow.value.nodes.some(n => n.type === 'condition');
    }
    return false;
});

const statusLabel = computed(() => {
    if (status.value === 'draft') return trans('Draft');
    if (status.value === 'inactive') return trans('Inactive');
    return trans('Active');
});

const statusBadgeClasses = computed(() => {
    if (status.value === 'draft') {
        return 'bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-500/20';
    }
    if (status.value === 'inactive') {
        return 'bg-slate-500/10 text-slate-700 dark:text-slate-400 border border-slate-500/20';
    }
    return 'bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-500/20';
});

const statusDotClasses = computed(() => {
    if (status.value === 'draft') return 'bg-amber-500';
    if (status.value === 'inactive') return 'bg-slate-400';
    return 'bg-emerald-500 animate-pulse';
});

const matchCriteriaLabel = computed(() => {
    if (props.automation.match_criteria === 'exact match') {
        return trans('Exact Match');
    }
    return trans('Contains');
});

const toggleStatus = () => {
    isMenuOpen.value = false;
    router.post(`/automation/builder/${props.automation.uuid}/status`, {}, {
        preserveScroll: true,
    });
};

const duplicateAutomation = () => {
    isMenuOpen.value = false;
    router.post(`/automation/basic/${props.automation.uuid}/duplicate`, {}, {
        preserveScroll: true,
    });
};
</script>
