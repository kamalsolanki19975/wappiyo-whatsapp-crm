<template>
    <div class="space-y-4">
        <div>
            <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">{{ $t('Developer Platform') }}</h1>
            <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">{{ $t('Manage programmatic API keys, webhooks, and endpoint documentation.') }}</p>
        </div>

        <div class="flex items-center gap-2 border-b border-slate-200/80 dark:border-zinc-800">
            <Link 
                href="/developer-tools/access-tokens" 
                class="px-4 py-2.5 text-xs font-semibold rounded-t-xl transition-all border-b-2 flex items-center gap-2"
                :class="$page.url.startsWith('/developer-tools/access-tokens') 
                    ? 'border-[#6C5CE7] text-[#6C5CE7] dark:text-indigo-400 bg-white dark:bg-zinc-900 shadow-2xs' 
                    : 'border-transparent text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white'"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 2l-2 2m-1.5 1.5L16 7m-1.5 1.5L13 10m-1.5 1.5L10 13m-1.5 1.5L7 16m-1.5 1.5L4 19m-2 2l2-2m0 0l4-4m-4 4l-2-2m6-2l-4-4"/></svg>
                <span>{{ $t('Access Tokens') }}</span>
            </Link>
            <Link 
                href="/developer-tools/webhooks" 
                class="px-4 py-2.5 text-xs font-semibold rounded-t-xl transition-all border-b-2 flex items-center gap-2"
                :class="$page.url.startsWith('/developer-tools/webhooks') 
                    ? 'border-[#6C5CE7] text-[#6C5CE7] dark:text-indigo-400 bg-white dark:bg-zinc-900 shadow-2xs' 
                    : 'border-transparent text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white'"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 16.98h-5.99c-1.1 0-1.95.94-2.48 1.9A4 4 0 0 1 2 17c0-2.21 1.79-4 4-4h10"/><circle cx="18" cy="17" r="3"/><circle cx="6" cy="17" r="3"/></svg>
                <span>{{ $t('Webhooks') }}</span>
            </Link>
        </div>
    </div>
</template>

<script setup>
    import { Link } from "@inertiajs/vue3";
</script>