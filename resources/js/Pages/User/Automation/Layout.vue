<template>
    <AppLayout>
        <div class="min-h-[calc(100vh-4rem)] p-4 md:p-8 space-y-6 text-slate-900 dark:text-white">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200/80 dark:border-white/10 pb-6">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-primary/10 text-primary dark:bg-primary/20">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                            </svg>
                        </span>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                            {{ $t('Automations & Workflows') }}
                        </h1>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        {{ $t('Automate conversations, customer journeys, and intelligent workflow logic.') }}
                    </p>
                </div>

                <!-- Navigation Tabs -->
                <div class="flex items-center gap-2 bg-slate-100 dark:bg-white/5 p-1 rounded-xl border border-slate-200/80 dark:border-white/10 text-sm">
                    <Link
                        href="/automation/basic"
                        class="px-3.5 py-1.5 rounded-lg font-medium transition-all duration-200 flex items-center gap-2"
                        :class="$page.url.startsWith('/automation/basic') || $page.url.startsWith('/automation/builder')
                            ? 'bg-white dark:bg-primary text-primary dark:text-white shadow-sm font-semibold'
                            : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                        </svg>
                        <span>{{ $t('All Automations') }}</span>
                    </Link>

                    <Link
                        v-if="aimodule"
                        href="/automation/ai"
                        class="px-3.5 py-1.5 rounded-lg font-medium transition-all duration-200 flex items-center gap-2"
                        :class="$page.url.startsWith('/automation/ai')
                            ? 'bg-white dark:bg-primary text-primary dark:text-white shadow-sm font-semibold'
                            : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.75" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z" />
                        </svg>
                        <span>{{ $t('AI Assistant') }}</span>
                    </Link>
                </div>
            </div>

            <!-- Content Slot -->
            <div>
                <slot />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "@/Pages/User/Layout/App.vue";
import { Link } from "@inertiajs/vue3";

const props = defineProps({
    aimodule: {
        type: Boolean,
        default: false,
    },
    fbmodule: {
        type: Boolean,
        default: false,
    },
});
</script>