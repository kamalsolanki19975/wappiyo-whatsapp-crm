<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    contactCount: {
        type: Number,
        default: 0,
    },
    campaignCount: {
        type: Number,
        default: 0,
    },
    templateCount: {
        type: Number,
        default: 0,
    },
    chatCount: {
        type: Number,
        default: 0,
    },
    inbound: {
        type: Array,
        default: () => [],
    },
    outbound: {
        type: Array,
        default: () => [],
    },
});

const formatNumber = (num) => {
    return new Intl.NumberFormat().format(num || 0);
};

const weeklyInbound = computed(() => {
    return props.inbound.reduce((acc, curr) => acc + (Number(curr) || 0), 0);
});

const weeklyOutbound = computed(() => {
    return props.outbound.reduce((acc, curr) => acc + (Number(curr) || 0), 0);
});
</script>

<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- 1. Total Contacts -->
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-5 shadow-card hover:shadow-elevated transition-all duration-200 hover:-translate-y-0.5">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                        {{ $t('Total Contacts') }}
                    </span>
                    <h3 class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                        {{ formatNumber(contactCount) }}
                    </h3>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-cyan-50 dark:bg-cyan-950/40 text-[#06B6D4] ring-1 ring-cyan-500/20 group-hover:scale-105 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                        <circle cx="9" cy="7" r="4"/>
                        <path d="M22 21v-2a4 4 0 0 0-3-3.87"/>
                        <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                    </svg>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-between border-t border-slate-100 dark:border-zinc-800/80 pt-3 text-xs">
                <span class="text-slate-400 dark:text-zinc-500">
                    {{ $t('Audience in CRM') }}
                </span>
                <Link href="/contacts" class="inline-flex items-center gap-1 font-semibold text-[#06B6D4] hover:underline">
                    <span>{{ $t('View contacts') }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                </Link>
            </div>
        </div>

        <!-- 2. All Chats / Conversations -->
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-5 shadow-card hover:shadow-elevated transition-all duration-200 hover:-translate-y-0.5">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                        {{ $t('All Conversations') }}
                    </span>
                    <h3 class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                        {{ formatNumber(chatCount) }}
                    </h3>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-purple-50 dark:bg-purple-950/40 text-[#6C5CE7] ring-1 ring-purple-500/20 group-hover:scale-105 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-between border-t border-slate-100 dark:border-zinc-800/80 pt-3 text-xs">
                <span class="text-slate-400 dark:text-zinc-500">
                    {{ formatNumber(weeklyInbound) }} in / {{ formatNumber(weeklyOutbound) }} out this wk
                </span>
                <Link href="/chats" class="inline-flex items-center gap-1 font-semibold text-[#6C5CE7] dark:text-purple-400 hover:underline">
                    <span>{{ $t('Open chats') }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                </Link>
            </div>
        </div>

        <!-- 3. Campaigns -->
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-5 shadow-card hover:shadow-elevated transition-all duration-200 hover:-translate-y-0.5">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                        {{ $t('Campaigns') }}
                    </span>
                    <h3 class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                        {{ formatNumber(campaignCount) }}
                    </h3>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-pink-50 dark:bg-pink-950/40 text-[#EC4899] ring-1 ring-pink-500/20 group-hover:scale-105 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="m3 11 19-9-9 19-2-8-8-2z"/>
                    </svg>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-between border-t border-slate-100 dark:border-zinc-800/80 pt-3 text-xs">
                <span class="text-slate-400 dark:text-zinc-500">
                    {{ $t('Broadcast dispatches') }}
                </span>
                <Link href="/campaigns" class="inline-flex items-center gap-1 font-semibold text-[#EC4899] hover:underline">
                    <span>{{ $t('View campaigns') }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                </Link>
            </div>
        </div>

        <!-- 4. Templates -->
        <div class="group relative overflow-hidden rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-5 shadow-card hover:shadow-elevated transition-all duration-200 hover:-translate-y-0.5">
            <div class="flex items-start justify-between">
                <div>
                    <span class="text-xs font-semibold uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                        {{ $t('Templates') }}
                    </span>
                    <h3 class="mt-2 text-2xl sm:text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                        {{ formatNumber(templateCount) }}
                    </h3>
                </div>
                <div class="flex h-11 w-11 items-center justify-center rounded-xl bg-violet-50 dark:bg-violet-950/40 text-[#8B5CF6] ring-1 ring-violet-500/20 group-hover:scale-105 transition-transform">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="18" height="18" x="3" y="3" rx="2"/>
                        <path d="M3 9h18"/>
                        <path d="M9 21V9"/>
                    </svg>
                </div>
            </div>

            <div class="mt-4 flex items-center justify-between border-t border-slate-100 dark:border-zinc-800/80 pt-3 text-xs">
                <span class="text-slate-400 dark:text-zinc-500">
                    {{ $t('Approved templates') }}
                </span>
                <Link href="/templates" class="inline-flex items-center gap-1 font-semibold text-[#8B5CF6] hover:underline">
                    <span>{{ $t('View templates') }}</span>
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                </Link>
            </div>
        </div>
    </div>
</template>
