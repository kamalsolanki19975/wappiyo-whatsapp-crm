<script setup>
    const props = defineProps({
        modelValue: [Boolean, Number],
        className: String,
        disabled:{
            type: Boolean,
            default: false
        },
    })

    const emit = defineEmits(['update:modelValue']);
    const updateValue = () => {
        if(!props.disabled){
            emit('update:modelValue', !props.modelValue);
        }
    };
</script>
<template>
    <div :class="className" class="inline-flex items-center">
        <button
            type="button"
            role="switch"
            :aria-checked="props.modelValue == true || props.modelValue == 1"
            :disabled="disabled"
            class="w-11 h-6 flex items-center rounded-full p-1 transition-colors duration-200 ease-in-out cursor-pointer focus:outline-none focus-visible:ring-2 focus-visible:ring-[#6C5CE7]" 
            :class="[
                (props.modelValue == true || props.modelValue == 1)
                    ? 'bg-[#6C5CE7] shadow-sm shadow-purple-600/30' 
                    : 'bg-slate-200 dark:bg-zinc-700',
                disabled ? 'opacity-50 cursor-not-allowed' : ''
            ]" 
            @click="updateValue"
        >
            <div 
                class="bg-white w-4 h-4 rounded-full shadow-md transform duration-200 ease-in-out" 
                :class="{ 'translate-x-5': props.modelValue == true || props.modelValue == 1 }"
            ></div>
        </button>
    </div>
</template>