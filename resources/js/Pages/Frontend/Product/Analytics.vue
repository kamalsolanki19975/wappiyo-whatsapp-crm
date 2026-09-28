<template>
    <WebsiteLayout
        :companyConfig="companyConfig"
        :languages="languages"
        :currentLanguage="currentLanguage"
        title="Real-Time WhatsApp Analytics & KPI Dashboards"
        description="Monitor delivery rates, team response times, campaign ROI, and customer growth trends with transparent, actionable WhatsApp analytics."
    >
        <!-- HERO -->
        <section class="relative pt-16 pb-20 sm:pt-24 sm:pb-32 overflow-hidden bg-slate-50/50 dark:bg-[#09090B]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-cyan-50 dark:bg-cyan-950/40 border border-cyan-200 dark:border-cyan-800 text-xs font-semibold text-[#06B6D4] mb-6">
                    {{ $t('Data & Insights') }}
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight max-w-4xl mx-auto">
                    <span>{{ $t('Turn conversation data into') }}</span>
                    <span class="block bg-gradient-to-r from-[#06B6D4] via-emerald-500 to-teal-500 bg-clip-text text-transparent">
                        {{ $t('predictable business growth.') }}
                    </span>
                </h1>
                <p class="mt-6 text-base sm:text-lg text-slate-600 dark:text-zinc-400 max-w-2xl mx-auto leading-relaxed">
                    {{ $t('Know exactly how fast your team responds, which broadcast campaigns generate revenue, and where automation is saving you time.') }}
                </p>

                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <Link href="/signup" class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 hover:scale-[1.02] transition-all">
                        {{ $t('Explore Analytics Dashboard') }}
                    </Link>
                    <Link href="/pricing" class="px-6 py-3.5 rounded-xl bg-white dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-slate-700 dark:text-zinc-300 font-semibold text-xs hover:bg-slate-50 dark:hover:bg-zinc-800 transition-all">
                        {{ $t('View Pricing') }}
                    </Link>
                </div>
            </div>
        </section>

        <!-- ANALYTICS METRICS -->
        <section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-24">
            <!-- 1. Live Message Delivery & Read Rates -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">
                        {{ $t('Broadcast Delivery, Read & Failure Metrics') }}
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-zinc-400 leading-relaxed">
                        {{ $t('Track every sent message status directly from Meta Webhooks. Monitor sent, delivered, read, and failed statuses in real-time to maintain exceptional phone quality.') }}
                    </p>
                    <ul class="space-y-3 text-xs text-slate-700 dark:text-zinc-300">
                        <li class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-cyan-100 dark:bg-cyan-950/60 text-[#06B6D4] flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Granular error breakdown for undelivered messages (e.g. invalid numbers, opt-outs)') }}</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-cyan-100 dark:bg-cyan-950/60 text-[#06B6D4] flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Broadcast template read speed and click-through rates') }}</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-cyan-100 dark:bg-cyan-950/60 text-[#06B6D4] flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Meta Phone Number Quality Rating indicator') }}</span>
                        </li>
                    </ul>
                </div>

                <!-- Visual Charts Mock -->
                <div class="p-6 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 shadow-xl space-y-4">
                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-zinc-800">
                        <span class="text-xs font-bold text-slate-900 dark:text-white">Live Broadcast Health Monitor</span>
                        <span class="text-[10px] text-emerald-500 font-bold bg-emerald-50 dark:bg-emerald-950 px-2 py-0.5 rounded">High Quality Tier</span>
                    </div>

                    <div class="grid grid-cols-2 gap-3 text-xs">
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-[#18181B] border border-slate-200/60 dark:border-zinc-800">
                            <span class="text-slate-400 text-[10px] block">Avg. First Response Time</span>
                            <span class="text-xl font-extrabold text-emerald-600 dark:text-emerald-400">1m 48s</span>
                            <span class="text-[10px] text-emerald-500 block mt-1">&darr; 32% vs last week</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-[#18181B] border border-slate-200/60 dark:border-zinc-800">
                            <span class="text-slate-400 text-[10px] block">Overall Resolution Rate</span>
                            <span class="text-xl font-extrabold text-[#06B6D4]">94.6%</span>
                            <span class="text-[10px] text-emerald-500 block mt-1">&uarr; 4.2% higher</span>
                        </div>
                    </div>

                    <!-- Progress bars -->
                    <div class="space-y-2 pt-2 text-xs">
                        <div>
                            <div class="flex justify-between text-[11px] mb-1">
                                <span>Delivered Messages</span>
                                <span class="font-bold">99.2%</span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-slate-100 dark:bg-zinc-800 overflow-hidden">
                                <div class="h-full bg-emerald-500 rounded-full" style="width: 99.2%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-[11px] mb-1">
                                <span>Read Rate</span>
                                <span class="font-bold">94.8%</span>
                            </div>
                            <div class="w-full h-2 rounded-full bg-slate-100 dark:bg-zinc-800 overflow-hidden">
                                <div class="h-full bg-[#06B6D4] rounded-full" style="width: 94.8%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Team Productivity & Agent Benchmarks -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="order-2 lg:order-1 p-6 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 shadow-xl space-y-3">
                    <span class="text-xs font-bold text-slate-900 dark:text-white block">Agent Leaderboard</span>
                    <div class="space-y-2 text-xs">
                        <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-[#18181B] flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 flex items-center justify-center font-bold text-[10px]">1</span>
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white">Alex Rivera</div>
                                    <div class="text-[10px] text-slate-400">142 tickets resolved</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-emerald-500">1m 12s FRT</div>
                                <div class="text-[10px] text-slate-400">99% CSAT</div>
                            </div>
                        </div>
                        <div class="p-2.5 rounded-lg bg-slate-50 dark:bg-[#18181B] flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-6 h-6 rounded-full bg-slate-200 dark:bg-zinc-800 text-slate-600 flex items-center justify-center font-bold text-[10px]">2</span>
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white">Rachel Green</div>
                                    <div class="text-[10px] text-slate-400">118 tickets resolved</div>
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="font-bold text-emerald-500">1m 30s FRT</div>
                                <div class="text-[10px] text-slate-400">97% CSAT</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="order-1 lg:order-2 space-y-6">
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">
                        {{ $t('Team Performance & Resolution Benchmarks') }}
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-zinc-400 leading-relaxed">
                        {{ $t('Empower managers with clear visibility into agent workload, resolution speed, and customer satisfaction ratings to recognize top performers and balance team distribution.') }}
                    </p>
                    <ul class="space-y-3 text-xs text-slate-700 dark:text-zinc-300">
                        <li class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Individual agent resolution counters and workload distribution') }}</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Peak hour conversation heatmaps for optimal staffing') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <WebsiteCtaBanner
            :title="$t('Gain complete visibility over your WhatsApp channels')"
            :subtitle="$t('Instant Setup')"
            :description="$t('Start tracking delivery rates, customer growth, and agent productivity in real time.')"
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
