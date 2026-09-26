<script setup>
import axios from 'axios';
import { ref, watchEffect, computed } from 'vue';
import Button from '@/Components/UI/Button.vue';

const props = defineProps(['contact', 'chatLimitReached']);
const emit = defineEmits(['response', 'viewTemplate']);

const processingForm = ref(false);
const formTextInput = ref('');
const textInputRef = ref(null);
const fileInputRef = ref(null);
const showAiSuggestions = ref(false);

const form = ref({
    uuid: props.contact?.uuid,
    message: null,
    type: 'text',
    file: null,
});

watchEffect(() => {
    form.value.uuid = props.contact?.uuid;
});

const viewTemplate = () => {
    emit('viewTemplate', true);
};

const isInboundChatWithin24Hours = computed(() => {
    if (props.contact?.last_inbound_chat?.created_at) {
        const lastInboundChatTime = new Date(props.contact.last_inbound_chat.created_at);
        const currentTime = new Date();
        const timeDifference = currentTime - lastInboundChatTime;
        return timeDifference < 24 * 60 * 60 * 1000;
    }
    return false;
});

const adjustTextareaHeight = () => {
    const textInput = textInputRef.value;
    if (textInput) {
        textInput.style.height = 'auto';
        textInput.style.height = Math.min(textInput.scrollHeight, 140) + 'px';
    }
};

const sendMessage = async () => {
    if (!formTextInput.value?.trim() && !form.value.file) return;

    form.value.message = formTextInput.value;
    const formData = new FormData();

    formData.append('message', form.value.message || '');
    formData.append('type', form.value.type || 'text');
    formData.append('uuid', form.value.uuid);

    if (form.value.file) {
        formData.append('file', form.value.file);
    }

    processingForm.value = true;

    try {
        await axios.post('/chats', formData);
        form.value.message = null;
        formTextInput.value = '';
        form.value.file = null;
        form.value.type = 'text';

        if (textInputRef.value) {
            textInputRef.value.style.height = 'auto';
        }
    } catch (error) {
        console.error('Failed to send message:', error);
    } finally {
        processingForm.value = false;
    }
};

const handleEnterKey = (event) => {
    if (!event.shiftKey) {
        if (formTextInput.value?.trim()) {
            sendMessage();
        }
    }
};

const triggerFileUpload = (fileType) => {
    form.value.type = fileType;
    if (fileInputRef.value) {
        fileInputRef.value.click();
    }
};

const handleFileUpload = (event) => {
    const file = event.target.files?.[0];
    if (file) {
        form.value.file = file;
        sendMessage();
    }
};

const getAcceptedFileTypes = () => {
    switch (form.value.type) {
        case 'image':
            return '.jpg, .jpeg, .png, .webp';
        case 'document':
            return '.txt, .pdf, .ppt, .doc, .xls, .docx, .pptx, .xlsx';
        case 'audio':
            return '.mp3, .ogg, .wav, .m4a';
        case 'video':
            return '.mp4, .mov';
        default:
            return '*/*';
    }
};

// Fast suggested replies for AI menu
const quickReplies = [
    'Hello! How can I assist you today?',
    'Thank you for reaching out. We are looking into your request right away.',
    'Could you please share your order number or account details?',
    'Is there anything else I can help you with today?',
];

const insertQuickReply = (text) => {
    formTextInput.value = text;
    showAiSuggestions.value = false;
    if (textInputRef.value) {
        textInputRef.value.focus();
        adjustTextareaHeight();
    }
};
</script>

