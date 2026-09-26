<template>
    <div class="rounded-3xl border border-slate-200/80 dark:border-white/10 bg-white dark:bg-slate-900 p-6 shadow-sm flex flex-col justify-between">
        <!-- Header -->
        <div class="border-b border-slate-100 dark:border-white/5 pb-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h3 class="text-base font-bold text-slate-900 dark:text-white">
                        {{ $t('Team & Agent Activity') }}
                    </h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-primary/10 text-primary dark:bg-primary/20">
                        {{ teams.length }} {{ $t('Members') }}
                    </span>
                </div>
                <Link
                    v-if="$page.props.auth?.user?.role === 'admin' || $page.props.auth?.user?.role === 'owner'"
                    href="/team"
                    class="text-xs font-semibold text-primary hover:underline flex items-center gap-1"
                >
                    <span>{{ $t('Manage') }}</span>
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                    </svg>
                </Link>
            </div>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                {{ $t('Real message counts and conversations handled by organization members in this period') }}
            </p>
        </div>

        <!-- Table / List -->
        <div class="mt-4 flex-1">
            <div v-if="teams.length > 0" class="overflow-x-auto">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-100 dark:border-white/5 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                            <th class="py-2.5 px-2">{{ $t('Agent') }}</th>
                            <th class="py-2.5 px-2">{{ $t('Role') }}</th>
                            <th class="py-2.5 px-2 text-right">{{ $t('Chats Handled') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                        <tr
                            v-for="member in teams"
                            :key="member.user_id"
                            class="hover:bg-slate-50/80 dark:hover:bg-white/5 transition"
                        >
                            <!-- Name & Avatar -->
                            <td class="py-3 px-2">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-xl bg-gradient-to-tr from-primary to-violet-500 text-white font-bold text-xs flex items-center justify-center shrink-0 shadow-xs">
                                        {{ getInitials(member.name) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 dark:text-white truncate">
                                            {{ member.name }}
                                        </div>
                                        <div class="text-[10px] text-slate-400 truncate">
                                            {{ member.email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Role -->
                            <td class="py-3 px-2">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-300">
                                    {{ member.role }}
                                </span>
                            </td>

                            <!-- Handled Messages -->
                            <td class="py-3 px-2 text-right font-black text-slate-900 dark:text-white">
                                {{ formatNumber(member.chats_handled) }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!-- Empty State -->
            <div
                v-else
                class="py-8 flex flex-col items-center justify-center text-center p-4"
            >
                <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-400 flex items-center justify-center mb-2">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h4 class="text-xs font-bold text-slate-900 dark:text-white mb-0.5">
                    {{ $t('No team activity recorded') }}
                </h4>
                <p class="text-[11px] text-slate-400 max-w-[200px]">
                    {{ $t('Agent responses and conversation assignments will be tracked here.') }}
                </p>
            </div>
        </div>
    </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    teams: {
        type: Array,
        default: () => [],
    },
});

function formatNumber(num) {
    if (num === null || num === undefined) return '0';
    return Number(num).toLocaleString();
}

function getInitials(name) {
    if (!name) return 'A';
    const parts = name.trim().split(' ');
    if (parts.length >= 2) {
        return (parts[0][0] + parts[1][0]).toUpperCase();
    }
    return name.substring(0, 2).toUpperCase();
}
</script>
