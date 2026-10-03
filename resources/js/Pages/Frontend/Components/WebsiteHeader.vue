<template>
    <header class="sticky top-0 z-50 w-full transition-all duration-200 bg-white/95 dark:bg-black backdrop-blur-md border-b border-slate-200/80 dark:border-neutral-900 shadow-xs dark:shadow-none">
        <!-- Top Announcement Bar (Subtle) -->
        <div class="hidden sm:flex items-center justify-between px-6 py-1.5 bg-gradient-to-r from-emerald-500/10 via-teal-500/10 to-cyan-500/10 dark:from-emerald-950/20 dark:via-black dark:to-cyan-950/20 border-b border-slate-200/50 dark:border-neutral-900 text-[11px] font-medium text-slate-600 dark:text-zinc-400 dark:bg-black">
            <div class="flex items-center gap-2 mx-auto">
                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-600 text-white">
                    {{ $t('NEW') }}
                </span>
                <span>{{ $t('Next-gen WhatsApp Automation & Flow Builder is now live') }}</span>
                <Link href="/product/automation" class="font-semibold text-emerald-600 dark:text-emerald-400 hover:underline inline-flex items-center gap-0.5">
                    <span>{{ $t('Explore automation') }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                </Link>
            </div>
            <div class="hidden lg:flex items-center gap-3">
                <LangToggle v-if="languages && Object.keys(languages).length" :languages="languages" :currentLanguage="currentLanguage" class="text-xs"/>
            </div>
        </div>

        <nav class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Identity -->
            <div class="flex items-center gap-8">
                <Link href="/" class="flex items-center gap-2.5 focus:outline-none">
                    <BrandLogo :custom-logo="companyConfig?.logo" :company-name="companyConfig?.company_name || 'Wappiyo'" mode="auto" />
                </Link>

                <!-- Desktop Mega-Menu Nav Links -->
                <div class="hidden lg:flex items-center gap-1">
                    <!-- Product Dropdown -->
                    <div class="relative" @mouseenter="openMenu('product')" @mouseleave="scheduleCloseMenu('product')">
                        <button
                            type="button"
                            class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-zinc-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-zinc-800/60 transition-colors"
                        >
                            <span>{{ $t('Product') }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 transition-transform duration-200" :class="activeDropdown === 'product' ? 'rotate-180 text-emerald-600' : 'text-slate-400'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                        </button>

                        <div
                            v-show="activeDropdown === 'product'"
                            class="absolute top-full left-0 w-[540px] bg-white dark:bg-[#0A0A0A] rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-black/70 border border-slate-200/80 dark:border-neutral-800 p-4 grid grid-cols-2 gap-2 mt-1 z-50"
                        >
                            <Link
                                href="/product/inbox"
                                class="flex items-start gap-3 p-3 rounded-xl hover:bg-emerald-50/50 dark:hover:bg-emerald-950/20 transition-all group"
                                @click="activeDropdown = null"
                            >
                                <div class="w-9 h-9 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-semibold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $t('Shared Inbox') }}</h4>
                                    <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5 leading-snug">{{ $t('Multi-agent team routing & collaborative chats') }}</p>
                                </div>
                            </Link>

                            <Link
                                href="/product/crm"
                                class="flex items-start gap-3 p-3 rounded-xl hover:bg-emerald-50/50 dark:hover:bg-emerald-950/20 transition-all group"
                                @click="activeDropdown = null"
                            >
                                <div class="w-9 h-9 rounded-lg bg-cyan-100 dark:bg-cyan-950/60 text-[#06B6D4] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-semibold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $t('WhatsApp CRM') }}</h4>
                                    <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5 leading-snug">{{ $t('Complete customer timeline, tags & custom fields') }}</p>
                                </div>
                            </Link>

                            <Link
                                href="/product/campaigns"
                                class="flex items-start gap-3 p-3 rounded-xl hover:bg-emerald-50/50 dark:hover:bg-emerald-950/20 transition-all group"
                                @click="activeDropdown = null"
                            >
                                <div class="w-9 h-9 rounded-lg bg-pink-100 dark:bg-pink-950/60 text-[#EC4899] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-semibold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $t('Broadcast Campaigns') }}</h4>
                                    <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5 leading-snug">{{ $t('High-converting targeted WhatsApp blasts') }}</p>
                                </div>
                            </Link>

                            <Link
                                href="/product/automation"
                                class="flex items-start gap-3 p-3 rounded-xl hover:bg-emerald-50/50 dark:hover:bg-emerald-950/20 transition-all group"
                                @click="activeDropdown = null"
                            >
                                <div class="w-9 h-9 rounded-lg bg-teal-100 dark:bg-teal-950/60 text-teal-600 flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="8" height="8" x="3" y="3" rx="2"/><path d="M7 11v4a2 2 0 0 0 2 2h4"/><rect width="8" height="8" x="13" y="13" rx="2"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-semibold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $t('Flow Builder') }}</h4>
                                    <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5 leading-snug">{{ $t('Interactive visual workflow automation') }}</p>
                                </div>
                            </Link>

                            <Link
                                href="/product/ai"
                                class="flex items-start gap-3 p-3 rounded-xl hover:bg-emerald-50/50 dark:hover:bg-emerald-950/20 transition-all group"
                                @click="activeDropdown = null"
                            >
                                <div class="w-9 h-9 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-[#22C55E] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-semibold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $t('AI Assistant') }}</h4>
                                    <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5 leading-snug">{{ $t('Smart AI suggested replies & auto-responder') }}</p>
                                </div>
                            </Link>

                            <Link
                                href="/product/analytics"
                                class="flex items-start gap-3 p-3 rounded-xl hover:bg-emerald-50/50 dark:hover:bg-emerald-950/20 transition-all group"
                                @click="activeDropdown = null"
                            >
                                <div class="w-9 h-9 rounded-lg bg-orange-100 dark:bg-orange-950/60 text-[#F97316] flex items-center justify-center shrink-0 group-hover:scale-105 transition-transform">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-xs font-semibold text-slate-900 dark:text-white group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">{{ $t('Analytics') }}</h4>
                                    <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5 leading-snug">{{ $t('Live delivery rates, response times & KPIs') }}</p>
                                </div>
                            </Link>
                        </div>
                    </div>

                    <!-- Features Link -->
                    <Link
                        href="/features"
                        :class="[
                            'px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                            $page.url.startsWith('/features')
                                ? 'text-emerald-600 dark:text-emerald-400 font-semibold'
                                : 'text-slate-600 dark:text-zinc-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-zinc-800/60'
                        ]"
                    >
                        {{ $t('Features') }}
                    </Link>

                    <!-- Integrations Link -->
                    <Link
                        href="/integrations"
                        :class="[
                            'px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                            $page.url.startsWith('/integrations')
                                ? 'text-emerald-600 dark:text-emerald-400 font-semibold'
                                : 'text-slate-600 dark:text-zinc-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-zinc-800/60'
                        ]"
                    >
                        {{ $t('Integrations') }}
                    </Link>

                    <!-- Pricing Link -->
                    <Link
                        href="/pricing"
                        :class="[
                            'px-3 py-2 rounded-lg text-sm font-medium transition-colors',
                            $page.url.startsWith('/pricing')
                                ? 'text-emerald-600 dark:text-emerald-400 font-semibold'
                                : 'text-slate-600 dark:text-zinc-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-zinc-800/60'
                        ]"
                    >
                        {{ $t('Pricing') }}
                    </Link>

                    <!-- Resources Dropdown -->
                    <div class="relative" @mouseenter="openMenu('resources')" @mouseleave="scheduleCloseMenu('resources')">
                        <button
                            type="button"
                            class="flex items-center gap-1.5 px-3 py-2 rounded-lg text-sm font-medium text-slate-600 dark:text-zinc-300 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100/70 dark:hover:bg-zinc-800/60 transition-colors"
                        >
                            <span>{{ $t('Resources') }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 transition-transform duration-200" :class="activeDropdown === 'resources' ? 'rotate-180 text-emerald-600' : 'text-slate-400'" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
                        </button>

                        <div
                            v-show="activeDropdown === 'resources'"
                            class="absolute top-full right-0 w-[260px] bg-white dark:bg-[#0A0A0A] rounded-2xl shadow-xl shadow-slate-900/10 dark:shadow-black/70 border border-slate-200/80 dark:border-neutral-800 p-2 space-y-1 mt-1 z-50"
                        >
                            <Link
                                href="/faq"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-zinc-300 hover:bg-emerald-50/60 dark:hover:bg-emerald-950/30 hover:text-emerald-600 dark:hover:text-emerald-300 transition-colors"
                                @click="activeDropdown = null"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                                <span>{{ $t('FAQ & Knowledgebase') }}</span>
                            </Link>
                            <Link
                                href="/about"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-zinc-300 hover:bg-emerald-50/60 dark:hover:bg-emerald-950/30 hover:text-emerald-600 dark:hover:text-emerald-300 transition-colors"
                                @click="activeDropdown = null"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/></svg>
                                <span>{{ $t('About Wappiyo') }}</span>
                            </Link>
                            <Link
                                href="/contact"
                                class="flex items-center gap-2.5 px-3 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-zinc-300 hover:bg-emerald-50/60 dark:hover:bg-emerald-950/30 hover:text-emerald-600 dark:hover:text-emerald-300 transition-colors"
                                @click="activeDropdown = null"
                            >
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                <span>{{ $t('Contact & Support') }}</span>
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Controls: Theme Toggle & Auth State -->
            <div class="flex items-center gap-3">
                <!-- Theme Toggle Button -->
                <button
                    type="button"
                    class="p-2 rounded-xl text-slate-600 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors focus:outline-none"
                    @click="toggleTheme"
                    :title="isDark ? $t('Switch to Light Mode') : $t('Switch to Dark Mode')"
                    aria-label="Theme toggle"
                >
                    <!-- Sun icon for dark mode -->
                    <svg v-if="isDark" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="4"/><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/></svg>
                    <!-- Moon icon for light mode -->
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 3a6 6 0 0 0 9 9 9 9 0 1 1-9-9Z"/></svg>
                </button>

                <!-- Authenticated state vs Guest -->
                <template v-if="authUser && authUser.id">
                    <Link
                        :href="authUser.role === 'admin' ? '/admin/dashboard' : '/dashboard'"
                        class="hidden sm:inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm shadow-emerald-600/30 transition-all cursor-pointer"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                        <span>{{ $t('Go to Workspace') }}</span>
                    </Link>
                </template>
                <template v-else>
                    <Link
                        href="/login"
                        class="hidden sm:inline-flex px-3.5 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-zinc-200 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors"
                    >
                        {{ $t('Sign In') }}
                    </Link>
                    <Link
                        href="/signup"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold shadow-sm shadow-emerald-600/30 transition-all cursor-pointer"
                    >
                        <span>{{ $t('Get Started') }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    </Link>
                </template>

                <!-- Mobile Menu Button -->
                <button
                    type="button"
                    class="p-2 rounded-xl text-slate-700 dark:text-zinc-300 lg:hidden hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors focus:outline-none"
                    @click="isMobileMenuOpen = !isMobileMenuOpen"
                    aria-label="Toggle navigation"
                >
                    <svg v-if="!isMobileMenuOpen" xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/></svg>
                    <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
                </button>
            </div>
        </nav>

        <!-- Mobile Drawer Navigation -->
        <div
            v-if="isMobileMenuOpen"
            class="lg:hidden border-t border-slate-200/80 dark:border-neutral-900 bg-white/95 dark:bg-black backdrop-blur-xl px-4 py-5 space-y-4 max-h-[calc(100vh-4rem)] overflow-y-auto"
        >
            <div class="space-y-1">
                <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500 px-3 py-1 select-none">
                    {{ $t('Product') }}
                </div>
                <Link
                    href="/product/inbox"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-slate-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800"
                    @click="isMobileMenuOpen = false"
                >
                    <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                    </div>
                    <span>{{ $t('Shared Inbox') }}</span>
                </Link>
                <Link
                    href="/product/crm"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-slate-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800"
                    @click="isMobileMenuOpen = false"
                >
                    <div class="w-7 h-7 rounded-lg bg-cyan-100 dark:bg-cyan-950/60 text-[#06B6D4] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    </div>
                    <span>{{ $t('WhatsApp CRM') }}</span>
                </Link>
                <Link
                    href="/product/campaigns"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-slate-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800"
                    @click="isMobileMenuOpen = false"
                >
                    <div class="w-7 h-7 rounded-lg bg-pink-100 dark:bg-pink-950/60 text-[#EC4899] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 11 18-5v12L3 14v-3z"/></svg>
                    </div>
                    <span>{{ $t('Broadcast Campaigns') }}</span>
                </Link>
                <Link
                    href="/product/automation"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-slate-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800"
                    @click="isMobileMenuOpen = false"
                >
                    <div class="w-7 h-7 rounded-lg bg-violet-100 dark:bg-violet-950/60 text-[#8B5CF6] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="8" height="8" x="3" y="3" rx="2"/></svg>
                    </div>
                    <span>{{ $t('Flow Builder') }}</span>
                </Link>
                <Link
                    href="/product/ai"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-slate-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800"
                    @click="isMobileMenuOpen = false"
                >
                    <div class="w-7 h-7 rounded-lg bg-emerald-100 dark:bg-emerald-950/60 text-[#22C55E] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"/></svg>
                    </div>
                    <span>{{ $t('AI Assistant') }}</span>
                </Link>
                <Link
                    href="/product/analytics"
                    class="flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium text-slate-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800"
                    @click="isMobileMenuOpen = false"
                >
                    <div class="w-7 h-7 rounded-lg bg-orange-100 dark:bg-orange-950/60 text-[#F97316] flex items-center justify-center">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                    </div>
                    <span>{{ $t('Analytics') }}</span>
                </Link>
            </div>

            <div class="pt-2 border-t border-slate-200/80 dark:border-zinc-800 space-y-1">
                <Link
                    href="/features"
                    class="block px-3 py-2 rounded-xl text-sm font-medium text-slate-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800"
                    @click="isMobileMenuOpen = false"
                >
                    {{ $t('Features') }}
                </Link>
                <Link
                    href="/integrations"
                    class="block px-3 py-2 rounded-xl text-sm font-medium text-slate-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800"
                    @click="isMobileMenuOpen = false"
                >
                    {{ $t('Integrations') }}
                </Link>
                <Link
                    href="/pricing"
                    class="block px-3 py-2 rounded-xl text-sm font-medium text-slate-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800"
                    @click="isMobileMenuOpen = false"
                >
                    {{ $t('Pricing') }}
                </Link>
                <Link
                    href="/faq"
                    class="block px-3 py-2 rounded-xl text-sm font-medium text-slate-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800"
                    @click="isMobileMenuOpen = false"
                >
                    {{ $t('FAQ') }}
                </Link>
                <Link
                    href="/about"
                    class="block px-3 py-2 rounded-xl text-sm font-medium text-slate-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800"
                    @click="isMobileMenuOpen = false"
                >
                    {{ $t('About Wappiyo') }}
                </Link>
                <Link
                    href="/contact"
                    class="block px-3 py-2 rounded-xl text-sm font-medium text-slate-700 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800"
                    @click="isMobileMenuOpen = false"
                >
                    {{ $t('Contact Us') }}
                </Link>
            </div>

            <!-- Mobile Auth Actions -->
            <div class="pt-4 border-t border-slate-200/80 dark:border-zinc-800 flex flex-col gap-2">
                <template v-if="authUser && authUser.id">
                    <Link
                        :href="authUser.role === 'admin' ? '/admin/dashboard' : '/dashboard'"
                        class="w-full text-center py-2.5 rounded-xl bg-emerald-600 text-white font-semibold text-sm shadow-sm"
                        @click="isMobileMenuOpen = false"
                    >
                        {{ $t('Go to Workspace') }}
                    </Link>
                </template>
                <template v-else>
                    <Link
                        href="/login"
                        class="w-full text-center py-2.5 rounded-xl border border-slate-200 dark:border-zinc-700 text-slate-800 dark:text-zinc-200 font-semibold text-sm"
                        @click="isMobileMenuOpen = false"
                    >
                        {{ $t('Sign In') }}
                    </Link>
                    <Link
                        href="/signup"
                        class="w-full text-center py-2.5 rounded-xl bg-emerald-600 text-white font-semibold text-sm shadow-sm shadow-emerald-600/30"
                        @click="isMobileMenuOpen = false"
                    >
                        {{ $t('Get Started Free') }}
                    </Link>
                </template>
            </div>
        </div>
    </header>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import BrandLogo from '@/Components/UI/BrandLogo.vue';
import LangToggle from '@/Components/LangToggle.vue';
import { useTheme } from '@/Composables/useTheme';

const props = defineProps({
    companyConfig: {
        type: Object,
        default: () => ({}),
    },
});

const { isDark, toggleTheme } = useTheme();

const authUser = computed(() => usePage().props.auth?.user || null);
const languages = computed(() => usePage().props.languages || {});
const currentLanguage = computed(() => usePage().props.currentLanguage || 'en');

const isMobileMenuOpen = ref(false);
const activeDropdown = ref(null);
let closeTimer = null;

const openMenu = (menu) => {
    if (closeTimer) clearTimeout(closeTimer);
    activeDropdown.value = menu;
};

const scheduleCloseMenu = () => {
    closeTimer = setTimeout(() => {
        activeDropdown.value = null;
    }, 200);
};
</script>
