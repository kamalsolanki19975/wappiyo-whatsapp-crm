<template>
    <AppLayout>
        <div class="flex h-full w-full overflow-hidden bg-slate-50 dark:bg-[#09090B] transition-colors">
            
            <!-- LEFT PANEL: Contacts Directory & Filter Sidebar -->
            <aside
                :class="[
                    'h-full flex-col bg-white dark:bg-[#111113] border-r border-slate-200/80 dark:border-zinc-800 shrink-0 z-10 transition-all duration-200',
                    ($page.url === '/contacts/add' || contact) ? 'hidden md:flex md:w-80 lg:w-88 xl:w-96' : 'flex w-full md:w-80 lg:w-88 xl:w-96'
                ]"
            >
                <!-- Contacts Header with Count and Add Button -->
                <div class="p-3.5 sm:p-4 border-b border-slate-100 dark:border-zinc-800/80 flex items-center justify-between shrink-0">
                    <div class="flex items-center gap-2">
                        <h2 class="text-base font-extrabold tracking-tight text-slate-900 dark:text-white">
                            {{ $t('Contacts') }}
                        </h2>
                        <span class="inline-flex items-center rounded-full bg-slate-100 dark:bg-zinc-800 px-2 py-0.5 text-xs font-semibold text-slate-600 dark:text-zinc-400">
                            {{ props.rowCount ?? 0 }}
                        </span>
                    </div>

                    <div class="flex items-center gap-1.5">
                        <Link
                            href="/contacts/add"
                            class="inline-flex items-center gap-1 px-2.5 py-1.5 rounded-xl text-xs font-semibold bg-[#6C5CE7] hover:bg-[#5B4BC4] text-white shadow-xs transition-colors"
                            :title="$t('Add Contact')"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            <span class="hidden sm:inline">{{ $t('New') }}</span>
                        </Link>
                    </div>
                </div>

                <!-- Contact Table Component -->
                <ContactTable
                    :rows="props.rows"
                    :filters="props.filters"
                    :type="'contact'"
                    :activeUuid="contact?.uuid"
                    @callback="handleContact"
                />
            </aside>

            <!-- RIGHT PANEL: Contact Details, Forms or CRM Overview -->
            <main
                :class="[
                    'flex-1 min-w-0 h-full flex flex-col relative overflow-hidden transition-colors',
                    ($page.url === '/contacts/add' || contact) ? 'flex' : 'hidden md:flex'
                ]"
            >
                <!-- Flash Notification Banner (e.g. from Excel Import) -->
                <div
                    v-if="flashMessage"
                    :class="[
                        'px-4 py-3 border-b text-xs flex items-center justify-between transition-colors z-20 shrink-0',
                        flashType === 'success'
                            ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-300 border-emerald-200 dark:border-emerald-800/40'
                            : 'bg-rose-50 dark:bg-rose-950/40 text-rose-800 dark:text-rose-300 border-rose-200 dark:border-rose-800/40'
                    ]"
                >
                    <div class="flex items-center gap-2">
                        <svg v-if="flashType === 'success'" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                        <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-600 dark:text-rose-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span class="font-medium">{{ flashMessage }}</span>
                    </div>
                    <button type="button" @click="dismissFlash" class="opacity-70 hover:opacity-100">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                </div>

                <!-- 1. VIEW CONTACT CRM PROFILE -->
                <template v-if="contact && !editContact">
                    <ContactInfo
                        :contact="contact"
                        :fields="props.fields || []"
                        :locationSettings="locationSettings"
                    />
                </template>

                <!-- 2. ADD / EDIT CONTACT FORM -->
                <template v-else-if="$page.url === '/contacts/add' || (contact && editContact)">
                    <ContactForm
                        :contactGroups="props.contactGroups || []"
                        :contact="props.contact"
                        :fields="props.fields || []"
                        :locationSettings="locationSettings"
                    />
                </template>

                <!-- 3. ZERO SELECTION: CRM WELCOME DASHBOARD -->
                <template v-else>
                    <div class="flex-1 overflow-y-auto p-6 sm:p-10 flex flex-col items-center justify-center text-center select-none">
                        <div class="max-w-xl mx-auto space-y-6">
                            <!-- Hero Icon with Glow -->
                            <div class="relative inline-flex items-center justify-center">
                                <div class="absolute -inset-4 bg-gradient-to-r from-purple-500/20 via-pink-500/15 to-emerald-500/20 rounded-full blur-xl animate-pulse"></div>
                                <div class="relative w-20 h-20 rounded-3xl bg-white dark:bg-[#111113] shadow-lg border border-slate-200/80 dark:border-zinc-800 flex items-center justify-center text-[#6C5CE7]">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/>
                                    </svg>
                                </div>
                            </div>

                            <!-- Header -->
                            <div class="space-y-2">
                                <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                                    {{ $t('Customer Intelligence & CRM Workspace') }}
                                </h1>
                                <p class="text-sm text-slate-500 dark:text-zinc-400 leading-relaxed max-w-md mx-auto">
                                    {{ $t('Select a contact from the directory on the left to inspect customer details, team notes, custom attributes, and initiate real-time WhatsApp conversations.') }}
                                </p>
                            </div>

                            <!-- Quick Action Cards Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2 text-left">
                                <Link
                                    href="/contacts/add"
                                    class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 hover:border-[#6C5CE7] hover:shadow-subtle transition-all duration-150 group"
                                >
                                    <div class="w-8 h-8 rounded-xl bg-purple-50 dark:bg-purple-950/50 text-[#6C5CE7] flex items-center justify-center mb-2.5 group-hover:scale-105 transition-transform">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    </div>
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-[#6C5CE7] transition-colors">
                                        {{ $t('Add Contact') }}
                                    </h4>
                                    <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-0.5">
                                        {{ $t('Create customer profile') }}
                                    </p>
                                </Link>

                                <button
                                    type="button"
                                    @click="isOpenModal = true"
                                    class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 hover:border-[#6C5CE7] hover:shadow-subtle transition-all duration-150 group text-left"
                                >
                                    <div class="w-8 h-8 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-2.5 group-hover:scale-105 transition-transform">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                    </div>
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-[#6C5CE7] transition-colors">
                                        {{ $t('Excel Import') }}
                                    </h4>
                                    <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-0.5">
                                        {{ $t('Upload CSV or XLSX') }}
                                    </p>
                                </button>

                                <Link
                                    href="/contact-groups"
                                    class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 hover:border-[#6C5CE7] hover:shadow-subtle transition-all duration-150 group"
                                >
                                    <div class="w-8 h-8 rounded-xl bg-blue-50 dark:bg-blue-950/50 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-2.5 group-hover:scale-105 transition-transform">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                                    </div>
                                    <h4 class="text-xs font-bold text-slate-900 dark:text-white group-hover:text-[#6C5CE7] transition-colors">
                                        {{ $t('Contact Groups') }}
                                    </h4>
                                    <p class="text-[11px] text-slate-400 dark:text-zinc-500 mt-0.5">
                                        {{ $t('Organize segments') }}
                                    </p>
                                </Link>
                            </div>

                            <!-- Shortcut Tip -->
                            <div class="pt-4 flex items-center justify-center gap-2 text-xs text-slate-400 dark:text-zinc-500">
                                <span class="inline-flex items-center px-1.5 py-0.5 rounded border border-slate-200 dark:border-zinc-800 bg-slate-100 dark:bg-zinc-800 font-mono text-[11px]">⌘K</span>
                                <span>{{ $t('Press ⌘K to instantly search contacts from anywhere') }}</span>
                            </div>
                        </div>
                    </div>
                </template>
            </main>
        </div>

        <!-- Excel / CSV Import Modal -->
        <ContactImportModal :type="'contact'" v-model:modelValue="isOpenModal"/>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import { ref, computed } from 'vue';
import { Link, router, usePage } from "@inertiajs/vue3";
import ContactForm from '@/Components/ContactComponents/CreateForm.vue';
import ContactImportModal from '@/Components/ContactImportModal.vue';
import ContactInfo from '@/Components/ContactInfo.vue';
import ContactTable from '@/Components/Tables/ContactTable.vue';

const props = defineProps({
    rows: Object,
    filters: Object,
    rowCount: Number,
    contactGroups: Object,
    contact: Object,
    editContact: Boolean,
    fields: Object,
    locationSettings: String,
    flash: Object
});

const isOpenModal = ref(false);
const dismissedFlash = ref(false);

const flashMessage = computed(() => {
    if (dismissedFlash.value) return null;
    return usePage().props.flash?.status?.message || null;
});

const flashType = computed(() => {
    return usePage().props.flash?.status?.type || 'success';
});

const dismissFlash = () => {
    dismissedFlash.value = true;
};

const handleContact = (value) => {
    router.visit('/contacts', {
        method: 'get',
        data: value,
        preserveState: true,
    });
};
</script>