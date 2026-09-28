<template>
    <AppLayout>
        <div class="p-4 sm:p-6 lg:p-8 space-y-6 max-w-7xl mx-auto w-full transition-colors duration-200">
            <!-- Navigation Back & Title Banner -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-3">
                    <Link
                        href="/admin/leads"
                        class="p-2 rounded-xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 text-slate-600 dark:text-zinc-300 hover:bg-slate-50 dark:hover:bg-zinc-800 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                    </Link>
                    <div>
                        <div class="flex items-center gap-2">
                            <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white">
                                {{ lead.name }}
                            </h1>
                            <span
                                :class="[
                                    'inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider',
                                    statusClasses[lead.status] || 'bg-slate-100 text-slate-700'
                                ]"
                            >
                                <span class="w-1.5 h-1.5 rounded-full" :class="statusDotClasses[lead.status]"></span>
                                {{ lead.status }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5 flex items-center gap-2">
                            <span>Captured {{ formatDate(lead.created_at) }}</span>
                            <span>•</span>
                            <span class="font-medium text-slate-700 dark:text-zinc-300">{{ lead.source || 'Website Contact Form' }}</span>
                        </p>
                    </div>
                </div>

                <!-- Delete Action -->
                <div class="flex items-center gap-2">
                    <button
                        @click="deleteLead"
                        class="px-3.5 py-2 rounded-xl text-xs font-semibold text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/30 transition-colors inline-flex items-center gap-1.5"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                        <span>{{ $t('Delete Lead') }}</span>
                    </button>
                </div>
            </div>

            <!-- Flash Alert -->
            <div v-if="$page.props.flash?.status" class="p-4 rounded-2xl flex items-center gap-3 text-xs" :class="$page.props.flash.status.type === 'success' ? 'bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300' : 'bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300'">
                <svg v-if="$page.props.flash.status.type === 'success'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                <span>{{ $page.props.flash.status.message }}</span>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                <!-- Left Column (8 cols): Submission Details, Message & Attribution -->
                <div class="lg:col-span-8 space-y-6">
                    <!-- 1. Lead Submission Content -->
                    <div class="rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-6 shadow-sm space-y-5">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-zinc-800/80 pb-4">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                <span>{{ $t('Submission Details') }}</span>
                            </h3>
                            <span class="text-xs px-2 py-0.5 rounded bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 font-mono">
                                {{ lead.form_type || 'contact' }}
                            </span>
                        </div>

                        <!-- Subject -->
                        <div>
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-zinc-500 block mb-1">
                                {{ $t('Subject') }}
                            </span>
                            <p class="text-sm font-bold text-slate-900 dark:text-white">
                                {{ lead.subject || 'Website Inquiry' }}
                            </p>
                        </div>

                        <!-- Message Body -->
                        <div>
                            <span class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-zinc-500 block mb-1">
                                {{ $t('Message') }}
                            </span>
                            <div class="p-4 rounded-xl bg-slate-50 dark:bg-[#18181B] border border-slate-200/70 dark:border-zinc-800 text-xs text-slate-700 dark:text-zinc-300 whitespace-pre-line leading-relaxed font-sans">
                                {{ lead.message || 'No message provided.' }}
                            </div>
                        </div>

                        <!-- Contact Overview Info Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                            <div class="p-3.5 rounded-xl bg-slate-50/70 dark:bg-zinc-900/60 border border-slate-100 dark:border-zinc-800 text-xs">
                                <span class="text-slate-400 dark:text-zinc-500 block text-[10px] uppercase font-semibold">{{ $t('Email Address') }}</span>
                                <a :href="`mailto:${lead.email}`" class="font-semibold text-emerald-600 dark:text-emerald-400 hover:underline mt-0.5 block truncate">
                                    {{ lead.email }}
                                </a>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50/70 dark:bg-zinc-900/60 border border-slate-100 dark:border-zinc-800 text-xs">
                                <span class="text-slate-400 dark:text-zinc-500 block text-[10px] uppercase font-semibold">{{ $t('Phone Number') }}</span>
                                <a v-if="lead.phone" :href="`tel:${lead.phone}`" class="font-semibold text-slate-800 dark:text-zinc-200 hover:text-emerald-600 mt-0.5 block font-mono">
                                    {{ lead.phone }}
                                </a>
                                <span v-else class="text-slate-400 italic mt-0.5 block">{{ $t('Not provided') }}</span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50/70 dark:bg-zinc-900/60 border border-slate-100 dark:border-zinc-800 text-xs">
                                <span class="text-slate-400 dark:text-zinc-500 block text-[10px] uppercase font-semibold">{{ $t('Company / Organization') }}</span>
                                <span class="font-semibold text-slate-800 dark:text-zinc-200 mt-0.5 block">
                                    {{ lead.company || $t('Not provided') }}
                                </span>
                            </div>

                            <div class="p-3.5 rounded-xl bg-slate-50/70 dark:bg-zinc-900/60 border border-slate-100 dark:border-zinc-800 text-xs">
                                <span class="text-slate-400 dark:text-zinc-500 block text-[10px] uppercase font-semibold">{{ $t('Captured IP & Device') }}</span>
                                <span class="font-mono text-[11px] text-slate-700 dark:text-zinc-300 mt-0.5 block truncate">
                                    {{ lead.ip_address || '—' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Marketing Attribution (UTM & Referrer) -->
                    <div class="rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-6 shadow-sm space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-zinc-800/80 pb-3">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m4.93 4.93 4.24 4.24"/><path d="m14.83 9.17 4.24-4.24"/><path d="m14.83 14.83 4.24 4.24"/><path d="m9.17 14.83-4.24 4.24"/><circle cx="12" cy="12" r="4"/></svg>
                                <span>{{ $t('Marketing Attribution & Journey') }}</span>
                            </h3>
                            <span class="text-[10px] font-semibold text-slate-400">{{ $t('Auto-captured') }}</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs">
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-[#18181B] border border-slate-200/60 dark:border-zinc-800">
                                <span class="text-[10px] uppercase font-semibold text-slate-400 dark:text-zinc-500 block">UTM Source</span>
                                <span class="font-mono font-bold text-slate-800 dark:text-zinc-200 mt-1 block truncate">
                                    {{ lead.utm_source || '—' }}
                                </span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-[#18181B] border border-slate-200/60 dark:border-zinc-800">
                                <span class="text-[10px] uppercase font-semibold text-slate-400 dark:text-zinc-500 block">UTM Medium</span>
                                <span class="font-mono font-bold text-slate-800 dark:text-zinc-200 mt-1 block truncate">
                                    {{ lead.utm_medium || '—' }}
                                </span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 dark:bg-[#18181B] border border-slate-200/60 dark:border-zinc-800">
                                <span class="text-[10px] uppercase font-semibold text-slate-400 dark:text-zinc-500 block">UTM Campaign</span>
                                <span class="font-mono font-bold text-slate-800 dark:text-zinc-200 mt-1 block truncate">
                                    {{ lead.utm_campaign || '—' }}
                                </span>
                            </div>
                        </div>

                        <div class="space-y-2 pt-2 text-xs">
                            <div v-if="lead.page_url" class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 p-2.5 rounded-xl bg-slate-50/70 dark:bg-zinc-900/60">
                                <span class="text-slate-400 text-[11px] font-medium shrink-0">{{ $t('Landing / Page URL') }}:</span>
                                <span class="font-mono text-slate-700 dark:text-zinc-300 truncate text-[11px]">{{ lead.page_url }}</span>
                            </div>
                            <div v-if="lead.referrer" class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 p-2.5 rounded-xl bg-slate-50/70 dark:bg-zinc-900/60">
                                <span class="text-slate-400 text-[11px] font-medium shrink-0">{{ $t('HTTP Referrer') }}:</span>
                                <span class="font-mono text-slate-700 dark:text-zinc-300 truncate text-[11px]">{{ lead.referrer }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- 3. Notes & Activity Timeline -->
                    <div class="rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-6 shadow-sm space-y-5">
                        <div class="flex items-center justify-between border-b border-slate-100 dark:border-zinc-800/80 pb-3">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                                <span>{{ $t('Activity Timeline & Notes') }}</span>
                            </h3>
                            <span class="text-xs text-slate-400">{{ (lead.notes || []).length }} events</span>
                        </div>

                        <!-- Add Note Form -->
                        <form @submit.prevent="submitNote" class="space-y-3">
                            <textarea
                                v-model="newNote"
                                rows="3"
                                required
                                :placeholder="$t('Add an internal note or record call notes for this lead...')"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-xs text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            ></textarea>
                            <div class="flex justify-end">
                                <button
                                    type="submit"
                                    :disabled="isSubmittingNote || !newNote.trim()"
                                    class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white font-bold text-xs shadow-sm transition-all cursor-pointer"
                                >
                                    {{ isSubmittingNote ? $t('Saving...') : $t('Add Note') }}
                                </button>
                            </div>
                        </form>

                        <!-- Timeline list -->
                        <div class="space-y-3 pt-2">
                            <div
                                v-for="(item, idx) in lead.notes || []"
                                :key="idx"
                                class="p-3.5 rounded-xl bg-slate-50/80 dark:bg-[#18181B]/60 border border-slate-100 dark:border-zinc-800 text-xs space-y-1"
                            >
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-bold text-slate-900 dark:text-white flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                                        {{ item.author || 'System' }}
                                    </span>
                                    <span class="text-slate-400 font-mono">{{ formatDate(item.created_at) }}</span>
                                </div>
                                <p class="text-slate-700 dark:text-zinc-300 whitespace-pre-line pl-3.5">
                                    {{ item.text }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column (4 cols): Lead Status, Assignment, and CRM Conversion -->
                <div class="lg:col-span-4 space-y-6">
                    <!-- Status Card -->
                    <div class="rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-6 shadow-sm space-y-4">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                            {{ $t('Lead Status') }}
                        </h3>
                        
                        <div class="space-y-2">
                            <label class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-zinc-500 block">
                                {{ $t('Current Pipeline Stage') }}
                            </label>
                            <select
                                v-model="statusForm.status"
                                @change="updateStatus"
                                class="w-full px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            >
                                <option value="new">New (Uncontacted)</option>
                                <option value="contacted">Contacted</option>
                                <option value="qualified">Qualified</option>
                                <option value="converted">Converted</option>
                                <option value="lost">Lost</option>
                            </select>
                        </div>
                    </div>

                    <!-- Assignment Card -->
                    <div class="rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-6 shadow-sm space-y-4">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                            {{ $t('Lead Owner / Assignee') }}
                        </h3>

                        <div class="space-y-2">
                            <label class="text-[11px] font-semibold uppercase tracking-wider text-slate-400 dark:text-zinc-500 block">
                                {{ $t('Assigned Staff Member') }}
                            </label>
                            <select
                                v-model="assignForm.assigned_to"
                                @change="updateAssignment"
                                class="w-full px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            >
                                <option :value="null">{{ $t('Unassigned') }}</option>
                                <option
                                    v-for="admin in adminUsers"
                                    :key="admin.id"
                                    :value="admin.id"
                                >
                                    {{ admin.first_name }} {{ admin.last_name }} ({{ admin.email }})
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Convert to CRM Contact Card -->
                    <div class="rounded-2xl border border-emerald-500/30 bg-emerald-50/30 dark:bg-emerald-950/20 p-6 shadow-sm space-y-4">
                        <div class="flex items-center gap-2 text-emerald-800 dark:text-emerald-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><line x1="19" x2="19" y1="8" y2="14"/><line x1="22" x2="16" y1="11" y2="11"/></svg>
                            <h3 class="text-sm font-bold">
                                {{ $t('Convert to CRM Contact') }}
                            </h3>
                        </div>

                        <p class="text-xs text-slate-600 dark:text-zinc-400 leading-relaxed">
                            {{ $t('Converting this lead will add them into an Organization CRM with duplicate protection (matching email or phone).') }}
                        </p>

                        <div v-if="lead.converted_to_organization_id" class="p-3 rounded-xl bg-emerald-100/70 dark:bg-emerald-900/40 text-xs text-emerald-900 dark:text-emerald-200 space-y-1">
                            <span class="font-bold flex items-center gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                                {{ $t('Already Converted') }}
                            </span>
                            <span class="block">
                                {{ $t('Organization') }}: <strong>{{ lead.converted_organization?.name || 'Customer Organization' }}</strong>
                            </span>
                        </div>

                        <form v-else @submit.prevent="convertLead" class="space-y-3">
                            <div>
                                <label class="text-[11px] font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400 block mb-1">
                                    {{ $t('Target Organization') }} *
                                </label>
                                <select
                                    v-model="convertForm.organization_id"
                                    required
                                    class="w-full px-3 py-2 rounded-xl bg-white dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500"
                                >
                                    <option value="" disabled>{{ $t('Select Organization...') }}</option>
                                    <option
                                        v-for="org in organizations"
                                        :key="org.id"
                                        :value="org.id"
                                    >
                                        {{ org.name }}
                                    </option>
                                </select>
                            </div>

                            <button
                                type="submit"
                                :disabled="!convertForm.organization_id || isConverting"
                                class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 disabled:opacity-50 text-white font-bold text-xs shadow-md shadow-emerald-600/20 transition-all cursor-pointer flex items-center justify-center gap-2"
                            >
                                <span v-if="isConverting" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                <span>{{ isConverting ? $t('Converting...') : $t('Confirm Conversion') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, router } from '@inertiajs/vue3';
import AppLayout from '../Layout/App.vue';
import { formatDateTime } from '@/Utils/dateTime';

const props = defineProps({
    title: String,
    lead: Object,
    adminUsers: Array,
    organizations: Array,
});

const statusClasses = {
    new: 'bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-950/40 dark:text-blue-300 dark:border-blue-800',
    contacted: 'bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-950/40 dark:text-amber-300 dark:border-amber-800',
    qualified: 'bg-purple-50 text-purple-700 border border-purple-200 dark:bg-purple-950/40 dark:text-purple-300 dark:border-purple-800',
    converted: 'bg-emerald-50 text-emerald-700 border border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800',
    lost: 'bg-rose-50 text-rose-700 border border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800',
};

const statusDotClasses = {
    new: 'bg-blue-500',
    contacted: 'bg-amber-500',
    qualified: 'bg-purple-500',
    converted: 'bg-emerald-500',
    lost: 'bg-rose-500',
};

const formatDate = (dateString) => {
    return formatDateTime(dateString);
};

// 1. Status Update
const statusForm = ref({
    status: props.lead.status || 'new',
});

const updateStatus = () => {
    router.post(`/admin/leads/${props.lead.uuid}/status`, {
        status: statusForm.value.status,
    }, { preserveScroll: true });
};

// 2. Assignment Update
const assignForm = ref({
    assigned_to: props.lead.assigned_to || null,
});

const updateAssignment = () => {
    router.post(`/admin/leads/${props.lead.uuid}/assign`, {
        assigned_to: assignForm.value.assigned_to,
    }, { preserveScroll: true });
};

// 3. Add Note
const newNote = ref('');
const isSubmittingNote = ref(false);

const submitNote = () => {
    if (!newNote.value.trim()) return;
    isSubmittingNote.value = true;
    router.post(`/admin/leads/${props.lead.uuid}/note`, {
        note: newNote.value,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            newNote.value = '';
            isSubmittingNote.value = false;
        },
        onError: () => {
            isSubmittingNote.value = false;
        }
    });
};

// 4. Convert Lead to Contact
const convertForm = ref({
    organization_id: '',
});
const isConverting = ref(false);

const convertLead = () => {
    if (!convertForm.value.organization_id) return;
    isConverting.value = true;
    router.post(`/admin/leads/${props.lead.uuid}/convert`, {
        organization_id: convertForm.value.organization_id,
    }, {
        preserveScroll: true,
        onSuccess: () => {
            isConverting.value = false;
        },
        onError: () => {
            isConverting.value = false;
        }
    });
};

// 5. Delete Lead
const deleteLead = () => {
    if (confirm('Are you sure you want to delete this lead?')) {
        router.delete(`/admin/leads/${props.lead.uuid}`);
    }
};
</script>
