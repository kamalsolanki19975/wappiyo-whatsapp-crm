<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'default',
        validator: (value) => [
            'default',
            'primary',
            'secondary',
            'success',
            'warning',
            'danger',
            'info',
            'ai',
            'outline',
        ].includes(value),
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['xs', 'sm', 'md', 'lg'].includes(value),
    },
    dot: {
        type: Boolean,
        default: false,
    },
    pulse: {
        type: Boolean,
        default: false,
    },
    rounded: {
        type: String,
        default: 'pill', // 'sm', 'md', 'lg', 'pill'
    },
    className: {
        type: String,
        default: '',
    },
});

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'xs': return 'text-[10px] px-1.5 py-0.5 gap-1';
        case 'sm': return 'text-xs px-2 py-0.5 gap-1';
        case 'lg': return 'text-sm px-3 py-1 gap-2';
        case 'md':
        default: return 'text-xs px-2.5 py-1 gap-1.5';
    }
});

const roundedClasses = computed(() => {
    switch (props.rounded) {
        case 'sm': return 'rounded';
        case 'md': return 'rounded-md';
        case 'lg': return 'rounded-lg';
        case 'pill':
        default: return 'rounded-full';
    }
});

const variantClasses = computed(() => {
    switch (props.variant) {
        case 'primary':
            return 'bg-purple-100 dark:bg-purple-950/60 text-[#6C5CE7] dark:text-purple-300 border border-purple-200/80 dark:border-purple-800/50';
        case 'secondary':
            return 'bg-violet-100 dark:bg-violet-950/60 text-[#8B5CF6] dark:text-violet-300 border border-violet-200/80 dark:border-violet-800/50';
        case 'success':
            return 'bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200/80 dark:border-emerald-800/50';
        case 'warning':
            return 'bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/80 dark:border-amber-800/50';
        case 'danger':
            return 'bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200/80 dark:border-rose-800/50';
        case 'info':
            return 'bg-cyan-50 dark:bg-cyan-950/60 text-cyan-700 dark:text-cyan-300 border border-cyan-200/80 dark:border-cyan-800/50';
        case 'ai':
            return 'bg-gradient-to-r from-violet-600/15 via-indigo-600/15 to-cyan-500/15 text-violet-700 dark:text-violet-300 border border-violet-300/60 dark:border-violet-700/50';
        case 'outline':
            return 'bg-transparent text-slate-700 dark:text-zinc-300 border border-slate-300 dark:border-zinc-700';
        case 'default':
        default:
            return 'bg-slate-100 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 border border-slate-200 dark:border-zinc-700';
    }
});

const dotColorClasses = computed(() => {
    switch (props.variant) {
        case 'primary': return 'bg-[#6C5CE7]';
        case 'secondary': return 'bg-[#8B5CF6]';
        case 'success': return 'bg-emerald-500';
        case 'warning': return 'bg-amber-500';
        case 'danger': return 'bg-rose-500';
        case 'info': return 'bg-cyan-500';
        case 'ai': return 'bg-violet-500';
        default: return 'bg-slate-500';
    }
});
</script>

<template>
    <span
        :class="[
            'inline-flex items-center font-medium leading-none select-none shrink-0 transition-colors',
            sizeClasses,
            roundedClasses,
            variantClasses,
            className
        ]"
    >
        <span
            v-if="dot"
            :class="[
                'w-1.5 h-1.5 rounded-full shrink-0',
                dotColorClasses,
                pulse ? 'animate-pulse' : ''
            ]"
        />
        <slot />
    </span>
</template>
