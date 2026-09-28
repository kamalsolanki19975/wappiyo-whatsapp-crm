<template>
    <div class="min-h-screen bg-slate-50 dark:bg-[#070709] text-slate-900 dark:text-zinc-100 flex flex-col lg:flex-row items-stretch justify-between relative overflow-x-hidden selection:bg-emerald-500 selection:text-white transition-colors duration-200">
        
        <!-- ======================================================== -->
        <!-- AMBIENT BACKGROUND GLOWS & GRADIENT ORBS (Brand Emerald & Dark Teal) -->
        <!-- ======================================================== -->
        <div class="login-orb orb-1 absolute -top-40 -left-20 w-[550px] h-[550px] rounded-full bg-gradient-to-tr from-[#22C55E]/20 via-[#10B981]/15 to-transparent blur-[120px] pointer-events-none -z-10"></div>
        <div class="login-orb orb-2 absolute top-1/2 left-1/3 -translate-y-1/2 w-[480px] h-[480px] rounded-full bg-gradient-to-br from-[#022828]/25 via-[#064E3B]/20 to-transparent blur-[140px] pointer-events-none -z-10"></div>
        <div class="login-orb orb-3 absolute -bottom-32 right-0 w-[500px] h-[500px] rounded-full bg-gradient-to-tl from-[#34D399]/20 via-[#22C55E]/15 to-transparent blur-[130px] pointer-events-none -z-10"></div>

        <!-- ======================================================== -->
        <!-- LEFT COLUMN: CREATIVE VISUAL SECTION (Desktop & Tablet) -->
        <!-- ======================================================== -->
        <div class="hidden lg:flex lg:w-[52%] xl:w-[56%] flex-col justify-between p-10 xl:p-14 relative border-r border-slate-200/70 dark:border-white/5 overflow-hidden">
            <!-- Subtle Grid Pattern Overlay -->
            <div class="absolute inset-0 bg-[linear-gradient(to_right,#80808012_1px,transparent_1px),linear-gradient(to_bottom,#80808012_1px,transparent_1px)] bg-[size:32px_32px] [mask-image:radial-gradient(ellipse_60%_50%_at_50%_50%,#000_70%,transparent_100%)] pointer-events-none"></div>

            <!-- Top Header & Brand Logo -->
            <div class="relative z-10 flex items-center justify-between">
                <Link href="/" class="flex items-center gap-3 group focus:outline-none">
                    <BrandLogo :custom-logo="props.companyConfig?.logo" :company-name="props.companyConfig?.company_name || 'Wappiyo'" mode="auto" />
                </Link>

                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/80 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 shadow-2xs text-xs font-semibold text-slate-700 dark:text-zinc-300 backdrop-blur-md">
                    <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>Meta Cloud API v20.0</span>
                </div>
            </div>

            <!-- Center Visual Section: Floating Communication Universe -->
            <div class="relative my-auto py-8 z-10">
                <!-- AI Sparkle Micro-Animations -->
                <span class="sparkle sparkle-1 text-emerald-400 absolute top-4 left-10 select-none">✨</span>
                <span class="sparkle sparkle-2 text-teal-300 absolute top-1/4 right-8 select-none">✦</span>
                <span class="sparkle sparkle-3 text-emerald-300 absolute bottom-12 left-1/4 select-none">✧</span>

                <!-- Hero Heading & Tagline -->
                <div class="max-w-xl mb-10">
                    <div class="inline-flex items-center gap-2 px-3 py-1 mb-4 rounded-xl bg-emerald-500/10 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300 text-xs font-bold uppercase tracking-wider">
                        <span>Conversations that move your business</span>
                    </div>
                    <h2 class="text-3xl xl:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-[1.15]">
                        Turn WhatsApp into your <span class="bg-gradient-to-r from-[#22C55E] via-[#10B981] to-[#34D399] bg-clip-text text-transparent">highest-converting</span> revenue engine.
                    </h2>
                    <p class="mt-3 text-sm text-slate-600 dark:text-zinc-400 leading-relaxed max-w-lg">
                        Manage support inboxes, trigger smart AI flows, broadcast high-impact campaigns, and automate customer journeys with zero friction.
                    </p>
                </div>

                <!-- Floating Communication Cards Container -->
                <div class="relative h-64 max-w-lg">
                    <!-- Bubble 1: Incoming Customer Message -->
                    <div class="floating-bubble bubble-1 absolute top-0 left-2 bg-white/95 dark:bg-[#111113]/90 border border-slate-200/80 dark:border-white/10 rounded-2xl p-3.5 shadow-floating backdrop-blur-md max-w-xs transition-all hover:scale-[1.02]">
                        <div class="flex items-start gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-emerald-400 to-teal-500 flex items-center justify-center text-white text-xs font-bold shrink-0">
                                💬
                            </div>
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-bold text-slate-900 dark:text-white">Customer Support</span>
                                    <span class="text-[10px] text-slate-400">Just now</span>
                                </div>
                                <p class="text-xs text-slate-600 dark:text-zinc-300 mt-0.5 leading-snug">
                                    "Hey! Can we get an onboarding demo for our sales team?"
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Bubble 2: AI Sparkle Reply Card -->
                    <div class="floating-bubble bubble-2 absolute top-16 right-0 bg-white/95 dark:bg-[#18181B]/95 border border-emerald-500/30 dark:border-emerald-500/40 rounded-2xl p-3.5 shadow-floating backdrop-blur-md max-w-xs ring-1 ring-emerald-500/20 transition-all hover:scale-[1.02]">
                        <div class="flex items-start gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-[#22C55E] to-[#022828] flex items-center justify-center text-white text-xs font-bold shrink-0 shadow-xs">
                                ✨
                            </div>
                            <div>
                                <div class="flex items-center gap-1.5">
                                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">AI Assistant</span>
                                    <span class="text-[10px] px-1.5 py-0.2 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 font-bold">Auto</span>
                                </div>
                                <p class="text-xs text-slate-600 dark:text-zinc-300 mt-0.5 leading-snug">
                                    "Demo slot reserved for Tuesday 2:00 PM. Meeting link dispatched via WhatsApp! ✓"
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- Bubble 3: Order Confirmed & Metrics -->
                    <div class="floating-bubble bubble-3 absolute bottom-2 left-10 bg-white/95 dark:bg-[#111113]/90 border border-slate-200/80 dark:border-white/10 rounded-2xl p-3 shadow-floating backdrop-blur-md flex items-center gap-3 transition-all hover:scale-[1.02]">
                        <div class="w-9 h-9 rounded-xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center font-bold text-sm shrink-0">
                            ✓
                        </div>
                        <div>
                            <div class="text-[11px] font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                <span>Order Confirmed</span>
                                <span class="font-mono text-emerald-600 dark:text-emerald-400">$340.00</span>
                            </div>
                            <div class="text-[10px] text-slate-400">Campaign: Summer Flash Sale • 98.4% Open</div>
                        </div>
                    </div>
                </div>

                <!-- Product Flow Connection (Contact → Message → Automation → AI → Customer) -->
                <div class="mt-8 pt-6 border-t border-slate-200/70 dark:border-white/5">
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500 mb-3">
                        Seamless End-to-End Pipeline
                    </div>
                    <div class="flex items-center gap-2 overflow-x-auto text-xs font-semibold py-1">
                        <span class="px-2.5 py-1 rounded-lg bg-white/80 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 text-slate-700 dark:text-zinc-300 shadow-2xs">
                            Contacts
                        </span>
                        <span class="text-slate-400">→</span>
                        <span class="px-2.5 py-1 rounded-lg bg-white/80 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 text-slate-700 dark:text-zinc-300 shadow-2xs">
                            WhatsApp API
                        </span>
                        <span class="text-slate-400">→</span>
                        <span class="px-2.5 py-1 rounded-lg bg-white/80 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 text-slate-700 dark:text-zinc-300 shadow-2xs">
                            Flows
                        </span>
                        <span class="text-slate-400">→</span>
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300 border border-emerald-500/20 font-bold shadow-2xs">
                            AI Automation
                        </span>
                        <span class="text-slate-400">→</span>
                        <span class="px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20 font-bold shadow-2xs">
                            Revenue
                        </span>
                    </div>
                </div>
            </div>

            <!-- Footer Trust Bar -->
            <div class="relative z-10 flex items-center justify-between text-xs text-slate-500 dark:text-zinc-500 pt-4 border-t border-slate-200/70 dark:border-white/5">
                <span>© {{ new Date().getFullYear() }} {{ props.companyConfig?.company_name || 'Wappiyo' }}. All rights reserved.</span>
                <div class="flex items-center gap-4">
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.857-9.809a.75.75 0 00-1.214-.882l-3.483 4.79-1.88-1.88a.75.75 0 10-1.06 1.061l2.5 2.5a.75.75 0 001.137-.089l4-5.5z" clip-rule="evenodd"/></svg>
                        99.9% SLA
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 1a4.5 4.5 0 00-4.5 4.5V9H5a2 2 0 00-2 2v6a2 2 0 002 2h10a2 2 0 002-2v-6a2 2 0 00-2-2h-.5V5.5A4.5 4.5 0 0010 1zm3 8V5.5a3 3 0 10-6 0V9h6z" clip-rule="evenodd"/></svg>
                        End-to-End Encrypted
                    </span>
                </div>
            </div>
        </div>

        <!-- ======================================================== -->
        <!-- RIGHT COLUMN: INTERACTIVE LOGIN FORM -->
        <!-- ======================================================== -->
        <div class="w-full lg:w-[48%] xl:w-[44%] flex flex-col justify-between p-6 sm:p-10 lg:p-12 xl:p-16 z-10 relative">
            <!-- Top Controls (Mobile Logo + Theme Switcher) -->
            <div class="flex items-center justify-between mb-8 sm:mb-10">
                <Link href="/" class="flex items-center gap-2.5 lg:hidden focus:outline-none">
                    <BrandLogo :custom-logo="props.companyConfig?.logo" :company-name="props.companyConfig?.company_name || 'Wappiyo'" mode="auto" />
                </Link>

                <div class="ml-auto flex items-center gap-2">
                    <!-- Theme Toggle Button -->
                    <button
                        type="button"
                        @click="toggleTheme"
                        class="p-2 rounded-xl border border-slate-200/80 dark:border-white/10 bg-white dark:bg-zinc-800 text-slate-700 dark:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-700 transition-colors shadow-2xs cursor-pointer"
                        :title="isDark ? $t('Switch to Light Mode') : $t('Switch to Dark Mode')"
                        aria-label="Toggle theme"
                    >
                        <!-- Sun Icon (when Dark) -->
                        <svg v-if="isDark" class="w-4 h-4 text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                        </svg>
                        <!-- Moon Icon (when Light) -->
                        <svg v-else class="w-4 h-4 text-slate-700" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Login Box Container -->
            <div class="w-full max-w-md mx-auto my-auto py-2">
                <!-- Welcome Microcopy -->
                <div class="mb-7">
                    <div class="inline-flex items-center gap-1.5 text-xs font-bold text-primary dark:text-purple-400 mb-2">
                        <span>👋</span>
                        <span>{{ $t('Welcome back') }}</span>
                    </div>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                        {{ $t('Sign in to your account') }}
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-zinc-400 mt-1.5 leading-relaxed">
                        {{ $t('Your customer conversations and team inboxes are waiting for you.') }}
                    </p>
                </div>

                <!-- Form Card -->
                <div class="bg-white/90 dark:bg-[#111113]/90 border border-slate-200/80 dark:border-white/10 rounded-3xl p-6 sm:p-8 shadow-xl shadow-slate-900/5 dark:shadow-black/50 backdrop-blur-xl">
                    <form @submit.prevent="submitForm()" class="space-y-4">
                        
                        <!-- Email Input with Dynamic Icon Reaction -->

                        <!-- Email Input with Dynamic Icon Reaction -->
                        <div>
                            <label for="login-email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1.5">
                                {{ $t('Email Address') }}
                            </label>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-slate-400 transition-colors" :class="{ 'text-emerald-600 dark:text-emerald-400': focusedField === 'email' }">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                                    </svg>
                                </span>
                                <input
                                    id="login-email"
                                    v-model="form.email"
                                    type="email"
                                    required
                                    autocomplete="username"
                                    @focus="focusedField = 'email'"
                                    @blur="focusedField = null"
                                    placeholder="name@company.com"
                                    class="w-full pl-10 pr-3.5 py-2.5 rounded-xl border bg-slate-50/70 dark:bg-zinc-900/80 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-zinc-500 outline-none transition-all"
                                    :class="form.errors.email
                                        ? 'border-rose-500 ring-2 ring-rose-500/20'
                                        : 'border-slate-200 dark:border-zinc-800 focus:border-[#22C55E] dark:focus:border-[#22C55E] focus:ring-2 focus:ring-[#22C55E]/20 dark:focus:ring-[#22C55E]/25'"
                                />
                            </div>
                            <p v-if="form.errors.email" class="text-xs text-rose-500 mt-1.5 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>{{ form.errors.email }}</span>
                            </p>
                        </div>

                        <!-- Password Input with Show/Hide Toggle & Icon Reaction -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label for="login-password" class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-zinc-300">
                                    {{ $t('Password') }}
                                </label>
                                <Link
                                    href="/forgot-password"
                                    class="text-xs font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 hover:underline transition-colors"
                                >
                                    {{ $t('Forgot password?') }}
                                </Link>
                            </div>
                            <div class="relative flex items-center">
                                <span class="absolute left-3.5 text-slate-400 transition-colors" :class="{ 'text-emerald-600 dark:text-emerald-400': focusedField === 'password' }">
                                    <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                    </svg>
                                </span>
                                <input
                                    id="login-password"
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    required
                                    autocomplete="current-password"
                                    @focus="focusedField = 'password'"
                                    @blur="focusedField = null"
                                    placeholder="••••••••••••"
                                    class="w-full pl-10 pr-10 py-2.5 rounded-xl border bg-slate-50/70 dark:bg-zinc-900/80 text-sm text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-zinc-500 outline-none transition-all"
                                    :class="form.errors.password
                                        ? 'border-rose-500 ring-2 ring-rose-500/20'
                                        : 'border-slate-200 dark:border-zinc-800 focus:border-[#22C55E] dark:focus:border-[#22C55E] focus:ring-2 focus:ring-[#22C55E]/20 dark:focus:ring-[#22C55E]/25'"
                                />
                                <!-- Accessible Show/Hide Toggle -->
                                <button
                                    type="button"
                                    @click="showPassword = !showPassword"
                                    class="absolute right-3 text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 focus:outline-none transition-colors"
                                    :aria-label="showPassword ? $t('Hide password') : $t('Show password')"
                                    :title="showPassword ? $t('Hide password') : $t('Show password')"
                                >
                                    <!-- Eye Off (Hide) -->
                                    <svg v-if="showPassword" class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18" />
                                    </svg>
                                    <!-- Eye (Show) -->
                                    <svg v-else class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </button>
                            </div>
                            <p v-if="form.errors.password" class="text-xs text-rose-500 mt-1.5 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                <span>{{ form.errors.password }}</span>
                            </p>
                        </div>

                        <!-- Recaptcha Error if any -->
                        <div v-if="form.errors.recaptcha_response" class="text-rose-500 text-xs font-medium">
                            {{ form.errors.recaptcha_response }}
                        </div>

                        <!-- Remember Me Checkbox -->
                        <div class="flex items-center justify-between pt-1">
                            <label class="flex items-center gap-2 cursor-pointer select-none text-xs font-medium text-slate-600 dark:text-zinc-400 group">
                                <input
                                    id="remember"
                                    type="checkbox"
                                    v-model="form.remember"
                                    class="w-4 h-4 rounded border-slate-300 dark:border-zinc-700 text-emerald-600 focus:ring-emerald-500/30 bg-slate-50 dark:bg-zinc-800 transition-colors cursor-pointer"
                                />
                                <span class="group-hover:text-slate-900 dark:group-hover:text-white transition-colors">
                                    {{ $t('Remember this browser') }}
                                </span>
                            </label>
                        </div>

                        <!-- Submit CTA Button with Loading and Gradient Hover -->
                        <div class="pt-3">
                            <button
                                type="submit"
                                :disabled="isLoading || form.processing"
                                class="w-full relative inline-flex items-center justify-center gap-2 py-3 px-5 rounded-xl text-xs sm:text-sm font-bold text-white bg-gradient-to-r from-[#22C55E] via-[#16A34A] to-[#022828] hover:from-[#15803D] hover:via-[#16A34A] hover:to-[#011d1d] shadow-lg shadow-emerald-500/25 hover:shadow-emerald-500/40 active:scale-[0.99] transition-all disabled:opacity-50 cursor-pointer overflow-hidden group"
                            >
                                <svg v-if="isLoading || form.processing" class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ (isLoading || form.processing) ? $t('Signing you in...') : $t('Sign in to Workspace') }}</span>
                                <span v-if="!isLoading && !form.processing" class="group-hover:translate-x-1 transition-transform">→</span>
                            </button>
                        </div>
                    </form>

                    <!-- Social Login (Google & Facebook) if enabled -->
                    <div v-if="props.companyConfig?.allow_facebook_login === '1' || props.companyConfig?.allow_google_login === '1'" class="mt-6">
                        <div class="relative mb-4">
                            <div class="absolute inset-0 flex items-center">
                                <div class="w-full border-t border-slate-200/80 dark:border-white/10"></div>
                            </div>
                            <div class="relative flex justify-center text-[10px] uppercase font-bold tracking-wider">
                                <span class="bg-white dark:bg-[#111113] px-3 text-slate-400 dark:text-zinc-500">
                                    {{ $t('Or continue with') }}
                                </span>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
                            <a 
                                v-if="props.companyConfig?.allow_google_login === '1'" 
                                href="/social-login/google" 
                                class="flex items-center justify-center gap-2 py-2 px-3 rounded-xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 hover:bg-slate-50 dark:hover:bg-zinc-700/60 text-xs font-semibold text-slate-700 dark:text-zinc-200 shadow-2xs transition-all cursor-pointer"
                            >
                                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                                    <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17Z"/>
                                    <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24Z"/>
                                    <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.14-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15Z"/>
                                    <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98Z"/>
                                </svg>
                                <span>Google</span>
                            </a>

                            <a 
                                v-if="props.companyConfig?.allow_facebook_login === '1'" 
                                href="/social-login/facebook" 
                                class="flex items-center justify-center gap-2 py-2 px-3 rounded-xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 hover:bg-slate-50 dark:hover:bg-zinc-700/60 text-xs font-semibold text-slate-700 dark:text-zinc-200 shadow-2xs transition-all cursor-pointer"
                            >
                                <svg class="w-4 h-4 text-[#1877F2] shrink-0" fill="currentColor" viewBox="0 0 24 24">
                                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
                                </svg>
                                <span>Facebook</span>
                            </a>
                        </div>
                    </div>
                </div>

                <!-- Registration Callout -->
                <div class="mt-6 text-center text-xs text-slate-600 dark:text-zinc-400">
                    <span>{{ $t('New to Wappiyo?') }}</span>
                    <Link
                        href="/signup"
                        class="ml-1 font-bold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 hover:underline"
                    >
                        {{ $t('Create your account →') }}
                    </Link>
                </div>
            </div>

            <!-- Mobile Footer Security Note -->
            <div class="text-center text-[11px] text-slate-400 dark:text-zinc-500 pt-6">
                <span>🔒 256-bit SSL encrypted • Meta Cloud API Partner</span>
            </div>
        </div>
    </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, watch } from 'vue';
