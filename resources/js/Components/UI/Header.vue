<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import { useCommandPalette } from '@/Composables/useCommandPalette';
import { useTheme } from '@/Composables/useTheme';
import NotificationPopover from './NotificationPopover.vue';
import LangToggle from '@/Components/LangToggle.vue';
import Avatar from './Avatar.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownItem from '@/Components/DropdownItem.vue';
import DropdownItemGroup from '@/Components/DropdownItemGroup.vue';

const props = defineProps({
    user: {
        type: Object,
        default: () => ({}),
    },
    organization: {
        type: Object,
        default: () => ({}),
    },
    organizations: {
        type: Array,
        default: () => [],
    },
    unreadMessages: {
        type: Number,
        default: 0,
    },
    languages: {
        type: Object,
        default: () => ({}),
    },
    currentLanguage: {
        type: String,
        default: 'en',
    },
});

const emit = defineEmits(['openProfile', 'switchTeams']);

const { open: openCommandPalette } = useCommandPalette();
const { isDark, toggleTheme } = useTheme();

const userFullName = computed(() => {
    if (!props.user) return 'User';
    return `${props.user.first_name || ''} ${props.user.last_name || ''}`.trim() || props.user.email || 'User';
});
</script>

<template>
    <header class="sticky top-0 z-30 h-16 w-full glass-header px-4 sm:px-6 flex items-center justify-between transition-colors">
        <!-- Left Section: Search / Command Trigger -->
        <div class="flex items-center gap-3">
            <button
                type="button"
                class="inline-flex items-center gap-2.5 px-3 py-1.5 text-xs sm:text-sm text-slate-500 dark:text-zinc-400 bg-slate-100/80 dark:bg-zinc-800/80 hover:bg-slate-200/80 dark:hover:bg-zinc-700/80 rounded-xl border border-slate-200/80 dark:border-zinc-700/80 transition-all duration-150 cursor-pointer shadow-subtle group"
                @click="openCommandPalette"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400 dark:text-zinc-500 group-hover:text-[#6C5CE7] transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                <span class="hidden sm:inline">Search or command...</span>
                <span class="sm:hidden">Search...</span>
                <kbd class="hidden md:inline-flex items-center gap-0.5 px-1.5 py-0.5 text-[10px] font-mono font-medium text-slate-400 dark:text-zinc-500 bg-white dark:bg-zinc-900 rounded border border-slate-200 dark:border-zinc-700 ml-2">
                    ⌘K
                </kbd>
            </button>
        </div>

        <!-- Right Section: Actions, Notifications, Theme, Profile -->
        <div class="flex items-center gap-2 sm:gap-3">
            <!-- Workspace / Organization pill -->
            <button
                v-if="organization && organization.name"
                type="button"
                class="hidden md:inline-flex items-center gap-1.5 px-2.5 py-1 text-xs font-semibold text-[#6C5CE7] dark:text-purple-300 bg-purple-50 dark:bg-purple-950/40 rounded-lg border border-purple-200/60 dark:border-purple-800/40 hover:bg-purple-100 dark:hover:bg-purple-900/50 transition-colors"
                @click="emit('switchTeams')"
                title="Switch Workspace"
            >
                <span class="w-1.5 h-1.5 rounded-full bg-[#6C5CE7] animate-pulse"></span>
                <span class="max-w-[120px] truncate">{{ organization.name }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3 h-3 text-purple-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m6 9 6 6 6-6"/></svg>
            </button>

            <!-- Language Switcher -->
            <LangToggle
                v-if="languages && Object.keys(languages).length > 0"
                :languages="languages"
                :currentLanguage="currentLanguage"
                class="hidden sm:block"
            />

            <!-- Dark / Light Mode Toggle -->
            <button
                type="button"
                class="rounded-xl p-2 text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 hover:text-slate-900 dark:hover:text-white transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-[#6C5CE7]"
                :title="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
                @click="toggleTheme"
            >
                <!-- Sun Icon for Dark Mode -->
                <svg v-if="isDark" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-amber-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="5"/>
                    <line x1="12" y1="1" x2="12" y2="3"/>
                    <line x1="12" y1="21" x2="12" y2="23"/>
                    <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"/>
                    <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"/>
                    <line x1="1" y1="12" x2="3" y2="12"/>
                    <line x1="21" y1="12" x2="23" y2="12"/>
                    <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"/>
                    <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"/>
                </svg>
                <!-- Moon Icon for Light Mode -->
                <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 text-slate-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"/>
                </svg>
            </button>

            <!-- Notification Popover -->
            <NotificationPopover :unreadCount="unreadMessages" />

            <!-- User Profile Dropdown -->
            <Dropdown align="right" width="w-56">
                <button
                    type="button"
                    class="flex items-center gap-2 p-1 rounded-xl hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors focus:outline-none"
                >
                    <Avatar
                        :src="user.avatar ? '/media/' + user.avatar : null"
                        :name="userFullName"
                        size="sm"
                    />
                    <div class="hidden lg:block text-left text-xs leading-tight">
                        <div class="font-semibold text-slate-800 dark:text-zinc-200 max-w-[100px] truncate">
                            {{ userFullName }}
                        </div>
                        <div class="text-[10px] text-slate-400 dark:text-zinc-500 capitalize">
                            {{ user.role || 'Member' }}
                        </div>
                    </div>
                </button>

                <template #items>
                    <DropdownItemGroup>
                        <div class="px-3 py-2 border-b border-slate-100 dark:border-zinc-800/80">
                            <p class="text-xs font-semibold text-slate-900 dark:text-white truncate">
                                {{ userFullName }}
                            </p>
                            <p class="text-[11px] text-slate-400 dark:text-zinc-500 truncate">
                                {{ user.email }}
                            </p>
                        </div>
                        <DropdownItem as="button" @click="emit('openProfile')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            <span>Profile Settings</span>
                        </DropdownItem>
                        <DropdownItem as="button" @click="emit('switchTeams')">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <span>Switch Workspace</span>
                        </DropdownItem>
                        <DropdownItem as="a" href="/settings">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                            <span>Settings</span>
                        </DropdownItem>
                    </DropdownItemGroup>
                    <DropdownItemGroup>
                        <DropdownItem as="a" href="/logout" class="text-rose-600 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/40">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                            <span>Sign Out</span>
                        </DropdownItem>
                    </DropdownItemGroup>
                </template>
            </Dropdown>
        </div>
    </header>
</template>
