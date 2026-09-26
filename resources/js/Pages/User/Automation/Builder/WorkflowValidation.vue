<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
        @click.self="$emit('close')"
    >
        <div class="relative w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-white/10 overflow-hidden select-none">
            <!-- Header -->
            <div class="p-5 border-b border-slate-100 dark:border-white/10 flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <span
                        class="w-8 h-8 rounded-xl flex items-center justify-center"
                        :class="errors.length === 0 ? 'bg-emerald-500/10 text-emerald-600' : 'bg-amber-500/10 text-amber-600'"
                    >
                        <svg v-if="errors.length === 0" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                        </svg>
                        <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </span>
                    <div>
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                            {{ $t('Workflow Validation') }}
                        </h3>
                        <p class="text-[11px] text-slate-400">
                            {{ errors.length === 0 ? $t('Workflow is ready for activation') : `${errors.length} ${$t('issue(s) need attention')}` }}
                        </p>
                    </div>
                </div>

                <button
                    @click="$emit('close')"
                    class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-white/5 transition"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Issues List -->
            <div class="p-5 space-y-2.5 max-h-96 overflow-y-auto">
                <div v-if="errors.length === 0" class="p-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-emerald-500/10 text-emerald-500 mx-auto flex items-center justify-center mb-2">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white mb-1">
                        {{ $t('All checks passed!') }}
                    </h4>
                    <p class="text-[11px] text-slate-400">
                        {{ $t('Every node is properly configured and connected in the sequence.') }}
                    </p>
                </div>

                <div
                    v-for="(err, idx) in errors"
                    :key="idx"
                    @click="$emit('selectNode', err.nodeId)"
                    class="p-3 rounded-2xl border border-amber-500/20 bg-amber-500/5 hover:bg-amber-500/10 cursor-pointer transition flex items-start gap-2.5"
                >
                    <span class="w-5 h-5 rounded-md bg-amber-500/20 text-amber-600 flex items-center justify-center shrink-0 mt-0.5">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </span>
                    <div class="flex-1">
                        <div class="text-xs font-semibold text-slate-800 dark:text-slate-200">
                            {{ err.message }}
                        </div>
                        <div v-if="err.nodeTitle" class="text-[10px] text-amber-700 dark:text-amber-400 mt-0.5 font-medium">
                            {{ $t('Click to inspect') }}: {{ err.nodeTitle }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="p-4 border-t border-slate-100 dark:border-white/10 flex justify-end">
                <button
                    @click="$emit('close')"
                    class="px-4 py-2 text-xs font-semibold bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-white/20 rounded-xl transition"
                >
                    {{ $t('Close') }}
                </button>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    isOpen: { type: Boolean, default: false },
    errors: { type: Array, default: () => [] },
});

defineEmits(['close', 'selectNode']);
</script>
