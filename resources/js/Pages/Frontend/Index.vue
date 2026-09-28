<template>
    <WebsiteLayout
        :companyConfig="companyConfig"
        :languages="languages"
        :currentLanguage="currentLanguage"
        title="WhatsApp CRM, Team Inbox & Broadcast Automation"
        description="One unified workspace for every WhatsApp customer conversation. Multi-agent shared inbox, automated workflows, verified broadcasts, and CRM integration."
    >
        <!-- HERO SECTION -->
        <section class="relative pt-12 pb-20 sm:pt-20 sm:pb-32 overflow-hidden">
            <!-- Background Glow & Grid -->
            <div class="absolute inset-0 -z-10 bg-[linear-gradient(to_right,#8080800a_1px,transparent_1px),linear-gradient(to_bottom,#8080800a_1px,transparent_1px)] bg-[size:32px_32px]"></div>
            <div class="absolute top-1/4 left-1/2 -translate-x-1/2 -translate-y-1/2 w-[600px] h-[360px] bg-gradient-to-tr from-emerald-500/20 via-teal-500/15 to-cyan-500/15 rounded-full blur-3xl pointer-events-none -z-10"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Announcement Tag -->
                <div class="flex justify-center mb-6">
                    <Link
                        href="/product/automation"
                        class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/80 dark:border-emerald-800/50 text-xs font-semibold text-emerald-700 dark:text-emerald-300 hover:border-emerald-300 transition-colors shadow-sm"
                    >
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping"></span>
                        <span>{{ $t('Next-Gen WhatsApp Business Platform 2026') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    </Link>
                </div>

                <!-- Main Hero Headline -->
                <div class="text-center max-w-4xl mx-auto">
                    <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-[1.1] mb-6">
                        <span>{{ $t('One powerful workspace for every') }}</span>
                        <span class="block bg-gradient-to-r from-emerald-500 via-teal-500 to-cyan-500 bg-clip-text text-transparent">
                            {{ $t('WhatsApp conversation.') }}
                        </span>
                    </h1>
                    <p class="text-base sm:text-lg lg:text-xl text-slate-600 dark:text-zinc-400 max-w-2xl mx-auto leading-relaxed mb-10">
                        {{ $t('Empower your team with a shared multi-agent inbox, drag-and-drop flow automation, targeted bulk broadcasts, deep CRM timeline context, and AI assistance.') }}
                    </p>

                    <!-- CTAs -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                        <Link
                            href="/signup"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2.5 px-8 py-4 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-sm shadow-xl shadow-emerald-600/25 transition-all hover:scale-[1.02] cursor-pointer"
                        >
                            <span>{{ $t('Get Started Free') }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                        </Link>
                        <Link
                            href="/features"
                            class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-4 rounded-xl bg-slate-100 dark:bg-zinc-900 hover:bg-slate-200 dark:hover:bg-zinc-800 text-slate-800 dark:text-zinc-200 font-semibold text-sm border border-slate-200 dark:border-zinc-800 transition-all cursor-pointer"
                        >
                            <span>{{ $t('Explore Platform') }}</span>
                        </Link>
                    </div>

                    <!-- Trust indicators -->
                    <div class="mt-8 flex flex-wrap items-center justify-center gap-6 text-xs text-slate-500 dark:text-zinc-400">
                        <span class="inline-flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>{{ $t('Official Meta Cloud API') }}</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>{{ $t('No Credit Card Required') }}</span>
                        </span>
                        <span class="inline-flex items-center gap-1.5">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                            <span>{{ $t('Unlimited Agent Collaboration') }}</span>
                        </span>
                    </div>
                </div>

                <!-- HERO INTERACTIVE UI PREVIEW -->
                <div class="mt-14 sm:mt-20 relative max-w-6xl mx-auto">
                    <!-- Ambient Glow -->
                    <div class="absolute -inset-1 rounded-3xl bg-gradient-to-r from-emerald-500/20 via-teal-500/20 to-cyan-500/20 blur-xl opacity-70 -z-10"></div>

                    <!-- App Mockup Window -->
                    <div class="rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] shadow-2xl overflow-hidden">
                        <!-- Window Titlebar -->
                        <div class="h-11 px-4 border-b border-slate-200 dark:border-zinc-800 bg-slate-50/80 dark:bg-zinc-900/60 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="w-3 h-3 rounded-full bg-rose-400"></span>
                                <span class="w-3 h-3 rounded-full bg-amber-400"></span>
                                <span class="w-3 h-3 rounded-full bg-emerald-400"></span>
                                <span class="ml-3 text-xs font-mono text-slate-400 dark:text-zinc-500 hidden sm:inline">wappiyo.io/chats/live</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    <span>Cloud API Connected</span>
                                </span>
                            </div>
                        </div>

                        <!-- Mockup Body (3-column layout) -->
                        <div class="grid grid-cols-1 md:grid-cols-12 min-h-[480px]">
                            <!-- Column 1: Conversations List -->
                            <div class="hidden md:block md:col-span-4 border-r border-slate-200 dark:border-zinc-800 bg-slate-50/50 dark:bg-[#0E0E10] p-3 space-y-2">
                                <div class="px-2 py-1.5 flex items-center justify-between">
                                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">Shared Inbox</span>
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300">3 Active</span>
                                </div>

                                <!-- Item 1 (Selected) -->
                                <div class="p-3 rounded-xl bg-white dark:bg-[#18181B] border border-emerald-500/40 dark:border-emerald-500/30 shadow-sm cursor-pointer">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-bold text-slate-900 dark:text-white">Sarah Jenkins</span>
                                        <span class="text-[10px] text-slate-400">14:28</span>
                                    </div>
                                    <p class="text-xs text-slate-600 dark:text-zinc-400 line-clamp-1">Could you confirm the enterprise onboarding schedule?</p>
                                    <div class="flex items-center gap-1.5 mt-2">
                                        <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">VIP Lead</span>
                                        <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded bg-emerald-50 dark:bg-emerald-950 text-emerald-600">Assigned: Alex</span>
                                    </div>
                                </div>

                                <!-- Item 2 -->
                                <div class="p-3 rounded-xl bg-transparent hover:bg-white/50 dark:hover:bg-zinc-800/40 border border-transparent transition-all cursor-pointer">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-medium text-slate-900 dark:text-white">Marcus Vance</span>
                                        <span class="text-[10px] text-slate-400">12:10</span>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-zinc-400 line-clamp-1">Payment receipt received, thank you!</p>
                                    <div class="flex items-center gap-1.5 mt-2">
                                        <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400">Resolved</span>
                                    </div>
                                </div>

                                <!-- Item 3 -->
                                <div class="p-3 rounded-xl bg-transparent hover:bg-white/50 dark:hover:bg-zinc-800/40 border border-transparent transition-all cursor-pointer">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-xs font-medium text-slate-900 dark:text-white">Elena Rostova</span>
                                        <span class="text-[10px] text-slate-400">Yesterday</span>
                                    </div>
                                    <p class="text-xs text-slate-500 dark:text-zinc-400 line-clamp-1">We would like to import 25,000 contacts.</p>
                                    <div class="flex items-center gap-1.5 mt-2">
                                        <span class="text-[9px] font-semibold px-1.5 py-0.5 rounded bg-cyan-50 dark:bg-cyan-950 text-[#06B6D4]">Campaign Reply</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Column 2: Active Chat Thread -->
                            <div class="col-span-1 md:col-span-5 p-4 flex flex-col justify-between bg-white dark:bg-[#111113]">
                                <div>
                                    <!-- Chat Header -->
                                    <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-zinc-800">
                                        <div class="flex items-center gap-2.5">
                                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-emerald-500 to-teal-600 text-white flex items-center justify-center font-bold text-xs">
                                                SJ
                                            </div>
                                            <div>
                                                <h4 class="text-xs font-bold text-slate-900 dark:text-white">Sarah Jenkins</h4>
                                                <p class="text-[10px] text-emerald-600 dark:text-emerald-400 flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                                    <span>WhatsApp Verified Customer</span>
                                                </p>
                                            </div>
                                        </div>
                                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-300">
                                            Assigned to you
                                        </span>
                                    </div>

                                    <!-- Chat Messages -->
                                    <div class="py-4 space-y-3">
                                        <!-- Customer incoming -->
                                        <div class="flex flex-col items-start max-w-[85%]">
                                            <div class="p-3 rounded-2xl rounded-tl-sm bg-slate-100 dark:bg-zinc-800 text-xs text-slate-800 dark:text-zinc-200">
                                                Hi team! We love the demo. Could you confirm if we can start onboarding our 15 support agents this Thursday?
                                            </div>
                                            <span class="text-[9px] text-slate-400 mt-1 ml-1">14:27 PM</span>
                                        </div>

                                        <!-- AI Draft Suggestion Pill -->
                                        <div class="p-2.5 rounded-xl bg-emerald-50/80 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/60 flex items-start gap-2">
                                            <div class="w-5 h-5 rounded-md bg-emerald-600 text-white flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5">
                                                AI
                                            </div>
                                            <div class="flex-1">
                                                <div class="flex items-center justify-between">
                                                    <span class="text-[10px] font-bold text-emerald-700 dark:text-emerald-300">Suggested Smart Reply</span>
                                                    <button type="button" class="text-[9px] font-semibold text-emerald-600 hover:underline">Apply Draft</button>
                                                </div>
                                                <p class="text-[11px] text-slate-600 dark:text-zinc-300 mt-0.5">
                                                    "Absolutely Sarah! We have Thursday 10:00 AM reserved for your team. I will send your calendar invite shortly."
                                                </p>
                                            </div>
                                        </div>

                                        <!-- Agent reply -->
                                        <div class="flex flex-col items-end ml-auto max-w-[85%]">
                                            <div class="p-3 rounded-2xl rounded-tr-sm bg-gradient-to-r from-emerald-600 to-teal-600 text-xs text-white">
                                                Absolutely Sarah! We have Thursday 10:00 AM reserved for your team. I will send your calendar invite shortly.
                                            </div>
                                            <span class="text-[9px] text-slate-400 mt-1 mr-1 flex items-center gap-1">
                                                <span>14:28 PM</span>
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-emerald-300" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="18 6 9 17 4 12"/><polyline points="22 6 13 17 10 14"/></svg>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Chat Input Simulator -->
                                <div class="pt-3 border-t border-slate-100 dark:border-zinc-800">
                                    <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-50 dark:bg-zinc-800/80 border border-slate-200 dark:border-zinc-700">
                                        <input
                                            type="text"
                                            readonly
                                            value="Type a message or press '/' for canned responses..."
                                            class="bg-transparent border-0 text-xs text-slate-400 w-full focus:outline-none"
                                        />
                                        <button class="w-7 h-7 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white flex items-center justify-center shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Column 3: CRM Context & Automation Node -->
                            <div class="hidden md:block md:col-span-3 border-l border-slate-200 dark:border-zinc-800 bg-slate-50/40 dark:bg-[#0D0D0F] p-4 space-y-4">
                                <div>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">Contact CRM Card</span>
                                    <div class="mt-2 space-y-2 text-xs">
                                        <div class="flex justify-between py-1 border-b border-slate-200/60 dark:border-zinc-800/60">
                                            <span class="text-slate-500">Status</span>
                                            <span class="font-semibold text-emerald-600 dark:text-emerald-400">Qualified Lead</span>
                                        </div>
                                        <div class="flex justify-between py-1 border-b border-slate-200/60 dark:border-zinc-800/60">
                                            <span class="text-slate-500">Lifecycle</span>
                                            <span class="font-semibold text-slate-800 dark:text-zinc-200">Onboarding</span>
                                        </div>
                                        <div class="flex justify-between py-1 border-b border-slate-200/60 dark:border-zinc-800/60">
                                            <span class="text-slate-500">Total Value</span>
                                            <span class="font-bold text-emerald-600 dark:text-emerald-400">$1,200/yr</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="pt-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">Active Workflow</span>
                                    <div class="mt-2 p-2.5 rounded-xl bg-white dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-[11px] space-y-1.5">
                                        <div class="flex items-center gap-1.5 font-semibold text-slate-900 dark:text-white">
                                            <span class="w-2 h-2 rounded-full bg-[#06B6D4]"></span>
                                            <span>Inbound Demo Triage</span>
                                        </div>
                                        <p class="text-[10px] text-slate-500 dark:text-zinc-400">
                                            Trigger: Keyword "demo" &rarr; Assign: Alex &rarr; Send Calendar link
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- TRUST & ARCHITECTURE HIGHLIGHTS -->
        <section class="py-12 border-y border-slate-200/80 dark:border-zinc-800 bg-slate-50/60 dark:bg-[#0D0D0F]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <p class="text-center text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500 mb-8">
                    {{ $t('Enterprise Reliability Built Directly on Meta Cloud Infrastructure') }}
                </p>

                <div class="grid grid-cols-2 md:grid-cols-4 gap-6 text-center">
                    <div class="p-4 rounded-xl bg-white dark:bg-[#111113] border border-slate-200/60 dark:border-zinc-800 shadow-sm">
                        <div class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400 mb-1">99.9%</div>
                        <div class="text-xs font-medium text-slate-600 dark:text-zinc-400">{{ $t('Message Delivery SLA') }}</div>
                    </div>
                    <div class="p-4 rounded-xl bg-white dark:bg-[#111113] border border-slate-200/60 dark:border-zinc-800 shadow-sm">
                        <div class="text-2xl font-extrabold text-[#06B6D4] mb-1">&lt; 1s</div>
                        <div class="text-xs font-medium text-slate-600 dark:text-zinc-400">{{ $t('Webhook Real-time Latency') }}</div>
                    </div>
                    <div class="p-4 rounded-xl bg-white dark:bg-[#111113] border border-slate-200/60 dark:border-zinc-800 shadow-sm">
                        <div class="text-2xl font-extrabold text-emerald-500 mb-1">100%</div>
                        <div class="text-xs font-medium text-slate-600 dark:text-zinc-400">{{ $t('Meta Cloud API Compliant') }}</div>
                    </div>
                    <div class="p-4 rounded-xl bg-white dark:bg-[#111113] border border-slate-200/60 dark:border-zinc-800 shadow-sm">
                        <div class="text-2xl font-extrabold text-teal-500 dark:text-teal-400 mb-1">E2E</div>
                        <div class="text-xs font-medium text-slate-600 dark:text-zinc-400">{{ $t('End-to-End Encryption') }}</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- PRODUCT OVERVIEW: 6 CORE PILLARS -->
        <section class="py-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-2 block">
                    {{ $t('Core Architecture') }}
                </span>
                <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    {{ $t('Everything your business needs to master WhatsApp at scale') }}
                </h2>
                <p class="mt-4 text-base text-slate-600 dark:text-zinc-400">
                    {{ $t('From individual conversation triage to automated 50,000-contact broadcasts, Wappiyo unites your entire customer communication lifecycle.') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <!-- 1. Shared Inbox -->
                <div class="group p-8 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 hover:border-emerald-300 dark:hover:border-emerald-800 transition-all hover:shadow-xl shadow-slate-900/5">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">{{ $t('Multi-Agent Shared Inbox') }}</h3>
                    <p class="text-xs text-slate-600 dark:text-zinc-400 leading-relaxed mb-6">
                        {{ $t('Multiple team members collaborate from a single WhatsApp number. Assign conversations, leave private internal notes, and use canned quick replies.') }}
                    </p>
                    <Link href="/product/inbox" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 hover:underline">
                        <span>{{ $t('Discover Shared Inbox') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    </Link>
                </div>

                <!-- 2. WhatsApp CRM -->
                <div class="group p-8 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 hover:border-cyan-300 dark:hover:border-cyan-800 transition-all hover:shadow-xl shadow-slate-900/5">
                    <div class="w-12 h-12 rounded-xl bg-cyan-100 dark:bg-cyan-950/60 text-[#06B6D4] flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">{{ $t('Complete WhatsApp CRM') }}</h3>
                    <p class="text-xs text-slate-600 dark:text-zinc-400 leading-relaxed mb-6">
                        {{ $t('Centralize customer profiles, contact tags, custom fields, lifecycle stages, and conversation timelines with zero context loss.') }}
                    </p>
                    <Link href="/product/crm" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#06B6D4] hover:underline">
                        <span>{{ $t('Explore CRM Features') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    </Link>
                </div>

                <!-- 3. Broadcast Campaigns -->
                <div class="group p-8 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 hover:border-pink-300 dark:hover:border-pink-800 transition-all hover:shadow-xl shadow-slate-900/5">
                    <div class="w-12 h-12 rounded-xl bg-pink-100 dark:bg-pink-950/60 text-[#EC4899] flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">{{ $t('Targeted Broadcast Campaigns') }}</h3>
                    <p class="text-xs text-slate-600 dark:text-zinc-400 leading-relaxed mb-6">
                        {{ $t('Send Meta-approved message templates with dynamic personalized tags. Schedule bulk campaigns and monitor delivery and read analytics in real-time.') }}
                    </p>
                    <Link href="/product/campaigns" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#EC4899] hover:underline">
                        <span>{{ $t('Learn About Campaigns') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    </Link>
                </div>

                <!-- 4. Flow Automation -->
                <div class="group p-8 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 hover:border-emerald-300 dark:hover:border-emerald-800 transition-all hover:shadow-xl shadow-slate-900/5">
                    <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">{{ $t('Visual Flow Automation') }}</h3>
                    <p class="text-xs text-slate-600 dark:text-zinc-400 leading-relaxed mb-6">
                        {{ $t('Build interactive chatbot journeys without code. Configure instant keyword triggers, conditions, branching paths, and automated CRM updates.') }}
                    </p>
                    <Link href="/product/automation" class="inline-flex items-center gap-1.5 text-xs font-semibold text-emerald-600 hover:underline">
                        <span>{{ $t('See Flow Builder') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    </Link>
                </div>

                <!-- 5. AI Assistant -->
                <div class="group p-8 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 hover:border-teal-300 dark:hover:border-teal-800 transition-all hover:shadow-xl shadow-slate-900/5">
                    <div class="w-12 h-12 rounded-xl bg-teal-100 dark:bg-teal-950/60 text-teal-600 flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">{{ $t('AI Smart Assistant') }}</h3>
                    <p class="text-xs text-slate-600 dark:text-zinc-400 leading-relaxed mb-6">
                        {{ $t('Empower agents with instant OpenAI-backed reply suggestions, conversation summarization, and automated intent classification.') }}
                    </p>
                    <Link href="/product/ai" class="inline-flex items-center gap-1.5 text-xs font-semibold text-teal-600 hover:underline">
                        <span>{{ $t('Explore AI Assistant') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    </Link>
                </div>

                <!-- 6. Real-Time Analytics -->
                <div class="group p-8 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 hover:border-cyan-300 dark:hover:border-cyan-800 transition-all hover:shadow-xl shadow-slate-900/5">
                    <div class="w-12 h-12 rounded-xl bg-cyan-100 dark:bg-cyan-950/60 text-[#06B6D4] flex items-center justify-center mb-6 group-hover:scale-110 transition-transform">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2">{{ $t('Actionable Analytics') }}</h3>
                    <p class="text-xs text-slate-600 dark:text-zinc-400 leading-relaxed mb-6">
                        {{ $t('Track agent first response time (FRT), resolution speed, broadcast delivery rates, and contact engagement trends through intuitive dashboards.') }}
                    </p>
                    <Link href="/product/analytics" class="inline-flex items-center gap-1.5 text-xs font-semibold text-[#06B6D4] hover:underline">
                        <span>{{ $t('View Analytics Tour') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    </Link>
                </div>
            </div>
        </section>

        <!-- DYNAMIC PRICING PREVIEW SECTION -->
        <section class="py-24 bg-slate-50/70 dark:bg-[#0E0E10] border-t border-slate-200/80 dark:border-zinc-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-3xl mx-auto mb-16">
                    <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-2 block">
                        {{ $t('Transparent & Scalable') }}
                    </span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                        {{ $t('Simple, transparent pricing that grows with your business') }}
                    </h2>
                    <p class="mt-4 text-base text-slate-600 dark:text-zinc-400">
                        {{ $t('No hidden setup fees. Choose the tier that matches your team and upgrade whenever your message volume expands.') }}
                    </p>

                    <!-- Billing Toggle -->
                    <div class="mt-8 inline-flex items-center p-1 rounded-xl bg-slate-200/80 dark:bg-zinc-800 text-xs font-semibold">
                        <button
                            type="button"
                            @click="billingCycle = 'monthly'"
                            class="px-4 py-2 rounded-lg transition-all"
                            :class="billingCycle === 'monthly' ? 'bg-white dark:bg-[#18181B] text-slate-900 dark:text-white shadow-sm' : 'text-slate-600 dark:text-zinc-400'"
                        >
                            {{ $t('Monthly Billing') }}
                        </button>
                        <button
                            type="button"
                            @click="billingCycle = 'yearly'"
                            class="px-4 py-2 rounded-lg transition-all flex items-center gap-1.5"
                            :class="billingCycle === 'yearly' ? 'bg-white dark:bg-[#18181B] text-slate-900 dark:text-white shadow-sm' : 'text-slate-600 dark:text-zinc-400'"
                        >
                            <span>{{ $t('Yearly Billing') }}</span>
                            <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-600 text-white">Save 20%</span>
                        </button>
                    </div>
                </div>

                <!-- Plans Grid (Dynamically rendered from backend) -->
                <div v-if="props.plans && props.plans.length" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-5xl mx-auto items-stretch">
                    <div
                        v-for="plan in props.plans"
                        :key="plan.uuid || plan.id"
                        class="relative rounded-3xl p-8 flex flex-col justify-between transition-all"
                        :class="isPlanFeatured(plan) 
                            ? 'bg-white dark:bg-[#111113] border-2 border-emerald-500 shadow-2xl shadow-emerald-600/15 ring-4 ring-emerald-600/10' 
                            : 'bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-md'"
                    >
                        <!-- Featured Badge -->
                        <div v-if="isPlanFeatured(plan)" class="absolute -top-3.5 left-1/2 -translate-x-1/2">
                            <span class="px-3.5 py-1 rounded-full text-[11px] font-bold tracking-wider uppercase bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md">
                                {{ $t('Most Popular') }}
                            </span>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="text-xl font-bold text-slate-900 dark:text-white">{{ plan.name }}</h3>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-zinc-400 min-h-[36px] mb-6">
                                {{ getPlanDescription(plan) }}
                            </p>

                            <!-- Price Display -->
                            <div class="mb-6 pb-6 border-b border-slate-100 dark:border-zinc-800">
                                <div class="flex items-baseline gap-1">
                                    <span class="text-4xl font-extrabold text-slate-900 dark:text-white">
                                        {{ formatPlanPrice(plan) }}
                                    </span>
                                    <span class="text-xs text-slate-500 dark:text-zinc-400">
                                        / {{ billingCycle === 'yearly' ? $t('year') : $t('month') }}
                                    </span>
                                </div>
                            </div>

                            <!-- Plan Limits & Capabilities -->
                            <ul class="space-y-3 text-xs text-slate-700 dark:text-zinc-300 mb-8">
                                <li class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    <span>
                                        <strong>{{ plan.contacts_limit || 'Unlimited' }}</strong> {{ $t('Stored Contacts') }}
                                    </span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    <span>
                                        <strong>{{ plan.messages_limit || 'Unlimited' }}</strong> {{ $t('Monthly Outbound Messages') }}
                                    </span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    <span>
                                        <strong>{{ plan.team_limit || 'Unlimited' }}</strong> {{ $t('Team Members / Agents') }}
                                    </span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    <span>{{ $t('Shared Inbox & Tag Routing') }}</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    <span>{{ $t('Visual Flow Builder & Automation') }}</span>
                                </li>
                                <li class="flex items-center gap-2">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                                    <span>{{ $t('Meta Cloud API Integration') }}</span>
                                </li>
                            </ul>
                        </div>

                        <!-- CTA Button leading to signup with plan uuid -->
                        <Link
                            :href="`/signup?plan=${plan.uuid}`"
                            class="w-full inline-flex items-center justify-center gap-2 py-3.5 px-6 rounded-xl font-bold text-xs transition-all text-center"
                            :class="isPlanFeatured(plan)
                                ? 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white shadow-lg shadow-emerald-600/20'
                                : 'bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 text-slate-900 dark:text-white'"
                        >
                            <span>{{ $t('Get Started with') }} {{ plan.name }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                        </Link>
                    </div>
                </div>

                <!-- Link to Full Pricing & Comparison -->
                <div class="mt-12 text-center">
                    <Link
                        href="/pricing"
                        class="inline-flex items-center gap-2 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline"
                    >
                        <span>{{ $t('View full plan comparison & available addons') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    </Link>
                </div>
            </div>
        </section>

        <!-- FREQUENTLY ASKED QUESTIONS SECTION -->
        <section class="py-24 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-2 block">
                    {{ $t('Answers') }}
                </span>
                <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    {{ $t('Frequently Asked Questions') }}
                </h2>
                <p class="mt-3 text-sm text-slate-600 dark:text-zinc-400">
                    {{ $t('Have questions about Wappiyo or WhatsApp Cloud API? Find quick answers here.') }}
                </p>
            </div>

            <div class="space-y-4">
                <div
                    v-for="(item, idx) in defaultFaqs"
                    :key="idx"
                    class="rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] overflow-hidden transition-all"
                >
                    <button
                        type="button"
                        @click="toggleFaq(idx)"
                        class="w-full px-6 py-4 flex items-center justify-between text-left text-sm font-semibold text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors cursor-pointer"
                    >
                        <span>{{ item.q }}</span>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 transition-transform text-slate-400 shrink-0 ml-4"
                            :class="activeFaq === idx ? 'rotate-180 text-emerald-600' : ''"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>
                    <div
                        v-show="activeFaq === idx"
                        class="px-6 pb-5 pt-1 text-xs text-slate-600 dark:text-zinc-400 leading-relaxed border-t border-slate-100 dark:border-zinc-800/60"
                    >
                        {{ item.a }}
                    </div>
                </div>
            </div>

            <div class="mt-8 text-center">
                <Link href="/faq" class="text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:underline">
                    {{ $t('Browse all FAQ categories & documentation &rarr;') }}
                </Link>
            </div>
        </section>

        <!-- CTA CONVERSION SECTION -->
        <WebsiteCtaBanner
            :title="$t('Ready to elevate your customer conversations on WhatsApp?')"
            :subtitle="$t('Instant Setup & 14-Day Free Trial')"
            :description="$t('Join modern organizations delivering faster customer support, automated marketing, and higher conversion rates through Wappiyo.') "
        />
    </WebsiteLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import WebsiteLayout from './Layout/WebsiteLayout.vue';
import WebsiteCtaBanner from './Components/WebsiteCtaBanner.vue';

const props = defineProps({
    companyConfig: Object,
    currency: String,
    plans: {
        type: Array,
        default: () => [],
    },
    addons: {
        type: Array,
        default: () => [],
    },
    faqs: {
        type: Array,
        default: () => [],
    },
    reviews: {
        type: Array,
        default: () => [],
    },
    languages: Object,
    currentLanguage: String,
});

const billingCycle = ref('monthly');
const activeFaq = ref(0);

const toggleFaq = (idx) => {
    activeFaq.value = activeFaq.value === idx ? null : idx;
};

const isPlanFeatured = (plan) => {
    return Boolean(plan.metadata?.featured || plan.featured);
};

const getPlanDescription = (plan) => {
    return plan.metadata?.description || plan.description || 'Complete WhatsApp CRM and messaging suite designed for scaling teams.';
};

const formatPlanPrice = (plan) => {
    const symbol = props.currency || '$';
    if (billingCycle.value === 'yearly') {
        const yearly = plan.metadata?.yearly_price || (parseFloat(plan.price || 0) * 10);
        return `${symbol}${yearly}`;
    }
    return `${symbol}${plan.price || 0}`;
};

const defaultFaqs = [
    {
        q: 'Do I need my own WhatsApp Business API or does Wappiyo provide it?',
        a: 'Wappiyo connects directly with the Official Meta WhatsApp Cloud API. You can bring your existing phone number or register a new verified business number in minutes using the embedded signup.'
    },
    {
        q: 'Can multiple agents log in and reply simultaneously?',
        a: 'Yes! Wappiyo includes a shared multi-agent inbox allowing your entire support and sales teams to collaborate on a single number with conversation assignment, collision protection, and private internal notes.'
    },
    {
        q: 'How do broadcast campaigns comply with Meta guidelines?',
        a: 'All broadcast campaigns use pre-approved WhatsApp message templates submitted through the Meta Cloud API. This protects your phone number quality rating and prevents blocking.'
    },
    {
        q: 'Can I integrate Wappiyo with our existing CRM and tools?',
        a: 'Yes, Wappiyo provides webhooks, REST APIs, and native integrations with popular payment gateways (Stripe, Razorpay) and AI services like OpenAI.'
    },
    {
        q: 'Is there a free trial available?',
        a: 'Yes, every new account can get started with a 14-day free trial. No credit card is required to explore the platform.'
    }
];
</script>