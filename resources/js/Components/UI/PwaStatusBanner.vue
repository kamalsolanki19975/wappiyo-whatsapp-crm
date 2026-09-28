<template>
    <div class="fixed top-0 inset-x-0 z-50 pointer-events-none flex flex-col items-center px-4 pt-2 gap-2">
        <!-- Offline Warning Banner -->
        <transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="-translate-y-4 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="-translate-y-4 opacity-0"
        >
            <div
                v-if="!isOnline"
                class="pointer-events-auto flex items-center gap-3 px-4 py-2.5 rounded-xl bg-amber-500/90 dark:bg-amber-600/90 backdrop-blur-md text-white shadow-lg shadow-amber-500/20 text-xs sm:text-sm font-medium border border-amber-400/30"
                role="alert"
            >
                <span class="flex h-2.5 w-2.5 relative">
                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-white opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-white"></span>
                </span>
                <span>You are currently offline. Actions will sync when connection returns.</span>
            </div>
        </transition>

        <!-- Reconnected Success Banner -->
        <transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="-translate-y-4 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="-translate-y-4 opacity-0"
        >
            <div
                v-if="showReconnectedBanner"
                class="pointer-events-auto flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600/90 backdrop-blur-md text-white shadow-lg shadow-emerald-600/20 text-xs sm:text-sm font-medium border border-emerald-400/30"
                role="status"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-200" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                <span>Connection restored. You are back online.</span>
            </div>
        </transition>

        <!-- App Update Available Banner -->
        <transition
            enter-active-class="transform ease-out duration-300 transition"
            enter-from-class="-translate-y-4 opacity-0"
            enter-to-class="translate-y-0 opacity-100"
            leave-active-class="transition ease-in duration-200"
            leave-from-class="translate-y-0 opacity-100"
            leave-to-class="-translate-y-4 opacity-0"
        >
            <div
                v-if="isUpdateAvailable"
                class="pointer-events-auto flex items-center justify-between gap-4 px-4 py-2.5 rounded-xl bg-slate-900/95 dark:bg-zinc-900/95 backdrop-blur-md text-white shadow-2xl border border-emerald-500/40 text-xs sm:text-sm font-medium max-w-md w-full"
                role="alert"
            >
                <div class="flex items-center gap-2.5 min-w-0">
                    <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
                    <span class="truncate">A new version of Wappiyo is available.</span>
                </div>
                <button
                    type="button"
                    class="px-3 py-1 bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg text-xs font-semibold shrink-0 transition-colors shadow-sm cursor-pointer"
                    @click="updateApp"
                >
                    Update Now
                </button>
            </div>
        </transition>
    </div>
</template>

<script setup>
import { usePwa } from '@/Composables/usePwa';

const {
    isOnline,
    showReconnectedBanner,
    isUpdateAvailable,
    updateApp
} = usePwa();
</script>