import { Link, useForm, usePage } from "@inertiajs/vue3";
import BrandLogo from '@/Components/UI/BrandLogo.vue';
import { useRecaptcha, unMountRecaptcha } from '../../Composables/ReCaptcha';
import { useTheme } from '@/Composables/useTheme';
import { toast } from 'vue3-toastify';

const props = defineProps(['flash', 'config', 'companyConfig']);

// Theme composable
const { isDark, toggleTheme, initTheme } = useTheme();

const isLoading = ref(false);
const showPassword = ref(false);
const focusedField = ref(null);

const form = useForm({
    email: null,
    password: null,
    remember: false,
    recaptcha_response: null,
});

const getValueByKey = (key) => {
    if (!props.config || !Array.isArray(props.config)) return '';
    const found = props.config.find(item => item.key === key);
    return found ? found.value : '';
};

const submitForm = async () => {
    isLoading.value = true;

    if (getValueByKey('recaptcha_active') === '1') {
        const token = await getRecaptchaToken();
        form.recaptcha_response = token;
    }

    form.post('/login', {
        onSuccess: () => form.reset(),
        onFinish: () => {
            isLoading.value = false;
        }
    });
};

const getRecaptchaToken = () => {
    return new Promise((resolve) => {
        if (typeof grecaptcha !== 'undefined' && grecaptcha.ready) {
            grecaptcha.ready(() => {
                grecaptcha.execute(getValueByKey('recaptcha_site_key'), { action: 'submit' })
                .then((token) => {
                    resolve(token);
                });
            });
        } else {
            resolve(null);
        }
    });
};

