<script setup>
const props = defineProps({
    modelValue: {
        type: Boolean,
        default: false,
    },
    label: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
    disabled: {
        type: Boolean,
        default: false,
    },
    size: {
        type: String,
        default: 'md', // 'sm', 'md', 'lg'
    },
    className: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue', 'change']);

const toggle = () => {
    if (!props.disabled) {
        const next = !props.modelValue;
        emit('update:modelValue', next);
        emit('change', next);
    }
};
</script>

<template>
    <div
        :class="[
            'inline-flex items-center gap-3 cursor-pointer select-none',
            disabled ? 'opacity-50 cursor-not-allowed' : '',
            className
        ]"
        @click="toggle"
    >
        <!-- Switch track -->
        <button
            type="button"
            role="switch"
            :aria-checked="modelValue"
            :disabled="disabled"
            :class="[
                'relative inline-flex shrink-0 cursor-pointer rounded-full transition-colors duration-200 ease-in-out focus:outline-none focus-visible:ring-2 focus-visible:ring-[#6C5CE7] focus-visible:ring-offset-2',
                size === 'sm' ? 'h-5 w-9' : size === 'lg' ? 'h-7 w-13' : 'h-6 w-11',
                modelValue
                    ? 'bg-[#6C5CE7] shadow-sm shadow-purple-600/30'
                    : 'bg-slate-200 dark:bg-zinc-700'
            ]"
        >
            <!-- Thumb -->
            <span
                :class="[
                    'pointer-events-none inline-block rounded-full bg-white shadow-md transform ring-0 transition duration-200 ease-in-out',
                    size === 'sm'
                        ? (modelValue ? 'translate-x-4 h-4 w-4 mt-0.5 ml-0.5' : 'translate-x-0.5 h-4 w-4 mt-0.5')
                        : size === 'lg'
                        ? (modelValue ? 'translate-x-6 h-6 w-6 mt-0.5 ml-0.5' : 'translate-x-0.5 h-6 w-6 mt-0.5')
                        : (modelValue ? 'translate-x-5 h-5 w-5 mt-0.5 ml-0.5' : 'translate-x-0.5 h-5 w-5 mt-0.5')
                ]"
            />
        </button>

        <!-- Label / description -->
        <div v-if="label || description" class="text-sm">
            <span v-if="label" class="font-medium text-slate-800 dark:text-zinc-200 block">
                {{ label }}
            </span>
            <span v-if="description" class="text-xs text-slate-500 dark:text-zinc-400 block mt-0.5">
                {{ description }}
            </span>
        </div>
    </div>
</template>
