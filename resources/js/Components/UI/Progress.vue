<script setup>
import { computed } from 'vue';

const props = defineProps({
    value: {
        type: Number,
        default: 0,
    },
    max: {
        type: Number,
        default: 100,
    },
    variant: {
        type: String,
        default: 'primary', // 'primary', 'gradient', 'ai', 'success', 'danger'
    },
    size: {
        type: String,
        default: 'md', // 'sm', 'md', 'lg'
    },
    showLabel: {
        type: Boolean,
        default: false,
    },
    className: {
        type: String,
        default: '',
    },
});

const percentage = computed(() => {
    return Math.min(100, Math.max(0, Math.round((props.value / props.max) * 100)));
});

const heightClasses = {
    sm: 'h-1.5',
    md: 'h-2.5',
    lg: 'h-4',
};

const barGradientClasses = computed(() => {
    switch (props.variant) {
        case 'gradient':
            return 'bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6]';
        case 'ai':
            return 'bg-gradient-to-r from-violet-600 via-indigo-600 to-cyan-500';
        case 'success':
            return 'bg-emerald-500';
        case 'danger':
            return 'bg-rose-500';
        case 'primary':
        default:
            return 'bg-[#6C5CE7]';
    }
});
</script>

<template>
    <div :class="['w-full', className]">
        <div v-if="showLabel" class="flex justify-between items-center text-xs font-medium text-slate-700 dark:text-zinc-300 mb-1.5">
            <span>Progress</span>
            <span>{{ percentage }}%</span>
        </div>

        <div
            :class="[
                'w-full bg-slate-200/80 dark:bg-zinc-800 rounded-full overflow-hidden shadow-inner',
                heightClasses[size] || 'h-2.5'
            ]"
        >
            <div
                :class="[
                    'h-full rounded-full transition-all duration-500 ease-out',
                    barGradientClasses
                ]"
                :style="{ width: `${percentage}%` }"
            />
        </div>
    </div>
</template>
