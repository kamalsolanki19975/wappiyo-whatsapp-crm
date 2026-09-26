<template>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Messages Volume -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-white/10 p-5 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-3 mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ $t('Total Messages') }}
                </span>
                <span class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
                    </svg>
                </span>
            </div>

            <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                {{ formatNumber(kpis.messages?.value) }}
            </div>

            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-xs">
                <div v-if="kpis.messages?.delta !== null" class="flex items-center gap-1 font-semibold" :class="kpis.messages.delta >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500'">
                    <span>{{ kpis.messages.delta >= 0 ? '↑' : '↓' }} {{ Math.abs(kpis.messages.delta) }}%</span>
                </div>
                <div v-else class="text-slate-400 text-[11px]">
                    {{ $t('No prior period') }}
                </div>
                <span class="text-[11px] text-slate-400">{{ $t('vs previous period') }}</span>
            </div>
        </div>

        <!-- Conversations Handled -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-white/10 p-5 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-3 mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ $t('Active Conversations') }}
                </span>
                <span class="w-8 h-8 rounded-xl bg-violet-500/10 text-violet-600 dark:text-violet-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" />
                    </svg>
                </span>
            </div>

            <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                {{ formatNumber(kpis.conversations?.value) }}
            </div>

            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-xs">
                <div v-if="kpis.conversations?.delta !== null" class="flex items-center gap-1 font-semibold" :class="kpis.conversations.delta >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500'">
                    <span>{{ kpis.conversations.delta >= 0 ? '↑' : '↓' }} {{ Math.abs(kpis.conversations.delta) }}%</span>
                </div>
                <div v-else class="text-slate-400 text-[11px]">
                    {{ $t('No prior period') }}
                </div>
                <span class="text-[11px] text-slate-400">{{ $t('vs previous period') }}</span>
            </div>
        </div>

        <!-- New Contacts -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-white/10 p-5 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-3 mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ $t('New Contacts') }}
                </span>
                <span class="w-8 h-8 rounded-xl bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                    </svg>
                </span>
            </div>

            <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                {{ formatNumber(kpis.contacts?.value) }}
            </div>

            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-xs">
                <div v-if="kpis.contacts?.delta !== null" class="flex items-center gap-1 font-semibold" :class="kpis.contacts.delta >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500'">
                    <span>{{ kpis.contacts.delta >= 0 ? '↑' : '↓' }} {{ Math.abs(kpis.contacts.delta) }}%</span>
                </div>
                <div v-else class="text-slate-400 text-[11px]">
                    {{ $t('No prior period') }}
                </div>
                <span class="text-[11px] text-slate-400">{{ $t('vs previous period') }}</span>
            </div>
        </div>

        <!-- Broadcast Campaigns -->
        <div class="bg-white dark:bg-slate-900 rounded-2xl border border-slate-200/80 dark:border-white/10 p-5 shadow-sm hover:shadow-md transition duration-200 flex flex-col justify-between">
            <div class="flex items-center justify-between gap-3 mb-3">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ $t('Campaigns Sent') }}
                </span>
                <span class="w-8 h-8 rounded-xl bg-pink-500/10 text-pink-600 dark:text-pink-400 flex items-center justify-center">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" />
                    </svg>
                </span>
            </div>

            <div class="text-2xl font-black text-slate-900 dark:text-white tracking-tight">
                {{ formatNumber(kpis.campaigns?.value) }}
            </div>

            <div class="mt-3 pt-3 border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-xs">
                <div v-if="kpis.campaigns?.delta !== null" class="flex items-center gap-1 font-semibold" :class="kpis.campaigns.delta >= 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-500'">
                    <span>{{ kpis.campaigns.delta >= 0 ? '↑' : '↓' }} {{ Math.abs(kpis.campaigns.delta) }}%</span>
                </div>
                <div v-else class="text-slate-400 text-[11px]">
                    {{ $t('No prior period') }}
                </div>
                <span class="text-[11px] text-slate-400">{{ $t('vs previous period') }}</span>
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    kpis: {
        type: Object,
        default: () => ({}),
    },
});

const formatNumber = (num) => {
    if (num === null || num === undefined) return '0';
    return Number(num).toLocaleString();
};
</script>
