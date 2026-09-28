<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    as: {
        type: String,
        default: 'button', // 'button', 'a', 'Link'
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
        default: 'primary',
        validator: (value) => [
            'primary',
            'secondary',
            'outline',
            'ghost',
            'danger',
            'success',
            'ai',
            'gradient',
            'subtle'
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
        default: 'lg', // 'sm', 'md', 'lg', 'xl', 'pill', 'full'
    },
    block: {
        type: Boolean,
        default: false,
    },
    className: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['click']);

const baseClasses = 'inline-flex items-center justify-center font-medium transition-all duration-200 focus:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed select-none relative active:scale-[0.98]';

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'xs':
            return 'text-xs px-2.5 py-1 gap-1.5 h-7';
        case 'sm':
            return 'text-xs px-3 py-1.5 gap-2 h-8';
        case 'lg':
            return 'text-base px-5 py-2.5 gap-2.5 h-11';
        case 'xl':
            return 'text-lg px-6 py-3 gap-3 h-13';
        case 'md':
        default:
            return 'text-sm px-4 py-2 gap-2 h-9.5';
    }
});

const roundedClasses = computed(() => {
    switch (props.rounded) {
        case 'sm': return 'rounded-sm';
        case 'md': return 'rounded-md';
        case 'lg': return 'rounded-lg';
        case 'xl': return 'rounded-xl';
        case 'pill':
        case 'full': return 'rounded-full';
        default: return 'rounded-lg';
    }
});

const variantClasses = computed(() => {
    switch (props.variant) {
        case 'secondary':
            return 'bg-slate-100 dark:bg-zinc-800 text-slate-800 dark:text-zinc-200 hover:bg-slate-200 dark:hover:bg-zinc-700 border border-slate-200/80 dark:border-zinc-700 focus-visible:ring-slate-400';
        case 'outline':
            return 'bg-transparent text-slate-700 dark:text-zinc-300 border border-slate-300 dark:border-zinc-700 hover:bg-slate-50 dark:hover:bg-zinc-800/80 focus-visible:ring-brand-500';
        case 'ghost':
            return 'bg-transparent text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 hover:text-slate-900 dark:hover:text-zinc-100 focus-visible:ring-slate-400';
        case 'danger':
            return 'bg-rose-600 text-white hover:bg-rose-700 shadow-sm shadow-rose-600/30 focus-visible:ring-rose-500';
        case 'success':
            return 'bg-emerald-600 text-white hover:bg-emerald-700 shadow-sm shadow-emerald-600/30 focus-visible:ring-emerald-500';
        case 'ai':
            return 'bg-gradient-to-r from-violet-600 via-indigo-600 to-cyan-500 text-white shadow-md shadow-violet-500/25 hover:shadow-lg hover:shadow-violet-500/35 hover:brightness-110 focus-visible:ring-violet-500 border border-violet-400/30';
        case 'gradient':
            return 'bg-gradient-to-r from-[#22C55E] via-[#16A34A] to-[#022828] text-white shadow-md shadow-emerald-500/20 hover:shadow-lg hover:shadow-emerald-500/30 hover:brightness-105 focus-visible:ring-emerald-500';
        case 'subtle':
            return 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-300 hover:bg-emerald-100 dark:hover:bg-emerald-900/50 border border-emerald-200/60 dark:border-emerald-800/40 focus-visible:ring-emerald-400';
        case 'primary':
        default:
            return 'bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm shadow-emerald-600/25 hover:shadow-md hover:shadow-emerald-600/35 focus-visible:ring-emerald-500';
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
        :class="[
            baseClasses,
            sizeClasses,
            roundedClasses,
            variantClasses,
            block ? 'w-full' : '',
            className
        ]"
        @click="handleClick"
    >
        <!-- Loading Spinner -->
        <svg
            v-if="props.loading"
            class="animate-spin -ml-1 mr-2 h-4 w-4 text-current"
            xmlns="http://www.w3.org/2000/svg"
            fill="none"
            viewBox="0 0 24 24"
        >
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>

        <!-- Prefix Slot -->
        <span v-if="$slots.prefix && !props.loading" class="inline-flex shrink-0">
            <slot name="prefix" />
        </span>

        <!-- Default Slot -->
        <slot />

        <!-- Suffix Slot -->
        <span v-if="$slots.suffix && !props.loading" class="inline-flex shrink-0">
            <slot name="suffix" />
        </span>
    </component>
</template>
