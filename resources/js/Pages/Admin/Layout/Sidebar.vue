<template>
    <aside class="md:flex flex-col h-full w-64 lg:w-72 bg-white dark:bg-[#111113] hidden shrink-0 transition-colors z-20">
        <Menu
            :config="props.config"
            :user="props.user"
            :organization="props.organization"
            :organizations="props.organizations"
        />
    </aside>
</template>

<script setup>
import { defineProps } from "vue";
import Menu from "./Menu.vue";

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },
    config: {
        type: Array,
        required: true
    },
    organization: {
        type: Object,
        default: () => ({})
    },
    organizations: {
        type: Object,
        default: () => ({})
    }
});
</script>
