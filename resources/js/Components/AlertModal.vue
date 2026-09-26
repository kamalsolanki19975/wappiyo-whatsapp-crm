<script setup>
    import { ref } from 'vue';
    import { Dialog, DialogPanel, DialogTitle, TransitionChild, TransitionRoot } from '@headlessui/vue';
    import { ExclamationTriangleIcon } from '@heroicons/vue/24/outline';

    const props = defineProps({
        modelValue: Boolean,
        label: String,
        description: String,
    })

    const isLoading = ref(false);
    const emit = defineEmits(['update:modelValue', 'confirm']);

    function confirm () {
        isLoading.value = true;
        setTimeout(() => {
            emit('confirm');
            isLoading.value = false;
        }, 1000);
    }

    function onClose() {
        emit('update:modelValue', false);
    }
</script>
<template>
    <TransitionRoot as="template" :show="modelValue">
        <Dialog as="div" class="relative z-50" @close="onClose">
            <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0" enter-to="opacity-100" leave="ease-in duration-200" leave-from="opacity-100" leave-to="opacity-0">
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 z-50 w-screen overflow-y-auto">
                <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-6">
                    <TransitionChild as="template" enter="ease-out duration-300" enter-from="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" enter-to="opacity-100 translate-y-0 sm:scale-100" leave="ease-in duration-200" leave-from="opacity-100 translate-y-0 sm:scale-100" leave-to="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                        <DialogPanel class="relative transform overflow-hidden rounded-2xl bg-white dark:bg-[#111113] text-left shadow-floating border border-slate-200/80 dark:border-zinc-800/80 transition-all sm:my-8 sm:w-full sm:max-w-lg">
                            <div class="p-6">
                                <div class="sm:flex sm:items-start gap-4">
                                    <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-2xl bg-rose-50 dark:bg-rose-950/50 text-rose-600 dark:text-rose-400 border border-rose-200/60 dark:border-rose-800/40 sm:mx-0">
                                        <ExclamationTriangleIcon class="h-6 w-6" aria-hidden="true" />
                                    </div>
                                    <div class="mt-3 text-center sm:mt-0 sm:text-left flex-1">
                                        <DialogTitle as="h3" class="text-lg font-bold leading-6 text-slate-900 dark:text-white">{{ props.label }}</DialogTitle>
                                        <div class="mt-2">
                                            <p class="text-sm text-slate-500 dark:text-zinc-400 leading-relaxed">{{ props.description }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="bg-slate-50/70 dark:bg-zinc-900/50 px-6 py-4 border-t border-slate-100 dark:border-zinc-800/80 sm:flex sm:flex-row-reverse gap-3">
                                <button v-if="!isLoading" type="button" class="inline-flex w-full justify-center rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-rose-700 transition-colors sm:w-auto" @click.stop="confirm()">{{ $t('Delete') }}</button>
                                <button v-else type="button" class="inline-flex w-full justify-center rounded-lg bg-slate-400 dark:bg-zinc-700 px-4 py-2 text-sm font-semibold text-white sm:w-auto cursor-not-allowed">
                                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                </button>
                                <button type="button" class="mt-3 sm:mt-0 inline-flex w-full justify-center rounded-lg bg-white dark:bg-zinc-800 border border-slate-300 dark:border-zinc-700 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-700 transition-colors sm:w-auto" @click.self="onClose">{{ $t('Cancel') }}</button>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>

  