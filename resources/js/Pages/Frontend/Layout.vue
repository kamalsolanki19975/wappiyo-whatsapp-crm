<template>
    <WebsiteLayout
        :title="props.title"
        :description="props.description"
        :companyConfig="props.companyConfig"
        :languages="props.languages"
        :currentLanguage="props.currentLanguage"
    >
        <slot />
    </WebsiteLayout>
</template>

<script setup>
import WebsiteLayout from './Layout/WebsiteLayout.vue';

const props = defineProps({
    title: String,
    description: String,
    companyConfig: Object,
    languages: Object,
    currentLanguage: String,
});
</script>