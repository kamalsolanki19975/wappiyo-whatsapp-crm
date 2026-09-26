<script setup>
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
    DialogTitle,
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
    position: {
        type: String,
        default: 'right', // 'right', 'left'
    },
    size: {
        type: String,
        default: 'md', // 'sm', 'md', 'lg', 'xl', 'full'
    },
});

const emit = defineEmits(['close']);

const sizeClasses = {
    sm: 'max-w-sm',
    md: 'max-w-md',
    lg: 'max-w-lg',
    xl: 'max-w-2xl',
    full: 'max-w-full',
};
</script>

<template>
    <TransitionRoot appear :show="isOpen" as="template">
        <Dialog as="div" class="relative z-50" @close="emit('close')">
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

            <div class="fixed inset-0 overflow-hidden">
                <div class="absolute inset-0 overflow-hidden">
                    <div
                        :class="[
                            'pointer-events-none fixed inset-y-0 flex max-w-full',
                            position === 'left' ? 'left-0 pr-10' : 'right-0 pl-10'
                        ]"
                    >
                        <TransitionChild
                            as="template"
                            enter="transform transition ease-in-out duration-300"
                            :enter-from="position === 'left' ? '-translate-x-full' : 'translate-x-full'"
                            enter-to="translate-x-0"
                            leave="transform transition ease-in-out duration-200"
                            leave-from="translate-x-0"
                            :leave-to="position === 'left' ? '-translate-x-full' : 'translate-x-full'"
                        >
                            <DialogPanel
                                :class="[
                                    'pointer-events-auto w-screen bg-white dark:bg-[#111113] shadow-2xl border-l border-slate-200/80 dark:border-zinc-800 flex flex-col',
                                    sizeClasses[size] || 'max-w-md'
                                ]"
                            >
                                <!-- Drawer Header -->
                                <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 dark:border-zinc-800/80 bg-slate-50/50 dark:bg-zinc-900/40">
                                    <DialogTitle as="h3" class="text-base font-bold text-slate-900 dark:text-white">
                                        {{ title }}
                                    </DialogTitle>
                                    <button
                                        type="button"
                                        class="rounded-lg p-1.5 text-slate-400 dark:text-zinc-500 hover:text-slate-700 dark:hover:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors"
                                        @click="emit('close')"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                    </button>
                                </div>

                                <!-- Drawer Body -->
                                <div class="relative flex-1 overflow-y-auto p-6 text-slate-900 dark:text-zinc-100">
                                    <slot />
                                </div>

                                <!-- Drawer Footer -->
                                <div v-if="$slots.footer" class="p-6 border-t border-slate-100 dark:border-zinc-800/80 bg-slate-50/50 dark:bg-zinc-900/40">
                                    <slot name="footer" />
                                </div>
                            </DialogPanel>
                        </TransitionChild>
                    </div>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