onMounted(() => {
    initTheme();
    if (getValueByKey('recaptcha_active') === '1') {
        useRecaptcha(getValueByKey('recaptcha_site_key'));
    }
});

onUnmounted(() => {
    unMountRecaptcha(getValueByKey('recaptcha_site_key'));
});

watch(() => [usePage().props.flash, { deep: true }], () => {
    if (usePage().props.flash?.status != null) {
        toast(usePage().props.flash.status.message, {
            autoClose: 3000,
        });
    }
});
</script>

<style scoped>
/* Floating message bubble animations with subtle deterministic timing */
@keyframes float-1 {
    0%, 100% {
        transform: translateY(0px) rotate(0deg);
    }
    50% {
        transform: translateY(-8px) rotate(-0.5deg);
    }
}

@keyframes float-2 {
    0%, 100% {
        transform: translateY(0px) rotate(0deg);
    }
    50% {
        transform: translateY(-10px) rotate(0.8deg);
    }
}

@keyframes float-3 {
    0%, 100% {
        transform: translateY(0px) rotate(0deg);
    }
    50% {
        transform: translateY(-6px) rotate(-0.4deg);
    }
}

/* Ambient gradient orb slow movement */
@keyframes orb-float {
    0%, 100% {
        transform: scale(1) translate(0, 0);
    }
    50% {
        transform: scale(1.08) translate(15px, -15px);
    }
}

