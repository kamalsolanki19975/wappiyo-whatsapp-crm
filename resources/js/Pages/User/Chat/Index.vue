<template>
    <AppLayout v-slot:default="slotProps">
        <!-- Main Workspace Container: 3 Panels -->
        <div class="flex h-full w-full overflow-hidden bg-slate-50 dark:bg-[#09090B]">
            <!-- PANEL 1: Conversations Sidebar -->
            <aside
                :class="[
                    'h-full flex-col bg-white dark:bg-[#111113] border-r border-slate-200/80 dark:border-zinc-800 shrink-0 z-10 transition-all duration-200',
                    contact ? 'hidden md:flex md:w-80 lg:w-88 xl:w-96' : 'flex w-full md:w-80 lg:w-88 xl:w-96'
                ]"
            >
                <ChatTable
                    :rows="rows"
                    :filters="props.filters"
                    :rowCount="props.rowCount"
                    :ticketingIsEnabled="ticketingIsEnabled"
                    :status="props?.status"
                    :chatSortDirection="props.chat_sort_direction"
                    :activeUuid="contact?.uuid"
                />
            </aside>

            <!-- PANEL 2: Conversation Center Area -->
            <main
                :class="[
                    'flex-1 min-w-0 h-full flex flex-col relative chat-bg transition-colors duration-200',
                    contact ? 'flex' : 'hidden md:flex'
                ]"
            >
                <!-- ACTIVE CONVERSATION -->
                <template v-if="contact">
                    <!-- Chat Header -->
                    <ChatHeader
                        :contact="contact"
                        :displayContactInfo="displayContactInfo"
                        :ticketingIsEnabled="ticketingIsEnabled"
                        :ticket="ticket"
                        :addon="addon"
                        @toggleView="toggleContactView"
                        @deleteThread="deleteThread"
                        @closeThread="closeThread"
                    />

                    <!-- Chat Body: Template Campaign View OR Thread View -->
                    <div v-if="displayTemplate" class="flex-1 overflow-y-auto bg-white dark:bg-[#111113]">
                        <CampaignForm
                            class="bg-white dark:bg-[#111113] h-full"
                            :contact="contact.uuid"
                            :templates="templates"
                            :contactGroups="[]"
                            :settings="props.settings"
                            :displayCancelBtn="true"
                            :displayTitle="true"
                            :isCampaignFlow="false"
                            :scheduleTemplate="false"
                            :sendText="'Send WhatsApp Template'"
                            @viewTemplate="displayTemplate = false"
                        />
                    </div>

                    <div
                        v-else
                        class="flex-1 overflow-y-auto relative scroll-smooth focus:outline-none"
                        ref="scrollContainer2"
                        tabindex="0"
                    >
                        <ChatThread
                            v-if="!loadingThread"
                            :rows="chatThread"
                        />

                        <!-- Loading Thread Skeleton -->
                        <div v-else class="p-6 space-y-4 max-w-lg mx-auto">
                            <div class="h-10 bg-slate-200/60 dark:bg-zinc-800/60 rounded-2xl w-3/4 animate-pulse"></div>
                            <div class="h-14 bg-purple-100/50 dark:bg-purple-950/40 rounded-2xl w-2/3 ml-auto animate-pulse"></div>
                            <div class="h-10 bg-slate-200/60 dark:bg-zinc-800/60 rounded-2xl w-1/2 animate-pulse"></div>
                        </div>
                    </div>

                    <!-- Chat Composer (Sticky at bottom) -->
                    <div v-if="!displayTemplate" class="shrink-0 w-full z-10">
                        <ChatForm
                            :contact="contact"
                            :chatLimitReached="isChatLimitReached"
                            @viewTemplate="displayTemplate = true"
                        />
                    </div>
                </template>

                <!-- NO CONVERSATION SELECTED EMPTY STATE (Desktop) -->
                <div
                    v-else
                    class="flex-1 flex flex-col items-center justify-center p-8 text-center select-none"
                >
                    <div class="relative mb-6">
                        <!-- Ambient back glow -->
                        <div class="absolute -inset-4 bg-gradient-to-r from-purple-500/20 via-pink-500/15 to-emerald-500/20 rounded-full blur-xl animate-pulse"></div>
                        <div class="relative w-20 h-20 rounded-3xl bg-white dark:bg-[#111113] shadow-lg border border-slate-200/80 dark:border-zinc-800 flex items-center justify-center text-[#6C5CE7]">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-10 h-10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/>
                            </svg>
                        </div>
                    </div>

                    <h2 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        {{ $t('Select a conversation') }}
                    </h2>
                    <p class="mt-2 text-sm text-slate-500 dark:text-zinc-400 max-w-sm leading-relaxed">
                        {{ $t('Choose a conversation from your inbox on the left to start messaging, manage tickets, and collaborate with your team.') }}
                    </p>

                    <div class="mt-6 flex flex-wrap items-center justify-center gap-3">
                        <Link
                            href="/contacts"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold bg-purple-50 dark:bg-purple-950/40 text-[#6C5CE7] dark:text-purple-300 border border-purple-200/60 dark:border-purple-800/40 hover:bg-purple-100/60 dark:hover:bg-purple-900/40 transition-colors shadow-xs"
                        >
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                            <span>{{ $t('Open Contacts Directory') }}</span>
                        </Link>
                    </div>

                    <div class="mt-8 flex items-center gap-2 text-xs text-slate-400 dark:text-zinc-500">
                        <span class="inline-flex items-center px-1.5 py-0.5 rounded border border-slate-200 dark:border-zinc-800 bg-slate-100 dark:bg-zinc-800 font-mono text-[11px]">⌘K</span>
                        <span>{{ $t('Press ⌘K to quickly search anything') }}</span>
                    </div>
                </div>
            </main>

            <!-- PANEL 3: Contact CRM Side Panel (Desktop xl: Screens) -->
            <aside
                v-if="contact && displayContactInfo"
                class="hidden xl:flex xl:w-80 2xl:w-96 shrink-0 h-full border-l border-slate-200/80 dark:border-zinc-800 flex-col bg-white dark:bg-[#111113] z-10 transition-all duration-200"
            >
                <ChatContact
                    :contact="contact"
                    :fields="props.fields"
                    :locationSettings="props.locationSettings"
                    @close="displayContactInfo = false"
                />
            </aside>

            <!-- MOBILE / TABLET CRM DRAWER (Slide-over Sheet < xl) -->
            <Teleport to="body">
                <div v-if="contact && displayContactInfo" class="xl:hidden">
                    <!-- Backdrop -->
                    <div
                        class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-50 transition-opacity duration-200"
                        @click="displayContactInfo = false"
                    />

                    <!-- Slide-over Container -->
                    <div class="fixed inset-y-0 right-0 max-w-full w-full sm:w-96 bg-white dark:bg-[#111113] shadow-2xl z-50 flex flex-col transform transition-transform duration-200 ease-out">
                        <ChatContact
                            :contact="contact"
                            :fields="props.fields"
                            :locationSettings="props.locationSettings"
                            @close="displayContactInfo = false"
                        />
                    </div>
                </div>
            </Teleport>
        </div>
        <button class="hidden" ref="toggleNavbarBtn" @click="slotProps.toggleNavBar"></button>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import axios from 'axios';
import { router, Link } from '@inertiajs/vue3';
import { ref, onMounted, onUnmounted, watch } from 'vue';
import CampaignForm from '@/Components/CampaignForm.vue';
import ChatForm from '@/Components/ChatComponents/ChatForm.vue';
import ChatHeader from '@/Components/ChatComponents/ChatHeader.vue';
import ChatTable from '@/Components/ChatComponents/ChatTable.vue';
import ChatThread from '@/Components/ChatComponents/ChatThread.vue';
import ChatContact from '@/Components/ChatComponents/ChatContact.vue';
import { getEchoInstance } from '../../../echo';

const props = defineProps([
    'rows',
    'rowCount',
    'pusherSettings',
    'organizationId',
    'isChatLimitReached',
    'toggleNavBar',
    'state',
    'demoNumber',
    'settings',
    'status',
    'chatThread',
    'addon',
    'contact',
    'ticket',
    'chat_sort_direction',
    'filters',
    'templates',
    'fields',
    'locationSettings'
]);

const rows = ref(props.rows);
const scrollContainer2 = ref(null);
const loadingThread = ref(false);
const displayContactInfo = ref(typeof window !== 'undefined' && window.innerWidth >= 1280);
const displayTemplate = ref(false);
const isChatLimitReached = ref(props.isChatLimitReached);
const toggleNavbarBtn = ref(null);

const config = ref(props.settings?.metadata);
const settings = ref(config.value ? JSON.parse(config.value) : null);
const ticketingIsEnabled = ref(settings.value?.tickets?.active ?? false);
const chatThread = ref(props.chatThread || []);
const contact = ref(props.contact);

watch(() => props.rows, (newRows) => {
    rows.value = newRows;
});

watch(() => props.contact, (newContact) => {
    contact.value = newContact;
});

watch(() => props.chatThread, (newThread) => {
    chatThread.value = newThread || [];
    setTimeout(scrollToBottom, 100);
});

function toggleContactView(value) {
    if (typeof value === 'boolean') {
        displayContactInfo.value = value;
    } else {
        displayContactInfo.value = !displayContactInfo.value;
    }
}

const scrollToBottom = () => {
    const container = scrollContainer2.value;
    if (container) {
        container.scrollTo({
            top: container.scrollHeight,
            behavior: 'smooth',
        });
    }
};

const closeThread = () => {
    if (toggleNavbarBtn.value) {
        toggleNavbarBtn.value.click();
    }
    contact.value = null;
    router.visit('/chats', { preserveState: true });
};

const deleteThread = () => {
    chatThread.value = [];
    if (contact.value?.uuid) {
        axios.delete('/chats/' + contact.value.uuid);
    }
};

const updateChatThread = (chat) => {
    if (!chat || !chat[0]?.value) return;
    const wamId = chat[0].value.wam_id;
    const wamIdExists = chatThread.value.some(existingChat => existingChat[0]?.value?.wam_id === wamId);

    if (!wamIdExists && chat[0].value.deleted_at == null) {
        chatThread.value.push(chat);
        setTimeout(scrollToBottom, 100);
    }
};

const updateSidePanel = async (chat) => {
    if (contact.value && chat?.[0]?.value?.contact_id && contact.value.id == chat[0].value.contact_id) {
        updateChatThread(chat);
    }

    try {
        const response = await axios.get('/chats');
        if (response?.data?.result) {
            rows.value = response.data.result;
        }
    } catch (e) {
        console.error('Failed to update side panel chats:', e);
    }
};

onMounted(() => {
    if (props.pusherSettings?.['pusher_app_key']) {
        try {
            const echo = getEchoInstance(
                props.pusherSettings['pusher_app_key'],
                props.pusherSettings['pusher_app_cluster']
            );

            if (echo && props.organizationId) {
                echo.channel('chats.ch' + props.organizationId).listen('NewChatEvent', (event) => {
                    updateSidePanel(event.chat);
                });
            }
        } catch (error) {
            console.warn('Realtime echo subscription error:', error);
        }
    }

    scrollToBottom();
});

onUnmounted(() => {
    if (props.organizationId && props.pusherSettings?.['pusher_app_key']) {
        try {
            const echo = getEchoInstance(
                props.pusherSettings['pusher_app_key'],
                props.pusherSettings['pusher_app_cluster']
            );
            if (echo) {
                echo.leave('chats.ch' + props.organizationId);
            }
        } catch (_) {}
    }
});
</script>