<script setup>
    const props = defineProps({
        position: {
            type: String,
            default: null
        },
    })

    const getRowClass = () => {
        if (props.position === 'first') {
            return 'pl-6 pr-4 py-3.5 text-left font-semibold';
        } else if (props.position === 'last') {
            return 'pl-4 pr-6 py-3.5 text-right font-semibold';
        } else {
            return 'px-4 py-3.5 text-left font-semibold'
        }
    };
</script>
<template>
    <th :class="getRowClass()">
        <slot/>
    </th>
</template>