<template>
    <WebsiteLayout
        :companyConfig="companyConfig"
        :languages="languages"
        :currentLanguage="currentLanguage"
        title="Platform Features — Complete WhatsApp CRM & Messaging Engine"
        description="Explore the comprehensive feature suite of Wappiyo: Shared Inbox, WhatsApp CRM, Flow Builder, Bulk Campaigns, AI Assistant, and Webhooks."
    >
        <!-- HERO -->
        <section class="relative pt-16 pb-20 sm:pt-24 sm:pb-28 overflow-hidden bg-slate-50/50 dark:bg-[#09090B]">
            <div class="absolute inset-0 -z-10 bg-[linear-gradient(to_right,#8080800a_1px,transparent_1px),linear-gradient(to_bottom,#8080800a_1px,transparent_1px)] bg-[size:32px_32px]"></div>
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-700 dark:text-emerald-300 mb-6">
                    {{ $t('Enterprise Capabilities') }}
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight max-w-4xl mx-auto">
                    <span>{{ $t('Engineered for scale, built for') }}</span>
                    <span class="block bg-gradient-to-r from-emerald-600 via-teal-500 to-cyan-500 bg-clip-text text-transparent">
                        {{ $t('high-velocity customer teams.') }}
                    </span>
                </h1>
                <p class="mt-6 text-base sm:text-lg text-slate-600 dark:text-zinc-400 max-w-2xl mx-auto leading-relaxed">
                    {{ $t('Every tool you need to communicate, qualify leads, automate repetitive interactions, and retain customers across WhatsApp.') }}
                </p>

                <div class="mt-8 flex flex-wrap justify-center gap-3">
                    <Link href="/signup" class="px-6 py-3 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 hover:scale-[1.02] transition-all">
                        {{ $t('Start Free Trial') }}
                    </Link>
                    <Link href="/pricing" class="px-6 py-3 rounded-xl bg-white dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-slate-700 dark:text-zinc-300 font-semibold text-xs hover:bg-slate-50 dark:hover:bg-zinc-800 transition-all">
                        {{ $t('View Pricing') }}
                    </Link>
                </div>
            </div>
        </section>

        <!-- ALTERNATING FEATURE SECTIONS -->
        <section class="py-20 space-y-28 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- 1. Shared Inbox -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 text-xs font-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                        <span>{{ $t('Multi-Agent Workspace') }}</span>
                    </div>
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white leading-tight">
                        {{ $t('Shared Team Inbox with Real-Time Routing') }}
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-zinc-400 leading-relaxed">
                        {{ $t('Stop juggling personal WhatsApp Web sessions. Give your whole customer service, sales, and operations team access to one shared verified business number.') }}
                    </p>
                    <ul class="space-y-3 text-xs text-slate-700 dark:text-zinc-300">
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Collision detection prevents agents from drafting duplicate responses') }}</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Private internal team mentions and sticky notes within conversation threads') }}</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Canned responses with keyboard shortcut "/" for instant answers') }}</span>
                        </li>
                    </ul>
                    <div>
                        <Link href="/product/inbox" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                            <span>{{ $t('Deep dive into Shared Inbox') }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                        </Link>
                    </div>
                </div>
                <!-- Visual Card -->
                <div class="p-6 rounded-2xl bg-gradient-to-tr from-emerald-500/10 via-transparent to-cyan-500/10 border border-slate-200/80 dark:border-zinc-800 shadow-xl">
                    <div class="rounded-xl bg-white dark:bg-[#111113] p-5 border border-slate-200/60 dark:border-zinc-800 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-zinc-800 text-xs font-semibold">
                            <span class="text-slate-900 dark:text-white">Active Team Tickets</span>
                            <span class="px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-700 text-[10px]">Round Robin</span>
                        </div>
                        <div class="space-y-2 text-xs">
                            <div class="p-3 rounded-lg bg-slate-50 dark:bg-[#18181B] border border-slate-200/60 dark:border-zinc-800 flex justify-between items-center">
                                <div>
                                    <div class="font-bold text-slate-800 dark:text-white">Order Status Query #892</div>
                                    <div class="text-[10px] text-slate-400">Assigned: Rachel Green &bull; 2m ago</div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] bg-emerald-100 dark:bg-emerald-950 text-emerald-600 font-semibold">In Progress</span>
                            </div>
                            <div class="p-3 rounded-lg bg-slate-50 dark:bg-[#18181B] border border-slate-200/60 dark:border-zinc-800 flex justify-between items-center">
                                <div>
                                    <div class="font-bold text-slate-800 dark:text-white">Enterprise Upgrade Request</div>
                                    <div class="text-[10px] text-slate-400">Assigned: Alex Rivera &bull; 14m ago</div>
                                </div>
                                <span class="px-2 py-0.5 rounded text-[10px] bg-amber-100 dark:bg-amber-950 text-amber-700 font-semibold">Priority</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 2. CRM & Contact Management (Alternating layout) -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <!-- Visual Card First on Desktop -->
                <div class="order-2 lg:order-1 p-6 rounded-2xl bg-gradient-to-tr from-cyan-500/10 via-transparent to-emerald-500/10 border border-slate-200/80 dark:border-zinc-800 shadow-xl">
                    <div class="rounded-xl bg-white dark:bg-[#111113] p-5 border border-slate-200/60 dark:border-zinc-800 space-y-3">
                        <div class="flex items-center gap-3 pb-3 border-b border-slate-100 dark:border-zinc-800">
                            <div class="w-10 h-10 rounded-full bg-[#06B6D4] text-white flex items-center justify-center font-bold text-sm">
                                JD
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white">Johnathan Doe</h4>
                                <p class="text-[11px] text-slate-400">+1 (555) 234-8901 &bull; California, US</p>
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-[11px]">
                            <div class="p-2 rounded bg-slate-50 dark:bg-[#18181B]">
                                <span class="text-slate-400 block text-[9px] uppercase">Segment</span>
                                <span class="font-bold text-[#06B6D4]">High LTV Customer</span>
                            </div>
                            <div class="p-2 rounded bg-slate-50 dark:bg-[#18181B]">
                                <span class="text-slate-400 block text-[9px] uppercase">Total Spent</span>
                                <span class="font-bold text-slate-900 dark:text-white">$4,850.00</span>
                            </div>
                        </div>
                        <div class="pt-2">
                            <span class="text-[10px] text-slate-400 block mb-1">Tags:</span>
                            <div class="flex flex-wrap gap-1">
                                <span class="px-2 py-0.5 rounded text-[10px] bg-emerald-50 dark:bg-emerald-950 text-emerald-700">B2B Wholesale</span>
                                <span class="px-2 py-0.5 rounded text-[10px] bg-cyan-50 dark:bg-cyan-950 text-[#06B6D4]">Repeat Buyer</span>
                                <span class="px-2 py-0.5 rounded text-[10px] bg-emerald-50 dark:bg-emerald-950 text-emerald-600">Verified</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="order-1 lg:order-2 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-cyan-100 dark:bg-cyan-950/60 text-[#06B6D4] text-xs font-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                        <span>{{ $t('Contextual CRM') }}</span>
                    </div>
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white leading-tight">
                        {{ $t('Rich Customer Profiles & Contact Intelligence') }}
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-zinc-400 leading-relaxed">
                        {{ $t('Never ask customers to repeat their order number or history. Full conversation records, contact properties, and activity logs are right beside every message.') }}
                    </p>
                    <ul class="space-y-3 text-xs text-slate-700 dark:text-zinc-300">
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Unlimited custom fields (Order ID, Tier, Account Manager, City)') }}</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Smart segmentation and group filters for laser-targeted campaigns') }}</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Instant CSV import & export with column header mapping') }}</span>
                        </li>
                    </ul>
                    <div>
                        <Link href="/product/crm" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#06B6D4] hover:underline">
                            <span>{{ $t('Discover CRM capabilities') }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- 3. Broadcast Campaigns & Templates -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-pink-100 dark:bg-pink-950/60 text-[#EC4899] text-xs font-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 11 18-5v12L3 14v-3z"/></svg>
                        <span>{{ $t('High-Conversion Broadcasts') }}</span>
                    </div>
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white leading-tight">
                        {{ $t('Official Meta Template Broadcasts with 98% Open Rates') }}
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-zinc-400 leading-relaxed">
                        {{ $t('Replace email spam folders with high-engagement WhatsApp messages. Send rich media templates with interactive Quick Reply buttons and variables.') }}
                    </p>
                    <ul class="space-y-3 text-xs text-slate-700 dark:text-zinc-300">
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Live template status sync with Meta Cloud API approval engine') }}</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Intelligent rate-limiting protects your phone number tier rating') }}</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Schedule broadcasts for specific timezones or immediate dispatch') }}</span>
                        </li>
                    </ul>
                    <div>
                        <Link href="/product/campaigns" class="inline-flex items-center gap-1.5 text-xs font-bold text-[#EC4899] hover:underline">
                            <span>{{ $t('Explore Broadcast Campaigns') }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                        </Link>
                    </div>
                </div>

                <div class="p-6 rounded-2xl bg-gradient-to-tr from-pink-500/10 via-transparent to-purple-500/10 border border-slate-200/80 dark:border-zinc-800 shadow-xl">
                    <div class="rounded-xl bg-white dark:bg-[#111113] p-5 border border-slate-200/60 dark:border-zinc-800 space-y-4">
                        <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-zinc-800">
                            <span class="text-xs font-bold text-slate-900 dark:text-white">Weekend Flash Sale Broadcast</span>
                            <span class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 dark:bg-emerald-950 px-2 py-0.5 rounded">Sent &bull; 100%</span>
                        </div>
                        <div class="grid grid-cols-3 gap-2 text-center text-xs">
                            <div class="p-2 rounded bg-slate-50 dark:bg-[#18181B]">
                                <div class="text-base font-extrabold text-slate-900 dark:text-white">12,450</div>
                                <div class="text-[10px] text-slate-400">Delivered</div>
                            </div>
                            <div class="p-2 rounded bg-slate-50 dark:bg-[#18181B]">
                                <div class="text-base font-extrabold text-[#EC4899]">98.2%</div>
                                <div class="text-[10px] text-slate-400">Read Rate</div>
                            </div>
                            <div class="p-2 rounded bg-slate-50 dark:bg-[#18181B]">
                                <div class="text-base font-extrabold text-emerald-500">24.5%</div>
                                <div class="text-[10px] text-slate-400">CTR</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 4. Visual Flow Automation Engine -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="order-2 lg:order-1 p-6 rounded-2xl bg-gradient-to-tr from-emerald-500/10 via-transparent to-cyan-500/10 border border-slate-200/80 dark:border-zinc-800 shadow-xl">
                    <div class="rounded-xl bg-white dark:bg-[#111113] p-5 border border-slate-200/60 dark:border-zinc-800 space-y-3">
                        <span class="text-xs font-bold text-slate-900 dark:text-white block">Workflow Flow Canvas</span>
                        <div class="space-y-2 text-xs">
                            <div class="p-2.5 rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50/50 dark:bg-emerald-950/30 flex items-center gap-2">
                                <span class="w-6 h-6 rounded bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold">1</span>
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white">Trigger: Customer sends "pricing"</div>
                                    <div class="text-[10px] text-slate-400">Inbound match on exact keyword</div>
                                </div>
                            </div>
                            <div class="flex justify-center text-slate-300 dark:text-zinc-700">&darr;</div>
                            <div class="p-2.5 rounded-lg border border-cyan-200 dark:border-cyan-800 bg-cyan-50/50 dark:bg-cyan-950/30 flex items-center gap-2">
                                <span class="w-6 h-6 rounded bg-[#06B6D4] text-white flex items-center justify-center text-[10px] font-bold">2</span>
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white">Action: Send Interactive Catalog</div>
                                    <div class="text-[10px] text-slate-400">Attach PDF quote & Quick Reply buttons</div>
                                </div>
                            </div>
                            <div class="flex justify-center text-slate-300 dark:text-zinc-700">&darr;</div>
                            <div class="p-2.5 rounded-lg border border-emerald-200 dark:border-emerald-800 bg-emerald-50/50 dark:bg-emerald-950/30 flex items-center gap-2">
                                <span class="w-6 h-6 rounded bg-emerald-500 text-white flex items-center justify-center text-[10px] font-bold">3</span>
                                <div>
                                    <div class="font-bold text-slate-900 dark:text-white">Condition: If clicked "Talk to Human"</div>
                                    <div class="text-[10px] text-slate-400">Assign ticket to Tier 2 Sales Rep</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="order-1 lg:order-2 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 text-xs font-bold">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                        <span>{{ $t('24/7 Automated Workflows') }}</span>
                    </div>
                    <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white leading-tight">
                        {{ $t('Visual Flow Builder: Zero-Code Chatbots & Logic') }}
                    </h2>
                    <p class="text-sm text-slate-600 dark:text-zinc-400 leading-relaxed">
                        {{ $t('Answer FAQs, capture leads, qualify prospects, and route tickets around the clock without keeping human staff on duty 24/7.') }}
                    </p>
                    <ul class="space-y-3 text-xs text-slate-700 dark:text-zinc-300">
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Visual node-based canvas: Triggers, Conditions, Actions, Bot Responses') }}</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Instant contact attribute updates & automated tagging based on choices') }}</span>
                        </li>
                        <li class="flex items-center gap-2.5">
                            <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                            <span>{{ $t('Human agent takeover with instant bot silencing') }}</span>
                        </li>
                    </ul>
                    <div>
                        <Link href="/product/automation" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 hover:underline">
                            <span>{{ $t('Discover Flow Automation') }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- CTA SECTION -->
        <WebsiteCtaBanner
            :title="$t('Experience all platform features risk-free for 14 days')"
            :subtitle="$t('Instant Setup')"
            :description="$t('Sign up in 30 seconds and connect your Meta WhatsApp Cloud API credentials to start exploring.')"
        />
    </WebsiteLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import WebsiteLayout from './Layout/WebsiteLayout.vue';
import WebsiteCtaBanner from './Components/WebsiteCtaBanner.vue';

const props = defineProps({
    companyConfig: Object,
    languages: Object,
    currentLanguage: String,
});
</script>
