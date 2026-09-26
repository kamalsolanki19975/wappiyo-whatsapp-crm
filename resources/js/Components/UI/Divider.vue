<script setup>
const props = defineProps({
    label: {
        type: String,
        default: '',
    },
    orientation: {
        type: String,
        default: 'horizontal', // 'horizontal', 'vertical'
    },
    className: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div
        v-if="orientation === 'vertical'"
        :class="['inline-block w-px self-stretch bg-slate-200 dark:bg-zinc-800 mx-2', className]"
    />
    <div
        v-else
        :class="['relative flex items-center w-full my-4', className]"
    >
        <div class="flex-grow border-t border-slate-200 dark:border-zinc-800" />
        <span
            v-if="label || $slots.default"
            class="shrink-0 px-3 text-xs font-medium uppercase tracking-wider text-slate-400 dark:text-zinc-500 bg-transparent select-none"
        >
            <slot>{{ label }}</slot>
        </span>
        <div class="flex-grow border-t border-slate-200 dark:border-zinc-800" />
    </div>
</template>
