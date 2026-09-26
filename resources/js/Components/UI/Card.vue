<script setup>
import { computed } from 'vue';

const props = defineProps({
    variant: {
        type: String,
        default: 'default',
        validator: (value) => [
            'default',
            'elevated',
            'interactive',
            'glass',
            'gradient',
            'ai',
            'bordered',
        ].includes(value),
    },
    padding: {
        type: String,
        default: 'md',
        validator: (value) => ['none', 'sm', 'md', 'lg', 'xl'].includes(value),
    },
    rounded: {
        type: String,
        default: 'xl', // 'sm', 'md', 'lg', 'xl', '2xl'
    },
    className: {
        type: String,
        default: '',
    },
});

const paddingClasses = computed(() => {
    switch (props.padding) {
        case 'none': return 'p-0';
        case 'sm': return 'p-3 sm:p-4';
        case 'lg': return 'p-6 sm:p-8';
        case 'xl': return 'p-8 sm:p-10';
        case 'md':
        default: return 'p-4 sm:p-6';
    }
});

const roundedClasses = computed(() => {
    switch (props.rounded) {
        case 'sm': return 'rounded-md';
        case 'md': return 'rounded-lg';
        case 'lg': return 'rounded-xl';
        case '2xl': return 'rounded-2xl';
        case 'xl':
        default: return 'rounded-xl';
    }
});

const variantClasses = computed(() => {
    switch (props.variant) {
        case 'elevated':
            return 'bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800/80 shadow-elevated';
        case 'interactive':
            return 'bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800/80 shadow-card hover:shadow-card-hover hover:border-[#6C5CE7]/40 dark:hover:border-[#8B5CF6]/40 hover:-translate-y-0.5 transition-all duration-200 cursor-pointer';
        case 'glass':
            return 'glass-panel shadow-card';
        case 'gradient':
            return 'bg-gradient-to-br from-purple-500/10 via-white to-violet-500/10 dark:from-purple-950/20 dark:via-[#111113] dark:to-violet-950/20 border border-purple-200/50 dark:border-purple-800/30 shadow-card';
        case 'ai':
            return 'bg-gradient-to-br from-violet-500/10 via-white to-cyan-500/10 dark:from-violet-950/25 dark:via-[#111113] dark:to-cyan-950/20 border border-violet-200/60 dark:border-violet-800/40 shadow-glow/10';
        case 'bordered':
            return 'bg-transparent border border-slate-200 dark:border-zinc-800';
        case 'default':
        default:
            return 'bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800/80 shadow-card';
    }
});
</script>

<template>
    <div
        :class="[
            'relative overflow-hidden text-slate-900 dark:text-zinc-100 transition-colors',
            variantClasses,
            roundedClasses,
            paddingClasses,
            className
        ]"
    >
        <!-- Card Header Slot -->
        <div v-if="$slots.header" class="mb-4 pb-3 border-b border-slate-100 dark:border-zinc-800/80 flex items-center justify-between">
            <slot name="header" />
        </div>

        <!-- Default Content -->
        <slot />

        <!-- Card Footer Slot -->
        <div v-if="$slots.footer" class="mt-4 pt-3 border-t border-slate-100 dark:border-zinc-800/80 flex items-center justify-between">
            <slot name="footer" />
        </div>
    </div>
</template>
