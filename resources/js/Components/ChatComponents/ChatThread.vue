<script setup>
import { computed } from 'vue';
import ChatBubble from '@/Components/ChatComponents/ChatBubble.vue';

const props = defineProps({
    rows: {
        type: Array,
        required: true,
        default: () => [],
    },
});

const isSameDay = (date1, date2) => {
    if (!date1 || !date2) return false;
    const d1 = new Date(date1);
    const d2 = new Date(date2);
    return (
        d1.getFullYear() === d2.getFullYear() &&
        d1.getMonth() === d2.getMonth() &&
        d1.getDate() === d2.getDate()
    );
};

const formatDateDivider = (dateString) => {
    if (!dateString) return '';
    const date = new Date(dateString);
    const today = new Date();
    const yesterday = new Date();
    yesterday.setDate(today.getDate() - 1);

    if (isSameDay(date, today)) return 'Today';
    if (isSameDay(date, yesterday)) return 'Yesterday';

    return date.toLocaleDateString([], {
        weekday: 'short',
        month: 'short',
        day: 'numeric',
        year: date.getFullYear() !== today.getFullYear() ? 'numeric' : undefined,
    });
};

const getMessageDate = (item) => {
    return item?.[0]?.value?.created_at || null;
};
</script>

<template>
    <div v-if="rows.length > 0" class="py-4 px-3 sm:px-6 lg:px-8 space-y-3">
        <template v-for="(chat, index) in rows" :key="index">
            <!-- Date Separator Divider -->
            <div
                v-if="index === 0 || !isSameDay(getMessageDate(rows[index - 1]), getMessageDate(chat))"
                class="flex items-center justify-center my-4"
            >
                <span class="px-3 py-1 rounded-full text-[11px] font-semibold tracking-wider text-slate-500 dark:text-zinc-400 bg-slate-200/70 dark:bg-zinc-800/80 shadow-subtle border border-slate-200 dark:border-zinc-700/60 uppercase">
                    {{ formatDateDivider(getMessageDate(chat)) }}
                </span>
            </div>

            <!-- Standard Chat Message -->
            <div
                v-if="chat[0]?.type === 'chat'"
                class="flex flex-col"
                :class="chat[0].value?.type === 'outbound' ? 'items-end' : 'items-start'"
            >
                <ChatBubble
                    :content="chat[0].value"
                    :type="chat[0].value?.type"
                />
            </div>

            <!-- Ticket System Event -->
            <div v-else-if="chat[0]?.type === 'ticket'" class="flex justify-center my-2">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-slate-100 dark:bg-zinc-800/70 border border-slate-200/80 dark:border-zinc-700 text-xs text-slate-600 dark:text-zinc-400 shadow-subtle">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <span>{{ chat[0].value?.description }}</span>
                    <span class="text-[10px] text-slate-400 dark:text-zinc-500">• {{ chat[0].value?.created_at }}</span>
                </div>
            </div>

            <!-- Internal Team Note -->
            <div v-else-if="chat[0]?.type === 'notes'" class="flex justify-end my-2">
                <div class="max-w-md rounded-2xl bg-amber-50 dark:bg-amber-950/30 border border-amber-200/80 dark:border-amber-800/50 p-3 shadow-subtle text-xs text-amber-900 dark:text-amber-200 space-y-1">
                    <div class="flex items-center gap-1.5 font-bold text-[11px] text-amber-700 dark:text-amber-400 uppercase tracking-wider">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        <span>Internal Team Note</span>
                    </div>
                    <p class="whitespace-pre-wrap leading-relaxed">{{ chat[0].value?.content }}</p>
                    <div class="text-[10px] text-amber-600/70 dark:text-amber-400/60 text-right">
                        {{ chat[0].value?.created_at }}
                    </div>
                </div>
            </div>
        </template>
    </div>
</template>