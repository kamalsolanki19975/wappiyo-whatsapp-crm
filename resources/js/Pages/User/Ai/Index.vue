<template>
  <AutomationLayout :aimodule="true">
    <div class="max-w-4xl mx-auto space-y-6 pb-12">
      <!-- Top Title and Action Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200/80 dark:border-zinc-800">
        <div class="flex items-center gap-3">
          <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-purple-600 via-indigo-600 to-pink-500 text-white flex items-center justify-center shadow-md shadow-purple-500/20 shrink-0">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
            </svg>
          </div>
          <div>
            <div class="flex items-center gap-2">
              <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">
                {{ $t('Wappiyo AI Assistant') }}
              </h2>
              <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                GPT Powered
              </span>
            </div>
            <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">
              {{ $t('Generate marketing campaigns, compose customer service replies, and draft Meta-compliant templates.') }}
            </p>
          </div>
        </div>

        <!-- Header Actions: Clear Chat -->
        <div v-if="messages.length" class="flex items-center gap-2">
          <button
            type="button"
            @click="clearHistory"
            :disabled="isClearing"
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 hover:bg-slate-50 dark:hover:bg-zinc-700/60 text-xs font-semibold text-slate-600 dark:text-zinc-300 transition-colors cursor-pointer disabled:opacity-50"
          >
            <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
            </svg>
            <span>{{ isClearing ? $t('Clearing...') : $t('Clear Conversation') }}</span>
          </button>
        </div>
      </div>

      <!-- Main Conversation Window Container -->
      <div class="bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 rounded-3xl shadow-sm overflow-hidden flex flex-col min-h-[520px] max-h-[680px]">
        
        <!-- Conversation Message Area -->
        <div ref="chatContainer" class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-4">
          
          <!-- EMPTY STATE: "How can I help you today?" -->
          <div v-if="!messages.length" class="h-full flex flex-col items-center justify-center text-center py-10 px-4 space-y-6">
            <div class="w-16 h-16 rounded-3xl bg-gradient-to-tr from-purple-100 via-indigo-50 to-pink-100 dark:from-purple-950/60 dark:via-indigo-950/40 dark:to-pink-950/40 text-purple-600 dark:text-purple-400 flex items-center justify-center shadow-inner">
              <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" />
              </svg>
            </div>

            <div class="space-y-1.5 max-w-md">
              <h3 class="text-xl font-bold text-slate-900 dark:text-white">
                {{ $t('How can I help you today?') }}
              </h3>
              <p class="text-xs text-slate-500 dark:text-zinc-400">
                {{ $t('Choose an example prompt below or ask any question to draft WhatsApp copy, troubleshoot automations, or write template responses.') }}
              </p>
            </div>

            <!-- Suggestion Prompt Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 w-full max-w-2xl text-left pt-2">
              <button
                v-for="item in props.presetPrompts"
                :key="item.id"
                type="button"
                @click="applyPreset(item.prompt)"
                class="p-3.5 rounded-2xl border border-slate-200/90 dark:border-zinc-800 bg-slate-50/60 dark:bg-zinc-800/40 hover:bg-white dark:hover:bg-zinc-800 hover:border-purple-300 dark:hover:border-purple-700 transition shadow-2xs hover:shadow-sm cursor-pointer group"
              >
                <div class="flex items-center justify-between mb-1">
                  <span class="text-[10px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400">
                    {{ item.category }}
                  </span>
                  <svg class="w-3.5 h-3.5 text-slate-300 dark:text-zinc-600 group-hover:text-purple-500 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                  </svg>
                </div>
                <h4 class="text-xs font-bold text-slate-800 dark:text-zinc-200 mb-0.5">
                  {{ item.title }}
                </h4>
                <p class="text-[11px] text-slate-500 dark:text-zinc-400 line-clamp-2 leading-relaxed">
                  {{ item.prompt }}
                </p>
              </button>
            </div>
          </div>

          <!-- ACTIVE CONVERSATION THREAD -->
          <template v-else>
            <div
              v-for="msg in messages"
              :key="msg.id"
              :class="[
                'flex gap-3 max-w-[85%] sm:max-w-[80%]',
                msg.sender === 'user' ? 'ml-auto flex-row-reverse' : 'mr-auto'
              ]"
            >
              <!-- Avatar -->
              <div
                :class="[
                  'w-8 h-8 rounded-full flex items-center justify-center text-xs font-bold shrink-0 mt-0.5',
                  msg.sender === 'user'
                    ? 'bg-slate-900 text-white dark:bg-zinc-700'
                    : 'bg-gradient-to-tr from-purple-600 to-indigo-600 text-white shadow-sm'
                ]"
              >
                <span v-if="msg.sender === 'user'">👤</span>
                <span v-else>AI</span>
              </div>

              <!-- Bubble -->
              <div class="space-y-1">
                <div
                  :class="[
                    'p-4 rounded-2xl text-xs leading-relaxed whitespace-pre-line shadow-xs',
                    msg.sender === 'user'
                      ? 'bg-purple-600 text-white rounded-tr-none'
                      : 'bg-slate-100/90 dark:bg-zinc-800 text-slate-800 dark:text-zinc-200 rounded-tl-none border border-slate-200/60 dark:border-zinc-700/60'
                  ]"
                >
                  <p>{{ msg.text }}</p>
                </div>
                <div :class="['text-[10px] text-slate-400 px-1', msg.sender === 'user' ? 'text-right' : 'text-left']">
                  {{ msg.time }}
                </div>
              </div>
            </div>

            <!-- Loading Indicator Bubble -->
            <div v-if="isLoading" class="flex gap-3 mr-auto max-w-[80%]">
              <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-purple-600 to-indigo-600 text-white flex items-center justify-center text-xs font-bold shrink-0 shadow-sm animate-pulse">
                AI
              </div>
              <div class="p-4 rounded-2xl rounded-tl-none bg-slate-100 dark:bg-zinc-800 border border-slate-200/60 dark:border-zinc-700/60 text-xs flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-purple-600 animate-bounce"></span>
                <span class="w-2 h-2 rounded-full bg-indigo-600 animate-bounce [animation-delay:0.15s]"></span>
                <span class="w-2 h-2 rounded-full bg-pink-500 animate-bounce [animation-delay:0.3s]"></span>
                <span class="text-slate-400 text-[11px] font-medium ml-1.5">{{ $t('Wappiyo AI is generating response...') }}</span>
              </div>
            </div>

            <!-- Error Banner -->
            <div v-if="errorMessage" class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs font-medium flex items-center justify-between gap-2">
              <div class="flex items-center gap-2">
                <svg class="w-4 h-4 shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ errorMessage }}</span>
              </div>
              <button
                type="button"
                @click="errorMessage = ''"
                class="text-[11px] underline hover:no-underline font-semibold"
              >
                {{ $t('Dismiss') }}
              </button>
            </div>
          </template>
        </div>

        <!-- Input Bar Section -->
        <div class="p-3 sm:p-4 border-t border-slate-100 dark:border-zinc-800 bg-slate-50/60 dark:bg-zinc-900/60">
          <form @submit.prevent="sendMessage" class="flex items-center gap-2">
            <div class="relative flex-1">
              <input
                ref="inputRef"
                v-model="promptInput"
                type="text"
                maxlength="2000"
                :disabled="isLoading"
                :placeholder="$t('Ask Wappiyo AI to draft a template, reply, or campaign...')"
                class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-xs sm:text-sm text-slate-900 dark:text-white placeholder:text-slate-400 dark:placeholder:text-zinc-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition shadow-inner disabled:opacity-50"
                @keydown.enter.exact.prevent="sendMessage"
              />
            </div>

            <button
              type="submit"
              :disabled="isLoading || !promptInput.trim()"
              class="inline-flex items-center justify-center gap-2 px-5 py-3 rounded-2xl bg-gradient-to-r from-purple-600 via-indigo-600 to-pink-500 hover:from-purple-700 hover:to-indigo-700 text-white font-bold text-xs shadow-md shadow-purple-500/20 active:scale-[0.98] transition cursor-pointer disabled:opacity-50"
            >
              <svg v-if="isLoading" class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <svg v-else class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
              </svg>
              <span class="hidden sm:inline">{{ isLoading ? $t('Thinking...') : $t('Send') }}</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </AutomationLayout>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue';
