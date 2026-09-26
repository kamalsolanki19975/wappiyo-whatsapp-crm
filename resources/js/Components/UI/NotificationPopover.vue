<script setup>
import { ref } from 'vue';
import { Popover, PopoverButton, PopoverPanel } from '@headlessui/vue';
import { Link } from '@inertiajs/vue3';
import Badge from './Badge.vue';

const props = defineProps({
    unreadCount: {
        type: Number,
        default: 0,
    },
});

// Mock notification items for presentation layer
const notifications = ref([
    {
        id: 1,
        title: 'New WhatsApp Message',
        description: 'You received a new inbound message on your channel.',
        time: '5m ago',
        unread: true,
        type: 'chat',
        link: '/chats',
    },
    {
        id: 2,
        title: 'Campaign Broadcast Completed',
        description: 'September Promotional Campaign sent to 450 contacts.',
        time: '1h ago',
        unread: false,
        type: 'campaign',
        link: '/campaigns',
    },
    {
        id: 3,
        title: 'Template Approved',
        description: 'Meta approved your "Order Confirmation" template.',
        time: '3h ago',
        unread: false,
        type: 'template',
        link: '/templates',
    },
]);

const markAllAsRead = () => {
    notifications.value.forEach(n => n.unread = false);
};
</script>

<template>
    <Popover class="relative">
        <PopoverButton
            class="relative rounded-xl p-2 text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 hover:text-slate-900 dark:hover:text-white transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#6C5CE7]"
            title="Notifications"
        >
            <span class="sr-only">Notifications</span>
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                <path d="M6 8a6 6 0 0 1 12 0c0 7 3 9 3 9H3s3-2 3-9"/>
                <path d="M10.3 21a1.94 1.94 0 0 0 3.4 0"/>
            </svg>

            <!-- Unread Ping Indicator -->
            <span
                v-if="unreadCount > 0 || notifications.some(n => n.unread)"
                class="absolute top-1.5 right-1.5 flex h-2 w-2"
            >
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#EC4899] opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2 w-2 bg-[#EC4899]"></span>
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
                class="absolute right-0 z-50 mt-2 w-80 sm:w-96 rounded-2xl bg-white dark:bg-[#18181B] shadow-floating border border-slate-200/80 dark:border-zinc-800 overflow-hidden focus:outline-none"
            >
                <!-- Header -->
                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-900/40">
                    <div class="flex items-center gap-2">
                        <h4 class="font-bold text-sm text-slate-900 dark:text-white">Notifications</h4>
                        <Badge variant="primary" size="xs">
                            {{ unreadCount || notifications.filter(n => n.unread).length }} new
                        </Badge>
                    </div>
                    <button
                        type="button"
                        class="text-xs text-[#6C5CE7] dark:text-purple-400 hover:underline font-medium"
                        @click="markAllAsRead"
                    >
                        Mark all as read
                    </button>
                </div>

                <!-- Notifications List -->
                <div class="max-h-80 overflow-y-auto divide-y divide-slate-100 dark:divide-zinc-800">
                    <div
                        v-for="item in notifications"
                        :key="item.id"
                        :class="[
                            'p-3.5 hover:bg-slate-50 dark:hover:bg-zinc-800/60 transition-colors flex items-start gap-3 cursor-pointer',
                            item.unread ? 'bg-purple-50/30 dark:bg-purple-950/20' : ''
                        ]"
                    >
                        <!-- Icon -->
                        <div
                            :class="[
                                'w-8 h-8 rounded-xl flex items-center justify-center shrink-0 text-sm mt-0.5',
                                item.type === 'chat' ? 'bg-cyan-50 dark:bg-cyan-950/50 text-[#06B6D4]' :
                                item.type === 'campaign' ? 'bg-purple-50 dark:bg-purple-950/50 text-[#6C5CE7]' :
                                'bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600'
                            ]"
                        >
                            <span v-if="item.type === 'chat'">💬</span>
                            <span v-else-if="item.type === 'campaign'">📢</span>
                            <span v-else>✓</span>
                        </div>

                        <!-- Content -->
                        <div class="flex-1 min-w-0 space-y-0.5">
                            <div class="flex items-center justify-between">
                                <h5 class="text-xs font-semibold text-slate-900 dark:text-white truncate">
                                    {{ item.title }}
                                </h5>
                                <span class="text-[10px] text-slate-400 dark:text-zinc-500 whitespace-nowrap ml-2">
                                    {{ item.time }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-zinc-400 line-clamp-2 leading-relaxed">
                                {{ item.description }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="p-2.5 border-t border-slate-100 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-900/40 text-center">
                    <Link
                        href="/chats"
                        class="text-xs font-semibold text-[#6C5CE7] dark:text-purple-400 hover:text-[#5B46D6] transition-colors"
                    >
                        View all activity →
                    </Link>
                </div>
            </PopoverPanel>
        </transition>
    </Popover>
</template>
