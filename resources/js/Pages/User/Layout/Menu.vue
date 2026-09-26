<script setup>
import { computed, ref } from "vue";
import { Link, useForm, usePage } from "@inertiajs/vue3";
import Modal from '@/Components/Modal.vue';
import ProfileModal from '@/Components/ProfileModal.vue';
import LangToggle from '@/Components/LangToggle.vue';
import Avatar from '@/Components/UI/Avatar.vue';
import Tooltip from '@/Components/UI/Tooltip.vue';

const props = defineProps(['config', 'user', 'organization', 'organizations', 'isSidebarOpen', 'menuIconsOnly', 'unreadMessages']);

const languages = computed(() => usePage().props.languages);
const currentLanguage = computed(() => usePage().props.currentLanguage);
const isOpen = ref(false);
const isLocationSwitchModalOpen = ref(false);

const emit = defineEmits(['closeSidebar']);

const form = useForm({
    uuid: null,
});

const getValueByKey = (key) => {
    if (!props.config || !Array.isArray(props.config)) return '';
    const found = props.config.find(item => item.key === key);
    return found ? found.value : '';
};

const closeSidebar = () => {
    emit('closeSidebar', true);
};

const closeModal = () => {
    isOpen.value = false;
};

const openModal = () => {
    isOpen.value = true;
    emit('closeSidebar', true);
};

const switchTeams = () => {
    isLocationSwitchModalOpen.value = true; 
    emit('closeSidebar', true);
};

const selectOrganization = (uuid) => {
    form.uuid = uuid;
    submitForm();
};

const submitForm = async () => {
    form.post('/organization', {
        preserveScroll: true,
        onFinish: () => { isLocationSwitchModalOpen.value = false; },
    });
};

const userFullName = computed(() => {
    if (!props.user) return 'User';
    return `${props.user.first_name || ''} ${props.user.last_name || ''}`.trim() || props.user.email || 'User';
});

const isOwner = computed(() => {
    return props.user?.teams?.[0]?.role === 'owner';
});
</script>

