<template>
    <nav
        class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 dark:bg-[#111113]/95 backdrop-blur-xl border-t border-slate-200/80 dark:border-zinc-800 px-2 py-1 shadow-lg transition-colors select-none"
        style="padding-bottom: max(0.35rem, env(safe-area-inset-bottom));"
        aria-label="Mobile Navigation"
    >
        <div class="grid grid-cols-5 items-center justify-around h-14">
            <!-- 1. Inbox (Chats) -->
            <Link
                href="/chats"
                class="flex flex-col items-center justify-center gap-0.5 py-1 px-1 rounded-xl transition-all"
                :class="[
                    isInboxActive
                        ? 'text-emerald-600 dark:text-emerald-400 font-semibold'
                        : 'text-slate-500 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-zinc-200'
                ]"
            >
                <div class="relative flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform" :class="isInboxActive ? 'scale-110' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>
                    </svg>
                    <span
                        v-if="unreadCount > 0"
                        class="absolute -top-1.5 -right-2.5 min-w-[18px] h-[18px] px-1 bg-rose-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center shadow-sm shadow-rose-500/40"
                    >
                        {{ unreadCount > 99 ? '99+' : unreadCount }}
                    </span>
                </div>
                <span class="text-[10px] leading-tight tracking-tight">Inbox</span>
            </Link>

            <!-- 2. Contacts -->
            <Link
                href="/contacts"
                class="flex flex-col items-center justify-center gap-0.5 py-1 px-1 rounded-xl transition-all"
                :class="[
                    isContactsActive
                        ? 'text-emerald-600 dark:text-emerald-400 font-semibold'
                        : 'text-slate-500 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-zinc-200'
                ]"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform" :class="isContactsActive ? 'scale-110' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span class="text-[10px] leading-tight tracking-tight">Contacts</span>
            </Link>

            <!-- 3. Campaigns -->
            <Link
                href="/campaigns"
                class="flex flex-col items-center justify-center gap-0.5 py-1 px-1 rounded-xl transition-all"
                :class="[
                    isCampaignsActive
                        ? 'text-emerald-600 dark:text-emerald-400 font-semibold'
                        : 'text-slate-500 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-zinc-200'
                ]"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform" :class="isCampaignsActive ? 'scale-110' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/>
                </svg>
                <span class="text-[10px] leading-tight tracking-tight">Campaigns</span>
            </Link>

            <!-- 4. Reports -->
            <Link
                href="/reports"
                class="flex flex-col items-center justify-center gap-0.5 py-1 px-1 rounded-xl transition-all"
                :class="[
                    isReportsActive
                        ? 'text-emerald-600 dark:text-emerald-400 font-semibold'
                        : 'text-slate-500 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-zinc-200'
                ]"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform" :class="isReportsActive ? 'scale-110' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/>
                </svg>
                <span class="text-[10px] leading-tight tracking-tight">Reports</span>
            </Link>

            <!-- 5. More (Drawer Toggle) -->
            <button
                type="button"
                class="flex flex-col items-center justify-center gap-0.5 py-1 px-1 rounded-xl text-slate-500 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-zinc-200 transition-all focus:outline-none"
                @click="$emit('toggleSidebar')"
                aria-label="More navigation"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="4" x2="20" y1="12" y2="12"/><line x1="4" x2="20" y1="6" y2="6"/><line x1="4" x2="20" y1="18" y2="18"/>
                </svg>
                <span class="text-[10px] leading-tight tracking-tight">More</span>
            </button>
        </div>
    </nav>
</template>

<script setup>
import { computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';

const props = defineProps({
    unreadMessages: {
        type: [String, Number],
        default: 0
    }
});

defineEmits(['toggleSidebar']);

const page = usePage();

const isInboxActive = computed(() => page.url.startsWith('/chats'));
const isContactsActive = computed(() => page.url.startsWith('/contact'));
const isCampaignsActive = computed(() => page.url.startsWith('/campaign'));
const isReportsActive = computed(() => page.url.startsWith('/reports') || page.url.startsWith('/analytics'));

const unreadCount = computed(() => parseInt(props.unreadMessages, 10) || 0);
</script>
