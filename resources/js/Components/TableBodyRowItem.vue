<script setup>
    const props = defineProps({
        position: {
            type: String,
            default: null
        },
    })

    const getRowClass = () => {
        if (props.position === 'first') {
            return 'pl-6 pr-4 py-4';
        } else if (props.position === 'last') {
            return 'pl-4 pr-6 py-4 text-right';
        } else {
            return 'px-4 py-4';
        }
    };
</script>
<template>
    <td :class="getRowClass()">
        <div class="text-left w-full h-full flex items-center"><slot /></div>
    </td>
</template>