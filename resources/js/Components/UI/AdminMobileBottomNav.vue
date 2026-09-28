<template>
    <nav
        class="md:hidden fixed bottom-0 inset-x-0 z-40 bg-white/95 dark:bg-[#111113]/95 backdrop-blur-xl border-t border-slate-200/80 dark:border-zinc-800 px-2 py-1 shadow-lg transition-colors select-none"
        style="padding-bottom: max(0.35rem, env(safe-area-inset-bottom));"
        aria-label="Admin Mobile Navigation"
    >
        <div class="grid grid-cols-5 items-center justify-around h-14">
            <!-- 1. Dashboard -->
            <Link
                href="/admin/dashboard"
                class="flex flex-col items-center justify-center gap-0.5 py-1 px-1 rounded-xl transition-all"
                :class="[
                    isDashboardActive
                        ? 'text-emerald-600 dark:text-emerald-400 font-semibold'
                        : 'text-slate-500 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-zinc-200'
                ]"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform" :class="isDashboardActive ? 'scale-110' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/>
                </svg>
                <span class="text-[10px] leading-tight tracking-tight">Dashboard</span>
            </Link>

            <!-- 2. Organizations -->
            <Link
                href="/admin/organizations"
                class="flex flex-col items-center justify-center gap-0.5 py-1 px-1 rounded-xl transition-all"
                :class="[
                    isOrgsActive
                        ? 'text-emerald-600 dark:text-emerald-400 font-semibold'
                        : 'text-slate-500 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-zinc-200'
                ]"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform" :class="isOrgsActive ? 'scale-110' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v8h4"/><path d="M18 9h2a2 2 0 0 1 2 2v11h-4"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/>
                </svg>
                <span class="text-[10px] leading-tight tracking-tight">Teams</span>
            </Link>

            <!-- 3. Users -->
            <Link
                href="/admin/users"
                class="flex flex-col items-center justify-center gap-0.5 py-1 px-1 rounded-xl transition-all"
                :class="[
                    isUsersActive
                        ? 'text-emerald-600 dark:text-emerald-400 font-semibold'
                        : 'text-slate-500 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-zinc-200'
                ]"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform" :class="isUsersActive ? 'scale-110' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                </svg>
                <span class="text-[10px] leading-tight tracking-tight">Users</span>
            </Link>

            <!-- 4. Plans -->
            <Link
                href="/admin/plans"
                class="flex flex-col items-center justify-center gap-0.5 py-1 px-1 rounded-xl transition-all"
                :class="[
                    isPlansActive
                        ? 'text-emerald-600 dark:text-emerald-400 font-semibold'
                        : 'text-slate-500 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-zinc-200'
                ]"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform" :class="isPlansActive ? 'scale-110' : ''" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/>
                </svg>
                <span class="text-[10px] leading-tight tracking-tight">Plans</span>
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

defineEmits(['toggleSidebar']);

const page = usePage();

const isDashboardActive = computed(() => page.url.startsWith('/admin/dashboard'));
const isOrgsActive = computed(() => page.url.startsWith('/admin/organization'));
const isUsersActive = computed(() => page.url.startsWith('/admin/user'));
const isPlansActive = computed(() => page.url.startsWith('/admin/plan'));
</script>
