<script setup>
import { ref, computed } from 'vue';
import debounce from 'lodash/debounce';
import { Link, router } from "@inertiajs/vue3";
import Pagination from '@/Components/Pagination.vue';
import TicketStatusToggle from '@/Components/TicketStatusToggle.vue';
import SortDirectionToggle from '@/Components/SortDirectionToggle.vue';
import Avatar from '@/Components/UI/Avatar.vue';
import EmptyState from '@/Components/UI/EmptyState.vue';
import { formatDate as formatDateUtil, formatTime as formatTimeUtil } from '@/Utils/dateTime';

const props = defineProps({
    rows: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    rowCount: {
        type: Number,
        required: true,
    },
    ticketingIsEnabled: {
        type: Boolean,
        default: false,
    },
    status: {
        type: String,
        default: 'all',
    },
    chatSortDirection: {
        type: String,
        default: 'desc',
    },
    activeUuid: {
        type: String,
        default: null,
    },
});

const isSearching = ref(false);

const contentType = (metadata) => {
    if (!metadata) return 'text';
    try {
        const chatData = typeof metadata === 'string' ? JSON.parse(metadata) : metadata;
        return chatData.type || 'text';
    } catch (_) {
        return 'text';
    }
};

const content = (metadata) => {
    if (!metadata) return {};
    try {
        return typeof metadata === 'string' ? JSON.parse(metadata) : metadata;
    } catch (_) {
        return {};
    }
};

const getExtension = (fileFormat) => {
    const formatMap = {
        'text/plain': 'TXT',
        'application/pdf': 'PDF',
        'application/vnd.ms-powerpoint': 'PPT',
        'application/msword': 'DOC',
        'application/vnd.ms-excel': 'XLS',
        'application/vnd.openxmlformats-officedocument.wordprocessingml.document': 'DOCX',
        'application/vnd.openxmlformats-officedocument.presentationml.presentation': 'PPTX',
        'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet': 'XLSX',
    };
    return formatMap[fileFormat] || 'File';
};

const getContactDisplayName = (metadata) => {
    try {
        const parsed = typeof metadata === 'string' ? JSON.parse(metadata) : metadata;
        const contacts = parsed.contacts || [];
        if (contacts.length === 1) {
            const c = contacts[0];
            return c.name?.formatted_name || `${c.name?.first_name || ''} ${c.name?.last_name || ''}`.trim() || 'Contact';
        } else if (contacts.length > 1) {
            return `${contacts[0].name?.first_name || 'Contact'} +${contacts.length - 1} more`;
        }
    } catch (_) {}
    return 'Contact';
};

const formatTime = (time) => {
    if (!time) return '';
    const currentTime = new Date();
    const targetTime = new Date(time);

    // Today
    if (
        targetTime.getDate() === currentTime.getDate() &&
        targetTime.getMonth() === currentTime.getMonth() &&
        targetTime.getFullYear() === currentTime.getFullYear()
    ) {
        return formatTimeUtil(targetTime);
    }

    // Yesterday
    const yesterday = new Date();
    yesterday.setDate(currentTime.getDate() - 1);
    if (
        targetTime.getDate() === yesterday.getDate() &&
        targetTime.getMonth() === yesterday.getMonth() &&
        targetTime.getFullYear() === yesterday.getFullYear()
    ) {
        return 'Yesterday';
    }

    // Older
    return formatDateUtil(targetTime, { month: 'short', day: 'numeric' });
};

const params = ref({
    search: props.filters?.search || '',
});

const search = debounce(() => {
    isSearching.value = true;
    runSearch();
}, 400);

const runSearch = () => {
    const url = window.location.pathname;
    router.visit(url, {
        method: 'get',
        data: params.value,
        preserveState: true,
        onFinish: () => {
            isSearching.value = false;
        },
    });
};

const clearSearch = () => {
    params.value.search = '';
    runSearch();
};
</script>

