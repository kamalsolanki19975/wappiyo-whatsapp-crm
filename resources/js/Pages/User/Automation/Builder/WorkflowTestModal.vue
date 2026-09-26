<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 select-none"
        @click.self="$emit('close')"
    >
        <div class="relative w-full max-w-2xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-white/10 overflow-hidden">
            <!-- Header -->
            <div class="p-6 border-b border-slate-100 dark:border-white/10 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-primary to-violet-500 text-white flex items-center justify-center shadow-md shadow-primary/20">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">
                            {{ $t('Test Workflow Simulation') }}
                        </h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $t('Simulate incoming customer message against this workflow') }}
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

            <!-- Modal Body -->
            <div class="p-6 space-y-5 max-h-[75vh] overflow-y-auto">
                <!-- Test Configuration Inputs -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- Contact Selector -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            {{ $t('Test Contact') }}
                        </label>
                        <select
                            v-model="selectedContactId"
                            class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                        >
                            <option value="">{{ $t('Default Sample Contact (Alex Smith)') }}</option>
                            <option
                                v-for="contact in contacts"
                                :key="contact.id"
                                :value="contact.id"
                            >
                                {{ contact.full_name || contact.first_name }} ({{ contact.phone }})
                            </option>
                        </select>
                    </div>

                    <!-- Trigger Reference -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                            {{ $t('Active Workflow Trigger') }}
                        </label>
                        <div class="px-3.5 py-2.5 rounded-xl border border-slate-200/60 dark:border-white/5 bg-slate-100/60 dark:bg-white/5 text-xs font-mono text-emerald-700 dark:text-emerald-400 font-semibold truncate">
                            {{ triggerPhrases || $t('(No trigger set)') }}
                        </div>
                    </div>
                </div>

                <!-- Inbound Message Input -->
                <div>
                    <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-1.5">
                        {{ $t('Simulated Customer Message') }} <span class="text-rose-500">*</span>
                    </label>
                    <div class="flex gap-2">
                        <input
                            v-model="testMessage"
                            type="text"
                            :placeholder="$t('Type an inbound message to test (e.g. Hello, what are your prices?)')"
                            @keydown.enter="runTest"
                            class="flex-1 px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-slate-900 dark:text-white text-xs focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                        />
                        <button
                            type="button"
                            @click="runTest"
                            :disabled="isRunning || !testMessage"
                            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-primary to-violet-600 hover:from-primary/90 text-white text-xs font-bold transition shadow-md shadow-primary/20 disabled:opacity-50 shrink-0"
                        >
                            <span v-if="!isRunning">{{ $t('Simulate') }}</span>
                            <span v-else>{{ $t('Testing...') }}</span>
                        </button>
                    </div>
                </div>

                <!-- Simulation Output -->
                <div v-if="testResult" class="space-y-4 pt-3 border-t border-slate-100 dark:border-white/10">
                    <div class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 flex items-center justify-between">
                        <span>{{ $t('Simulation Results') }}</span>
                        <span
                            class="px-2.5 py-0.5 rounded-full text-[11px] font-bold"
                            :class="testResult.matched ? 'bg-emerald-500/10 text-emerald-600 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-600 border border-rose-500/20'"
                        >
                            {{ testResult.matched ? $t('Trigger Matched ✓') : $t('No Match ✕') }}
                        </span>
                    </div>

                    <!-- Diagnostic Trace Timeline -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/5 space-y-3 text-xs">
                        <!-- Step 1: Input -->
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-slate-200 dark:bg-white/10 text-slate-600 dark:text-slate-300 flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">1</span>
                            <div>
                                <div class="font-semibold text-slate-900 dark:text-white">{{ $t('Inbound WhatsApp Message') }}</div>
                                <div class="text-slate-500 font-mono text-[11px] mt-0.5">"{{ testResult.input_message }}"</div>
                            </div>
                        </div>

                        <!-- Step 2: Match Evaluation -->
                        <div class="flex items-start gap-2.5">
                            <span
                                class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5 text-white"
                                :class="testResult.matched ? 'bg-emerald-500' : 'bg-rose-500'"
                            >
                                2
                            </span>
                            <div>
                                <div class="font-semibold text-slate-900 dark:text-white">
                                    {{ $t('Trigger Criteria Evaluation') }} ({{ testResult.criteria }})
                                </div>
                                <div class="text-slate-500 text-[11px] mt-0.5">
                                    {{ testResult.matched
                                        ? $t('Matched successfully against keywords:') + ` [${testResult.trigger}]`
                                        : $t('Message text did not meet trigger keyword criteria') }}
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Response Generated -->
                        <div v-if="testResult.matched" class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-primary text-white flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">3</span>
                            <div class="flex-1">
                                <div class="font-semibold text-slate-900 dark:text-white">{{ $t('Automated Action Executed') }}</div>
                                <div class="text-slate-500 text-[11px] mt-0.5">
                                    {{ $t('Variables substituted for contact:') }} {{ testResult.contact?.name || 'Alex Smith' }}
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- WhatsApp Chat Bubble Preview -->
                    <div v-if="testResult.matched" class="rounded-2xl p-4 bg-[#E5DDD5] dark:bg-[#111B21] border border-slate-300 dark:border-white/10">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-600 dark:text-slate-400 mb-2">
                            {{ $t('WhatsApp Chat Simulation') }}
                        </div>

                        <div class="space-y-3">
                            <!-- Inbound message bubble (Right side) -->
                            <div class="flex justify-end">
                                <div class="bg-[#E7FFDB] dark:bg-[#005C4B] text-slate-900 dark:text-white px-3.5 py-2 rounded-2xl rounded-tr-none shadow-sm max-w-sm text-xs leading-relaxed">
                                    <p>{{ testResult.input_message }}</p>
                                    <div class="text-[10px] text-slate-400 text-right mt-1">10:42 AM ✓✓</div>
                                </div>
                            </div>

                            <!-- Bot reply bubble (Left side) -->
                            <div class="flex justify-start">
                                <div class="bg-white dark:bg-[#202C33] text-slate-900 dark:text-white px-3.5 py-2 rounded-2xl rounded-tl-none shadow-sm max-w-md text-xs leading-relaxed">
                                    <p class="whitespace-pre-line">{{ testResult.simulated_output }}</p>
                                    <div class="text-[10px] text-slate-400 text-right mt-1">10:42 AM</div>
                                </div>
                            </div>
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
import { ref } from 'vue';
import axios from 'axios';

const props = defineProps({
    isOpen: { type: Boolean, default: false },
    contacts: { type: Array, default: () => [] },
    triggerPhrases: { type: String, default: '' },
    matchCriteria: { type: String, default: 'contains' },
    primaryResponse: { type: String, default: '' },
    responseType: { type: String, default: 'text' },
});

const emit = defineEmits(['close']);

const selectedContactId = ref('');
const testMessage = ref('Hello, need information');
const isRunning = ref(false);
const testResult = ref(null);

const runTest = async () => {
    if (!testMessage.value) return;
    isRunning.value = true;
    try {
        const payload = {
            message: testMessage.value,
            trigger: props.triggerPhrases,
            match_criteria: props.matchCriteria,
            response_type: props.responseType,
            response: props.primaryResponse,
            contact_id: selectedContactId.value || null,
        };

        const res = await axios.post('/automation/test', payload);
        testResult.value = res.data;
    } catch (err) {
        console.error('Test simulation error', err);
    } finally {
        isRunning.value = false;
    }
};
</script>
