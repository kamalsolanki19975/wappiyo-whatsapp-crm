<script setup>
import { ref, computed, watch, nextTick } from 'vue';
import { router } from '@inertiajs/vue3';
import {
    TransitionRoot,
    TransitionChild,
    Dialog,
    DialogPanel,
} from '@headlessui/vue';
import { useCommandPalette } from '@/Composables/useCommandPalette';
import { useTheme } from '@/Composables/useTheme';

const { isOpen, close } = useCommandPalette();
const { toggleTheme, isDark } = useTheme();

const searchQuery = ref('');
const selectedIndex = ref(0);
const searchInput = ref(null);

const baseCommands = [
    // Quick Actions
    {
        id: 'action-create-campaign',
        title: 'Create Campaign',
        category: 'Quick Actions',
        icon: '✦',
        shortcut: 'C',
        action: () => router.visit('/campaigns/create'),
    },
    {
        id: 'action-add-contact',
        title: 'Add Contact',
        category: 'Quick Actions',
        icon: '+',
        shortcut: 'A',
        action: () => router.visit('/contacts/add'),
    },
    {
        id: 'action-create-template',
        title: 'Create Message Template',
        category: 'Quick Actions',
        icon: '💬',
        shortcut: 'T',
        action: () => router.visit('/templates/create'),
    },
    {
        id: 'action-toggle-theme',
        title: 'Toggle Dark / Light Mode',
        category: 'Quick Actions',
        icon: '⚡',
        shortcut: 'M',
        action: () => toggleTheme(),
    },
    // Navigation
    {
        id: 'nav-dashboard',
        title: 'Dashboard',
        category: 'Navigation',
        icon: '⊞',
        action: () => router.visit('/dashboard'),
    },
    {
        id: 'nav-chats',
        title: 'Inbox / Chats',
        category: 'Navigation',
        icon: '💬',
        action: () => router.visit('/chats'),
    },
    {
        id: 'nav-contacts',
        title: 'Contacts',
        category: 'Navigation',
        icon: '👥',
        action: () => router.visit('/contacts'),
    },
    {
        id: 'nav-campaigns',
        title: 'Campaigns',
        category: 'Navigation',
        icon: '📢',
        action: () => router.visit('/campaigns'),
    },
    {
        id: 'nav-templates',
        title: 'Message Templates',
        category: 'Navigation',
        icon: '📋',
        action: () => router.visit('/templates'),
    },
    {
        id: 'nav-automation',
        title: 'Automation Workflows',
        category: 'Navigation',
        icon: '⚡',
        action: () => router.visit('/automation/basic'),
    },
    {
        id: 'nav-team',
        title: 'Team & Members',
        category: 'Navigation',
        icon: '🛡️',
        action: () => router.visit('/team'),
    },
    {
        id: 'nav-billing',
        title: 'Billing & Invoices',
        category: 'Billing & Plans',
        icon: '💳',
        action: () => router.visit('/billing'),
    },
    {
        id: 'nav-plans',
        title: 'Upgrade & Subscription Plans',
        category: 'Billing & Plans',
        icon: '⭐',
        action: () => router.visit('/subscription'),
    },
    {
        id: 'nav-settings',
        title: 'General Settings & Organization',
        category: 'Settings',
        icon: '⚙️',
        action: () => router.visit('/settings'),
    },
    {
        id: 'nav-settings-whatsapp',
        title: 'WhatsApp Cloud API Connection',
        category: 'Settings',
        icon: '📱',
        action: () => router.visit('/settings/whatsapp'),
    },
    {
        id: 'nav-settings-contacts',
        title: 'Custom Contact Fields',
        category: 'Settings',
        icon: '🏷️',
        action: () => router.visit('/settings/contacts'),
    },
    {
        id: 'nav-settings-tickets',
        title: 'Ticket Workflow & Auto-Assignment',
        category: 'Settings',
        icon: '🎫',
        action: () => router.visit('/settings/tickets'),
    },
    {
        id: 'nav-settings-automation',
        title: 'Automation Reply Priority Sequence',
        category: 'Settings',
        icon: '⚡',
        action: () => router.visit('/settings/automation'),
    },
    {
        id: 'nav-support',
        title: 'Support & Tickets',
        category: 'Navigation',
        icon: '🎧',
        action: () => router.visit('/support'),
    },
    {
        id: 'nav-developer',
        title: 'Developer Tools & API Keys',
        category: 'Developer',
        icon: '🔑',
        action: () => router.visit('/developer-tools/access-tokens'),
    },
];

const filteredCommands = computed(() => {
    if (!searchQuery.value.trim()) return baseCommands;
    const q = searchQuery.value.toLowerCase().trim();
    return baseCommands.filter(item =>
        item.title.toLowerCase().includes(q) ||
        item.category.toLowerCase().includes(q)
    );
});

const groupedCommands = computed(() => {
    const groups = {};
    filteredCommands.value.forEach((cmd) => {
        if (!groups[cmd.category]) groups[cmd.category] = [];
        groups[cmd.category].push(cmd);
    });
    return groups;
});

const executeCommand = (cmd) => {
    if (cmd && cmd.action) {
        close();
        cmd.action();
    }
};

const handleKeyDown = (e) => {
    const total = filteredCommands.value.length;
    if (total === 0) return;

    if (e.key === 'ArrowDown') {
        e.preventDefault();
        selectedIndex.value = (selectedIndex.value + 1) % total;
    } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        selectedIndex.value = (selectedIndex.value - 1 + total) % total;
    } else if (e.key === 'Enter') {
        e.preventDefault();
        const cmd = filteredCommands.value[selectedIndex.value];
        if (cmd) executeCommand(cmd);
    }
};

watch(searchQuery, () => {
    selectedIndex.value = 0;
});