import { useI18n } from 'vue-i18n';
import AutomationLayout from '@/Pages/User/Automation/Layout.vue';
import axios from 'axios';

const { t } = useI18n();

const props = defineProps({
  organization: Object,
  presetPrompts: Array,
  initialHistory: Array,
  aimodule: Boolean,
});

const messages = ref(props.initialHistory || []);
const promptInput = ref('');
const isLoading = ref(false);
const isClearing = ref(false);
const errorMessage = ref('');

const chatContainer = ref(null);
const inputRef = ref(null);

const scrollToBottom = async () => {
  await nextTick();
  if (chatContainer.value) {
    chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
  }
};

const applyPreset = (promptText) => {
  promptInput.value = promptText;
  nextTick(() => {
    inputRef.value?.focus();
  });
};

const sendMessage = async () => {
  const query = promptInput.value.trim();
  if (!query || isLoading.value) return;

  errorMessage.value = '';
  promptInput.value = '';

  // Optimistically append user message
  const userMsg = {
    id: 'user_' + Date.now(),
    sender: 'user',
    text: query,
    time: new Date().toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' }),
  };
  messages.value.push(userMsg);
  scrollToBottom();

  isLoading.value = true;

  try {
    const response = await axios.post('/automation/ai/chat', { prompt: query });
    if (response.data.success) {
      messages.value.push(response.data.assistant_message);
    } else {
      errorMessage.value = response.data.message || t("I couldn't complete that request. Please try again.");
    }
  } catch (err) {
    errorMessage.value = err.response?.data?.message || t("I couldn't complete that request. Please try again.");
  } finally {
    isLoading.value = false;
    scrollToBottom();
  }
};

const clearHistory = async () => {
  if (isClearing.value) return;
  isClearing.value = true;
  try {
    await axios.post('/automation/ai/clear');
    messages.value = [];
  } catch {
    // ignore
  } finally {
    isClearing.value = false;
  }
};

onMounted(() => {
  scrollToBottom();
  inputRef.value?.focus();
});
</script>