/* Sparkle twinkling */
@keyframes twinkle {
    0%, 100% {
        opacity: 0.2;
        transform: scale(0.8) rotate(0deg);
    }
    50% {
        opacity: 0.9;
        transform: scale(1.15) rotate(15deg);
    }
}

.floating-bubble.bubble-1 {
    animation: float-1 6s ease-in-out infinite;
}

.floating-bubble.bubble-2 {
    animation: float-2 7s ease-in-out infinite 0.5s;
}

.floating-bubble.bubble-3 {
    animation: float-3 5.5s ease-in-out infinite 1s;
}

.login-orb.orb-1 {
    animation: orb-float 12s ease-in-out infinite;
}

.login-orb.orb-2 {
    animation: orb-float 15s ease-in-out infinite 2s;
}

.login-orb.orb-3 {
    animation: orb-float 14s ease-in-out infinite 4s;
}

.sparkle-1 {
    animation: twinkle 3s ease-in-out infinite;
}

.sparkle-2 {
    animation: twinkle 4s ease-in-out infinite 1s;
}

.sparkle-3 {
    animation: twinkle 3.5s ease-in-out infinite 2s;
}

/* Accessibility: Disable motion when user requests reduced motion */
@media (prefers-reduced-motion: reduce) {
    .floating-bubble,
    .login-orb,
    .sparkle {
        animation: none !important;
        transform: none !important;
    }
}
</style>