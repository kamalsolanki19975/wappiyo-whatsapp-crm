<script setup>
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
    DialogDescription,
} from '@headlessui/vue';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    title: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', 'full'].includes(value),
    },
    closeBtn: {
        type: Boolean,
        default: true,
    },
});

const emit = defineEmits(['close']);

const sizeClasses = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-xl',
    '2xl': 'max-w-2xl',
    '3xl': 'max-w-3xl',
    '4xl': 'max-w-4xl',
    full: 'max-w-6xl',
};
</script>

<template>
    <TransitionRoot appear :show="isOpen" as="template">
        <Dialog as="div" class="relative z-50" @close="emit('close')">
            <!-- Glassy Backdrop -->
            <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-6">
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95 translate-y-2"
                        enter-to="opacity-100 scale-100 translate-y-0"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100 translate-y-0"
                        leave-to="opacity-0 scale-95 translate-y-2"
                    >
                        <DialogPanel
                            :class="[
                                'w-full transform overflow-hidden rounded-2xl bg-white dark:bg-[#111113] text-left align-middle shadow-floating border border-slate-200/80 dark:border-zinc-800/80 transition-all text-slate-900 dark:text-zinc-100',
                                sizeClasses[size] || 'max-w-md'
                            ]"
                        >
                            <!-- Header -->
                            <div
                                v-if="title || $slots.header || closeBtn"
                                class="flex items-center justify-between px-6 py-4.5 border-b border-slate-100 dark:border-zinc-800/80 bg-slate-50/50 dark:bg-zinc-900/40"
                            >
                                <div class="space-y-0.5">
                                    <DialogTitle
                                        v-if="title"
                                        as="h3"
                                        class="text-lg font-bold leading-6 text-slate-900 dark:text-white"
                                    >
                                        {{ title }}
                                    </DialogTitle>
                                    <DialogDescription
                                        v-if="description"
                                        class="text-xs text-slate-500 dark:text-zinc-400"
                                    >
                                        {{ description }}
                                    </DialogDescription>
                                </div>

                                <button
                                    v-if="closeBtn"
                                    type="button"
                                    class="rounded-lg p-1.5 text-slate-400 dark:text-zinc-500 hover:text-slate-700 dark:hover:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors"
                                    @click="emit('close')"
                                >
                                    <span class="sr-only">Close</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                </button>
                            </div>

                            <!-- Body -->
                            <div class="px-6 py-5">
                                <slot />
                            </div>

                            <!-- Footer -->
                            <div
                                v-if="$slots.footer"
                                class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 dark:border-zinc-800/80 bg-slate-50/50 dark:bg-zinc-900/40"
                            >
                                <slot name="footer" />
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
