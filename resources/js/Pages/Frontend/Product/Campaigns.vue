<template>
    <WebsiteLayout
        :companyConfig="companyConfig"
        :languages="languages"
        :currentLanguage="currentLanguage"
        title="WhatsApp Broadcast Campaigns & Bulk Messaging"
        description="Deliver personalized WhatsApp marketing broadcasts that get opened and read. Dynamic templates, segmentation, scheduling, and real-time delivery analytics."
    >
        <!-- HERO -->
        <section class="relative pt-16 pb-20 sm:pt-24 sm:pb-32 overflow-hidden bg-slate-50/50 dark:bg-[#09090B]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-pink-50 dark:bg-pink-950/40 border border-pink-200 dark:border-pink-800 text-xs font-semibold text-[#EC4899] mb-6">
                    {{ $t('High-ROI Marketing') }}
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight max-w-4xl mx-auto">
                    <span>{{ $t('Deliver campaigns with') }}</span>
                    <span class="block bg-gradient-to-r from-[#EC4899] via-teal-500 to-emerald-500 bg-clip-text text-transparent">
                        {{ $t('98% open rates on WhatsApp.') }}
                    </span>
                </h1>
                <p class="mt-6 text-base sm:text-lg text-slate-600 dark:text-zinc-400 max-w-2xl mx-auto leading-relaxed">
                    {{ $t('Engage your audience where they are already paying attention. Send pre-approved Meta message templates, personalize with dynamic tags, schedule broadcasts, and track real-time conversions.') }}
                </p>

                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <Link href="/signup" class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-pink-600 to-emerald-600 text-white font-bold text-xs shadow-lg shadow-pink-600/20 hover:scale-[1.02] transition-all">
                        {{ $t('Launch Your First Campaign') }}
                    </Link>
                    <Link href="/pricing" class="px-6 py-3.5 rounded-xl bg-white dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-slate-700 dark:text-zinc-300 font-semibold text-xs hover:bg-slate-50 dark:hover:bg-zinc-800 transition-all">
                        {{ $t('View Pricing') }}
                    </Link>
                </div>
            </div>
        </section>

        <!-- CAMPAIGN WORKFLOW -->
        <section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-24">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">
                        {{ $t('Effortless 4-Step Broadcast Launcher') }}
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-zinc-400 leading-relaxed">
                        {{ $t('Creating a high-impact broadcast campaign takes under two minutes. Select your approved Meta template, filter target segments, customize variables, and hit schedule.') }}
                    </p>
                    <div class="space-y-4 text-xs text-slate-700 dark:text-zinc-300">
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 flex items-start gap-3">
                            <span class="w-6 h-6 rounded-lg bg-[#EC4899] text-white flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">1</span>
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white">{{ $t('Select Pre-Approved Meta Template') }}</h4>
                                <p class="text-slate-500 dark:text-zinc-400 text-[11px]">{{ $t('Choose from your library of approved marketing, utility, or authentication templates.') }}</p>
                            </div>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 flex items-start gap-3">
                            <span class="w-6 h-6 rounded-lg bg-teal-600 text-white flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">2</span>
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white">{{ $t('Target Dynamic Segments & Tags') }}</h4>
                                <p class="text-slate-500 dark:text-zinc-400 text-[11px]">{{ $t('Filter recipients by location, lifecycle status, previous purchases, or custom tags.') }}</p>
                            </div>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 flex items-start gap-3">
                            <span class="w-6 h-6 rounded-lg bg-emerald-600 text-white flex items-center justify-center font-bold text-xs shrink-0 mt-0.5">3</span>
                            <div>
                                <h4 class="font-bold text-slate-900 dark:text-white">{{ $t('Inject Personalized Variables') }}</h4>
                                <p class="text-slate-500 dark:text-zinc-400 text-[11px]">{{ $t('Auto-fill template parameters with recipient first name, dynamic discount code, and expiry date.') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Interactive Campaign Card Mockup -->
                <div class="p-6 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 shadow-xl space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-zinc-800">
                        <span class="text-xs font-bold text-slate-900 dark:text-white">Broadcast Preview Simulator</span>
                        <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 dark:bg-emerald-950 px-2 py-0.5 rounded">Meta Approved</span>
                    </div>

                    <!-- WhatsApp Message Preview -->
                    <div class="p-4 rounded-xl bg-[#0b141a] text-white space-y-3 font-sans">
                        <div class="p-3 rounded-2xl rounded-tl-sm bg-[#202c33] max-w-sm text-xs space-y-2">
                            <div class="font-bold text-[#00a884]">Wappiyo Official Store</div>
                            <p class="text-slate-200">
                                Hello <strong>Sarah</strong>! 🎉 Your VIP exclusive 20% discount on the Summer Collection is now live. Use code <strong>SUMMER20</strong> before midnight.
                            </p>
                            <div class="text-[10px] text-slate-400 text-right">10:45 AM &bull; ✓✓</div>
                        </div>
                        <div class="flex gap-2">
                            <button class="flex-1 py-1.5 rounded-lg bg-[#202c33] text-center text-[11px] font-semibold text-[#00a884] border border-slate-700">
                                🛍️ Shop Now
                            </button>
                            <button class="flex-1 py-1.5 rounded-lg bg-[#202c33] text-center text-[11px] font-semibold text-[#00a884] border border-slate-700">
                                💬 Talk to Stylist
                            </button>
                        </div>
                    </div>

                    <!-- Stats banner -->
                    <div class="grid grid-cols-3 gap-2 text-center text-xs pt-2">
                        <div class="p-2 rounded bg-slate-50 dark:bg-[#18181B]">
                            <div class="font-bold text-slate-900 dark:text-white">10,000</div>
                            <div class="text-[10px] text-slate-400">Target Audience</div>
                        </div>
                        <div class="p-2 rounded bg-slate-50 dark:bg-[#18181B]">
                            <div class="font-bold text-emerald-500">99.1%</div>
                            <div class="text-[10px] text-slate-400">Delivery SLA</div>
                        </div>
                        <div class="p-2 rounded bg-slate-50 dark:bg-[#18181B]">
                            <div class="font-bold text-[#EC4899]">3.2s</div>
                            <div class="text-[10px] text-slate-400">Avg. Open Speed</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <WebsiteCtaBanner
            :title="$t('Drive higher conversions with WhatsApp broadcast campaigns')"
            :subtitle="$t('Instant Setup')"
            :description="$t('Start engaging your contact lists today with official Meta-approved broadcast templates.')"
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
