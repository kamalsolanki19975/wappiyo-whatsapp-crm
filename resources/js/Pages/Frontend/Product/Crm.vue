<template>
    <WebsiteLayout
        :companyConfig="companyConfig"
        :languages="languages"
        :currentLanguage="currentLanguage"
        title="WhatsApp CRM & Contact Intelligence"
        description="Transform contacts into lasting customer relationships. Custom attributes, lifecycle stages, smart tags, dynamic segmentation, and unified conversation timelines."
    >
        <!-- HERO -->
        <section class="relative pt-16 pb-20 sm:pt-24 sm:pb-32 overflow-hidden bg-slate-50/50 dark:bg-[#09090B]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan-50 dark:bg-cyan-950/40 border border-cyan-200 dark:border-cyan-800 text-xs font-semibold text-[#06B6D4] mb-6">
                    {{ $t('Conversational CRM') }}
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight max-w-4xl mx-auto">
                    <span>{{ $t('Every customer relationship,') }}</span>
                    <span class="block bg-gradient-to-r from-[#06B6D4] via-[#8B5CF6] to-[#EC4899] bg-clip-text text-transparent">
                        {{ $t('enriched with instant context.') }}
                    </span>
                </h1>
                <p class="mt-6 text-base sm:text-lg text-slate-600 dark:text-zinc-400 max-w-2xl mx-auto leading-relaxed">
                    {{ $t('Turn WhatsApp chats into organized, revenue-generating customer profiles. Track purchase habits, assign custom attributes, create targeted segments, and maintain a 360-degree timeline.') }}
                </p>

                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <Link href="/signup" class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 hover:scale-[1.02] transition-all">
                        {{ $t('Explore CRM Workspace') }}
                    </Link>
                    <Link href="/pricing" class="px-6 py-3.5 rounded-xl bg-white dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-slate-700 dark:text-zinc-300 font-semibold text-xs hover:bg-slate-50 dark:hover:bg-zinc-800 transition-all">
                        {{ $t('View Pricing') }}
                    </Link>
                </div>
            </div>
        </section>

        <!-- CRM FEATURES -->
        <section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-24">
            <!-- 1. Customer Profiles & Timeline -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">
                        {{ $t('Complete 360° Customer Timeline') }}
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-zinc-400 leading-relaxed">
                        {{ $t('When a customer contacts your team on WhatsApp, their full history is right in front of the agent: past orders, previous support tickets, broadcast campaign clicks, and notes.') }}
                    </p>
                    <div class="space-y-3 text-xs text-slate-700 dark:text-zinc-300">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-cyan-100 dark:bg-cyan-950/60 text-[#06B6D4] flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Instant contact search by phone, email, name, or custom tags') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-cyan-100 dark:bg-cyan-950/60 text-[#06B6D4] flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Custom fields for industry-specific data (e.g. Account ID, Renewal Date)') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-cyan-100 dark:bg-cyan-950/60 text-[#06B6D4] flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Bulk CSV import and export with real-time field validation') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Visual Mockup -->
                <div class="p-6 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 shadow-xl space-y-4">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-zinc-800 pb-3">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-emerald-500 to-teal-600 text-white flex items-center justify-center font-bold text-base">
                                MT
                            </div>
                            <div>
                                <h3 class="text-sm font-bold text-slate-900 dark:text-white">Maya Thornton</h3>
                                <p class="text-xs text-slate-400">Head of Growth, Acme Logistics</p>
                            </div>
                        </div>
                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950 text-emerald-600 border border-emerald-200 dark:border-emerald-800">
                            Enterprise Tier
                        </span>
                    </div>

                    <!-- Custom Fields Grid -->
                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-[#18181B] border border-slate-200/60 dark:border-zinc-800">
                            <span class="text-slate-400 text-[10px] block">WhatsApp Verified</span>
                            <span class="font-bold text-slate-800 dark:text-white">+1 (415) 890-2345</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-[#18181B] border border-slate-200/60 dark:border-zinc-800">
                            <span class="text-slate-400 text-[10px] block">Renewal Contract</span>
                            <span class="font-bold text-[#06B6D4]">Annual ($12,000)</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-[#18181B] border border-slate-200/60 dark:border-zinc-800">
                            <span class="text-slate-400 text-[10px] block">Dedicated Rep</span>
                            <span class="font-bold text-slate-800 dark:text-white">Marcus Vance</span>
                        </div>
                        <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-[#18181B] border border-slate-200/60 dark:border-zinc-800">
                            <span class="text-slate-400 text-[10px] block">Satisfaction Score</span>
                            <span class="font-bold text-emerald-500">9.8 / 10 (CSAT)</span>
                        </div>
                    </div>

                    <!-- Timeline events -->
                    <div class="pt-2 border-t border-slate-100 dark:border-zinc-800 space-y-2">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400">Activity Stream</span>
                        <div class="text-[11px] space-y-1.5 text-slate-600 dark:text-zinc-400">
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-[#06B6D4]"></span>
                                <span>Opened Broadcast Campaign "Q3 Product Reveal" (Today, 11:20)</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                <span>Completed Chat Resolution in 3m 42s (Yesterday, 16:04)</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <WebsiteCtaBanner
            :title="$t('Organize your WhatsApp customer relationships today')"
            :subtitle="$t('Instant Setup')"
            :description="$t('Import your contacts, configure tags, and start communicating with total clarity.')"
        />
    </WebsiteLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import WebsiteLayout from '../Layout/WebsiteLayout.vue';
import WebsiteCtaBanner from '../Components/WebsiteCtaBanner.vue';

const props = defineProps({
    companyConfig: Object,
    languages: Object,
    currentLanguage: String,
});
</script>
