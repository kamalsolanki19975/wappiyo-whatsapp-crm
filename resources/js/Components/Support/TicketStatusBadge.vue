<template>
    <span
        class="inline-flex items-center gap-1.5 font-bold uppercase tracking-wider rounded-full transition-colors"
        :class="[badgeClasses, sizeClasses]"
    >
        <span class="w-1.5 h-1.5 rounded-full" :class="dotClasses"></span>
        <span>{{ label || status }}</span>
    </span>
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        default: 'open',
    },
    size: {
        type: String,
        default: 'sm', // 'xs', 'sm', 'md'
    },
});

const label = computed(() => {
    switch (props.status?.toLowerCase()) {
        case 'open':
            return 'Open';
        case 'pending':
            return 'Pending';
        case 'resolved':
            return 'Resolved';
        case 'closed':
            return 'Closed';
        default:
            return props.status;
    }
});

const badgeClasses = computed(() => {
    switch (props.status?.toLowerCase()) {
        case 'open':
            return 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20';
        case 'pending':
            return 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20';
        case 'resolved':
            return 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20';
        case 'closed':
            return 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20';
        default:
            return 'bg-slate-100 text-slate-700 dark:bg-white/10 dark:text-slate-300';
    }
});

const dotClasses = computed(() => {
    switch (props.status?.toLowerCase()) {
        case 'open':
            return 'bg-emerald-500';
        case 'pending':
            return 'bg-amber-500';
        case 'resolved':
            return 'bg-purple-500';
        case 'closed':
            return 'bg-slate-400';
        default:
            return 'bg-slate-400';
    }
});

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'xs':
            return 'px-2 py-0.5 text-[9px]';
        case 'md':
            return 'px-3 py-1 text-xs';
        case 'sm':
        default:
            return 'px-2.5 py-0.5 text-[10px]';
    }
});
</script>
