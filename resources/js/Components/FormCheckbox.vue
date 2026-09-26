<script setup>
    import { computed } from 'vue';

    const props = defineProps({
        modelValue: [String, Number, Boolean, Array],
        value: { type: [Boolean, Object, String, Number] },
        name: String,
        className: String,
        label: String,
        disabled: Boolean
    })

    const emit = defineEmits(['update:modelValue']);
    const model = computed({
        get() {
            return props.modelValue;
        },
        set(value) {
            emit("update:modelValue", value);
        },
    });
</script>
<template>
    <div :class="className">
        <label :for="name" class="relative inline-flex items-center gap-2.5 mb-2 cursor-pointer select-none group">
            <input 
                v-model="model"
                :value="value"
                :id="name"
                :name="name"
                :disabled="disabled"
                type="checkbox" 
                class="h-4 w-4 rounded border-slate-300 dark:border-zinc-700 text-[#6C5CE7] focus:ring-[#6C5CE7] dark:bg-[#111113] transition-colors"
            >
            <span v-if="label" class="text-sm font-medium text-slate-700 dark:text-zinc-300 capitalize group-hover:text-slate-900 dark:group-hover:text-white transition-colors">
                {{ label }}
            </span>
        </label>
    </div>
</template>