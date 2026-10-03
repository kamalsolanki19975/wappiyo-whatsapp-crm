<script setup>
import { ref, watchEffect, computed } from 'vue';
import { router, useForm, Link, usePage } from '@inertiajs/vue3';
import AlertModal from '@/Components/AlertModal.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownItemGroup from '@/Components/DropdownItemGroup.vue';
import DropdownItem from '@/Components/DropdownItem.vue';
import FormSelectCombo from '@/Components/FormSelectCombo.vue';
import FormTextArea from '@/Components/FormTextArea.vue';
import Modal from '@/Components/Modal.vue';
import Avatar from '@/Components/UI/Avatar.vue';
import Badge from '@/Components/UI/Badge.vue';
import Button from '@/Components/UI/Button.vue';
import CallButton from '@/Components/Calling/CallButton.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps(['contact', 'displayContactInfo', 'ticketingIsEnabled', 'ticket', 'addon']);
const emit = defineEmits(['toggleView', 'deleteThread', 'closeThread']);

const accountUser = computed(() => usePage().props.auth?.user || {});
const showAlert = ref(false);
const displayContact = ref(props.displayContactInfo);
const ticketState = ref(null);
const isOpenModal = ref(false);

const user = ref({ 
    label: props.ticket?.user ? props.ticket?.user?.full_name : trans('Unassigned'),
    value: props.ticket?.user ? props.ticket?.user?.id : 0, 
});

watchEffect(() => {
    displayContact.value = props.displayContactInfo;
    ticketState.value = props.ticket?.status;
    if (props.ticket?.user) {
        user.value = {
            label: props.ticket.user.full_name,
            value: props.ticket.user.id,
        };
    }
});

const closeThread = () => {
    emit('closeThread', true);
};

const toggleView = () => {
    displayContact.value = !displayContact.value;
    emit('toggleView', displayContact.value);
};

const deleteThread = () => {
    router.visit('/chats/' + props.contact.uuid, {
        method: 'delete',
        onFinish: () => {
            showAlert.value = false;
        }
    });
};

const form2 = useForm({
    notes: null,
    contact: null
});

const form3 = useForm({
    ai_assistant: props.contact?.ai_assistance_enabled ?? false,
});

const changeTicketStatus = (value) => {
    router.put('/tickets/' + props.contact.uuid + '/update', {
        status: value
    }, {
        preserveState: true,
    });
};

const changeTicketAgent = () => {
    router.put('/tickets/' + props.contact.uuid + '/assign', {
        id: user.value.value
    }, {
        preserveState: true,
    });
};

