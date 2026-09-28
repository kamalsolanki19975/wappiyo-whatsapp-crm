<template>
    <div class="min-h-screen bg-white dark:bg-[#09090B] text-slate-900 dark:text-zinc-100 antialiased selection:bg-emerald-500/20 selection:text-emerald-700 dark:selection:text-emerald-400 transition-colors duration-200 flex flex-col font-sans">
        <Head>
            <title>{{ fullTitle }}</title>
            <meta name="description" :content="metaDescription" />
            <!-- Open Graph / Facebook -->
            <meta property="og:type" content="website" />
            <meta property="og:title" :content="fullTitle" />
            <meta property="og:description" :content="metaDescription" />
            <meta property="og:image" :content="companyConfig?.logo ? '/media/' + companyConfig.logo : '/images/og-image.png'" />
            <!-- Twitter -->
            <meta name="twitter:card" content="summary_large_image" />
            <meta name="twitter:title" :content="fullTitle" />
            <meta name="twitter:description" :content="metaDescription" />
            <meta name="twitter:image" :content="companyConfig?.logo ? '/media/' + companyConfig.logo : '/images/og-image.png'" />
        </Head>

        <!-- Flash Alert if returned from backend actions (e.g. contact form) -->
        <div v-if="flashMessage" class="fixed bottom-6 right-6 z-50 max-w-md animate-bounce-in">
            <div
                class="flex items-center gap-3 p-4 rounded-2xl shadow-2xl border backdrop-blur-md transition-all"
                :class="flashType === 'success' 
                    ? 'bg-emerald-500/90 border-emerald-400 text-white shadow-emerald-500/20' 
                    : 'bg-rose-500/90 border-rose-400 text-white shadow-rose-500/20'"
            >
                <div class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center shrink-0">
                    <svg v-if="flashType === 'success'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </div>
                <div class="flex-1 text-xs font-semibold leading-relaxed">
                    {{ flashMessage }}
                </div>
                <button
                    type="button"
                    @click="dismissFlash"
                    class="p-1 rounded-lg hover:bg-white/20 text-white/80 hover:text-white transition-colors"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
        </div>

        <!-- Global Header -->
        <WebsiteHeader
            :companyConfig="companyConfig"
            :languages="languages"
            :currentLanguage="currentLanguage"
        />

        <!-- Page Content -->
        <main class="flex-1 w-full">
            <slot />
        </main>

        <!-- Global Footer -->
        <WebsiteFooter
            :companyConfig="companyConfig"
        />
    </div>
</template>

<script setup>
import { computed, ref, onMounted, watch } from 'vue';
import { Head, usePage } from '@inertiajs/vue3';
import WebsiteHeader from '../Components/WebsiteHeader.vue';
import WebsiteFooter from '../Components/WebsiteFooter.vue';
import { useTheme } from '../../../composables/useTheme';

const props = defineProps({
    title: {
        type: String,
        default: '',
    },
    description: {
        type: String,
        default: '',
    },
    companyConfig: {
        type: Object,
        default: () => ({}),
    },
    languages: {
        type: Object,
        default: () => ({}),
    },
    currentLanguage: {
        type: String,
        default: 'en',
    },
});

const page = usePage();
const { initTheme } = useTheme();

onMounted(() => {
    initTheme();
});

const companyName = computed(() => {
    return props.companyConfig?.company_name || page.props.companyConfig?.company_name || 'Wappiyo';
});

const fullTitle = computed(() => {
    if (props.title) {
        return `${props.title} — ${companyName.value}`;
    }
    return `${companyName.value} — WhatsApp CRM, Broadcast Campaigns & Flow Automation`;
});

const metaDescription = computed(() => {
    if (props.description) {
        return props.description;
    }
    return 'Scale your business with Wappiyo. The high-performance WhatsApp CRM platform with multi-agent team inbox, automated workflows, bulk broadcasts, and AI assistance.';
});

// Flash handling
const flashDismissed = ref(false);

const flashMessage = computed(() => {
    if (flashDismissed.value) return null;
    return page.props.flash?.status?.message || page.props.flash?.message || null;
});

const flashType = computed(() => {
    return page.props.flash?.status?.type || 'success';
});

const dismissFlash = () => {
    flashDismissed.value = true;
};

watch(() => page.props.flash, () => {
    flashDismissed.value = false;
}, { deep: true });
</script>
