<template>
    <WebsiteLayout
        :companyConfig="companyConfig"
        :languages="languages"
        :currentLanguage="currentLanguage"
        :title="pageData?.name || 'Page'"
    >
        <section class="py-16 sm:py-24 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div v-if="pageData" class="space-y-6">
                <div class="text-center pb-8 border-b border-slate-200/80 dark:border-zinc-800">
                    <h1 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white">{{ pageData.name }}</h1>
                    <p v-if="pageData.updated_at" class="text-xs text-slate-500 dark:text-zinc-400 mt-2">
                        Last updated: {{ pageData.updated_at }}
                    </p>
                </div>
                <div class="prose prose-slate dark:prose-invert max-w-none text-sm text-slate-700 dark:text-zinc-300 leading-relaxed bg-white dark:bg-[#111113] p-8 sm:p-12 rounded-3xl border border-slate-200/80 dark:border-zinc-800 shadow-sm" v-html="pageData.content"></div>
            </div>
            <div v-else class="text-center py-20">
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Page not found</h2>
                <Link href="/" class="mt-4 inline-block text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                    Return to Home &rarr;
                </Link>
            </div>
        </section>
    </WebsiteLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import WebsiteLayout from './Layout/WebsiteLayout.vue';

const props = defineProps({
    companyConfig: Object,
    languages: Object,
    currentLanguage: String,
    page: Object,
});

const pageData = computed(() => props.page?.data || props.page);
</script>