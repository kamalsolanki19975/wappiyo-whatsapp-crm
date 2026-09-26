<script setup>
const props = defineProps({
    type: {
        type: String,
        default: 'text', // 'text', 'avatar', 'button', 'card', 'stat', 'table-row', 'custom'
    },
    lines: {
        type: Number,
        default: 3,
    },
    rounded: {
        type: String,
        default: 'md', // 'sm', 'md', 'lg', 'xl', 'full'
    },
    className: {
        type: String,
        default: '',
    },
});

const roundedClasses = {
    sm: 'rounded-sm',
    md: 'rounded-md',
    lg: 'rounded-lg',
    xl: 'rounded-xl',
    full: 'rounded-full',
};
</script>

<template>
    <!-- Text Skeleton with multiple lines -->
    <div v-if="type === 'text'" :class="['space-y-2.5 w-full', className]">
        <div
            v-for="n in lines"
            :key="n"
            :class="[
                'h-3.5 bg-slate-200/80 dark:bg-zinc-800 animate-pulse',
                roundedClasses[rounded] || 'rounded-md',
                n === lines ? 'w-3/5' : 'w-full'
            ]"
        />
    </div>

    <!-- Avatar Skeleton -->
    <div
        v-else-if="type === 'avatar'"
        :class="[
            'w-10 h-10 rounded-full bg-slate-200/80 dark:bg-zinc-800 animate-pulse shrink-0',
            className
        ]"
    />

    <!-- Button Skeleton -->
    <div
        v-else-if="type === 'button'"
        :class="[
            'h-9 w-24 rounded-lg bg-slate-200/80 dark:bg-zinc-800 animate-pulse',
            className
        ]"
    />

    <!-- Stat Card Skeleton -->
    <div
        v-else-if="type === 'stat'"
        :class="[
            'p-5 rounded-xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] space-y-4 animate-pulse',
            className
        ]"
    >
        <div class="flex justify-between items-center">
            <div class="h-3 w-20 bg-slate-200 dark:bg-zinc-800 rounded"></div>
            <div class="h-9 w-9 rounded-xl bg-slate-200 dark:bg-zinc-800"></div>
        </div>
        <div class="h-7 w-28 bg-slate-200 dark:bg-zinc-800 rounded"></div>
        <div class="h-3 w-36 bg-slate-200 dark:bg-zinc-800 rounded"></div>
    </div>

    <!-- Table Row Skeleton -->
    <div
        v-else-if="type === 'table-row'"
        :class="[
            'flex items-center gap-4 py-3.5 px-4 border-b border-slate-100 dark:border-zinc-800/80 animate-pulse',
            className
        ]"
    >
        <div class="w-8 h-8 rounded-lg bg-slate-200/80 dark:bg-zinc-800 shrink-0"></div>
        <div class="flex-1 space-y-1.5">
            <div class="h-3.5 w-1/3 bg-slate-200/80 dark:bg-zinc-800 rounded"></div>
            <div class="h-2.5 w-1/4 bg-slate-200/60 dark:bg-zinc-800/60 rounded"></div>
        </div>
        <div class="h-4 w-20 bg-slate-200/80 dark:bg-zinc-800 rounded"></div>
        <div class="h-4 w-12 bg-slate-200/80 dark:bg-zinc-800 rounded"></div>
    </div>

    <!-- Custom / Default Rectangle -->
    <div
        v-else
        :class="[
            'bg-slate-200/80 dark:bg-zinc-800 animate-pulse',
            roundedClasses[rounded] || 'rounded-md',
            className
        ]"
    />
</template>
