<script setup>
defineProps({
    max: {
        type: Number,
        default: 4,
    },
    className: {
        type: String,
        default: '',
    },
});
</script>

<template>
    <div :class="['flex items-center -space-x-2.5 overflow-hidden', className]">
        <slot />
    </div>
</template>