<template>
    <div class="flex flex-col h-full bg-white dark:bg-[#111113] border-r border-slate-200/80 dark:border-zinc-800 transition-colors">
        <!-- Top Header & Search Bar -->
        <div class="p-3.5 sm:p-4 border-b border-slate-100 dark:border-zinc-800/80 space-y-3 shrink-0">
            <!-- Header Row -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <h2 class="text-base font-extrabold tracking-tight text-slate-900 dark:text-white">
                        {{ $t('Conversations') }}
                    </h2>
                    <span class="inline-flex items-center rounded-full bg-slate-100 dark:bg-zinc-800 px-2 py-0.5 text-xs font-semibold text-slate-600 dark:text-zinc-400">
                        {{ rowCount }}
                    </span>
                </div>

                <div class="flex items-center gap-1.5">
                    <TicketStatusToggle v-if="ticketingIsEnabled" :status="status" :rowCount="rowCount" />
                    <SortDirectionToggle :direction="props.chatSortDirection" :url="'/chats/update-sort-direction'" />
                </div>
            </div>

            <!-- Search Field -->
            <div class="relative flex items-center">
                <div class="pointer-events-none absolute inset-y-0 left-0 pl-3 flex items-center text-slate-400 dark:text-zinc-500">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/>
                    </svg>
                </div>

                <input
                    v-model="params.search"
                    @input="search"
                    type="text"
                    :placeholder="$t('Search by name or number...')"
                    class="w-full pl-9 pr-8 py-2 text-xs sm:text-sm bg-slate-50 dark:bg-zinc-900/80 text-slate-800 dark:text-zinc-200 border border-slate-200 dark:border-zinc-800 rounded-xl focus:outline-none focus:border-[#6C5CE7] focus:ring-1 focus:ring-[#6C5CE7] placeholder-slate-400 dark:placeholder-zinc-500 transition-colors"
                />

                <div class="absolute inset-y-0 right-0 pr-2.5 flex items-center">
                    <button
                        v-if="params.search && !isSearching"
                        type="button"
                        @click="clearSearch"
                        class="text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 transition-colors"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </button>
                    <svg v-if="isSearching" class="animate-spin h-3.5 w-3.5 text-[#6C5CE7]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Conversations List -->
        <div class="flex-1 overflow-y-auto divide-y divide-slate-100/80 dark:divide-zinc-800/60" ref="scrollContainer">
            <!-- Empty state -->
            <div v-if="!rows.data || rows.data.length === 0" class="p-6">
                <EmptyState
                    title="No Conversations"
                    description="No chat threads match your current filter or search criteria."
                />
            </div>

            <!-- List Rows -->
            <Link
                v-for="(contact, index) in rows.data"
                :key="index"
                :href="'/chats/' + contact.uuid + '?page=' + (rows.meta?.current_page || 1)"
                :class="[
                    'block px-3.5 py-3 transition-all duration-150 cursor-pointer relative group',
                    activeUuid === contact.uuid
                        ? 'bg-purple-50/70 dark:bg-purple-950/30 border-l-4 border-[#6C5CE7]'
                        : contact.unread_messages > 0
                            ? 'bg-emerald-50/30 dark:bg-emerald-950/20 hover:bg-slate-50 dark:hover:bg-zinc-800/60'
                            : 'hover:bg-slate-50/80 dark:hover:bg-zinc-800/40'
                ]"
            >
                <div class="flex items-center gap-3">
                    <!-- Avatar with unread ring -->
                    <div class="relative shrink-0">
                        <Avatar
                            :src="contact.avatar || null"
                            :name="contact.full_name || 'Contact'"
                            size="md"
                        />
                        <span
                            v-if="contact.unread_messages > 0"
                            class="absolute -top-0.5 -right-0.5 h-3 w-3 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-[#111113]"
                        />
                    </div>

                    <!-- Details -->
                    <div class="min-w-0 flex-1">
                        <div class="flex items-center justify-between gap-1 mb-1">
                            <h3 :class="[
                                'text-xs sm:text-sm truncate',
                                contact.unread_messages > 0
                                    ? 'font-bold text-slate-900 dark:text-white'
                                    : 'font-semibold text-slate-800 dark:text-zinc-200'
                            ]">
                                {{ contact.full_name }}
                            </h3>
                            <span class="text-[11px] text-slate-400 dark:text-zinc-500 shrink-0">
                                {{ formatTime(contact.last_chat?.created_at) }}
                            </span>
                        </div>

                        <!-- Snippet Preview & Unread Count -->
                        <div class="flex items-center justify-between gap-2">
                            <div class="text-xs text-slate-500 dark:text-zinc-400 truncate flex items-center gap-1.5">
                                <template v-if="contact.last_chat && contact.last_chat.deleted_at === null">
                                    <!-- Text -->
                                    <span v-if="contentType(contact.last_chat.metadata) === 'text'" class="truncate">
                                        {{ content(contact.last_chat.metadata).text?.body }}
                                    </span>

                                    <!-- Button / Interactive -->
                                    <span v-else-if="contentType(contact.last_chat.metadata) === 'button'" class="truncate">
                                        {{ content(contact.last_chat.metadata).button?.text }}
                                    </span>
                                    <span v-else-if="contentType(contact.last_chat.metadata) === 'interactive'" class="truncate">
                                        {{ content(contact.last_chat.metadata).interactive?.button_reply?.title || content(contact.last_chat.metadata).interactive?.list_reply?.title }}
                                    </span>

                                    <!-- Image -->
                                    <span v-else-if="contentType(contact.last_chat.metadata) === 'image'" class="inline-flex items-center gap-1 text-slate-600 dark:text-zinc-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#06B6D4]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                                        <span>{{ $t('Photo') }}</span>
                                    </span>

                                    <!-- Document -->
                                    <span v-else-if="contentType(contact.last_chat.metadata) === 'document'" class="inline-flex items-center gap-1 text-slate-600 dark:text-zinc-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#6C5CE7]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                                        <span>{{ getExtension(contact.last_chat.media?.type) }}</span>
                                    </span>

                                    <!-- Video -->
                                    <span v-else-if="contentType(contact.last_chat.metadata) === 'video'" class="inline-flex items-center gap-1 text-slate-600 dark:text-zinc-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#EC4899]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="23 7 16 12 23 17 23 7"/><rect width="15" height="14" x="1" y="5" rx="2" ry="2"/></svg>
                                        <span>{{ $t('Video') }}</span>
                                    </span>

                                    <!-- Audio -->
                                    <span v-else-if="contentType(contact.last_chat.metadata) === 'audio'" class="inline-flex items-center gap-1 text-slate-600 dark:text-zinc-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-[#22C55E]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/></svg>
                                        <span>{{ $t('Voice message') }}</span>
                                    </span>

                                    <!-- Contact card -->
                                    <span v-else-if="contentType(contact.last_chat.metadata) === 'contacts'" class="truncate">
                                        👤 {{ getContactDisplayName(contact.last_chat.metadata) }}
                                    </span>

                                    <!-- Location -->
                                    <span v-else-if="contentType(contact.last_chat.metadata) === 'location'" class="inline-flex items-center gap-1 text-slate-600 dark:text-zinc-300">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-amber-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                                        <span>{{ $t('Location') }}</span>
                                    </span>
                                </template>
                                <span v-else class="text-slate-400 dark:text-zinc-500 italic">
                                    {{ $t('No messages yet') }}
                                </span>
                            </div>

                            <!-- Unread Pill -->
                            <span
                                v-if="contact.unread_messages > 0"
                                class="inline-flex items-center justify-center rounded-full bg-emerald-500 text-white font-bold text-[10px] h-4.5 min-w-[18px] px-1.5 shadow-sm shrink-0"
                            >
                                {{ contact.unread_messages }}
                            </span>
                        </div>
                    </div>
                </div>
            </Link>
        </div>

        <!-- Pagination Footer -->
        <div v-if="rows.meta && rows.meta.last_page > 1" class="p-3 border-t border-slate-100 dark:border-zinc-800/80 bg-slate-50/50 dark:bg-zinc-900/40 shrink-0">
            <Pagination :pagination="rows.meta" />
        </div>
    </div>
</template>