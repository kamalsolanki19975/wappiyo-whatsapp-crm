<script setup>
import Button from './Button.vue';

const props = defineProps({
    title: {
        type: String,
        required: true,
    },
    description: {
        type: String,
        default: '',
    },
    primaryActionLabel: {
        type: String,
        default: '',
    },
    secondaryActionLabel: {
        type: String,
        default: '',
    },
    className: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['primaryAction', 'secondaryAction']);
</script>

<template>
    <div :class="['text-center py-12 px-6 flex flex-col items-center justify-center max-w-md mx-auto', className]">
        <!-- Icon Container with Glow Ring -->
        <div class="relative mb-5">
            <div class="w-16 h-16 rounded-2xl bg-purple-50 dark:bg-purple-950/40 text-[#6C5CE7] dark:text-purple-400 flex items-center justify-center border border-purple-200/60 dark:border-purple-800/40 shadow-glow/20">
                <slot name="icon">
                    <svg class="w-8 h-8" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </slot>
            </div>
        </div>

        <!-- Title -->
        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">
            {{ title }}
        </h3>

        <!-- Description -->
        <p v-if="description" class="text-sm text-slate-500 dark:text-zinc-400 mb-6 max-w-xs leading-relaxed">
            {{ description }}
        </p>

        <!-- Custom Content Slot -->
        <slot />

        <!-- Actions -->
        <div v-if="primaryActionLabel || secondaryActionLabel || $slots.actions" class="flex flex-wrap items-center justify-center gap-3">
            <slot name="actions">
                <Button
                    v-if="primaryActionLabel"
                    variant="primary"
                    size="md"
                    @click="emit('primaryAction')"
                >
                    {{ primaryActionLabel }}
                </Button>

                <Button
                    v-if="secondaryActionLabel"
                    variant="outline"
                    size="md"
                    @click="emit('secondaryAction')"
                >
                    {{ secondaryActionLabel }}
                </Button>
            </slot>
        </div>
    </div>
</template>
