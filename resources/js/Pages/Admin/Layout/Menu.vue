<script setup>
import { computed, ref } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import ProfileModal from '@/Components/ProfileModal.vue';
import LangToggle from '@/Components/LangToggle.vue';
import Avatar from '@/Components/UI/Avatar.vue';

const props = defineProps({
    config: {
        type: Array,
        required: true
    },
    user: {
        type: Object,
        required: true,
    },
    organization: {
        type: Object,
        default: () => ({}),
    },
    organizations: {
        type: Object,
        default: () => ({}),
    },
    isSidebarOpen: {
        type: Boolean,
        default: false
    }
});

const languages = computed(() => usePage().props.languages);
const currentLanguage = computed(() => usePage().props.currentLanguage);
const isOpen = ref(false);

const emit = defineEmits(['closeSidebar']);

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

const userFullName = computed(() => {
    if (!props.user) return 'Admin';
    return `${props.user.first_name || ''} ${props.user.last_name || ''}`.trim() || props.user.email || 'Admin';
});
</script>

<template>
    <div class="flex flex-col h-full bg-white dark:bg-[#111113] border-r border-slate-200/80 dark:border-zinc-800 text-slate-800 dark:text-zinc-200 transition-colors">
        <!-- Logo / Brand Header -->
        <div class="flex items-center justify-between px-4 h-16 border-b border-slate-100 dark:border-zinc-800/80 shrink-0">
            <Link href="/admin/dashboard" class="flex items-center gap-2.5 min-w-0" @click="closeSidebar">
                <div v-if="getValueByKey('logo')" class="h-8 flex items-center">
                    <img :src="'/media/' + getValueByKey('logo')" :alt="getValueByKey('company_name') || 'Wappiyo Admin'" class="max-h-8 object-contain">
                </div>
                <div v-else class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-[#6C5CE7] to-[#EC4899] flex items-center justify-center text-white font-bold text-sm shadow-md shadow-purple-600/30 shrink-0">
                        A
                    </div>
                    <div class="min-w-0">
                        <span class="font-extrabold text-base tracking-tight text-slate-900 dark:text-white truncate block">
                            {{ getValueByKey('company_name') || 'Wappiyo' }}
                        </span>
                        <span class="text-[10px] font-bold uppercase tracking-wider text-[#6C5CE7] dark:text-purple-400 block -mt-1">
                            Admin Center
                        </span>
                    </div>
                </div>
            </Link>

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
            <!-- Management Section -->
            <div class="space-y-1">
                <div class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500 mb-1.5 select-none">
                    Platform Management
                </div>

                <!-- Dashboard -->
                <Link
                    href="/admin/dashboard"
                    :class="[
                        'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150',
                        $page.url.startsWith('/admin/dashboard')
                            ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                            : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white'
                    ]"
                    @click="closeSidebar"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="7" height="9" x="3" y="3" rx="1"/><rect width="7" height="5" x="14" y="3" rx="1"/><rect width="7" height="9" x="14" y="12" rx="1"/><rect width="7" height="5" x="3" y="16" rx="1"/></svg>
                    <span>{{ $t('Dashboard') }}</span>
                </Link>

                <!-- Organizations -->
                <Link
                    href="/admin/organizations"
                    :class="[
                        'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150',
                        $page.url.startsWith('/admin/organization')
                            ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                            : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white'
                    ]"
                    @click="closeSidebar"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 22V4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v18Z"/><path d="M6 12H4a2 2 0 0 0-2 2v8h4"/><path d="M18 9h2a2 2 0 0 1 2 2v11h-4"/><path d="M10 6h4"/><path d="M10 10h4"/><path d="M10 14h4"/><path d="M10 18h4"/></svg>
                    <span>{{ $t('Organizations') }}</span>
                </Link>

                <!-- Users -->
                <Link
                    href="/admin/users"
                    :class="[
                        'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150',
                        $page.url.startsWith('/admin/user')
                            ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                            : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white'
                    ]"
                    @click="closeSidebar"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    <span>{{ $t('Users') }}</span>
                </Link>

                <!-- Billing Logs -->
                <Link
                    href="/admin/payment-logs"
                    :class="[
                        'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150',
                        $page.url.startsWith('/admin/payment-logs')
                            ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                            : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white'
                    ]"
                    @click="closeSidebar"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="5" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                    <span>{{ $t('Billing') }}</span>
                </Link>

                <!-- Support Desk -->
                <Link
                    href="/admin/support"
                    :class="[
                        'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150',
                        $page.url.startsWith('/admin/support')
                            ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                            : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white'
                    ]"
                    @click="closeSidebar"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M18 18.72a9 9 0 1 1 0-13.44"/><path d="M12 8v4"/><path d="M12 16h.01"/><circle cx="12" cy="12" r="9"/></svg>
                    <span>{{ $t('Support desk') }}</span>
                </Link>

                <!-- Team Users -->
                <Link
                    href="/admin/team/users"
                    :class="[
                        'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150',
                        $page.url.startsWith('/admin/team/users')
                            ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                            : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white'
                    ]"
                    @click="closeSidebar"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>
                    <span>{{ $t('Team') }}</span>
                </Link>
            </div>

            <!-- Configuration Section -->
            <div class="space-y-1">
                <div class="px-3 text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500 mb-1.5 select-none">
                    System Configuration
                </div>

                <!-- Roles -->
                <Link
                    href="/admin/team/roles"
                    :class="[
                        'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150',
                        $page.url.startsWith('/admin/team/roles')
                            ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                            : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white'
                    ]"
                    @click="closeSidebar"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10"/></svg>
                    <span>{{ $t('Roles') }}</span>
                </Link>

                <!-- Subscription Plans -->
                <Link
                    href="/admin/plans"
                    :class="[
                        'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150',
                        $page.url.startsWith('/admin/plan')
                            ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                            : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white'
                    ]"
                    @click="closeSidebar"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2v20"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
                    <span>{{ $t('Subscription plans') }}</span>
                </Link>

                <!-- FAQs -->
                <Link
                    href="/admin/faqs"
                    :class="[
                        'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150',
                        $page.url.startsWith('/admin/faq')
                            ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                            : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white'
                    ]"
                    @click="closeSidebar"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>
                    <span>{{ $t('FAQs') }}</span>
                </Link>

                <!-- Testimonials -->
                <Link
                    href="/admin/testimonials"
                    :class="[
                        'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150',
                        $page.url.startsWith('/admin/testimonial')
                            ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                            : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white'
                    ]"
                    @click="closeSidebar"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                    <span>{{ $t('Reviews') }}</span>
                </Link>

                <!-- Settings -->
                <Link
                    href="/admin/settings/general"
                    :class="[
                        'flex items-center gap-3 px-3 py-2 rounded-xl text-sm font-medium transition-all duration-150',
                        $page.url.startsWith('/admin/setting')
                            ? 'bg-[#6C5CE7] text-white shadow-sm shadow-purple-600/30'
                            : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800/80 hover:text-slate-900 dark:hover:text-white'
                    ]"
                    @click="closeSidebar"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
                    <span>{{ $t('Settings') }}</span>
                </Link>
            </div>
        </div>

        <!-- Addons Pill & User footer -->
        <div class="p-3 border-t border-slate-100 dark:border-zinc-800/80 bg-slate-50/50 dark:bg-zinc-900/40 space-y-2 shrink-0">
            <!-- Addons Link -->
            <Link
                href="/admin/addons"
                class="w-full flex items-center justify-between p-2 rounded-xl border border-purple-200/60 dark:border-purple-800/40 bg-purple-50/50 dark:bg-purple-950/30 text-[#6C5CE7] dark:text-purple-300 hover:bg-purple-100/60 dark:hover:bg-purple-900/40 transition-colors"
                @click="closeSidebar"
            >
                <div class="flex items-center gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m7.5 4.27 9 5.15"/><path d="M21 8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16Z"/><path d="m3.3 7 8.7 5 8.7-5"/><path d="M12 22V12"/></svg>
                    <span class="text-xs font-bold">{{ $t('Addons') }}</span>
                </div>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
            </Link>

            <!-- Admin User profile footer -->
            <div class="flex items-center justify-between p-1.5 rounded-xl">
                <div class="flex items-center gap-2.5 min-w-0 cursor-pointer" @click="openModal">
                    <Avatar
                        :src="user.avatar ? '/media/' + user.avatar : null"
                        :name="userFullName"
                        size="sm"
                    />
                    <div class="min-w-0">
                        <h4 class="text-xs font-semibold text-slate-900 dark:text-white truncate">
                            {{ userFullName }}
                        </h4>
                        <span class="text-[11px] text-[#6C5CE7] dark:text-purple-400 hover:underline block leading-tight">
                            {{ $t('View profile') }}
                        </span>
                    </div>
                </div>

                <Link
                    href="/logout"
                    class="p-1.5 rounded-lg text-slate-400 dark:text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors"
                    title="Sign out"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                </Link>
            </div>
        </div>

        <ProfileModal :user="props.user" :organization="{}" :isOpen="isOpen" role="admin" @close="closeModal()"/>
    </div>
</template>