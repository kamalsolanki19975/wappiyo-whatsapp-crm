<script setup>
import { computed } from 'vue';

const props = defineProps({
    modelValue: {
        type: [Boolean, Array, String, Number],
        default: false,
    },
    value: {
        type: [Boolean, String, Number, Object],
        default: true,
    },
    label: {
        type: String,
        default: '',
    },
    name: {
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
    className: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue', 'change']);

const isChecked = computed(() => {
    if (Array.isArray(props.modelValue)) {
        return props.modelValue.includes(props.value);
    }
    return Boolean(props.modelValue);
});

const handleChange = (event) => {
    if (props.disabled) return;
    
    let newValue;
    if (Array.isArray(props.modelValue)) {
        newValue = [...props.modelValue];
        if (event.target.checked) {
            newValue.push(props.value);
        } else {
            const index = newValue.indexOf(props.value);
            if (index > -1) newValue.splice(index, 1);
        }
    } else {
        newValue = event.target.checked;
    }
    
    emit('update:modelValue', newValue);
    emit('change', newValue);
};
</script>

<template>
    <label
        :class="[
            'relative inline-flex items-start gap-3 cursor-pointer select-none group',
            disabled ? 'opacity-50 cursor-not-allowed' : '',
            className
        ]"
    >
        <div class="relative flex items-center justify-center mt-0.5">
            <input
                :id="name"
                :name="name"
                type="checkbox"
                :checked="isChecked"
                :disabled="disabled"
                class="sr-only peer"
                @change="handleChange"
            />
            <div
                :class="[
                    'w-4.5 h-4.5 rounded-md border flex items-center justify-center transition-all duration-150',
                    isChecked
                        ? 'bg-[#6C5CE7] border-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                        : 'bg-white dark:bg-[#111113] border-slate-300 dark:border-zinc-700 group-hover:border-[#6C5CE7]/60'
                ]"
            >
                <svg
                    v-if="isChecked"
                    class="w-3 h-3 text-white stroke-current"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke-width="3.5"
                    stroke-linecap="round"
                    stroke-linejoin="round"
                >
                    <polyline points="20 6 9 17 4 12" />
                </svg>
            </div>
        </div>

        <div v-if="label || description" class="text-sm">
            <span v-if="label" class="font-medium text-slate-800 dark:text-zinc-200 block">
                {{ label }}
            </span>
            <span v-if="description" class="text-xs text-slate-500 dark:text-zinc-400 block mt-0.5">
                {{ description }}
            </span>
        </div>
    </label>
</template>
