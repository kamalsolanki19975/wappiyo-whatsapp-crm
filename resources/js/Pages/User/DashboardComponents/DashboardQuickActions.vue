<script setup>
import { Link } from '@inertiajs/vue3';

const actions = [
    {
        title: 'Open Inbox',
        description: 'View active chats & replies',
        href: '/chats',
        color: 'text-[#6C5CE7]',
        bg: 'bg-purple-50 dark:bg-purple-950/40',
        ring: 'group-hover:ring-purple-500/30',
        icon: 'chat',
    },
    {
        title: 'Add Contact',
        description: 'Create individual lead or user',
        href: '/contacts/add',
        color: 'text-[#06B6D4]',
        bg: 'bg-cyan-50 dark:bg-cyan-950/40',
        ring: 'group-hover:ring-cyan-500/30',
        icon: 'contact',
    },
    {
        title: 'New Campaign',
        description: 'Broadcast WhatsApp messages',
        href: '/campaigns/create',
        color: 'text-[#EC4899]',
        bg: 'bg-pink-50 dark:bg-pink-950/40',
        ring: 'group-hover:ring-pink-500/30',
        icon: 'campaign',
    },
    {
        title: 'Message Template',
        description: 'Build & submit Meta templates',
        href: '/templates/create',
        color: 'text-[#8B5CF6]',
        bg: 'bg-violet-50 dark:bg-violet-950/40',
        ring: 'group-hover:ring-violet-500/30',
        icon: 'template',
    },
    {
        title: 'Quick Automation',
        description: 'Setup canned replies & rules',
        href: '/automation/basic',
        color: 'text-[#F97316]',
        bg: 'bg-orange-50 dark:bg-orange-950/40',
        ring: 'group-hover:ring-orange-500/30',
        icon: 'automation',
    },
    {
        title: 'Support Tickets',
        description: 'Manage customer service issues',
        href: '/support',
        color: 'text-[#22C55E]',
        bg: 'bg-emerald-50 dark:bg-emerald-950/40',
        ring: 'group-hover:ring-emerald-500/30',
        icon: 'support',
    },
];
</script>

<template>
    <div class="rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-5 sm:p-6 shadow-card transition-all">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-zinc-800/80 pb-4 mb-4">
            <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">
                    {{ $t('Quick Operations') }}
                </h3>
                <p class="text-xs text-slate-500 dark:text-zinc-400">
                    {{ $t('Direct shortcuts to common CRM workflows') }}
                </p>
            </div>
        </div>

        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
            <Link
                v-for="act in actions"
                :key="act.title"
                :href="act.href"
                class="group relative flex flex-col p-3.5 rounded-xl border border-slate-100 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-900/40 hover:bg-white dark:hover:bg-zinc-800/60 hover:shadow-subtle hover:-translate-y-0.5 transition-all duration-150"
            >
                <div class="flex items-center justify-between mb-2">
                    <div :class="['h-9 w-9 rounded-xl flex items-center justify-center ring-1 ring-black/5 dark:ring-white/10 transition-transform group-hover:scale-105', act.bg, act.color]">
                        <!-- Chat -->
                        <svg v-if="act.icon === 'chat'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        <!-- Contact -->
                        <svg v-else-if="act.icon === 'contact'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                        <!-- Campaign -->
                        <svg v-else-if="act.icon === 'campaign'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 11 19-9-9 19-2-8-8-2z"/></svg>
                        <!-- Template -->
                        <svg v-else-if="act.icon === 'template'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                        <!-- Automation -->
                        <svg v-else-if="act.icon === 'automation'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        <!-- Support -->
                        <svg v-else-if="act.icon === 'support'" xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    </div>

                    <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-300 dark:text-zinc-600 group-hover:text-slate-600 dark:group-hover:text-zinc-300 group-hover:translate-x-0.5 transition-all" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                </div>

                <div class="mt-1">
                    <h4 class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-[#6C5CE7] dark:group-hover:text-purple-300 transition-colors">
                        {{ $t(act.title) }}
                    </h4>
                    <p class="text-[11px] text-slate-400 dark:text-zinc-500 line-clamp-1 mt-0.5">
                        {{ $t(act.description) }}
                    </p>
                </div>
            </Link>
        </div>
    </div>
</template>
