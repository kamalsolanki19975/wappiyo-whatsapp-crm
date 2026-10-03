<script setup>
import { ref, onMounted } from 'vue';
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue';
import { Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import Badge from './Badge.vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const notifications = ref([]);
const unreadCount = ref(0);
const isLoading = ref(false);

const fetchNotifications = async () => {
    try {
        const response = await axios.get('/notifications');
        notifications.value = response.data.notifications || [];
        unreadCount.value = response.data.unread_count || 0;
    } catch {
        // Silent fallback
    }
};

const markAsRead = async (item) => {
    if (!item.seen) {
        item.seen = true;
        unreadCount.value = Math.max(0, unreadCount.value - 1);
        try {
            await axios.post(`/notifications/${item.id}/read`);
        } catch {
            // ignore
        }
    }
    if (item.url) {
        router.visit(item.url);
    }
};

const markAllAsRead = async () => {
    notifications.value.forEach(n => n.seen = true);
    unreadCount.value = 0;
    try {
        await axios.post('/notifications/read-all');
    } catch {
        // ignore
    }
};

onMounted(() => {
    fetchNotifications();
});
</script>

<template>
    <Popover class="relative">
        <PopoverButton
            @click="fetchNotifications"
            class="relative rounded-xl p-2 text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 hover:text-slate-900 dark:hover:text-white transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#6C5CE7] cursor-pointer"
            title="Notifications"
        >
            <span class="sr-only">Notifications</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
                <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
            </svg>

            <!-- Unread Ping Indicator -->
            <span
                v-if="unreadCount > 0"
                class="absolute top-1.5 right-1.5 flex h-2.5 w-2.5"
            >
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#EC4899] opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#EC4899]"></span>
            </span>
        </PopoverButton>

        <transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="transform scale-95 opacity-0 -translate-y-1"
            enter-to-class="transform scale-100 opacity-100 translate-y-0"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="transform scale-100 opacity-100 translate-y-0"
            leave-to-class="transform scale-95 opacity-0 -translate-y-1"
        >
            <PopoverPanel
                class="absolute right-0 z-50 mt-2 w-80 sm:w-96 rounded-2xl bg-white dark:bg-[#18181B] shadow-2xl border border-slate-200/80 dark:border-zinc-800 overflow-hidden focus:outline-none"
            >
                <!-- Header -->
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-zinc-800 bg-slate-50/70 dark:bg-zinc-900/60">
                    <div class="flex items-center gap-2">
                        <h4 class="font-bold text-sm text-slate-900 dark:text-white">{{ $t('Notifications') }}</h4>
                        <Badge variant="primary" size="xs">
                            {{ unreadCount }} {{ $t('new') }}
                        </Badge>
                    </div>
                    <button
                        v-if="unreadCount > 0"
                        type="button"
                        class="text-xs text-[#6C5CE7] dark:text-purple-400 hover:underline font-semibold cursor-pointer"
                        @click="markAllAsRead"
                    >
                        {{ $t('Mark all as read') }}
                    </button>
                </div>

                <!-- Notifications List -->
                <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-zinc-800">
                    <div
                        v-for="item in notifications"
                        :key="item.id"
                        @click="markAsRead(item)"
                        :class="[
                            'p-3.5 hover:bg-slate-50 dark:hover:bg-zinc-800/60 transition-colors flex items-start gap-3 cursor-pointer',
                            !item.seen ? 'bg-purple-50/40 dark:bg-purple-950/20' : ''
                        ]"
                    >
                        <!-- Icon -->
                        <div
                            :class="[
                                'w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-sm mt-0.5',
                                !item.seen ? 'bg-purple-100 dark:bg-purple-900/50 text-[#6C5CE7] dark:text-purple-300' : 'bg-slate-100 dark:bg-zinc-800 text-slate-500 dark:text-zinc-400'
                            ]"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0 space-y-0.5">
                            <div class="flex items-center justify-between">
                                <h5 class="text-xs font-semibold text-slate-900 dark:text-white truncate">
                                    {{ item.title }}
                                </h5>
                                <span class="text-[10px] text-slate-400 dark:text-zinc-500 whitespace-nowrap ml-2">
                                    {{ item.created_at }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-zinc-400 line-clamp-2 leading-relaxed">
                                {{ item.comment }}
                            </p>
                        </div>
                    </div>

                    <div v-if="!notifications.length" class="text-center py-8 text-xs text-slate-400 dark:text-zinc-500">
                        {{ $t('No notifications at this time.') }}
                    </div>
                </div>

                <!-- Footer -->
                <div class="p-2.5 border-t border-slate-100 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-900/40 text-center">
                    <span class="text-[11px] text-slate-400 dark:text-zinc-500">
                        {{ $t('Notifications update in real time') }}
                    </span>
                </div>
            </PopoverPanel>
        </transition>
    </Popover>
</template>
