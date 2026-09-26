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
    type: {
        type: String,
        default: 'text',
    },
    placeholder: {
        type: String,
        default: '',
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
    size: {
        type: String,
        default: 'md',
        validator: (value) => ['sm', 'md', 'lg'].includes(value),
    },
    rounded: {
        type: String,
        default: 'lg',
    },
    className: {
        type: String,
        default: '',
    },
    inputClassName: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue', 'blur', 'focus', 'clear']);

const sizeClasses = computed(() => {
    switch (props.size) {
        case 'sm': return 'py-1.5 px-3 text-xs';
        case 'lg': return 'py-2.5 px-4 text-base';
        case 'md':
        default: return 'py-2 px-3.5 text-sm';
    }
});

const roundedClasses = computed(() => {
    switch (props.rounded) {
        case 'sm': return 'rounded-md';
        case 'lg': return 'rounded-lg';
        case 'xl': return 'rounded-xl';
        case 'pill':
        case 'full': return 'rounded-full';
        case 'md':
        default: return 'rounded-lg';
    }
});
</script>

<template>
    <div :class="['w-full', className]">
        <!-- Label -->
        <label
            v-if="label || name"
            :for="name"
            class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1.5 select-none"
        >
            {{ label || name }}
            <span v-if="required" class="text-rose-500">*</span>
        </label>

        <!-- Input Wrapper -->
        <div class="relative flex items-center">
            <!-- Prefix Icon / Slot -->
            <div
                v-if="$slots.prefix"
                class="absolute left-3 flex items-center pointer-events-none text-slate-400 dark:text-zinc-500"
            >
                <slot name="prefix" />
            </div>

            <!-- Input Element -->
            <input
                :id="name"
                :name="name"
                :type="type"
                :value="modelValue"
                :placeholder="placeholder"
                :disabled="disabled"
                :readonly="readonly"
                :required="required"
                :class="[
                    'w-full bg-white dark:bg-[#111113] text-slate-900 dark:text-zinc-100 placeholder:text-slate-400 dark:placeholder:text-zinc-500 border transition-all duration-150 outline-none',
                    error
                        ? 'border-rose-500 focus:ring-2 focus:ring-rose-500/20 focus:border-rose-500 dark:border-rose-500'
                        : 'border-slate-300 dark:border-zinc-700 hover:border-slate-400 dark:hover:border-zinc-600 focus:border-[#6C5CE7] focus:ring-2 focus:ring-[#6C5CE7]/20 dark:focus:border-[#8B5CF6] dark:focus:ring-[#8B5CF6]/25',
                    disabled ? 'opacity-50 cursor-not-allowed bg-slate-50 dark:bg-zinc-900' : '',
                    $slots.prefix ? 'pl-9.5' : '',
                    $slots.suffix ? 'pr-9.5' : '',
                    sizeClasses,
                    roundedClasses,
                    inputClassName
                ]"
                @input="emit('update:modelValue', $event.target.value)"
                @blur="emit('blur', $event)"
                @focus="emit('focus', $event)"
            />

            <!-- Suffix Icon / Slot -->
            <div
                v-if="$slots.suffix"
                class="absolute right-3 flex items-center text-slate-400 dark:text-zinc-500"
            >
                <slot name="suffix" />
            </div>
        </div>

        <!-- Error Message -->
        <p v-if="error" class="mt-1.5 text-xs text-rose-500 font-medium flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
            <span>{{ error }}</span>
        </p>

        <!-- Hint Text -->
        <p v-else-if="hint" class="mt-1.5 text-xs text-slate-500 dark:text-zinc-400">
            {{ hint }}
        </p>
    </div>
</template>
