<template>
    <WebsiteLayout
        :companyConfig="companyConfig"
        :languages="languages"
        :currentLanguage="currentLanguage"
        title="Multi-Agent Shared WhatsApp Inbox"
        description="Collaborate seamlessly on customer support and sales. Real-time agent assignment, collision detection, private internal notes, and canned replies from one verified WhatsApp number."
    >
        <!-- HERO -->
        <section class="relative pt-16 pb-20 sm:pt-24 sm:pb-32 overflow-hidden bg-slate-50/50 dark:bg-[#09090B]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-700 dark:text-emerald-300 mb-6">
                    {{ $t('Unified Team Inbox') }}
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight max-w-4xl mx-auto">
                    <span>{{ $t('One WhatsApp number,') }}</span>
                    <span class="block bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 bg-clip-text text-transparent">
                        {{ $t('unlimited agent collaboration.') }}
                    </span>
                </h1>
                <p class="mt-6 text-base sm:text-lg text-slate-600 dark:text-zinc-400 max-w-2xl mx-auto leading-relaxed">
                    {{ $t('Say goodbye to disconnected phones and lost chats. Route inbound WhatsApp conversations to the right agent instantly with full context, internal notes, and collision prevention.') }}
                </p>

                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <Link href="/signup" class="px-8 py-3.5 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 hover:scale-[1.02] transition-all">
                        {{ $t('Start Managing Conversations') }}
                    </Link>
                    <Link href="/pricing" class="px-6 py-3.5 rounded-xl bg-white dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-slate-700 dark:text-zinc-300 font-semibold text-xs hover:bg-slate-50 dark:hover:bg-zinc-800 transition-all">
                        {{ $t('View Pricing') }}
                    </Link>
                </div>
            </div>
        </section>

        <!-- DEEP-DIVE INBOX CAPABILITIES -->
        <section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-24">
            <!-- 1. Collision & Assignment -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">
                        {{ $t('Agent Assignment & Collision Detection') }}
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-zinc-400 leading-relaxed">
                        {{ $t('Prevent awkward customer experiences where two reps reply at the same time. Wappiyo highlights who is actively viewing or typing in any conversation in real time.') }}
                    </p>
                    <div class="space-y-3 text-xs text-slate-700 dark:text-zinc-300">
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">✓</span>
                            <span><strong>{{ $t('Auto-Assignment:') }}</strong> {{ $t('Distribute leads evenly across online agents using round-robin or skill-based routing.') }}</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">✓</span>
                            <span><strong>{{ $t('Collision Protection:') }}</strong> {{ $t('Visual indicator shows when a colleague is drafting a response.') }}</span>
                        </div>
                        <div class="flex items-start gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0 mt-0.5">✓</span>
                            <span><strong>{{ $t('Status Management:') }}</strong> {{ $t('Mark chats Open, Pending, or Resolved with 1 click.') }}</span>
                        </div>
                    </div>
                </div>
                <!-- Visual -->
                <div class="p-6 rounded-2xl bg-slate-50 dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 shadow-lg">
                    <div class="p-4 rounded-xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/50 flex items-center gap-3 text-xs mb-4">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 animate-pulse"></span>
                        <span class="font-semibold text-amber-800 dark:text-amber-300">
                            {{ $t('Sarah is currently typing a reply...') }}
                        </span>
                    </div>
                    <div class="p-4 rounded-xl bg-white dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 space-y-2 text-xs">
                        <div class="flex justify-between font-bold text-slate-900 dark:text-white pb-2 border-b border-slate-100 dark:border-zinc-800">
                            <span>David Miller (+44 7911 123456)</span>
                            <span class="text-emerald-500">Online</span>
                        </div>
                        <p class="text-slate-600 dark:text-zinc-400">"Hello, could we get an update on our shipment delivery date?"</p>
                        <div class="mt-3 pt-2 flex items-center justify-between text-[11px] text-slate-500">
                            <span>Assigned to: <strong>Sarah (Support Team)</strong></span>
                            <span class="px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300">Ticket #1042</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. Internal Notes & Collaboration -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="order-2 lg:order-1 p-6 rounded-2xl bg-slate-50 dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 shadow-lg">
                    <div class="p-4 rounded-xl bg-yellow-50 dark:bg-yellow-950/30 border border-yellow-200 dark:border-yellow-800/50 space-y-2 text-xs">
                        <div class="flex items-center justify-between text-yellow-800 dark:text-yellow-400 font-bold">
                            <span class="flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 20h9"/><path d="M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4L16.5 3.5z"/></svg>
                                <span>Private Internal Team Note</span>
                            </span>
                            <span class="text-[10px]">Visible to team only</span>
                        </div>
                        <p class="text-yellow-900 dark:text-yellow-200">
                            @Alex, customer is asking for a custom volume discount on 100 seats. Let's offer Tier 3 pricing.
                        </p>
                    </div>
                </div>
                <div class="order-1 lg:order-2 space-y-6">
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">
                        {{ $t('Private Internal Notes & Mentions') }}
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-zinc-400 leading-relaxed">
                        {{ $t('Collaborate on complex customer inquiries without switching to Slack or Teams. Mention teammates directly in the chat thread with private notes that the customer never sees.') }}
                    </p>
                    <ul class="space-y-3 text-xs text-slate-700 dark:text-zinc-300">
                        <li class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Seamless handover between support shifts without losing history') }}</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Tag teammates with @mention notifications') }}</span>
                        </li>
                        <li class="flex items-center gap-2">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Keeps CRM notes permanently attached to customer records') }}</span>
                        </li>
                    </ul>
                </div>
            </div>
        </section>

        <!-- CTA -->
        <WebsiteCtaBanner
            :title="$t('Start managing WhatsApp conversations as a team')"
            :subtitle="$t('Instant Setup')"
            :description="$t('Connect your team in minutes. Streamline inquiries, shorten response times, and elevate satisfaction.')"
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
