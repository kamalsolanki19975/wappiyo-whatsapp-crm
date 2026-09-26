<script setup>
import { ref, computed } from 'vue';

const props = defineProps({
    modelValue: [String, Number],
})

const textInput = ref('');
const textAreaRef = ref(null);
const placeholders = ref([]);
const customValues = ref([]);
const characterLimit = ref('1098');
const characterCount = ref('0');
const maxNu = ref(0);

const addVariable = () => {
    let limit = parseInt(characterLimit.value);
    let count = parseInt(textInput.value.length);

    if(count < limit){
        let nextIndex = placeholders.value.length + 1;
        const newPlaceholder = `{{${nextIndex}}}`;
        if (textInput.value.indexOf(newPlaceholder) === -1) {
            textInput.value += newPlaceholder;
            placeholders.value.push(newPlaceholder);
            customValues.value.push('');
        }

        countCharacters();
        updateValue();
    }
};

const updatePlaceholders = () => {
    const regex = /\{\{\d+\}\}/g;
    const matches = textInput.value.match(regex);
    if (matches) {
        // Create a set of unique numbers in the placeholders
        let allNumbersMatch = true;
        const maxNumber = matches.reduce((max, placeholder) => {
            const number = parseInt(placeholder.match(/\d+/)[0]);
            return number > max ? number : max;
        }, 0);
        
        for (let i = 1; i <= matches.length; i++) {
            const expectedPlaceholder = `{{${i}}}`;
            if (!placeholders.value.includes(expectedPlaceholder)) {
                allNumbersMatch = false;
                break;
            }
        }

        maxNu.value = matches.length;
        
        const uniquePlaceholders = [...new Set(matches)];
            placeholders.value = uniquePlaceholders;
            
            placeholders.value.forEach((placeholder, index) => {
                const oldNumber = parseInt(placeholder.match(/\d+/)[0]);
                
                const newNumber = index + 1;
                    const oldPlaceholder = `{{${oldNumber}}}`;
                    const newPlaceholder = `{{${newNumber}}}`;
                    textInput.value = textInput.value.replace(oldPlaceholder, newPlaceholder);

                    placeholders.value[index] = newPlaceholder; 
            });
    } else {
        placeholders.value = [];
        customValues.value = [];
    }

    updateCustomValues();
    countCharacters();
    updateValue();
};

const updateCustomValues = () => {
    const placeholdersLength = placeholders.value.length;
    const customValuesLength = customValues.value.length;

    if (placeholdersLength !== customValuesLength) {
        const difference = placeholdersLength - customValuesLength;
        if (difference > 0) {
            for (let i = 0; i < difference; i++) {
                customValues.value.push('');
            }
        } else if (difference < 0) {
            customValues.value.splice(placeholdersLength);
        }
    }
}

const countCharacters = (type) => {
    let limit = parseInt(characterLimit.value);
    let count = parseInt(textInput.value.length);

    if (count <= limit) {
        characterCount.value = count;
    } else {
        textInput.value = textInput.value.slice(0, limit);
        characterCount.value = limit;
    }
};

const emit = defineEmits(['update:modelValue', 'updateExamples']);
const updateValue = (event) => {
    emit('update:modelValue', textInput.value);
    emit('updateExamples', customValues.value);
};

const isCustomValuesIncomplete = computed(() => {
    // Check if any item in customValues is empty
    return customValues.value.some(value => !value);
});

const checkCustomValuesCompleteness = () => {
    // Check if any item in customValues is empty
    return customValues.value.some(value => !value);
};

