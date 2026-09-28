<template>
    <WebsiteLayout
        :companyConfig="companyConfig"
        :languages="languages"
        :currentLanguage="currentLanguage"
        title="About Us — Empowering Modern Business Communication"
        description="Learn about Wappiyo's mission to help businesses build closer, faster, and more personal relationships through WhatsApp."
    >
        <!-- HERO -->
        <section class="relative pt-16 pb-20 sm:pt-24 sm:pb-32 overflow-hidden bg-slate-50/50 dark:bg-[#09090B]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-700 dark:text-emerald-300 mb-6">
                    {{ $t('Our Mission') }}
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight max-w-4xl mx-auto">
                    <span>{{ $t('Redefining how businesses connect') }}</span>
                    <span class="block bg-gradient-to-r from-emerald-600 via-teal-500 to-cyan-500 bg-clip-text text-transparent">
                        {{ $t('with customers on WhatsApp.') }}
                    </span>
                </h1>
                <p class="mt-6 text-base sm:text-lg text-slate-600 dark:text-zinc-400 max-w-2xl mx-auto leading-relaxed">
                    {{ $t('We believe customer communication should be as immediate, personal, and conversational as texting a friend, backed by enterprise-grade infrastructure.') }}
                </p>
            </div>
        </section>

        <!-- PHILOSOPHY & VALUES -->
        <section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-8 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center font-bold text-xl">
                        🎯
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $t('Conversational First') }}</h3>
                    <p class="text-xs text-slate-600 dark:text-zinc-400 leading-relaxed">
                        {{ $t('Email spam is declining while messaging apps are where decisions happen. We build software that respects conversational privacy and maximizes relevance.') }}
                    </p>
                </div>

                <div class="p-8 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-cyan-100 dark:bg-cyan-950/60 text-[#06B6D4] flex items-center justify-center font-bold text-xl">
                        ⚡
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $t('Speed & Reliability') }}</h3>
                    <p class="text-xs text-slate-600 dark:text-zinc-400 leading-relaxed">
                        {{ $t('Customer support and sales live or die by seconds. Our platform is built on direct Meta Cloud API hooks with sub-second message dispatch and webhook delivery.') }}
                    </p>
                </div>

                <div class="p-8 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 space-y-4">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center font-bold text-xl">
                        🛡️
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $t('Data Security & Compliance') }}</h3>
                    <p class="text-xs text-slate-600 dark:text-zinc-400 leading-relaxed">
                        {{ $t('We prioritize tenant isolation, role-based access permissions, strict rate-limiting, and end-to-end data encryption for every organization.') }}
                    </p>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <WebsiteCtaBanner
            :title="$t('Ready to experience modern conversational CRM?')"
            :subtitle="$t('Get Started Today')"
            :description="$t('Join businesses managing high-velocity WhatsApp operations with Wappiyo.')"
        />
    </WebsiteLayout>
</template>

<script setup>
import WebsiteLayout from './Layout/WebsiteLayout.vue';
import WebsiteCtaBanner from './Components/WebsiteCtaBanner.vue';

const props = defineProps({
    companyConfig: Object,
    languages: Object,
    currentLanguage: String,
});
</script>
