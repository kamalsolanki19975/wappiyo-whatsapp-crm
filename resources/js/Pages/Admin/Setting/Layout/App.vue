<template>
    <AppLayout>
        <!-- Mobile Header Bar for Settings -->
        <div class="sticky top-0 z-20 w-full bg-white/95 dark:bg-zinc-900/95 backdrop-blur-md border-b border-slate-200/80 dark:border-zinc-800 px-4 py-3 flex items-center justify-between md:hidden">
            <Link href="/admin/settings" class="text-slate-600 dark:text-zinc-300 hover:text-slate-900 dark:hover:text-white p-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
            </Link>
            <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $t('System Settings') }}</h3>
            <div class="w-6"></div>
        </div>

        <div class="flex flex-1 h-full min-h-0 overflow-hidden">
            <!-- Desktop Settings Sub-Sidebar -->
            <Sidebar class="hidden md:flex shrink-0" />

            <!-- Sub-Page Content Scroll Area -->
            <div class="flex-1 overflow-y-auto px-4 sm:px-8 lg:px-12 py-8 bg-slate-50/70 dark:bg-[#09090B]">
                <slot></slot>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
    import AppLayout from "./../../Layout/App.vue";
    import Sidebar from "./Sidebar.vue";
    import { Link } from "@inertiajs/vue3";
</script>