function loadUsers(query, setOptions) {
    fetch("/team?search=" + query, {
        headers: {
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(result => {
        setOptions(result.rows);
    })
    .catch(error => {
        console.error("Error fetching agents:", error);
    });
}

const submitForm = () => {
    form2.contact = props.contact.uuid;
    form2.post('/notes', {
        preserveState: false,
        onSuccess: () => {
            form2.reset();
            isOpenModal.value = false;
        }
    });
};

const toggleAiAssistant = () => {
    form3.ai_assistant = !form3.ai_assistant;
    form3.post('/automation/contact/' + props.contact.uuid, {
        preserveState: true,
    });
};
</script>

<template>
    <div class="h-16 px-4 border-b border-slate-200/80 dark:border-zinc-800 glass-header flex items-center justify-between z-20 shrink-0 transition-colors">
        <!-- Left Section: Mobile Back Button & Contact Info -->
        <div class="flex items-center gap-3 min-w-0">
            <!-- Mobile Back Button -->
            <button
                type="button"
                @click="closeThread"
                class="md:hidden p-1.5 -ml-1 text-slate-600 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-xl transition-colors"
                title="Back to conversations"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
            </button>

            <!-- Contact Avatar -->
            <div class="relative cursor-pointer" @click="toggleView">
                <Avatar
                    :src="contact.avatar || null"
                    :name="contact.full_name || 'Contact'"
                    size="md"
                />
                <span class="absolute bottom-0 right-0 h-3 w-3 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-[#111113]" />
            </div>

            <!-- Contact Name & Phone -->
            <div class="min-w-0 cursor-pointer" @click="toggleView">
                <div class="flex items-center gap-1.5">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate">
                        {{ contact.full_name }}
                    </h3>
                    <Badge v-if="ticketState" :variant="ticketState === 'open' ? 'success' : 'neutral'" size="sm">
                        {{ ticketState }}
                    </Badge>
                </div>
                <div class="text-[11px] text-slate-500 dark:text-zinc-400 truncate flex items-center gap-1.5">
                    <span>{{ contact.formatted_phone_number }}</span>
                    <span class="text-slate-300 dark:text-zinc-700">•</span>
                    <span class="text-emerald-600 dark:text-emerald-400 font-medium">{{ $t('WhatsApp') }}</span>
                </div>
            </div>
        </div>

        <!-- Right Section: Actions, Agent Assignment, Status & CRM Toggle -->
        <div class="flex items-center gap-2 sm:gap-3 shrink-0">
            <!-- Agent Assignment (Ticketing Enabled) -->
            <div v-if="ticketingIsEnabled && accountUser.teams?.[0]?.role !== 'agent'" class="hidden lg:block w-36 xl:w-44">
                <FormSelectCombo
                    v-model="user"
                    :name="''"
                    :loadOptions="loadUsers"
                    :placeholder="$t('Assign Agent')"
                    @update:modelValue="changeTicketAgent()"
                />
            </div>

            <!-- Ticket Status Button -->
            <template v-if="ticketingIsEnabled">
                <Button
                    v-if="ticketState === 'open'"
                    type="button"
                    variant="outline"
                    size="xs"
                    @click="changeTicketStatus('closed')"
                    class="hidden sm:inline-flex"
                >
                    {{ $t('Close Ticket') }}
                </Button>
                <Button
                    v-if="ticketState === 'closed'"
                    type="button"
                    variant="secondary"
                    size="xs"
                    @click="changeTicketStatus('open')"
                    class="hidden sm:inline-flex"
                >
                    {{ $t('Reopen Ticket') }}
                </Button>
            </template>

            <!-- AI Assistant Toggle Button (Addon Enabled) -->
            <button
                v-if="addon == 1"
                type="button"
                @click="toggleAiAssistant"
                :class="[
                    'inline-flex items-center gap-1 px-2.5 py-1 rounded-xl text-xs font-semibold border transition-all duration-150',
                    form3.ai_assistant
                        ? 'bg-purple-50 dark:bg-purple-950/40 text-[#6C5CE7] dark:text-purple-300 border-purple-200 dark:border-purple-800/40'
                        : 'bg-slate-50 dark:bg-zinc-800 text-slate-500 dark:text-zinc-400 border-slate-200 dark:border-zinc-700'
                ]"
                :title="form3.ai_assistant ? 'AI Assistant is active' : 'AI Assistant is paused'"
            >
                <span class="h-1.5 w-1.5 rounded-full" :class="form3.ai_assistant ? 'bg-[#6C5CE7] animate-pulse' : 'bg-slate-400'" />
                <span class="hidden sm:inline">AI</span>
                <span>✨</span>
            </button>

            <!-- WhatsApp Call Action -->
            <CallButton
                :contact="contact"
                :phone="contact?.phone"
                variant="green"
                size="xs"
                :showLabel="true"
            />

            <!-- Toggle Contact Info / CRM Panel -->
            <button
                type="button"
                @click="toggleView"
                :class="[
                    'p-2 rounded-xl text-slate-600 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors focus:outline-none',
                    displayContact ? 'bg-purple-50 dark:bg-purple-950/50 text-[#6C5CE7]' : ''
                ]"
                :title="$t('Toggle Contact CRM')"
            >
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </button>

            <!-- More Actions Dropdown -->
            <Dropdown align="right" width="w-48">
                <button
                    type="button"
                    class="p-2 rounded-xl text-slate-600 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors focus:outline-none"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/></svg>
                </button>
                <template #items>
                    <DropdownItemGroup>
                        <DropdownItem as="button" @click="isOpenModal = true">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            <span>{{ $t('Add Note') }}</span>
                        </DropdownItem>
                        <DropdownItem
                            v-if="ticketState === 'open' && ticketingIsEnabled"
                            as="button"
                            @click="changeTicketStatus('closed')"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            <span>{{ $t('Mark as Closed') }}</span>
                        </DropdownItem>
                        <DropdownItem
                            v-if="ticketState === 'closed' && ticketingIsEnabled"
                            as="button"
                            @click="changeTicketStatus('open')"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-blue-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>{{ $t('Mark as Open') }}</span>
                        </DropdownItem>
                    </DropdownItemGroup>
                    <DropdownItemGroup>
                        <DropdownItem as="button" @click="showAlert = true" class="text-rose-600 dark:text-rose-400">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-rose-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="3 6 5 6 21 6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
                            <span>{{ $t('Clear Chat History') }}</span>
                        </DropdownItem>
                    </DropdownItemGroup>
                </template>
            </Dropdown>
        </div>
    </div>

    <!-- Add Note Modal -->
    <Modal :label="$t('Add Internal Note')" :isOpen="isOpenModal" @close="isOpenModal = false">
        <form @submit.prevent="submitForm" class="space-y-4 mt-3">
            <div>
                <label class="block text-xs font-semibold text-slate-700 dark:text-zinc-300 mb-1.5">
                    {{ $t('Note Content') }}
                </label>
                <FormTextArea
                    v-model="form2.notes"
                    :error="form2.errors?.note"
                    :placeholder="$t('Add an internal team note about this customer...')"
                    class="w-full"
                />
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <Button type="button" variant="secondary" size="sm" @click="isOpenModal = false">
                    {{ $t('Cancel') }}
                </Button>
                <Button type="submit" variant="primary" size="sm" :loading="form2.processing">
                    {{ $t('Save Note') }}
                </Button>
            </div>
        </form>
    </Modal>

    <!-- Delete Thread Confirm Alert -->
    <AlertModal 
        v-model="showAlert" 
        :label="$t('Clear Chat Thread')" 
        :description="$t('Are you sure you want to delete all messages in this conversation? This action cannot be undone.')" 
        @confirm="deleteThread" 
    />
</template>