const format = (type) => {
    const textarea = textAreaRef.value;
    const start = textarea.selectionStart;
    const end = textarea.selectionEnd;
    const selectedText = textInput.value.slice(start, end);
    let newText = '';

    if(type == 'bold'){
        newText = textInput.value.slice(0, start) + '*' + selectedText + '*' + textInput.value.slice(end);
    } else if(type == 'italic'){
        newText = textInput.value.slice(0, start) + '_' + selectedText + '_' + textInput.value.slice(end);
    } else if(type == 'strike-through'){
        newText = textInput.value.slice(0, start) + '~' + selectedText + '~' + textInput.value.slice(end);
    } else if(type == 'monospace'){
        newText = textInput.value.slice(0, start) + '```' + selectedText + '```' + textInput.value.slice(end);
    }

    textInput.value = newText;
    countCharacters();
    updateValue();

    // Set selection to highlight the content between the asterisks
    setTimeout(() => {
        if(type == 'monospace'){
            textarea.setSelectionRange(start + 3, end + 3);
        } else {
            textarea.setSelectionRange(start + 1, end + 1);
        }
        textarea.focus();
    }, 0);
};
</script>
<template>
    <div class="normal-case space-y-2">
        <div>
            <div class="mt-1">
                <textarea 
                    ref="textAreaRef"
                    class="block w-full rounded-xl border border-slate-200 dark:border-zinc-700/80 py-2.5 px-3.5 text-slate-900 dark:text-white bg-white dark:bg-zinc-800/80 shadow-sm outline-none focus:ring-2 focus:ring-[#6C5CE7]/30 focus:border-[#6C5CE7] placeholder:text-slate-400 sm:text-sm sm:leading-6 transition-all"
                    v-model="textInput"
                    @input="updatePlaceholders"
                    :rows="'5'"
                    :placeholder="$t('Type your message text here. Use {{1}}, {{2}} for dynamic variables...')"
                ></textarea>
            </div>
            
            <!-- Toolbar below textarea -->
            <div class="flex flex-wrap items-center justify-between gap-2 mt-2">
                <span 
                    class="text-xs font-mono font-medium"
                    :class="parseInt(characterCount) > (parseInt(characterLimit) - 100) ? 'text-amber-500 font-bold' : 'text-slate-500 dark:text-zinc-400'"
                >
                    {{ characterCount }} / {{ characterLimit }} {{ $t('chars') }}
                </span>

                <div class="flex items-center gap-1 bg-slate-100 dark:bg-zinc-800/80 p-1 rounded-xl border border-slate-200/60 dark:border-zinc-700/60">
                    <button 
                        type="button"
                        @click="format('bold')" 
                        :title="$t('Bold (*text*)')" 
                        class="p-1 rounded-lg text-slate-600 dark:text-zinc-300 hover:bg-white dark:hover:bg-zinc-700 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3h8c1.06 0 2.078.474 2.828 1.318C16.578 5.162 17 6.307 17 7.5c0 1.193-.421 2.338-1.172 3.182C15.078 11.526 14.061 12 13 12H5zm0 9h10.039a4.44 4.44 0 0 1 3.154 1.318A4.52 4.52 0 0 1 19.5 16.5a4.52 4.52 0 0 1-1.307 3.182A4.442 4.442 0 0 1 15.038 21H5z"/></svg>
                    </button>
                    <button 
                        type="button"
                        @click="format('italic')" 
                        :title="$t('Italic (_text_)')" 
                        class="p-1 rounded-lg text-slate-600 dark:text-zinc-300 hover:bg-white dark:hover:bg-zinc-700 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path fill="currentColor" d="M10 4.75a.75.75 0 0 1 .75-.75h8.5a.75.75 0 0 1 0 1.5h-3.514l-5.828 13h3.342a.75.75 0 0 1 0 1.5h-8.5a.75.75 0 0 1 0-1.5h3.514l5.828-13H20.75a.75.75 0 0 1-.75-.75Z"/></svg>
                    </button>
                    <button 
                        type="button"
                        @click="format('strike-through')" 
                        :title="$t('Strikethrough (~text~)')" 
                        class="p-1 rounded-lg text-slate-600 dark:text-zinc-300 hover:bg-white dark:hover:bg-zinc-700 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path fill="currentColor" d="m16.533 12.5l.054.043c.93.75 1.538 1.77 1.538 3.066a4.13 4.13 0 0 1-1.479 3.177c-1.058.904-2.679 1.464-4.974 1.464c-2.35 0-4.252-.837-5.318-1.865a.75.75 0 1 1 1.042-1.08c.747.722 2.258 1.445 4.276 1.445c2.065 0 3.296-.504 3.999-1.105a2.63 2.63 0 0 0 .954-2.036c0-.764-.337-1.38-.979-1.898c-.649-.523-1.598-.931-2.76-1.211H3.75a.75.75 0 0 1 0-1.5h26.5a.75.75 0 0 1 0 1.5ZM12.36 5C9.37 5 8.105 6.613 8.105 7.848c0 .411.072.744.193 1.02a.75.75 0 0 1-1.373.603a3.988 3.988 0 0 1-.32-1.623c0-2.363 2.271-4.348 5.755-4.348c1.931 0 3.722.794 4.814 1.5a.75.75 0 1 1-.814 1.26c-.94-.607-2.448-1.26-4-1.26Z"/></svg>
                    </button>
                    <button 
                        type="button"
                        @click="format('monospace')" 
                        :title="$t('Monospace (```code```)')" 
                        class="p-1 rounded-lg text-slate-600 dark:text-zinc-300 hover:bg-white dark:hover:bg-zinc-700 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.5 6L10 18.5m-3.5-10L3 12l3.5 3.5m11-7L21 12l-3.5 3.5"/></svg>
                    </button>
                    <div class="h-4 w-px bg-slate-300 dark:bg-zinc-700 mx-0.5"></div>
                    <button 
                        type="button" 
                        @click="addVariable" 
                        class="inline-flex items-center gap-1 px-2 py-1 rounded-lg text-xs font-semibold text-[#6C5CE7] dark:text-purple-300 bg-purple-50 dark:bg-purple-950/40 hover:bg-purple-100 dark:hover:bg-purple-900/60 transition-colors"
                    >
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                        <span>{{ $t('Add variable') }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Sample Values Panel for Variables -->
        <div v-if="placeholders.length > 0" class="bg-slate-50 dark:bg-zinc-800/50 p-4 rounded-2xl border border-slate-200/80 dark:border-zinc-700/80 space-y-3">
            <div>
                <h4 class="text-xs font-bold text-slate-800 dark:text-zinc-200 uppercase tracking-wider flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-[#6C5CE7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="16" x2="12" y2="12"/><line x1="12" y1="8" x2="12.01" y2="8"/></svg>
                    {{ $t('Sample Values for Variables') }}
                </h4>
                <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5">
                    {{ $t('Meta requires sample values for all variables (e.g. Kamal, #10492) to review your template. Do not include sensitive personal data.') }}
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
                            :placeholder="'e.g. John Doe, 49204, etc. for ' + placeholder" 
                            class="block w-full rounded-xl border border-slate-200 dark:border-zinc-700/80 py-1.5 px-3 text-slate-900 dark:text-white bg-white dark:bg-zinc-800 text-xs shadow-sm outline-none focus:ring-2 focus:ring-[#6C5CE7]/30 focus:border-[#6C5CE7] placeholder:text-slate-400 transition-all" 
                            required
                        >
                    </div>
                </div>
            </div>

            <div v-if="isCustomValuesIncomplete" class="p-2.5 bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/40 rounded-xl flex items-center gap-2 text-rose-700 dark:text-rose-400 text-xs">
                <svg class="w-4 h-4 shrink-0 text-rose-600" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path fill-rule="evenodd" d="M12 2.25c-5.385 0-9.75 4.365-9.75 9.75s4.365 9.75 9.75 9.75 9.75-4.365 9.75-9.75S17.385 2.25 12 2.25zm-1.72 6.97a.75.75 0 10-1.06 1.06L10.94 12l-1.72 1.72a.75.75 0 101.06 1.06L12 13.06l1.72 1.72a.75.75 0 101.06-1.06L13.06 12l1.72-1.72a.75.75 0 10-1.06-1.06L12 10.94l-1.72-1.72z" clip-rule="evenodd"/></svg>
                <span>{{ $t('Please provide sample values for all variable parameters before submitting.') }}</span>
            </div>
        </div>
    </div>
</template>