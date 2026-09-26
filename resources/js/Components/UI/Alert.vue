<script setup>
import { ref } from 'vue';

const props = defineProps({
    title: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
    variant: {
        type: String,
        default: 'info',
        validator: (value) => ['info', 'success', 'warning', 'danger', 'ai'].includes(value),
    },
    dismissible: {
        type: Boolean,
        default: false,
    },
    className: {
        type: String,
        default: '',
    },
});

const isDismissed = ref(false);
const emit = defineEmits(['dismiss']);

const handleDismiss = () => {
    isDismissed.value = true;
    emit('dismiss');
};
</script>

<template>
    <div
        v-if="!isDismissed"
        :class="[
            'relative rounded-xl p-4 border transition-all duration-200 flex items-start gap-3.5',
            variant === 'success'
                ? 'bg-emerald-50/80 dark:bg-emerald-950/30 border-emerald-200/80 dark:border-emerald-800/50 text-emerald-900 dark:text-emerald-200'
                : variant === 'warning'
                ? 'bg-amber-50/80 dark:bg-amber-950/30 border-amber-200/80 dark:border-amber-800/50 text-amber-900 dark:text-amber-200'
                : variant === 'danger'
                ? 'bg-rose-50/80 dark:bg-rose-950/30 border-rose-200/80 dark:border-rose-800/50 text-rose-900 dark:text-rose-200'
                : variant === 'ai'
                ? 'bg-gradient-to-r from-violet-500/10 via-purple-500/10 to-cyan-500/10 border-violet-200/80 dark:border-violet-800/50 text-slate-900 dark:text-zinc-100'
                : 'bg-purple-50/80 dark:bg-purple-950/30 border-purple-200/80 dark:border-purple-800/50 text-[#6C5CE7] dark:text-purple-300',
            className
        ]"
    >
        <!-- Icon -->
        <div class="shrink-0 mt-0.5">
            <slot name="icon">
                <svg v-if="variant === 'success'" class="w-5 h-5 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                <svg v-else-if="variant === 'warning'" class="w-5 h-5 text-amber-600 dark:text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m21.73 18-8-14a2 2 0 0 0-3.48 0l-8 14A2 2 0 0 0 4 21h16a2 2 0 0 0 1.73-3Z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                <svg v-else-if="variant === 'danger'" class="w-5 h-5 text-rose-600 dark:text-rose-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                <svg v-else-if="variant === 'ai'" class="w-5 h-5 text-violet-600 dark:text-violet-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"/></svg>
                <svg v-else class="w-5 h-5 text-[#6C5CE7] dark:text-purple-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
            </slot>
        </div>

        <!-- Content -->
        <div class="flex-1 space-y-0.5 text-sm">
            <h5 v-if="title" class="font-semibold leading-tight">
                {{ title }}
            </h5>
            <div v-if="description" class="text-xs opacity-90 leading-relaxed">
                {{ description }}
            </div>
            <slot />
        </div>

        <!-- Dismiss button -->
        <button
            v-if="dismissible"
            type="button"
            class="shrink-0 rounded-lg p-1 text-current opacity-60 hover:opacity-100 transition-opacity"
            @click="handleDismiss"
        >
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
        </button>
    </div>
</template>
