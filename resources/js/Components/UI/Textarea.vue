<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
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
        default: '',
    },
    rows: {
        type: Number,
        default: 4,
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    readonly: {
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
    maxlength: {
        type: Number,
        default: null,
    },
    showCount: {
        type: Boolean,
        default: false,
    },
    className: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue', 'blur', 'focus']);

const charCount = computed(() => {
    return props.modelValue ? String(props.modelValue).length : 0;
});
</script>

<template>
    <div :class="['w-full', className]">
        <div class="flex items-center justify-between mb-1.5">
            <label
                v-if="label || name"
                :for="name"
                class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-zinc-300 select-none"
            >
                {{ label || name }}
                <span v-if="required" class="text-rose-500">*</span>
            </label>

            <span
                v-if="showCount && maxlength"
                class="text-[11px] text-slate-400 dark:text-zinc-500 font-mono"
            >
                {{ charCount }} / {{ maxlength }}
            </span>
        </div>

        <div class="relative">
            <textarea
                :id="name"
                :name="name"
                :rows="rows"
                :value="modelValue"
                :placeholder="placeholder"
                :disabled="disabled"
                :readonly="readonly"
                :required="required"
                :maxlength="maxlength"
                :class="[
                    'w-full bg-white dark:bg-[#111113] text-slate-900 dark:text-zinc-100 placeholder:text-slate-400 dark:placeholder:text-zinc-500 border rounded-lg py-2.5 px-3.5 text-sm transition-all duration-150 outline-none resize-y',
                    error
                        ? 'border-rose-500 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500'
                        : 'border-slate-300 dark:border-zinc-700 hover:border-slate-400 dark:hover:border-zinc-600 focus:border-[#6C5CE7] focus:ring-2 focus:ring-[#6C5CE7]/20 dark:focus:border-[#8B5CF6] dark:focus:ring-[#8B5CF6]/25',
                    disabled ? 'opacity-50 cursor-not-allowed bg-slate-50 dark:bg-zinc-900' : ''
                ]"
                @input="emit('update:modelValue', $event.target.value)"
                @blur="emit('blur', $event)"
                @focus="emit('focus', $event)"
            />
        </div>

        <p v-if="error" class="mt-1.5 text-xs text-rose-500 font-medium flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
            <span>{{ error }}</span>
        </p>
        <p v-else-if="hint" class="mt-1.5 text-xs text-slate-500 dark:text-zinc-400">
            {{ hint }}
        </p>
    </div>
</template>
