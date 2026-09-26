<template>
    <span
        class="inline-flex items-center gap-1 font-bold uppercase tracking-wider rounded-lg transition-colors"
        :class="[badgeClasses, sizeClasses]"
    >
        <!-- Icon based on priority -->
        <svg v-if="normalizedPriority === 'urgent'" class="w-3 h-3 text-rose-500 animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
        </svg>
        <svg v-else-if="normalizedPriority === 'high'" class="w-3 h-3 text-orange-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7" />
        </svg>
        <svg v-else-if="normalizedPriority === 'medium'" class="w-3 h-3 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M18 12H6" />
        </svg>
        <svg v-else class="w-3 h-3 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7" />
        </svg>

        <span>{{ label }}</span>
    </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    priority: {
        type: String,
        default: 'normal',
    },
    size: {
        type: String,
        default: 'sm',
    },
});

const normalizedPriority = computed(() => {
    return props.priority?.toLowerCase() || 'medium';
});

const label = computed(() => {
    switch (normalizedPriority.value) {
        case 'urgent':
            return 'Urgent';
        case 'high':
            return 'High';
        case 'medium':
            return 'Medium';
        case 'low':
            return 'Low';
        default:
            return props.priority || 'Normal';
    }
});

const badgeClasses = computed(() => {
    switch (normalizedPriority.value) {
        case 'urgent':
            return 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20';
        case 'high':
            return 'bg-orange-500/10 text-orange-600 dark:text-orange-400 border border-orange-500/20';
        case 'medium':
            return 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20';
        case 'low':
        default:
            return 'bg-slate-100 text-slate-600 dark:bg-white/5 dark:text-slate-400 border border-slate-200/60 dark:border-white/5';
    }
});

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'xs':
            return 'px-1.5 py-0.5 text-[9px]';
        case 'md':
            return 'px-2.5 py-1 text-xs';
        case 'sm':
        default:
            return 'px-2 py-0.5 text-[10px]';
    }
});
</script>