<template>
    <div class="flex flex-col h-full bg-white dark:bg-[#111113] border-r border-slate-200/80 dark:border-zinc-800 text-slate-800 dark:text-zinc-200 transition-colors">
        <!-- Logo / Brand Header -->
        <div class="flex items-center justify-between px-4 h-16 border-b border-slate-100 dark:border-zinc-800/80 shrink-0">
            <Link href="/dashboard" class="flex items-center gap-2.5 min-w-0" @click="closeSidebar">
                <div v-if="getValueByKey('logo')" class="h-8 flex items-center">
                    <img :src="'/media/' + getValueByKey('logo')" :alt="getValueByKey('company_name') || 'Wappiyo'" class="max-h-8 object-contain">
                </div>
                <div v-else class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-[#6C5CE7] to-[#8B5CF6] flex items-center justify-center text-white font-bold text-sm shadow-md shadow-purple-600/30 shrink-0">
                        W
                    </div>
                    <span v-if="!menuIconsOnly" class="font-extrabold text-lg tracking-tight text-slate-900 dark:text-white truncate">
                        {{ getValueByKey('company_name') || 'Wappiyo' }}
                    </span>
                </div>
            </Link>

            <!-- Close button on mobile -->
            <button
                v-if="isSidebarOpen"
                type="button"
                class="md:hidden rounded-lg p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors"
                @click="closeSidebar"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
        </div>

        <!-- Navigation items list -->
        <div class="flex-1 overflow-y-auto px-3 py-4 space-y-6">
            <!-- Main Section -->
            <div class="space-y-1">
                <div v-if="!menuIconsOnly" class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500 mb-1.5 select-none">
                    Menu
                </div>

                <!-- Dashboard -->
                <Tooltip :content="$t('Dashboard')" :position="'right'" :className="!menuIconsOnly ? 'hidden' : ''">
                    <Link
                        href="/dashboard"
                        :class="[
                            'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150',
                            $page.url.startsWith('/dashboard')
                                ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                                : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white',
                            menuIconsOnly ? 'justify-center px-2' : ''
                        ]"
                        @click="closeSidebar"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                        <span v-if="!menuIconsOnly" class="truncate">{{ $t('Dashboard') }}</span>
                    </Link>
                </Tooltip>

                <!-- Analytics -->
                <Tooltip :content="$t('Analytics')" :position="'right'" :className="!menuIconsOnly ? 'hidden' : ''">
                    <Link
                        href="/analytics"
                        :class="[
                            'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150',
                            $page.url.startsWith('/analytics')
                                ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                                : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white',
                            menuIconsOnly ? 'justify-center px-2' : ''
                        ]"
                        @click="closeSidebar"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <line x1="18" y1="20" x2="18" y2="10" />
                            <line x1="12" y1="20" x2="12" y2="4" />
                            <line x1="6" y1="20" x2="6" y2="14" />
                        </svg>
                        <span v-if="!menuIconsOnly" class="truncate">{{ $t('Analytics') }}</span>
                    </Link>
                </Tooltip>

                <!-- Chats -->
                <Tooltip :content="$t('Chats')" :position="'right'" :className="!menuIconsOnly ? 'hidden' : ''">
                    <Link
                        href="/chats"
                        :class="[
                            'flex items-center justify-between px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150',
                            $page.url.startsWith('/chats')
                                ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                                : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white',
                            menuIconsOnly ? 'justify-center px-2' : ''
                        ]"
                        @click="closeSidebar"
                    >
                        <div class="flex items-center gap-3 min-w-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M7.9 20A9 9 0 1 0 4 16.1L2 22Z"/></svg>
                            <span v-if="!menuIconsOnly" class="truncate">{{ $t('Chats') }}</span>
                        </div>
                        <span
                            v-if="parseInt(unreadMessages) > 0"
                            :class="[
                                'px-1.5 py-0.5 text-[10px] font-bold rounded-full',
                                $page.url.startsWith('/chats') ? 'bg-white text-[#6C5CE7]' : 'bg-[#EC4899] text-white shadow-sm shadow-pink-500/30',
                                menuIconsOnly ? 'absolute top-1 right-1 w-2 h-2 p-0 text-transparent' : ''
                            ]"
                        >
                            {{ unreadMessages }}
                        </span>
                    </Link>
                </Tooltip>

                <!-- Contacts -->
                <Tooltip :content="$t('Contacts')" :position="'right'" :className="!menuIconsOnly ? 'hidden' : ''">
                    <Link
                        href="/contacts"
                        :class="[
                            'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150',
                            $page.url.startsWith('/contact')
                                ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                                : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white',
                            menuIconsOnly ? 'justify-center px-2' : ''
                        ]"
                        @click="closeSidebar"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                        <span v-if="!menuIconsOnly" class="truncate">{{ $t('Contacts') }}</span>
                    </Link>
                </Tooltip>

                <!-- Campaigns -->
                <Tooltip :content="$t('Campaigns')" :position="'right'" :className="!menuIconsOnly ? 'hidden' : ''">
                    <Link
                        href="/campaigns"
                        :class="[
                            'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150',
                            $page.url.startsWith('/campaign')
                                ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                                : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white',
                            menuIconsOnly ? 'justify-center px-2' : ''
                        ]"
                        @click="closeSidebar"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m3 11 18-5v12L3 14v-3z"/><path d="M11.6 16.8a3 3 0 1 1-5.8-1.6"/></svg>
                        <span v-if="!menuIconsOnly" class="truncate">{{ $t('Campaigns') }}</span>
                    </Link>
                </Tooltip>

                <!-- Message Templates -->
                <Tooltip :content="$t('Message templates')" :position="'right'" :className="!menuIconsOnly ? 'hidden' : ''">
                    <Link
                        href="/templates"
                        :class="[
                            'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150',
                            $page.url.startsWith('/template')
                                ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                                : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white',
                            menuIconsOnly ? 'justify-center px-2' : ''
                        ]"
                        @click="closeSidebar"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><line x1="10" y1="9" x2="8" y2="9"/></svg>
                        <span v-if="!menuIconsOnly" class="truncate">{{ $t('Message templates') }}</span>
                    </Link>
                </Tooltip>

                <!-- Automation -->
                <Tooltip :content="$t('Automation')" :position="'right'" :className="!menuIconsOnly ? 'hidden' : ''">
                    <Link
                        href="/automation/basic"
                        :class="[
                            'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150',
                            $page.url.startsWith('/automation')
                                ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                                : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white',
                            menuIconsOnly ? 'justify-center px-2' : ''
                        ]"
                        @click="closeSidebar"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg>
                        <span v-if="!menuIconsOnly" class="truncate">{{ $t('Automation') }}</span>
                    </Link>
                </Tooltip>
            </div>

            <!-- Workspace Section -->
            <div class="space-y-1">
                <div v-if="!menuIconsOnly" class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500 mb-1.5 select-none">
                    Workspace
                </div>

                <!-- Team -->
                <Tooltip :content="$t('Team')" :position="'right'" :className="!menuIconsOnly ? 'hidden' : ''">
                    <Link
                        href="/team"
                        :class="[
                            'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150',
                            $page.url.startsWith('/team')
                                ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                                : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white',
                            menuIconsOnly ? 'justify-center px-2' : ''
                        ]"
                        @click="closeSidebar"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/><path d="m9 12 2 2 4-4"/></svg>
                        <span v-if="!menuIconsOnly" class="truncate">{{ $t('Team') }}</span>
                    </Link>
                </Tooltip>

                <!-- Settings -->
                <Tooltip :content="$t('Settings')" :position="'right'" :className="!menuIconsOnly ? 'hidden' : ''">
                    <Link
                        href="/settings"
                        :class="[
                            'md:flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150 hidden',
                            $page.url.startsWith('/settings')
                                ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                                : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white',
                            menuIconsOnly ? 'justify-center px-2' : ''
                        ]"
                        @click="closeSidebar"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        <span v-if="!menuIconsOnly" class="truncate">{{ $t('Settings') }}</span>
                    </Link>
                    <Link
                        href="/settings/m"
                        :class="[
                            'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150 md:hidden',
                            $page.url.startsWith('/settings')
                                ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                                : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white',
                        ]"
                        @click="closeSidebar"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                        <span class="truncate">{{ $t('Settings') }}</span>
                    </Link>
                </Tooltip>

                <!-- Billing & Subscription -->
                <Tooltip :content="$t('Billing and subscription')" :position="'right'" :className="!menuIconsOnly ? 'hidden' : ''">
                    <Link
                        href="/billing"
                        :class="[
                            'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150',
                            $page.url.startsWith('/billing') || $page.url.startsWith('/subscription')
                                ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                                : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white',
                            menuIconsOnly ? 'justify-center px-2' : ''
                        ]"
                        @click="closeSidebar"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                        <span v-if="!menuIconsOnly" class="truncate">{{ $t('Billing and subscription') }}</span>
                    </Link>
                </Tooltip>

                <!-- Support -->
                <Tooltip :content="$t('Support')" :position="'right'" :className="!menuIconsOnly ? 'hidden' : ''">
                    <Link
                        href="/support"
                        :class="[
                            'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150',
                            $page.url.startsWith('/support')
                                ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                                : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white',
                            menuIconsOnly ? 'justify-center px-2' : ''
                        ]"
                        @click="closeSidebar"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 18.72a9 9 0 1 1 0-13.44"/><path d="M12 8v4"/><path d="M12 16h.01"/><circle cx="12" cy="12" r="9"/></svg>
                        <span v-if="!menuIconsOnly" class="truncate">{{ $t('Support') }}</span>
                    </Link>
                </Tooltip>

                <!-- Developer Tools (owner only) -->
                <Tooltip v-if="isOwner" :content="$t('Developer Tools')" :position="'right'" :className="!menuIconsOnly ? 'hidden' : ''">
                    <Link
                        href="/developer-tools/access-tokens"
                        :class="[
                            'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150',
                            $page.url.startsWith('/developer-tools')
                                ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                                : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white',
                            menuIconsOnly ? 'justify-center px-2' : ''
                        ]"
                        @click="closeSidebar"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                        <span v-if="!menuIconsOnly" class="truncate">{{ $t('Developer Tools') }}</span>
                    </Link>
                </Tooltip>
            </div>
        </div>

        <!-- Footer / Workspace & Profile Area -->
        <div class="p-3 border-t border-slate-100 dark:border-zinc-800/80 bg-slate-50/50 dark:bg-zinc-900/40 space-y-2 shrink-0">
            <!-- Organization / Team Switcher -->
            <button
                v-if="!menuIconsOnly && props.organization"
                type="button"
                class="w-full flex items-center justify-between p-2 rounded-xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#18181B] hover:border-[#6C5CE7]/60 text-left transition-colors cursor-pointer"
                @click="switchTeams"
            >
                <div class="flex items-center gap-2 min-w-0">
                    <div class="w-7 h-7 rounded-lg bg-purple-50 dark:bg-purple-950/50 text-[#6C5CE7] flex items-center justify-center text-xs font-bold shrink-0">
                        {{ props.organization.name ? props.organization.name[0].toUpperCase() : 'T' }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-[10px] uppercase font-bold text-slate-400 dark:text-zinc-500 leading-none">Team</p>
                        <p class="text-xs font-semibold text-slate-800 dark:text-zinc-200 truncate mt-0.5">
                            {{ props.organization.name }}
                        </p>
                    </div>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7 15 5 5 5-5"/><path d="m7 9 5-5 5 5"/></svg>
            </button>

            <!-- User profile footer bar -->
            <div
                :class="[
                    'flex items-center justify-between p-1.5 rounded-xl transition-colors',
                    menuIconsOnly ? 'justify-center' : ''
                ]"
            >
                <div
                    class="flex items-center gap-2.5 min-w-0 cursor-pointer"
                    @click="openModal"
                >
                    <Avatar
                        :src="user.avatar ? '/media/' + user.avatar : null"
                        :name="userFullName"
                        size="sm"
                    />
                    <div v-if="!menuIconsOnly" class="min-w-0">
                        <h4 class="text-xs font-semibold text-slate-900 dark:text-white truncate">
                            {{ userFullName }}
                        </h4>
                        <span class="text-[11px] text-[#6C5CE7] dark:text-purple-400 hover:underline block leading-tight">
                            {{ $t('View profile') }}
                        </span>
                    </div>
                </div>

                <!-- Sign out action -->
                <Link
                    v-if="!menuIconsOnly"
                    href="/logout"
                    class="p-1.5 rounded-lg text-slate-400 dark:text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors"
                    title="Sign out"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </Link>
            </div>
        </div>

        <!-- Switch Teams Modal (preserved functionality) -->
        <Modal :label="$t('Switch teams')" :isOpen="isLocationSwitchModalOpen" @close="isLocationSwitchModalOpen = false">
            <div class="mt-2 space-y-2">
                <div
                    v-for="(item, index) in props.organizations"
                    :key="index"
                    class="flex items-center justify-between p-3 rounded-xl border border-slate-200 dark:border-zinc-800 hover:border-[#6C5CE7] hover:bg-purple-50/50 dark:hover:bg-purple-950/30 cursor-pointer transition-all duration-150"
                    @click="selectOrganization(item.organization.uuid)"
                >
                    <div class="flex items-center gap-3">
                        <span class="w-9 h-9 rounded-xl bg-purple-100 dark:bg-purple-950/60 text-[#6C5CE7] flex items-center justify-center font-bold text-sm">
                            {{ item.organization.name[0].toUpperCase() }}
                        </span>
                        <div>
                            <h4 class="font-semibold text-sm text-slate-900 dark:text-white">{{ item.organization.name }}</h4>
                            <p class="text-xs text-slate-400 dark:text-zinc-500 capitalize">{{ item.role }}</p>
                        </div>
                    </div>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                </div>
            </div>
            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-zinc-800 flex justify-end">
                <button
                    type="button"
                    class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-zinc-300 bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 rounded-lg transition-colors"
                    @click="isLocationSwitchModalOpen = false"
                >
                    {{ $t('Cancel') }}
                </button>
            </div>
        </Modal>

        <!-- Profile Modal (preserved functionality) -->
        <ProfileModal
            :user="props.user"
            :organization="props.organization"
            :isOpen="isOpen"
            role="user"
            @close="closeModal()"
        />
    </div>
</template>