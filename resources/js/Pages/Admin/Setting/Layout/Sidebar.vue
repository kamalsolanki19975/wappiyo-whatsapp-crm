<template>
    <aside class="md:flex flex-col w-72 h-full bg-white dark:bg-[#111113] border-r border-slate-200/80 dark:border-zinc-800 text-slate-800 dark:text-zinc-200 hidden shrink-0 transition-colors">
        <div class="flex-1 overflow-y-auto px-4 py-6 space-y-4">
            <div>
                <h2 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500 px-3 mb-2">
                    {{ $t('System Configuration') }}
                </h2>
                <ul class="space-y-1 text-xs font-medium">
                    <!-- General Settings -->
                    <li>
                        <Link 
                            href="/admin/settings/general" 
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all"
                            :class="$page.url.startsWith('/admin/settings/general') 
                                ? 'bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] text-white shadow-sm shadow-indigo-500/20 font-semibold' 
                                : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-zinc-800/60'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"></circle><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"></path></svg>
                            <span>{{ $t('General Settings') }}</span>
                        </Link>
                    </li>

                    <!-- Timezone & Currency -->
                    <li>
                        <Link 
                            href="/admin/settings/timezone" 
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all"
                            :class="$page.url.startsWith('/admin/settings/timezone') 
                                ? 'bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] text-white shadow-sm shadow-indigo-500/20 font-semibold' 
                                : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-zinc-800/60'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                            <span>{{ $t('Timezone & Currency') }}</span>
                        </Link>
                    </li>

                    <!-- Broadcast Drivers -->
                    <li>
                        <Link 
                            href="/admin/settings/broadcast-drivers" 
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all"
                            :class="$page.url.startsWith('/admin/settings/broadcast-drivers') 
                                ? 'bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] text-white shadow-sm shadow-indigo-500/20 font-semibold' 
                                : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-zinc-800/60'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4.9 19.1C1 15.2 1 8.8 4.9 4.9"></path><path d="M7.8 16.2c-2.3-2.3-2.3-6.1 0-8.5"></path><circle cx="12" cy="12" r="2"></circle><path d="M16.2 7.8c2.3 2.3 2.3 6.1 0 8.5"></path><path d="M19.1 4.9C23 8.8 23 15.1 19.1 19"></path></svg>
                            <span>{{ $t('Broadcast Driver') }}</span>
                        </Link>
                    </li>

                    <!-- Social Login -->
                    <li>
                        <Link 
                            href="/admin/settings/socials" 
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all"
                            :class="$page.url.startsWith('/admin/settings/socials') 
                                ? 'bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] text-white shadow-sm shadow-indigo-500/20 font-semibold' 
                                : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-zinc-800/60'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                            <span>{{ $t('Social Login (OAuth)') }}</span>
                        </Link>
                    </li>

                    <!-- Storage Settings -->
                    <li>
                        <Link 
                            href="/admin/settings/storage" 
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all"
                            :class="$page.url.startsWith('/admin/settings/storage') 
                                ? 'bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] text-white shadow-sm shadow-indigo-500/20 font-semibold' 
                                : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-zinc-800/60'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"></ellipse><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"></path><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"></path></svg>
                            <span>{{ $t('Storage & Media (S3)') }}</span>
                        </Link>
                    </li>

                    <!-- Payment Gateways -->
                    <li>
                        <Link 
                            href="/admin/payment-gateways" 
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all"
                            :class="$page.url.startsWith('/admin/payment-gateways') 
                                ? 'bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] text-white shadow-sm shadow-indigo-500/20 font-semibold' 
                                : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-zinc-800/60'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>
                            <span>{{ $t('Payment Gateways') }}</span>
                        </Link>
                    </li>

                    <!-- SMTP Email -->
                    <li>
                        <Link 
                            href="/admin/settings/smtp" 
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all"
                            :class="$page.url.startsWith('/admin/settings/smtp') 
                                ? 'bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] text-white shadow-sm shadow-indigo-500/20 font-semibold' 
                                : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-zinc-800/60'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                            <span>{{ $t('SMTP & Mail Server') }}</span>
                        </Link>
                    </li>

                    <!-- Email Templates -->
                    <li>
                        <Link 
                            href="/admin/settings/email-templates" 
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all"
                            :class="$page.url.startsWith('/admin/settings/email-templates') 
                                ? 'bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] text-white shadow-sm shadow-indigo-500/20 font-semibold' 
                                : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-zinc-800/60'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="22 12 16 12 14 15 10 15 8 12 2 12"></polyline><path d="M5.45 5.11L2 12v6a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-6l-3.45-6.89A2 2 0 0 0 16.76 4H7.24a2 2 0 0 0-1.79 1.11z"></path></svg>
                            <span>{{ $t('Email Templates') }}</span>
                        </Link>
                    </li>

                    <!-- Subscription Settings -->
                    <li>
                        <Link 
                            href="/admin/settings/subscription" 
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all"
                            :class="$page.url.startsWith('/admin/settings/subscription') 
                                ? 'bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] text-white shadow-sm shadow-indigo-500/20 font-semibold' 
                                : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-zinc-800/60'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            <span>{{ $t('Subscription Settings') }}</span>
                        </Link>
                    </li>

                    <!-- Billing Info -->
                    <li>
                        <Link 
                            href="/admin/settings/billing" 
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all"
                            :class="$page.url.startsWith('/admin/settings/billing') 
                                ? 'bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] text-white shadow-sm shadow-indigo-500/20 font-semibold' 
                                : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-zinc-800/60'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path><polyline points="14 2 14 8 20 8"></polyline><line x1="16" y1="13" x2="8" y2="13"></line><line x1="16" y1="17" x2="8" y2="17"></line><polyline points="10 9 9 9 8 9"></polyline></svg>
                            <span>{{ $t('Platform Billing Info') }}</span>
                        </Link>
                    </li>

                    <!-- Tax Rates -->
                    <li>
                        <Link 
                            href="/admin/tax-rates" 
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all"
                            :class="$page.url.startsWith('/admin/tax-rates') 
                                ? 'bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] text-white shadow-sm shadow-indigo-500/20 font-semibold' 
                                : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-zinc-800/60'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="19" y1="5" x2="5" y2="19"></line><circle cx="6.5" cy="6.5" r="2.5"></circle><circle cx="17.5" cy="17.5" r="2.5"></circle></svg>
                            <span>{{ $t('Tax Rates') }}</span>
                        </Link>
                    </li>

                    <!-- Coupons -->
                    <li>
                        <Link 
                            href="/admin/coupons" 
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all"
                            :class="$page.url.startsWith('/admin/coupons') 
                                ? 'bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] text-white shadow-sm shadow-indigo-500/20 font-semibold' 
                                : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-zinc-800/60'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
                            <span>{{ $t('Discount Coupons') }}</span>
                        </Link>
                    </li>

                    <!-- Languages -->
                    <li>
                        <Link 
                            href="/admin/languages" 
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl transition-all"
                            :class="$page.url.startsWith('/admin/languages') 
                                ? 'bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] text-white shadow-sm shadow-indigo-500/20 font-semibold' 
                                : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-zinc-800/60'"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="2" y1="12" x2="22" y2="12"></line><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"></path></svg>
                            <span>{{ $t('Languages & Localization') }}</span>
                        </Link>
                    </li>
                </ul>
            </div>
        </div>

        <!-- Version Footer -->
        <div class="p-4 border-t border-slate-200/80 dark:border-zinc-800/80">
            <div class="px-3 py-2 rounded-xl bg-slate-50 dark:bg-zinc-800/50 border border-slate-200/60 dark:border-zinc-700/60 flex items-center justify-between text-[11px] text-slate-500 dark:text-zinc-400">
                <span>{{ $t('Wappiyo Platform') }}</span>
                <span class="font-mono font-semibold text-slate-700 dark:text-zinc-300">v{{ version || '2.0.0' }}</span>
            </div>
        </div>
    </aside>
</template>

<script setup>
    import { Link, usePage } from "@inertiajs/vue3";
    import { computed } from "vue";

    const version = computed(() => usePage().props.applicationVersion);
</script>