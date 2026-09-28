<template>
    <WebsiteLayout
        :companyConfig="companyConfig"
        :languages="languages"
        :currentLanguage="currentLanguage"
        title="Visual WhatsApp Automation & Flow Builder"
        description="Automate 24/7 customer journeys with our drag-and-drop workflow canvas. Keyword triggers, intelligent branching conditions, webhook execution, and smart bot replies."
    >
        <!-- HERO -->
        <section class="relative pt-16 pb-20 sm:pt-24 sm:pb-32 overflow-hidden bg-slate-50/50 dark:bg-[#09090B]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-600 dark:text-emerald-400 mb-6">
                    {{ $t('Flow Builder Engine') }}
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight max-w-4xl mx-auto">
                    <span>{{ $t('Build intelligent WhatsApp workflows') }}</span>
                    <span class="block bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 bg-clip-text text-transparent">
                        {{ $t('without writing a single line of code.') }}
                    </span>
                </h1>
                <p class="mt-6 text-base sm:text-lg text-slate-600 dark:text-zinc-400 max-w-2xl mx-auto leading-relaxed">
                    {{ $t('Automate support triage, lead capture, appointment booking, and FAQs 24/7. Connect triggers, dynamic branching conditions, AI responses, and human handovers on a visual drag-and-drop canvas.') }}
                </p>

                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <Link href="/signup" class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-emerald-600 to-[#06B6D4] text-white font-bold text-xs shadow-lg shadow-emerald-600/20 hover:scale-[1.02] transition-all">
                        {{ $t('Start Building Flows') }}
                    </Link>
                    <Link href="/pricing" class="px-6 py-3.5 rounded-xl bg-white dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-slate-700 dark:text-zinc-300 font-semibold text-xs hover:bg-slate-50 dark:hover:bg-zinc-800 transition-all">
                        {{ $t('View Pricing') }}
                    </Link>
                </div>
            </div>
        </section>

        <!-- VISUAL WORKFLOW CANVAS SHOWCASE -->
        <section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-24">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 mb-2 block">
                    {{ $t('How the Engine Operates') }}
                </span>
                <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">
                    {{ $t('Trigger &rarr; Condition &rarr; Action &rarr; AI &rarr; Response &rarr; End') }}
                </h2>
                <p class="mt-3 text-sm text-slate-600 dark:text-zinc-400">
                    {{ $t('Every incoming WhatsApp event is processed with millisecond latency through our high-performance automation worker queue.') }}
                </p>
            </div>

            <!-- Visual Flow Diagram -->
            <div class="p-8 rounded-3xl bg-slate-50 dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 shadow-xl overflow-x-auto">
                <div class="min-w-[700px] flex items-center justify-between gap-4">
                    <!-- Node 1: Trigger -->
                    <div class="flex-1 p-4 rounded-2xl bg-white dark:bg-[#18181B] border-2 border-emerald-400 dark:border-emerald-600 text-center shadow-md">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300">TRIGGER</span>
                        <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-2">Inbound WhatsApp Keyword</h4>
                        <p class="text-[10px] text-slate-400 mt-1">Customer chats "demo" or "quote"</p>
                    </div>

                    <div class="text-slate-400 font-bold">&rarr;</div>

                    <!-- Node 2: Condition -->
                    <div class="flex-1 p-4 rounded-2xl bg-white dark:bg-[#18181B] border-2 border-cyan-400 dark:border-cyan-600 text-center shadow-md">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-100 dark:bg-cyan-950 text-[#06B6D4]">CONDITION</span>
                        <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-2">Existing Customer?</h4>
                        <p class="text-[10px] text-slate-400 mt-1">Check CRM tags and past orders</p>
                    </div>

                    <div class="text-slate-400 font-bold">&rarr;</div>

                    <!-- Node 3: AI Assistant -->
                    <div class="flex-1 p-4 rounded-2xl bg-white dark:bg-[#18181B] border-2 border-[#8B5CF6] dark:border-[#8B5CF6] text-center shadow-md">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-purple-100 dark:bg-purple-950 text-[#8B5CF6]">AI AGENT</span>
                        <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-2">LLM Intent Triage</h4>
                        <p class="text-[10px] text-slate-400 mt-1">Classify questions & recommend solution</p>
                    </div>

                    <div class="text-slate-400 font-bold">&rarr;</div>

                    <!-- Node 4: Action & Response -->
                    <div class="flex-1 p-4 rounded-2xl bg-white dark:bg-[#18181B] border-2 border-emerald-400 dark:border-emerald-600 text-center shadow-md">
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950 text-emerald-600">ACTION</span>
                        <h4 class="font-bold text-xs text-slate-900 dark:text-white mt-2">Interactive WhatsApp Menu</h4>
                        <p class="text-[10px] text-slate-400 mt-1">Send Quick Reply buttons & book slot</p>
                    </div>
                </div>
            </div>

            <!-- Automation Features Checklist -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="p-6 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 space-y-2">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">{{ $t('Keyword & Intent Triggers') }}</h3>
                    <p class="text-xs text-slate-500 dark:text-zinc-400 leading-relaxed">
                        {{ $t('Match exact keywords, regex patterns, or fuzzy AI intents to initiate flows automatically.') }}
                    </p>
                </div>
                <div class="p-6 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 space-y-2">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">{{ $t('Dynamic Branching Logic') }}</h3>
                    <p class="text-xs text-slate-500 dark:text-zinc-400 leading-relaxed">
                        {{ $t('Route contacts down different paths based on business hours, customer tier, or questionnaire responses.') }}
                    </p>
                </div>
                <div class="p-6 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 space-y-2">
                    <h3 class="font-bold text-sm text-slate-900 dark:text-white">{{ $t('Seamless Human Takeover') }}</h3>
                    <p class="text-xs text-slate-500 dark:text-zinc-400 leading-relaxed">
                        {{ $t('Allow customers to request a human agent at any moment. Bots automatically pause until the ticket is resolved.') }}
                    </p>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <WebsiteCtaBanner
            :title="$t('Put your WhatsApp customer journeys on autopilot')"
            :subtitle="$t('Instant Setup')"
            :description="$t('Start saving hours of manual support time with no-code conversational workflows.')"
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
