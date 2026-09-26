<script setup>
import { ref, watchEffect, computed } from 'vue';
import { Link, router, useForm, usePage } from '@inertiajs/vue3';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownItemGroup from '@/Components/DropdownItemGroup.vue';
import DropdownItem from '@/Components/DropdownItem.vue';
import FormTextArea from '@/Components/FormTextArea.vue';
import Modal from '@/Components/Modal.vue';
import Avatar from '@/Components/UI/Avatar.vue';
import Badge from '@/Components/UI/Badge.vue';
import Button from '@/Components/UI/Button.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    contact: {
        type: Object,
        required: true,
    },
    fields: {
        type: Array,
        default: () => [],
    },
    locationSettings: {
        type: String,
        default: 'before',
    },
});

const contact = ref(props.contact);
const activeTab = ref('overview');
const isOpenNoteModal = ref(false);
const isAddingNote = ref(false);

watchEffect(() => {
    contact.value = props.contact;
});

const metadata = computed(() => {
    try {
        if (!contact.value?.metadata) return {};
        return typeof contact.value.metadata === 'string'
            ? JSON.parse(contact.value.metadata)
            : contact.value.metadata;
    } catch (_) {
        return {};
    }
});

const favorite = async () => {
    contact.value.is_favorite = !contact.value.is_favorite;
    router.put('/contacts/favorite/' + contact.value.uuid, {
        favorite: contact.value.is_favorite
    }, {
        preserveState: true,
    });
};

const noteForm = useForm({
    notes: '',
    contact: props.contact?.uuid,
});

const submitNote = () => {
    if (!noteForm.notes?.trim()) return;
    noteForm.contact = contact.value.uuid;
    isAddingNote.value = true;

    noteForm.post('/notes', {
        preserveState: true,
        onSuccess: () => {
            noteForm.reset('notes');
            isOpenNoteModal.value = false;
            // update contact notes if returned in flash
            if (usePage().props.flash?.status?.contact) {
                contact.value = usePage().props.flash.status.contact;
            }
        },
        onFinish: () => {
            isAddingNote.value = false;
        }
    });
};

const deleteContact = () => {
    if (confirm(trans('Are you sure you want to delete this contact? This will remove all associated chat history.'))) {
        router.visit('/contacts', {
            method: 'delete',
            data: { uuids: [contact.value.uuid] },
        });
    }
};

const getAddressDetail = (value, key) => {
    if (!value) return null;
    try {
        const address = typeof value === 'string' ? JSON.parse(value) : value;
        return address?.[key] && address?.[key] !== 'Not Set' ? address[key] : null;
    } catch (_) {
        return null;
    }
};
</script>

