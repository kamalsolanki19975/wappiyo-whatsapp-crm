<script setup>
import { ref, watch, computed } from 'vue';

const props = defineProps({
    modelValue: [String, Number],
})

const textInput = ref('');
const placeholders = ref([]);
const originalNumbers = ref({});
const customValues = ref([]);
const characterLimit = ref('60');
const characterCount = ref('0');
const variableButtonDisabled = ref(false);

const addVariable = () => {
    let limit = parseInt(characterLimit.value);
    let count = parseInt(textInput.value.length);

    if (count < limit && placeholders.value.length === 0) {
        const newPlaceholder = `{{1}}`;
        textInput.value += newPlaceholder;
        placeholders.value.push(newPlaceholder);
        originalNumbers.value[newPlaceholder] = 1;
        customValues.value.push('');
        variableButtonDisabled.value = true;
        countCharacters();
    }
};

const updatePlaceholders = () => {
    const regex = /\{\{\d+\}\}/g;
    const matches = textInput.value.match(regex);

    // If there are matches and more than one variable exists, remove additional variables
    if (matches && matches.length > 1) {
        textInput.value = matches[0]; // Keep only the first variable
    }

    // Update placeholders and original numbers
    if (matches) {
        placeholders.value = [matches[0]]; // Ensure only the first match is kept
        originalNumbers.value = { [matches[0]]: 1 };
        customValues.value = [''];
        variableButtonDisabled.value = true;
    } else {
        placeholders.value = [];
        originalNumbers.value = {};
        customValues.value = [];
        variableButtonDisabled.value = false;
    }

    countCharacters();
    updateValue();
};


const countCharacters = (type) => {
    let limit = parseInt(characterLimit.value);
    let count = parseInt(textInput.value.length);

    if (count <= limit) {
        characterCount.value = count;
    } else {
        textInput.value = textInput.value.slice(0, limit);
        characterCount.value = limit;
    }

    updateValue();
};

const emit = defineEmits(['update:modelValue', 'updateExamples']);
const updateValue = (event) => {
    emit('update:modelValue', textInput.value);
    emit('updateExamples', customValues.value);
};

// Watch for changes in textInput to handle character limit
watch(textInput, (newVal) => {
    const limit = parseInt(characterLimit.value);
    if (newVal.length > limit) {
        textInput.value = newVal.slice(0, limit);
    }
    countCharacters();
    updatePlaceholders();
});

const isCustomValuesIncomplete = computed(() => {
    // Check if any item in customValues is empty
    return customValues.value.some(value => !value);
});

const checkCustomValuesCompleteness = () => {
    // Check if any item in customValues is empty
    return customValues.value.some(value => !value);
};
</script>
<template>
    <div class="normal-case space-y-2">
        <div>
            <div class="mt-1">
                <input
                    type="text" 
                    class="block w-full rounded-xl border border-slate-200 dark:border-zinc-700/80 py-2.5 px-3.5 text-slate-900 dark:text-white bg-white dark:bg-zinc-800/80 shadow-sm outline-none focus:ring-2 focus:ring-[#6C5CE7]/30 focus:border-[#6C5CE7] placeholder:text-slate-400 sm:text-sm sm:leading-6 transition-all"
                    v-model="textInput"
                    @input="updatePlaceholders"
                    :placeholder="$t('Enter header text (e.g. Order Confirmation, Special Offer)...')"
                >
            </div>
            <div class="flex items-center justify-between mt-2">
                <span class="text-xs font-mono font-medium text-slate-500 dark:text-zinc-400">
                    {{ characterCount }} / {{ characterLimit }} {{ $t('chars') }}
                </span>
                <div class="flex items-center space-x-3">
                    <button 
                        type="button" 
                        @click="addVariable" 
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-semibold text-[#6C5CE7] dark:text-purple-300 bg-purple-50 dark:bg-purple-950/40 hover:bg-purple-100 dark:hover:bg-purple-900/60 transition-colors disabled:opacity-50 disabled:cursor-not-allowed" 
                        :disabled="variableButtonDisabled"
                    >
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <span>{{ $t('Add variable') }} ({{ $t('Max: 1') }})</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Samples for Header Content -->
        <div v-if="placeholders.length > 0" class="bg-slate-50 dark:bg-zinc-800/50 p-4 rounded-2xl border border-slate-200/80 dark:border-zinc-700/80 space-y-3">
            <div>
                <h4 class="text-xs font-bold text-slate-800 dark:text-zinc-200 uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#6C5CE7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    {{ $t('Sample for Header Variable') }}
                </h4>
                <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5">
                    {{ $t('Provide an example value for the variable in your header.') }}
                </p>
            </div>

            <div class="space-y-2 pt-1">
                <div v-for="(placeholder, index) in placeholders" :key="index" class="flex items-center gap-2">
                    <span class="w-14 px-2 py-1 rounded-lg bg-purple-100 dark:bg-purple-950/60 text-[#6C5CE7] dark:text-purple-300 font-mono text-xs font-bold text-center border border-purple-200 dark:border-purple-800 shrink-0">
                        {{ placeholder }}
                    </span>
                    <div class="flex-1">
                        <input 
                            type="text" 
                            v-model="customValues[index]" 
                            :placeholder="'e.g. VIP Member for ' + placeholder" 
                            class="block w-full rounded-xl border border-slate-200 dark:border-zinc-700/80 py-1.5 px-3 text-slate-900 dark:text-white bg-white dark:bg-zinc-800 text-xs shadow-sm outline-none focus:ring-2 focus:ring-[#6C5CE7]/30 focus:border-[#6C5CE7] placeholder:text-slate-400 transition-all" 
                            required
                        >
                    </div>
                </div>
            </div>

            <div v-if="isCustomValuesIncomplete" class="p-2.5 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/40 rounded-xl flex items-center gap-2 text-rose-700 dark:text-rose-400 text-xs">
                <svg class="w-4 h-4 shrink-0 text-rose-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd"/></svg>
                <span>{{ $t('Add sample text for the header variable.') }}</span>
            </div>
        </div>
    </div>
</template>