watch(isOpen, (newVal) => {
    if (newVal) {
        searchQuery.value = '';
        selectedIndex.value = 0;
        nextTick(() => {
            if (searchInput.value) searchInput.value.focus();
        });
    }
});
</script>

<template>
    <TransitionRoot appear :show="isOpen" as="template">
        <Dialog as="div" class="relative z-50" @close="close">
            <!-- Backdrop -->
            <TransitionChild
                as="template"
                enter="duration-200 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-150 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-md transition-opacity" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto p-4 sm:p-6 md:p-20">
                <div class="flex min-h-full items-start justify-center">
                    <TransitionChild
                        as="template"
                        enter="duration-200 ease-out"
                        enter-from="opacity-0 scale-95 -translate-y-4"
                        enter-to="opacity-100 scale-100 translate-y-0"
                        leave="duration-150 ease-in"
                        leave-from="opacity-100 scale-100 translate-y-0"
                        leave-to="opacity-0 scale-95 -translate-y-4"
                    >
                        <DialogPanel
                            class="w-full max-w-xl transform overflow-hidden rounded-2xl bg-white/95 dark:bg-[#111113]/95 text-left align-middle shadow-floating border border-slate-200/80 dark:border-zinc-800 backdrop-blur-xl transition-all"
                            @keydown="handleKeyDown"
                        >
                            <!-- Search Header -->
                            <div class="relative flex items-center px-4 py-3.5 border-b border-slate-100 dark:border-zinc-800">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-400 dark:text-zinc-500 mr-3 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                                
                                <input
                                    ref="searchInput"
                                    v-model="searchQuery"
                                    type="text"
                                    placeholder="Search Wappiyo or type a command..."
                                    class="w-full bg-transparent border-0 text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-zinc-500 text-sm focus:outline-none focus:ring-0"
                                />

                                <kbd class="hidden sm:inline-flex items-center gap-0.5 px-2 py-0.5 text-[11px] font-mono text-slate-400 dark:text-zinc-500 bg-slate-100 dark:bg-zinc-800 rounded border border-slate-200 dark:border-zinc-700">
                                    ESC
                                </kbd>
                            </div>

                            <!-- Command Results List -->
                            <div class="max-h-80 overflow-y-auto p-2">
                                <div v-if="filteredCommands.length === 0" class="py-12 text-center text-sm text-slate-400 dark:text-zinc-500">
                                    No commands found for "<span class="font-semibold text-slate-600 dark:text-zinc-300">{{ searchQuery }}</span>"
                                </div>

                                <div v-for="(cmds, category) in groupedCommands" :key="category" class="mb-3 last:mb-0">
                                    <div class="px-3 py-1.5 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                                        {{ category }}
                                    </div>
                                    <div class="space-y-0.5">
                                        <button
                                            v-for="cmd in cmds"
                                            :key="cmd.id"
                                            type="button"
                                            :class="[
                                                'w-full flex items-center justify-between px-3 py-2 rounded-xl text-sm transition-all duration-100 text-left',
                                                filteredCommands[selectedIndex]?.id === cmd.id
                                                    ? 'bg-purple-50 dark:bg-purple-950/60 text-[#6C5CE7] dark:text-purple-300 font-medium'
                                                    : 'text-slate-700 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800/60'
                                            ]"
                                            @click="executeCommand(cmd)"
                                            @mouseenter="selectedIndex = filteredCommands.findIndex(c => c.id === cmd.id)"
                                        >
                                            <div class="flex items-center gap-3">
                                                <span class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 flex items-center justify-center text-xs shrink-0">
                                                    {{ cmd.icon }}
                                                </span>
                                                <span>{{ cmd.title }}</span>
                                            </div>

                                            <div class="flex items-center gap-1.5">
                                                <kbd
                                                    v-if="cmd.shortcut"
                                                    class="px-1.5 py-0.5 text-[10px] font-mono text-slate-400 dark:text-zinc-500 bg-slate-100 dark:bg-zinc-800 rounded border border-slate-200 dark:border-zinc-700"
                                                >
                                                    {{ cmd.shortcut }}
                                                </kbd>
                                                <svg
                                                    v-if="filteredCommands[selectedIndex]?.id === cmd.id"
                                                    class="w-4 h-4 text-[#6C5CE7] dark:text-purple-400"
                                                    viewBox="0 0 24 24"
                                                    fill="none"
                                                    stroke="currentColor"
                                                    stroke-width="2"
                                                >
                                                    <polyline points="9 18 15 12 9 6" />
                                                </svg>
                                            </div>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Footer helper bar -->
                            <div class="flex items-center justify-between px-4 py-2.5 bg-slate-50 dark:bg-zinc-900/70 border-t border-slate-100 dark:border-zinc-800 text-xs text-slate-400 dark:text-zinc-500">
                                <div class="flex items-center gap-3">
                                    <span class="inline-flex items-center gap-1">
                                        <kbd class="px-1.5 py-0.5 text-[10px] font-mono bg-white dark:bg-zinc-800 rounded border border-slate-200 dark:border-zinc-700">↑</kbd>
                                        <kbd class="px-1.5 py-0.5 text-[10px] font-mono bg-white dark:bg-zinc-800 rounded border border-slate-200 dark:border-zinc-700">↓</kbd>
                                        to navigate
                                    </span>
                                    <span class="inline-flex items-center gap-1">
                                        <kbd class="px-1.5 py-0.5 text-[10px] font-mono bg-white dark:bg-zinc-800 rounded border border-slate-200 dark:border-zinc-700">↵</kbd>
                                        to select
                                    </span>
                                </div>
                                <span class="text-[11px] font-medium text-[#6C5CE7] dark:text-purple-400">
                                    Wappiyo Search
                                </span>
                            </div>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>
