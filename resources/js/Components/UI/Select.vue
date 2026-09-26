<script setup>
import { computed, ref } from 'vue';
import {
    Listbox,
    ListboxLabel,
    ListboxButton,
    ListboxOptions,
    ListboxOption,
} from '@headlessui/vue';
import { CheckIcon, ChevronUpDownIcon } from '@heroicons/vue/20/solid';

const props = defineProps({
    options: {
        type: Array,
        default: () => [],
    },
    modelValue: {
        type: [String, Number, Array],
        default: null,
    },
    label: {
        type: String,
        default: '',
    },
    name: {
        type: String,
        default: '',
    },
    placeholder: {
        type: String,
        default: 'Select option',
    },
    multiple: {
        type: Boolean,
        default: false,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    required: {
        type: Boolean,
        default: false,
    },
    error: {
        type: String,
        default: '',
    },
    hint: {
        type: String,
        default: '',
    },
    className: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);

const selectedLabel = computed(() => {
    if (props.modelValue === null || props.modelValue === undefined || props.modelValue === '') {
        return null;
    }

    if (props.multiple && Array.isArray(props.modelValue)) {
        const selected = props.options.filter(opt => props.modelValue.includes(opt.value));
        return selected.length > 0 ? selected.map(opt => opt.label).join(', ') : null;
    }

    const match = props.options.find(opt => opt.value === props.modelValue);
    return match ? match.label : null;
});
</script>

<template>
    <div :class="['w-full', className]">
        <label
            v-if="label || name"
            :for="name"
            class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1.5 select-none"
        >
            {{ label || name }}
            <span v-if="required" class="text-rose-500">*</span>
        </label>

        <Listbox
            :multiple="multiple"
            :disabled="disabled"
            :model-value="modelValue"
            @update:modelValue="value => emit('update:modelValue', value)"
        >
            <div class="relative">
                <ListboxButton
                    :class="[
                        'relative w-full cursor-pointer rounded-lg bg-white dark:bg-[#111113] py-2 px-3.5 pr-10 text-left text-sm border shadow-sm outline-none transition-all duration-150',
                        error
                            ? 'border-rose-500 focus:ring-2 focus:ring-rose-500/20'
                            : 'border-slate-300 dark:border-zinc-700 hover:border-slate-400 dark:hover:border-zinc-600 focus:border-[#6C5CE7] focus:ring-2 focus:ring-[#6C5CE7]/20 dark:focus:border-[#8B5CF6]',
                        disabled ? 'opacity-50 cursor-not-allowed bg-slate-50 dark:bg-zinc-900' : ''
                    ]"
                >
                    <span v-if="selectedLabel" class="block truncate text-slate-900 dark:text-zinc-100">
                        {{ selectedLabel }}
                    </span>
                    <span v-else class="block truncate text-slate-400 dark:text-zinc-500">
                        {{ placeholder }}
                    </span>

                    <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400 dark:text-zinc-500">
                        <ChevronUpDownIcon class="h-4 w-4" aria-hidden="true" />
                    </span>
                </ListboxButton>

                <transition
                    leave-active-class="transition duration-100 ease-in"
                    leave-from-class="opacity-100 scale-100"
                    leave-to-class="opacity-0 scale-95"
                >
                    <ListboxOptions
                        class="absolute z-50 mt-1 max-h-60 w-full overflow-auto rounded-xl bg-white dark:bg-[#18181B] py-1 text-sm shadow-floating border border-slate-200 dark:border-zinc-700 focus:outline-none"
                    >
                        <ListboxOption
                            v-for="option in options"
                            :key="option.value"
                            :value="option.value"
                            :disabled="option.disabled"
                            v-slot="{ active, selected }"
                            as="template"
                        >
                            <li
                                :class="[
                                    'relative cursor-pointer select-none py-2 pl-9 pr-4 transition-colors',
                                    active ? 'bg-purple-50 dark:bg-purple-950/50 text-[#6C5CE7] dark:text-purple-300' : 'text-slate-900 dark:text-zinc-200',
                                    option.disabled ? 'opacity-40 cursor-not-allowed' : ''
                                ]"
                            >
                                <span :class="[selected ? 'font-semibold text-[#6C5CE7] dark:text-purple-400' : 'font-normal', 'block truncate']">
                                    {{ option.label }}
                                </span>

                                <span
                                    v-if="selected"
                                    class="absolute inset-y-0 left-0 flex items-center pl-2.5 text-[#6C5CE7] dark:text-purple-400"
                                >
                                    <CheckIcon class="h-4 w-4" aria-hidden="true" />
                                </span>
                            </li>
                        </ListboxOption>
                    </ListboxOptions>
                </transition>
            </div>
        </Listbox>

        <p v-if="error" class="mt-1.5 text-xs text-rose-500 font-medium flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
            <span>{{ error }}</span>
        </p>
        <p v-else-if="hint" class="mt-1.5 text-xs text-slate-500 dark:text-zinc-400">
            {{ hint }}
        </p>
    </div>
</template>
