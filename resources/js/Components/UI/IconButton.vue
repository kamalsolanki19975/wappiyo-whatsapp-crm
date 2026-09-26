<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    as: {
        type: String,
        default: 'button',
    },
    href: {
        type: String,
        default: null,
    },
    type: {
        type: String,
        default: 'button',
    },
    variant: {
        type: String,
        default: 'ghost',
        validator: (value) => [
            'primary',
            'secondary',
            'outline',
            'ghost',
            'danger',
            'success',
            'ai',
            'gradient'
        ].includes(value),
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl'].includes(value),
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    loading: {
        type: Boolean,
        default: false,
    },
    rounded: {
        type: String,
        default: 'lg', // 'sm', 'md', 'lg', 'xl', 'full'
    },
    title: {
        type: String,
        default: '',
    },
    className: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['click']);

const baseClasses = 'inline-flex items-center justify-center font-medium transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed select-none active:scale-95 shrink-0';

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'xs': return 'w-7 h-7 text-xs';
        case 'sm': return 'w-8 h-8 text-sm';
        case 'lg': return 'w-11 h-11 text-base';
        case 'xl': return 'w-13 h-13 text-lg';
        case 'md':
        default: return 'w-9.5 h-9.5 text-sm';
    }
});

const roundedClasses = computed(() => {
    switch (props.rounded) {
        case 'sm': return 'rounded-sm';
        case 'md': return 'rounded-md';
        case 'lg': return 'rounded-lg';
        case 'xl': return 'rounded-xl';
        case 'full':
        case 'pill': return 'rounded-full';
        default: return 'rounded-lg';
    }
});

const variantClasses = computed(() => {
    switch (props.variant) {
        case 'primary':
            return 'bg-[#6C5CE7] hover:bg-[#5B46D6] text-white shadow-sm shadow-purple-600/30 hover:shadow-md hover:shadow-purple-600/40 focus-visible:ring-[#6C5CE7]';
        case 'secondary':
            return 'bg-slate-100 dark:bg-zinc-800 text-slate-800 dark:text-zinc-200 hover:bg-slate-200 dark:hover:bg-zinc-700 border border-slate-200/80 dark:border-zinc-700 focus-visible:ring-slate-400';
        case 'outline':
            return 'bg-transparent text-slate-700 dark:text-zinc-300 border border-slate-300 dark:border-zinc-700 hover:bg-slate-50 dark:hover:bg-zinc-800/80 focus-visible:ring-brand-500';
        case 'danger':
            return 'bg-rose-50 text-rose-600 hover:bg-rose-100 dark:bg-rose-950/30 dark:text-rose-400 dark:hover:bg-rose-900/40 focus-visible:ring-rose-500';
        case 'ai':
            return 'bg-gradient-to-r from-violet-600 to-cyan-500 text-white shadow-md shadow-violet-500/25 hover:shadow-lg hover:brightness-110 focus-visible:ring-violet-500';
        case 'ghost':
        default:
            return 'bg-transparent text-slate-500 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-zinc-100 focus-visible:ring-slate-400';
    }
});

const handleClick = (event) => {
    if (!props.disabled && !props.loading) {
        emit('click', event);
    }
};
</script>

<template>
    <component
        :is="props.href ? (props.as === 'a' ? 'a' : Link) : 'button'"
        :href="props.href"
        :type="!props.href ? props.type : undefined"
        :disabled="props.disabled || props.loading"
        :title="title"
        :aria-label="title"
        :class="[
            baseClasses,
            sizeClasses,
            roundedClasses,
            variantClasses,
            className
        ]"
        @click="handleClick"
    >
        <svg
            v-if="props.loading"
            class="animate-spin h-4 w-4 text-current"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
        >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>

        <slot v-else />
    </component>
</template>
