<script setup>
import { computed } from 'vue';

const props = defineProps({
    status: {
        type: String,
        default: 'PENDING',
    },
    size: {
        type: String,
        default: 'sm', // xs, sm, md
    },
});

const normalizedStatus = computed(() => {
    return (props.status || 'PENDING').toUpperCase();
});

const config = computed(() => {
    switch (normalizedStatus.value) {
        case 'APPROVED':
            return {
                label: 'Approved',
                classes: 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/20 ring-emerald-500/10',
                dotClass: 'bg-emerald-500',
                icon: 'check',
            };
        case 'REJECTED':
            return {
                label: 'Rejected',
                classes: 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/20 ring-rose-500/10',
                dotClass: 'bg-rose-500',
                icon: 'cross',
            };
        case 'PENDING':
        case 'IN_APPEAL':
            return {
                label: normalizedStatus.value === 'IN_APPEAL' ? 'In Appeal' : 'Pending Review',
                classes: 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/20 ring-amber-500/10',
                dotClass: 'bg-amber-500 animate-pulse',
                icon: 'clock',
            };
        case 'PAUSED':
        case 'DISABLED':
            return {
                label: normalizedStatus.value === 'PAUSED' ? 'Paused' : 'Disabled',
                classes: 'bg-orange-500/10 text-orange-600 dark:text-orange-400 border-orange-500/20 ring-orange-500/10',
                dotClass: 'bg-orange-500',
                icon: 'pause',
            };
        case 'DRAFT':
        default:
            return {
                label: props.status ? (props.status.charAt(0).toUpperCase() + props.status.slice(1).toLowerCase()) : 'Draft',
                classes: 'bg-slate-500/10 text-slate-600 dark:text-zinc-400 border-slate-500/20 ring-slate-500/10',
                dotClass: 'bg-slate-400 dark:bg-zinc-500',
                icon: 'draft',
            };
    }
});

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'xs':
            return 'px-2 py-0.5 text-[11px] gap-1';
        case 'md':
            return 'px-3 py-1.5 text-xs gap-1.5';
        case 'sm':
        default:
            return 'px-2.5 py-1 text-xs gap-1.5';
    }
});
</script>

<template>
    <span
        :class="[
            'inline-flex items-center font-medium rounded-full border ring-1 transition-colors',
            config.classes,
            sizeClasses
        ]"
    >
        <span :class="['w-1.5 h-1.5 rounded-full shrink-0', config.dotClass]"></span>
        <span class="truncate">{{ $t(config.label) }}</span>
    </span>
</template>
