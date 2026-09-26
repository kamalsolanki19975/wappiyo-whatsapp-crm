<script setup>
import { computed } from 'vue';

const props = defineProps({
    src: {
        type: String,
        default: null,
    },
    name: {
        type: String,
        default: '',
    },
    alt: {
        type: String,
        default: '',
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['xs', 'sm', 'md', 'lg', 'xl', '2xl'].includes(value),
    },
    status: {
        type: String,
        default: null, // 'online', 'offline', 'away', 'busy'
    },
    shape: {
        type: String,
        default: 'circle', // 'circle', 'rounded'
    },
    className: {
        type: String,
        default: '',
    },
});

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'xs': return 'w-6 h-6 text-[10px]';
        case 'sm': return 'w-8 h-8 text-xs';
        case 'lg': return 'w-12 h-12 text-base';
        case 'xl': return 'w-14 h-14 text-lg';
        case '2xl': return 'w-20 h-20 text-2xl';
        case 'md':
        default: return 'w-10 h-10 text-sm';
    }
});

const statusSizeClasses = computed(() => {
    switch (props.size) {
        case 'xs': return 'w-1.5 h-1.5 border';
        case 'sm': return 'w-2 h-2 border';
        case 'lg': return 'w-3 h-3 border-2';
        case 'xl': return 'w-3.5 h-3.5 border-2';
        case '2xl': return 'w-4 h-4 border-2';
        case 'md':
        default: return 'w-2.5 h-2.5 border-2';
    }
});

const statusColorClasses = computed(() => {
    switch (props.status) {
        case 'online': return 'bg-emerald-500';
        case 'away': return 'bg-amber-500';
        case 'busy': return 'bg-rose-500';
        case 'offline':
        default: return 'bg-slate-400 dark:bg-zinc-500';
    }
});

const initials = computed(() => {
    if (!props.name) return '';
    const parts = props.name.trim().split(/\s+/);
    if (parts.length >= 2) {
        return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
    }
    return parts[0].substring(0, 2).toUpperCase();
});

// Deterministic gradient background for initials
const gradientClass = computed(() => {
    if (!props.name) return 'from-purple-600 to-indigo-600';
    const charCode = props.name.charCodeAt(0) || 0;
    const gradients = [
        'from-purple-600 to-indigo-600',
        'from-violet-600 to-cyan-600',
        'from-pink-600 to-purple-600',
        'from-indigo-600 to-blue-600',
        'from-cyan-600 to-emerald-600',
        'from-amber-600 to-orange-600',
    ];
    return gradients[charCode % gradients.length];
});
</script>

<template>
    <div :class="['relative inline-flex shrink-0 select-none', className]">
        <img
            v-if="src"
            :src="src"
            :alt="alt || name || 'Avatar'"
            :class="[
                'object-cover shadow-inner',
                sizeClasses,
                shape === 'circle' ? 'rounded-full' : 'rounded-xl'
            ]"
        />
        <div
            v-else
            :class="[
                'flex items-center justify-center font-semibold text-white bg-gradient-to-br shadow-inner',
                gradientClass,
                sizeClasses,
                shape === 'circle' ? 'rounded-full' : 'rounded-xl'
            ]"
        >
            <span v-if="initials">{{ initials }}</span>
            <svg
                v-else
                class="w-1/2 h-1/2 text-white/80"
                xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 24 24"
                fill="none"
                stroke="currentColor"
                stroke-width="2"
                stroke-linecap="round"
                stroke-linejoin="round"
            >
                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                <circle cx="12" cy="7" r="4"/>
            </svg>
        </div>

        <!-- Status Indicator Dot -->
        <span
            v-if="status"
            :class="[
                'absolute bottom-0 right-0 rounded-full border-white dark:border-[#111113]',
                statusSizeClasses,
                statusColorClasses
            ]"
        />
    </div>
</template>
