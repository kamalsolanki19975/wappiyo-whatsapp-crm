<template>
    <WebsiteLayout
        :companyConfig="companyConfig"
        :languages="languages"
        :currentLanguage="currentLanguage"
        title="AI Assistant for WhatsApp Support & Sales"
        description="Supercharge your customer operations with OpenAI-powered intelligence. Suggested replies, conversation summaries, and automated 24/7 AI conversational support."
    >
        <!-- HERO -->
        <section class="relative pt-16 pb-20 sm:pt-24 sm:pb-32 overflow-hidden bg-slate-50/50 dark:bg-[#09090B]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-teal-50 dark:bg-teal-950/40 border border-teal-200 dark:border-teal-800 text-xs font-semibold text-teal-700 dark:text-teal-300 mb-6">
                    {{ $t('AI-Powered Productivity') }}
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight max-w-4xl mx-auto">
                    <span>{{ $t('Deliver instant, intelligent answers') }}</span>
                    <span class="block bg-gradient-to-r from-teal-500 via-emerald-500 to-cyan-500 bg-clip-text text-transparent">
                        {{ $t('with the Wappiyo AI Assistant.') }}
                    </span>
                </h1>
                <p class="mt-6 text-base sm:text-lg text-slate-600 dark:text-zinc-400 max-w-2xl mx-auto leading-relaxed">
                    {{ $t('Augment human agents with real-time AI reply suggestions, instant chat thread summaries, and automated conversational assistants powered by OpenAI models.') }}
                </p>

                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <Link href="/signup" class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 hover:scale-[1.02] transition-all">
                        {{ $t('Try AI Assistant Free') }}
                    </Link>
                    <Link href="/pricing" class="px-6 py-3.5 rounded-xl bg-white dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-slate-700 dark:text-zinc-300 font-semibold text-xs hover:bg-slate-50 dark:hover:bg-zinc-800 transition-all">
                        {{ $t('View Pricing') }}
                    </Link>
                </div>
            </div>
        </section>

        <!-- AI CAPABILITIES -->
        <section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-24">
            <!-- 1. AI Suggested Replies -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">
                        {{ $t('Smart Suggested Replies for Agents') }}
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-zinc-400 leading-relaxed">
                        {{ $t('Empower your human agents to resolve inquiries 3x faster. The AI assistant analyzes the incoming customer inquiry and drafts an accurate, polite, and contextual reply in under one second.') }}
                    </p>
                    <div class="space-y-3 text-xs text-slate-700 dark:text-zinc-300">
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-purple-100 dark:bg-purple-950/60 text-[#8B5CF6] flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('One-click insertion into the message composer for immediate editing') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-purple-100 dark:bg-purple-950/60 text-[#8B5CF6] flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Maintains your brand voice, tone, and customer greeting etiquette') }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-purple-100 dark:bg-purple-950/60 text-[#8B5CF6] flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Multilingual comprehension across dozens of international languages') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Visual AI Suggestion Demo -->
                <div class="p-6 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 shadow-xl space-y-3">
                    <div class="p-3 rounded-xl bg-slate-100 dark:bg-zinc-800 text-xs text-slate-800 dark:text-zinc-200">
                        "Can you explain your cancellation and refund terms before we sign the annual contract?"
                    </div>
                    <div class="p-3 rounded-xl bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800 text-xs space-y-2">
                        <div class="flex items-center justify-between text-[#8B5CF6] font-bold text-[11px]">
                            <span class="flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                                <span>AI Assistant Suggested Response</span>
                            </span>
                            <span class="text-emerald-600 font-semibold">99% confidence</span>
                        </div>
                        <p class="text-slate-700 dark:text-zinc-300 text-[11px] leading-relaxed">
                            "Hi! We offer a full 14-day money-back guarantee on all plans. If you ever decide to cancel, you can do so directly from your billing portal with no penalty fees."
                        </p>
                        <div class="flex gap-2 pt-1">
                            <button class="px-2.5 py-1 rounded bg-[#8B5CF6] text-white text-[10px] font-bold">Use Draft</button>
                            <button class="px-2.5 py-1 rounded bg-slate-200 dark:bg-zinc-700 text-slate-700 dark:text-zinc-300 text-[10px] font-semibold">Regenerate</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. AI Thread Summarization -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="order-2 lg:order-1 p-6 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 shadow-xl space-y-3">
                    <span class="text-xs font-bold text-slate-900 dark:text-white block">AI Instant Thread Summary</span>
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-[#18181B] border border-slate-200/60 dark:border-zinc-800 text-xs space-y-2">
                        <div class="flex items-center gap-1.5 font-bold text-emerald-600 dark:text-emerald-400">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>Key Takeaway:</span>
                        </div>
                        <p class="text-slate-600 dark:text-zinc-400 text-[11px] leading-relaxed">
                            Client is upgrading from Starter to Pro for 12 agents. Requires custom invoicing in EUR. Approved discount code APPLIED10. Ticket ready for final manager confirmation.
                        </p>
                    </div>
                </div>

                <div class="order-1 lg:order-2 space-y-6">
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">
                        {{ $t('Instant Conversation Summaries') }}
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-zinc-400 leading-relaxed">
                        {{ $t('When handing off a conversation or reviewing an escalated ticket, agents no longer have to scroll through 50 messages. Wappiyo AI distills the context into three crisp bullet points.') }}
                    </p>
                    <ul class="space-y-3 text-xs text-slate-700 dark:text-zinc-300">
                        <li class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Zero shift-change friction between team members') }}</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Automated sentiment analysis (Positive, Neutral, Urgent)') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <WebsiteCtaBanner
            :title="$t('Empower your team with AI customer intelligence')"
            :subtitle="$t('Instant Setup')"
            :description="$t('Connect your OpenAI API key in Wappiyo settings and unlock smart replies and automated workflows.')"
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