<template>
    <div class="h-full overflow-y-auto bg-slate-50/70 dark:bg-[#09090B] flex flex-col transition-colors">
        <!-- Sticky Top Profile Navigation Bar -->
        <div class="sticky top-0 z-20 px-4 sm:px-6 lg:px-8 py-3.5 glass-header border-b border-slate-200/80 dark:border-zinc-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <Link
                    href="/contacts"
                    class="md:hidden p-1.5 -ml-1 text-slate-500 hover:text-slate-800 dark:hover:text-zinc-200 rounded-lg hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors"
                    title="Back to contacts list"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                </Link>

                <div>
                    <h2 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                        <span>{{ contact.full_name }}</span>
                        <span v-if="contact.is_favorite" class="text-amber-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                        </span>
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-zinc-400 font-mono">
                        {{ contact.formatted_phone_number }}
                    </p>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex items-center gap-2">
                <!-- Jump to WhatsApp Inbox -->
                <Link :href="'/chats/' + contact.uuid">
                    <Button variant="primary" size="xs" class="gap-1.5 shadow-sm">
                        <template #icon>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/></svg>
                        </template>
                        <span>{{ $t('Open Chat') }}</span>
                    </Button>
                </Link>

                <!-- Edit Contact -->
                <Link :href="'/contacts/' + contact.uuid + '?edit=true'">
                    <Button variant="secondary" size="xs" class="gap-1.5">
                        <template #icon>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        </template>
                        <span class="hidden sm:inline">{{ $t('Edit') }}</span>
                    </Button>
                </Link>

                <!-- Favorite Toggle -->
                <button
                    type="button"
                    @click="favorite"
                    :class="[
                        'p-2 rounded-xl border transition-colors',
                        contact.is_favorite
                            ? 'bg-amber-50 dark:bg-amber-950/40 border-amber-200 dark:border-amber-800 text-amber-500'
                            : 'bg-white dark:bg-zinc-800 border-slate-200 dark:border-zinc-700 text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200'
                    ]"
                    :title="contact.is_favorite ? 'Remove from favorites' : 'Mark as favorite'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" :class="contact.is_favorite ? 'fill-current' : 'none'" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z"/></svg>
                </button>

                <!-- More Options -->
                <Dropdown align="right" width="w-44">
                    <button
                        type="button"
                        class="p-2 rounded-xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-slate-500 dark:text-zinc-400 hover:text-slate-700 dark:hover:text-zinc-200 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                    </button>
                    <template #items>
                        <DropdownItemGroup>
                            <DropdownItem as="button" @click="isOpenNoteModal = true">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                <span>{{ $t('Add Internal Note') }}</span>
                            </DropdownItem>
                            <DropdownItem as="button" @click="deleteContact" class="text-rose-600 dark:text-rose-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 6h18"/><path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"/><path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"/></svg>
                                <span>{{ $t('Delete Contact') }}</span>
                            </DropdownItem>
                        </DropdownItemGroup>
                    </template>
                </Dropdown>
            </div>
        </div>

        <!-- Main Profile Content Container -->
        <div class="flex-1 p-4 sm:p-6 lg:p-8 max-w-5xl mx-auto w-full space-y-6">
            
            <!-- Hero Profile Banner Card -->
            <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-6 shadow-xs transition-colors">
                <!-- Top ambient accent strip -->
                <div class="absolute top-0 inset-x-0 h-1.5 bg-gradient-to-r from-[#6C5CE7] via-[#a29bfe] to-[#00CEC9]"></div>

                <div class="flex flex-col sm:flex-row items-center sm:items-start gap-5 text-center sm:text-left">
                    <!-- Avatar -->
                    <div class="relative shrink-0">
                        <Avatar
                            :src="contact.avatar || null"
                            :name="contact.full_name || 'Contact'"
                            size="2xl"
                        />
                        <span class="absolute bottom-1 right-1 h-4 w-4 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-[#111113]" />
                    </div>

                    <!-- Identity -->
                    <div class="min-w-0 flex-1 space-y-1.5">
                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-2">
                            <h1 class="text-xl sm:text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                                {{ contact.full_name }}
                            </h1>
                            <Badge v-if="contact.contact_group?.name" variant="primary" size="md">
                                {{ contact.contact_group.name }}
                            </Badge>
                            <Badge v-else variant="neutral" size="md">
                                {{ $t('General Lead') }}
                            </Badge>
                        </div>

                        <div class="flex flex-wrap items-center justify-center sm:justify-start gap-3 text-xs text-slate-500 dark:text-zinc-400">
                            <span class="flex items-center gap-1 font-mono font-medium text-slate-700 dark:text-zinc-300">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                                {{ contact.formatted_phone_number }}
                            </span>
                            <span v-if="contact.email" class="flex items-center gap-1">
                                <span>•</span>
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                                {{ contact.email }}
                            </span>
                        </div>
                    </div>

                    <!-- Quick Stats Pill -->
                    <div class="flex items-center gap-2 shrink-0">
                        <Link :href="'/chats/' + contact.uuid">
                            <Button variant="outline" size="sm" class="gap-1.5">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/></svg>
                                <span>{{ $t('View Chat') }}</span>
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Profile Tabs Navigation -->
            <div class="flex items-center gap-1 border-b border-slate-200 dark:border-zinc-800 text-xs sm:text-sm font-semibold">
                <button
                    type="button"
                    @click="activeTab = 'overview'"
                    :class="[
                        'pb-3 px-3 border-b-2 transition-all duration-150',
                        activeTab === 'overview'
                            ? 'border-[#6C5CE7] text-[#6C5CE7] dark:text-purple-400 font-bold'
                            : 'border-transparent text-slate-500 dark:text-zinc-400 hover:text-slate-800 dark:hover:text-zinc-200'
                    ]"
                >
                    {{ $t('Overview & Details') }}
                </button>
                <button
                    v-if="fields.length > 0"
                    type="button"
                    @click="activeTab = 'custom_fields'"
                    :class="[
                        'pb-3 px-3 border-b-2 transition-all duration-150',
                        activeTab === 'custom_fields'
                            ? 'border-[#6C5CE7] text-[#6C5CE7] dark:text-purple-400 font-bold'
                            : 'border-transparent text-slate-500 dark:text-zinc-400 hover:text-slate-800 dark:hover:text-zinc-200'
                    ]"
                >
                    {{ $t('Custom Fields') }} ({{ fields.length }})
                </button>
                <button
                    type="button"
                    @click="activeTab = 'notes'"
                    :class="[
                        'pb-3 px-3 border-b-2 transition-all duration-150',
                        activeTab === 'notes'
                            ? 'border-[#6C5CE7] text-[#6C5CE7] dark:text-purple-400 font-bold'
                            : 'border-transparent text-slate-500 dark:text-zinc-400 hover:text-slate-800 dark:hover:text-zinc-200'
                    ]"
                >
                    {{ $t('Team Notes') }}
                    <span v-if="contact.notes?.length" class="ml-1 px-1.5 py-0.5 rounded-full bg-purple-100 dark:bg-purple-950/60 text-[#6C5CE7] text-[10px]">
                        {{ contact.notes.length }}
                    </span>
                </button>
            </div>

            <!-- TAB 1: OVERVIEW & DETAILS -->
            <div v-if="activeTab === 'overview'" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Basic Contact Info Card -->
                <div class="rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-5 shadow-xs space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                        {{ $t('Contact Information') }}
                    </h3>

                    <dl class="divide-y divide-slate-100 dark:divide-zinc-800/60 text-xs sm:text-sm">
                        <div class="py-2.5 flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-zinc-400">{{ $t('Full Name') }}</dt>
                            <dd class="font-semibold text-slate-800 dark:text-zinc-200 text-right">{{ contact.full_name }}</dd>
                        </div>
                        <div class="py-2.5 flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-zinc-400">{{ $t('Phone') }}</dt>
                            <dd class="font-mono font-medium text-slate-800 dark:text-zinc-200 text-right">{{ contact.formatted_phone_number }}</dd>
                        </div>
                        <div class="py-2.5 flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-zinc-400">{{ $t('Email') }}</dt>
                            <dd class="text-slate-800 dark:text-zinc-200 text-right">{{ contact.email || $t('Not set') }}</dd>
                        </div>
                        <div class="py-2.5 flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-zinc-400">{{ $t('Group') }}</dt>
                            <dd class="text-right">
                                <span v-if="contact.contact_group?.name" class="font-semibold text-purple-600 dark:text-purple-400">
                                    {{ contact.contact_group.name }}
                                </span>
                                <span v-else class="text-slate-400">{{ $t('Not set') }}</span>
                            </dd>
                        </div>
                        <div class="py-2.5 flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-zinc-400">{{ $t('Created') }}</dt>
                            <dd class="text-slate-600 dark:text-zinc-400 text-right">{{ contact.created_at || '—' }}</dd>
                        </div>
                    </dl>
                </div>

                <!-- Address & Location Card -->
                <div class="rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-5 shadow-xs space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500 flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                        <span>{{ $t('Address & Location') }}</span>
                    </h3>

                    <dl class="divide-y divide-slate-100 dark:divide-zinc-800/60 text-xs sm:text-sm">
                        <div class="py-2.5 flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-zinc-400">{{ $t('Street') }}</dt>
                            <dd class="text-slate-800 dark:text-zinc-200 text-right">{{ getAddressDetail(contact.address, 'street') || $t('Not set') }}</dd>
                        </div>
                        <div class="py-2.5 flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-zinc-400">{{ $t('City') }}</dt>
                            <dd class="text-slate-800 dark:text-zinc-200 text-right">{{ getAddressDetail(contact.address, 'city') || $t('Not set') }}</dd>
                        </div>
                        <div class="py-2.5 flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-zinc-400">{{ $t('State') }}</dt>
                            <dd class="text-slate-800 dark:text-zinc-200 text-right">{{ getAddressDetail(contact.address, 'state') || $t('Not set') }}</dd>
                        </div>
                        <div class="py-2.5 flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-zinc-400">{{ $t('Zip code') }}</dt>
                            <dd class="font-mono text-slate-800 dark:text-zinc-200 text-right">{{ getAddressDetail(contact.address, 'zip') || $t('Not set') }}</dd>
                        </div>
                        <div class="py-2.5 flex justify-between gap-4">
                            <dt class="text-slate-500 dark:text-zinc-400">{{ $t('Country') }}</dt>
                            <dd class="font-medium text-slate-800 dark:text-zinc-200 text-right">{{ getAddressDetail(contact.address, 'country') || $t('Not set') }}</dd>
                        </div>
                    </dl>
                </div>
            </div>

            <!-- TAB 2: CUSTOM FIELDS -->
            <div v-else-if="activeTab === 'custom_fields'">
                <div class="rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-5 shadow-xs space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                        {{ $t('Organization Custom CRM Attributes') }}
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div
                            v-for="(field, index) in fields"
                            :key="index"
                            class="p-3.5 rounded-xl bg-slate-50/60 dark:bg-zinc-900/40 border border-slate-100 dark:border-zinc-800/80"
                        >
                            <p class="text-xs font-semibold text-slate-500 dark:text-zinc-400 capitalize mb-1">
                                {{ field.name }}
                            </p>
                            <p v-if="metadata && metadata[field.name] != null && metadata[field.name] !== ''" class="text-sm font-bold text-slate-900 dark:text-white">
                                {{ metadata[field.name] }}
                            </p>
                            <p v-else class="text-xs text-slate-400 dark:text-zinc-600 italic">
                                {{ $t('Not configured') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- TAB 3: TEAM NOTES -->
            <div v-else-if="activeTab === 'notes'" class="space-y-4">
                <!-- Add Note Form Card -->
                <div class="rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-5 shadow-xs space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500 flex items-center gap-1.5">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-[#6C5CE7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        <span>{{ $t('Add Internal Note') }}</span>
                    </h3>

                    <form @submit.prevent="submitNote" class="space-y-3">
                        <FormTextArea
                            v-model="noteForm.notes"
                            name="notes"
                            :placeholder="$t('Write a team note regarding customer preferences, requirements or status...')"
                            class="w-full"
                        />
                        <div class="flex justify-end">
                            <Button
                                type="submit"
                                variant="primary"
                                size="sm"
                                :disabled="!noteForm.notes?.trim() || isAddingNote"
                            >
                                <span v-if="!isAddingNote">{{ $t('Save Note') }}</span>
                                <span v-else>{{ $t('Saving...') }}</span>
                            </Button>
                        </div>
                    </form>
                </div>

                <!-- Existing Notes Timeline -->
                <div class="rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 p-5 shadow-xs space-y-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                        {{ $t('Notes History') }}
                    </h3>

                    <div v-if="contact.notes && contact.notes.length > 0" class="space-y-3">
                        <div
                            v-for="(item, idx) in contact.notes"
                            :key="idx"
                            class="p-4 rounded-xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200/60 dark:border-amber-900/40 text-xs sm:text-sm space-y-1.5"
                        >
                            <div class="flex items-center justify-between text-xs text-amber-800 dark:text-amber-400 font-semibold">
                                <span class="flex items-center gap-1.5">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                                    <span>Internal Team Note</span>
                                </span>
                                <span class="font-normal text-amber-600/80 dark:text-amber-500/80">{{ item.created_at }}</span>
                            </div>
                            <p class="text-slate-800 dark:text-zinc-200 whitespace-pre-wrap leading-relaxed">
                                {{ item.content }}
                            </p>
                        </div>
                    </div>

                    <div v-else class="text-center py-6 text-slate-400 dark:text-zinc-500 text-xs">
                        {{ $t('No internal notes recorded for this contact yet.') }}
                    </div>
                </div>
            </div>

        </div>

        <!-- Add Note Modal -->
        <Modal :label="$t('Add Internal Team Note')" :isOpen="isOpenNoteModal" @close="isOpenNoteModal = false">
            <form @submit.prevent="submitNote" class="space-y-4">
                <FormTextArea
                    v-model="noteForm.notes"
                    name="notes"
                    :label="$t('Note details')"
                    :placeholder="$t('Add specific details or instructions for this customer...')"
                    class="w-full"
                />
                <div class="flex justify-end gap-2">
                    <Button variant="secondary" size="sm" type="button" @click="isOpenNoteModal = false">
                        {{ $t('Cancel') }}
                    </Button>
                    <Button variant="primary" size="sm" type="submit" :disabled="!noteForm.notes?.trim() || isAddingNote">
                        {{ $t('Save Note') }}
                    </Button>
                </div>
            </form>
        </Modal>
    </div>
</template>