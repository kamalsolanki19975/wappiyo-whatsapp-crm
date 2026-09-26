<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
        @click.self="$emit('close')"
    >
        <div class="relative w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-white/10 overflow-hidden transform transition-all">
            <!-- Header -->
            <div class="p-6 border-b border-slate-100 dark:border-white/10 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-primary to-violet-500 text-white flex items-center justify-center shadow-md shadow-primary/20">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                            {{ $t('Create Automation') }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $t('Set up a new workflow trigger and response logic') }}
                        </p>
                    </div>
                </div>

                <button
                    @click="$emit('close')"
                    class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-white rounded-lg hover:bg-slate-100 dark:hover:bg-white/5 transition"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Form Body -->
            <form @submit.prevent="submit" class="p-6 space-y-4">
                <!-- Name -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        {{ $t('Automation Name') }} <span class="text-rose-500">*</span>
                    </label>
                    <input
                        v-model="form.name"
                        type="text"
                        required
                        :placeholder="$t('e.g. Welcome New Customer')"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white placeholder:text-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                    />
                    <p v-if="form.errors.name" class="text-rose-500 text-xs mt-1">{{ form.errors.name }}</p>
                </div>

                <!-- Trigger text -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        {{ $t('Trigger Phrase(s)') }} <span class="text-rose-500">*</span>
                    </label>
                    <input
                        v-model="form.trigger"
                        type="text"
                        required
                        :placeholder="$t('e.g. hi, hello, start, info (comma-separated)')"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white placeholder:text-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition font-mono"
                    />
                    <p class="text-[11px] text-slate-400 mt-1">
                        {{ $t('Incoming message text that will trigger this automation') }}
                    </p>
                    <p v-if="form.errors.trigger" class="text-rose-500 text-xs mt-1">{{ form.errors.trigger }}</p>
                </div>

                <!-- Match Criteria -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        {{ $t('Match Criteria') }}
                    </label>
                    <div class="grid grid-cols-2 gap-3">
                        <label
                            class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition text-xs font-medium"
                            :class="form.match_criteria === 'contains'
                                ? 'border-primary bg-primary/5 text-primary dark:border-primary dark:bg-primary/10'
                                : 'border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5'"
                        >
                            <input
                                type="radio"
                                v-model="form.match_criteria"
                                value="contains"
                                class="text-primary focus:ring-primary"
                            />
                            <span>{{ $t('Contains keyword') }}</span>
                        </label>

                        <label
                            class="flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition text-xs font-medium"
                            :class="form.match_criteria === 'exact match'
                                ? 'border-primary bg-primary/5 text-primary dark:border-primary dark:bg-primary/10'
                                : 'border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-white/5'"
                        >
                            <input
                                type="radio"
                                v-model="form.match_criteria"
                                value="exact match"
                                class="text-primary focus:ring-primary"
                            />
                            <span>{{ $t('Exact match only') }}</span>
                        </label>
                    </div>
                </div>

                <!-- Default Response Text (Starting reply) -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        {{ $t('Initial Message Response') }} <span class="text-rose-500">*</span>
                    </label>
                    <textarea
                        v-model="form.response"
                        rows="3"
                        required
                        :placeholder="$t('e.g. Hello {first_name}! How can we assist you today?')"
                        class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white placeholder:text-slate-400 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                    ></textarea>
                    <p class="text-[11px] text-slate-400 mt-1">
                        {{ $t('You can further expand this with conditions, AI, and branching in the Visual Builder.') }}
                    </p>
                    <p v-if="form.errors.response" class="text-rose-500 text-xs mt-1">{{ form.errors.response }}</p>
                </div>

                <!-- Workflow Builder Option -->
                <div class="p-3.5 rounded-2xl bg-gradient-to-r from-primary/10 via-violet-500/10 to-transparent border border-primary/20 flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <span class="w-8 h-8 rounded-lg bg-primary/20 text-primary flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z" />
                            </svg>
                        </span>
                        <div>
                            <div class="text-xs font-bold text-slate-900 dark:text-white">
                                {{ $t('Launch in Visual Flow Builder') }}
                            </div>
                            <div class="text-[11px] text-slate-500">
                                {{ $t('Open canvas directly after creation') }}
                            </div>
                        </div>
                    </div>

                    <input
                        type="checkbox"
                        v-model="openBuilderAfterSave"
                        class="w-4 h-4 text-primary rounded border-slate-300 focus:ring-primary cursor-pointer"
                    />
                </div>

                <!-- Actions -->
                <div class="pt-4 border-t border-slate-100 dark:border-white/10 flex items-center justify-end gap-3">
                    <button
                        type="button"
                        @click="$emit('close')"
                        class="px-4 py-2.5 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/5 rounded-xl transition"
                    >
                        {{ $t('Cancel') }}
                    </button>

                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-primary to-violet-600 hover:from-primary/90 hover:to-violet-700 text-white text-xs font-bold shadow-md shadow-primary/20 transition-all duration-200 disabled:opacity-50"
                    >
                        <svg v-if="form.processing" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ openBuilderAfterSave ? $t('Create & Open Builder') : $t('Save Automation') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(['close']);

const openBuilderAfterSave = ref(true);

const form = useForm({
    name: '',
    trigger: '',
    match_criteria: 'contains',
    response_type: 'text',
    response: '',
});

const submit = () => {
    form.post('/automation/basic', {
        preserveScroll: true,
        onSuccess: (page) => {
            emit('close');
            form.reset();
            // If openBuilderAfterSave, the flash/session has the new item, or user can open from table.
        },
    });
};
</script>