<template>
    <div class="px-3 sm:px-6 lg:px-8 w-full max-w-5xl mx-auto space-y-2">
        <!-- 1. Subscription Message Limit Warning -->
        <div
            v-if="props.chatLimitReached"
            class="flex items-center gap-3 p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 text-rose-900 dark:text-rose-200 text-xs shadow-subtle"
        >
            <div class="h-8 w-8 rounded-xl bg-rose-100 dark:bg-rose-900/60 flex items-center justify-center shrink-0 text-rose-600">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            </div>
            <div>
                <h4 class="font-bold text-xs">{{ $t('Monthly Message Limit Reached') }}</h4>
                <p class="text-rose-700 dark:text-rose-300 mt-0.5">
                    {{ $t('Your workspace has reached its plan message limit. Please upgrade your subscription to send and receive more messages.') }}
                </p>
            </div>
        </div>

        <!-- 2. WhatsApp 24-Hour Session Policy Alert -->
        <div
            v-if="!isInboundChatWithin24Hours && !props.chatLimitReached"
            class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-3.5 rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-800/50 text-xs shadow-subtle"
        >
            <div class="flex items-start gap-3">
                <div class="h-8 w-8 rounded-xl bg-amber-100 dark:bg-amber-900/60 flex items-center justify-center shrink-0 text-amber-600 dark:text-amber-400">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                </div>
                <div>
                    <h4 class="font-bold text-amber-900 dark:text-amber-200">
                        {{ $t('24-Hour WhatsApp Session Window Closed') }}
                    </h4>
                    <p class="text-amber-700 dark:text-amber-300 mt-0.5 max-w-xl">
                        {{ $t('WhatsApp policy allows direct freeform messaging only within 24 hours of customer contact. You can resume conversation with an approved Template message.') }}
                    </p>
                </div>
            </div>

            <Button
                type="button"
                variant="primary"
                size="sm"
                @click="viewTemplate"
                class="shrink-0 self-start sm:self-center"
            >
                <template #icon>
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                </template>
                {{ $t('Send Template') }}
            </Button>
        </div>

        <!-- 3. AI Quick Replies Flyout Popup -->
        <div
            v-if="showAiSuggestions"
            class="p-3 bg-white dark:bg-[#18181B] border border-purple-200/80 dark:border-purple-800/50 rounded-2xl shadow-elevated space-y-2 animate-in fade-in slide-in-from-bottom-2 duration-150"
        >
            <div class="flex items-center justify-between text-xs font-bold text-slate-700 dark:text-zinc-200">
                <div class="flex items-center gap-1.5 text-[#6C5CE7] dark:text-purple-300">
                    <span>✨</span>
                    <span>Wappiyo AI Smart Replies</span>
                </div>
                <button
                    type="button"
                    class="text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200"
                    @click="showAiSuggestions = false"
                >
                    ✕
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                <button
                    v-for="(reply, idx) in quickReplies"
                    :key="idx"
                    type="button"
                    @click="insertQuickReply(reply)"
                    class="text-left text-xs p-2 rounded-xl bg-slate-50 dark:bg-zinc-900 border border-slate-100 dark:border-zinc-800 text-slate-700 dark:text-zinc-300 hover:bg-purple-50 dark:hover:bg-purple-950/40 hover:border-purple-200 transition-colors"
                >
                    {{ reply }}
                </button>
            </div>
        </div>

        <!-- 4. Main Composer Bar -->
        <form
            v-if="isInboundChatWithin24Hours && !props.chatLimitReached"
            @submit.prevent="sendMessage"
            class="relative flex items-end gap-2 bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 rounded-2xl p-2 shadow-card focus-within:border-[#6C5CE7] focus-within:ring-2 focus-within:ring-[#6C5CE7]/20 transition-all duration-150"
        >
            <!-- Hidden native file input -->
            <input
                ref="fileInputRef"
                type="file"
                class="hidden"
                :accept="getAcceptedFileTypes()"
                @change="handleFileUpload($event)"
            />

            <!-- Attachment Toolbar -->
            <div class="flex items-center gap-0.5 text-slate-400 dark:text-zinc-500 pb-1">
                <!-- Image attachment -->
                <button
                    type="button"
                    @click="triggerFileUpload('image')"
                    class="p-2 rounded-xl hover:text-[#06B6D4] hover:bg-cyan-50 dark:hover:bg-cyan-950/30 transition-colors"
                    title="Send Image"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><circle cx="9" cy="9" r="2"/><path d="m21 15-3.086-3.086a2 2 0 0 0-2.828 0L6 21"/></svg>
                </button>

                <!-- Document attachment -->
                <button
                    type="button"
                    @click="triggerFileUpload('document')"
                    class="p-2 rounded-xl hover:text-[#6C5CE7] hover:bg-purple-50 dark:hover:bg-purple-950/30 transition-colors"
                    title="Send Document"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                </button>

                <!-- Audio attachment -->
                <button
                    type="button"
                    @click="triggerFileUpload('audio')"
                    class="p-2 rounded-xl hover:text-[#22C55E] hover:bg-emerald-50 dark:hover:bg-emerald-950/30 transition-colors"
                    title="Send Audio"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" x2="12" y1="19" y2="22"/></svg>
                </button>

                <!-- Video attachment -->
                <button
                    type="button"
                    @click="triggerFileUpload('video')"
                    class="p-2 rounded-xl hover:text-[#EC4899] hover:bg-pink-50 dark:hover:bg-pink-950/30 transition-colors"
                    title="Send Video"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="23 7 16 12 23 17 23 7"/><rect width="15" height="14" x="1" y="5" rx="2" ry="2"/></svg>
                </button>

                <!-- Template message picker -->
                <button
                    type="button"
                    @click="viewTemplate"
                    class="p-2 rounded-xl hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-950/30 transition-colors"
                    title="Send Template Message"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
                </button>

                <!-- AI Quick Replies Toggle -->
                <button
                    type="button"
                    @click="showAiSuggestions = !showAiSuggestions"
                    :class="[
                        'p-2 rounded-xl transition-colors',
                        showAiSuggestions
                            ? 'bg-purple-100 dark:bg-purple-950 text-[#6C5CE7]'
                            : 'hover:text-[#6C5CE7] hover:bg-purple-50 dark:hover:bg-purple-950/30'
                    ]"
                    title="AI Suggested Replies"
                >
                    <span class="text-sm leading-none">✨</span>
                </button>
            </div>

            <!-- Textarea -->
            <div class="flex-1 min-w-0 pb-1">
                <textarea
                    ref="textInputRef"
                    v-model="formTextInput"
                    @input="adjustTextareaHeight"
                    @keydown.enter.exact="handleEnterKey"
                    rows="1"
                    :placeholder="$t('Type a WhatsApp message... (Press Enter to send, Shift+Enter for new line)')"
                    :disabled="processingForm"
                    class="w-full bg-transparent resize-none text-xs sm:text-sm text-slate-900 dark:text-zinc-100 placeholder-slate-400 dark:placeholder-zinc-500 outline-none leading-relaxed max-h-36 py-1"
                />
            </div>

            <!-- Send Action Button -->
            <div class="pb-1 shrink-0">
                <button
                    type="submit"
                    :disabled="!formTextInput?.trim() && !form.file || processingForm"
                    :class="[
                        'flex items-center justify-center h-9 w-9 rounded-xl transition-all duration-150',
                        formTextInput?.trim() || form.file
                            ? 'bg-gradient-to-tr from-[#6C5CE7] to-[#8B5CF6] text-white shadow-md shadow-purple-600/30 hover:scale-105 active:scale-95'
                            : 'bg-slate-100 dark:bg-zinc-800 text-slate-300 dark:text-zinc-600 cursor-not-allowed'
                    ]"
                    :title="$t('Send Message (Enter)')"
                >
                    <svg v-if="!processingForm" xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 translate-x-0.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
                    </svg>
                    <svg v-else class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</template>