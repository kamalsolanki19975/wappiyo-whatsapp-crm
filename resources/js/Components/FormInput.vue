<script setup>
    const props = defineProps({
        modelValue: [String, Number],
        label: String,
        name: String,
        placeholder: String,
        type: {
            type: String,
            default: 'text'
        },
        className: String,
        labelClass: String,
        required: Boolean,
        error: String,
        disabled: Boolean
    })

    const emit = defineEmits(['update:modelValue']);
    const updateValue = (event) => {
        emit('update:modelValue', event.target.value);
    };
</script>
<template>
    <div :class="className" class="w-full">
        <label v-if="label || name" :for="name" class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1.5" :class="labelClass">
            {{ label ?? name }}
            <span v-if="required" class="text-rose-500">*</span>
        </label>
        <div>
            <input
                :id="name"
                class="block w-full rounded-lg border py-2 px-3.5 text-slate-900 dark:text-zinc-100 bg-white dark:bg-[#111113] shadow-sm outline-none placeholder:text-slate-400 dark:placeholder:text-zinc-500 text-sm transition-all duration-150"
                :class="[
                    error 
                        ? 'border-rose-500 focus:border-rose-500 focus:ring-2 focus:ring-rose-500/20' 
                        : 'border-slate-300 dark:border-zinc-700 hover:border-slate-400 dark:hover:border-zinc-600 focus:border-[#6C5CE7] focus:ring-2 focus:ring-[#6C5CE7]/20 dark:focus:border-[#8B5CF6] dark:focus:ring-[#8B5CF6]/25',
                    disabled ? 'opacity-50 cursor-not-allowed bg-slate-50 dark:bg-zinc-900' : ''
                ]"
                :type="type"
                :value="props.modelValue"
                @input="updateValue"
                :step="'any'"
                :placeholder="placeholder"
                :disabled="disabled"
                :required="required"
            />
        </div>
        <div v-if="error" class="form-error text-rose-500 text-xs mt-1.5 font-medium flex items-center gap-1">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" x2="12" y1="8" y2="12"/><line x1="12" x2="12.01" y1="16" y2="16"/></svg>
            <span>{{ error }}</span>
        </div>
    </div>
</template>