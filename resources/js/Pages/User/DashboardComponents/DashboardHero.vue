<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import Button from '@/Components/UI/Button.vue';
import Badge from '@/Components/UI/Badge.vue';

const props = defineProps({
    user: {
        type: Object,
        default: () => ({}),
    },
    organization: {
        type: Object,
        default: () => ({}),
    },
    setupWhatsapp: {
        type: Boolean,
        default: false,
    },
});

const greeting = computed(() => {
    const hour = new Date().getHours();
    if (hour < 12) return 'Good morning';
    if (hour < 18) return 'Good afternoon';
    return 'Good evening';
});

const todayDate = computed(() => {
    try {
        const options = { weekday: 'long', day: 'numeric', month: 'short', year: 'numeric' };
        return new Intl.DateTimeFormat('en-US', options).format(new Date());
    } catch (_) {
        return new Date().toDateString();
    }
});
</script>

<template>
    <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-5 sm:p-7 shadow-card transition-all">
        <!-- Subtle Ambient Gradient Backdrop -->
        <div class="pointer-events-none absolute -top-24 -right-24 h-72 w-72 rounded-full bg-gradient-to-br from-[#6C5CE7]/15 via-[#8B5CF6]/10 to-transparent blur-3xl dark:from-[#6C5CE7]/25 dark:via-[#8B5CF6]/15" />
        <div class="pointer-events-none absolute -bottom-24 left-1/3 h-64 w-64 rounded-full bg-gradient-to-tr from-[#06B6D4]/10 to-transparent blur-3xl dark:from-[#06B6D4]/15" />

        <div class="relative z-10 flex flex-col gap-5 lg:flex-row lg:items-center lg:justify-between">
            <!-- Left: Greeting & Status Info -->
            <div class="space-y-2">
                <div class="flex flex-wrap items-center gap-2 text-xs">
                    <span class="inline-flex items-center gap-1.5 font-medium text-slate-500 dark:text-zinc-400">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6C5CE7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <rect width="18" height="18" x="3" y="4" rx="2" ry="2"/>
                            <line x1="16" x2="16" y1="2" y2="6"/>
                            <line x1="8" x2="8" y1="2" y2="6"/>
                            <line x1="3" x2="21" y1="10" y2="10"/>
                        </svg>
                        {{ todayDate }}
                    </span>

                    <span class="text-slate-300 dark:text-zinc-700">•</span>

                    <span v-if="organization?.name" class="font-semibold text-slate-700 dark:text-zinc-300">
                        {{ organization.name }}
                    </span>

                    <span class="text-slate-300 dark:text-zinc-700">•</span>

                    <!-- WhatsApp Connection State Badge -->
                    <Badge v-if="!setupWhatsapp" variant="success" size="sm" dot>
                        WhatsApp Connected
                    </Badge>
                    <Badge v-else variant="warning" size="sm" dot>
                        WhatsApp Setup Needed
                    </Badge>
                </div>

                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                        {{ greeting }}, {{ user?.first_name || 'there' }} 👋
                    </h1>
                    <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400 max-w-2xl">
                        {{ $t("Here is what's happening across your WhatsApp CRM and campaign workspace today.") }}
                    </p>
                </div>
            </div>

            <!-- Right: Quick CTA Buttons -->
            <div class="flex flex-wrap items-center gap-2.5 shrink-0 pt-2 lg:pt-0">
                <Link href="/chats">
                    <Button variant="secondary" size="md">
                        <template #icon>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-[#6C5CE7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/>
                            </svg>
                        </template>
                        {{ $t('Open Inbox') }}
                    </Button>
                </Link>

                <Link href="/contacts/add">
                    <Button variant="outline" size="md">
                        <template #icon>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/>
                                <circle cx="9" cy="7" r="4"/>
                                <line x1="19" x2="19" y1="8" y2="14"/>
                                <line x1="22" x2="16" y1="11" y2="11"/>
                            </svg>
                        </template>
                        {{ $t('Add Contact') }}
                    </Button>
                </Link>

                <Link href="/campaigns/create">
                    <Button variant="primary" size="md" glow>
                        <template #icon>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m3 11 19-9-9 19-2-8-8-2z"/>
                            </svg>
                        </template>
                        {{ $t('Create Campaign') }}
                    </Button>
                </Link>
            </div>
        </div>
    </div>
</template>
