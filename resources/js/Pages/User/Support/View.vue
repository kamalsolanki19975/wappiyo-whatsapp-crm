<template>
    <AppLayout>
        <Head :title="`${$t('Ticket')} ${ticket.reference}`" />

        <div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto space-y-6">
            <!-- Header & Action Bar -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200/80 dark:border-white/10 pb-6">
                <!-- Left: Title & Badges -->
                <div class="flex items-start gap-3">
                    <Link
                        href="/support"
                        class="inline-flex items-center justify-center w-9 h-9 rounded-xl border border-slate-200 dark:border-white/10 text-slate-500 hover:text-slate-900 dark:hover:text-white transition shrink-0 mt-0.5"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                        </svg>
                    </Link>

                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <span class="font-mono text-xs font-bold text-primary px-2 py-0.5 rounded-lg bg-primary/10 dark:bg-primary/20">
                                {{ ticket.reference }}
                            </span>
                            <TicketStatusBadge :status="ticket.status" size="sm" />
                            <TicketPriorityBadge :priority="ticket.priority" size="sm" />
                            <span class="px-2 py-0.5 rounded-lg text-[10px] font-semibold bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-300">
                                {{ ticket.category?.name || 'Support' }}
                            </span>
                        </div>
                        <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                            {{ ticket.subject }}
                        </h1>
                    </div>
                </div>

                <!-- Right: Action Buttons -->
                <div class="flex flex-wrap items-center gap-2">
                    <!-- If Open or Pending: Can Resolve or Close -->
                    <template v-if="ticket.status === 'open' || ticket.status === 'pending'">
                        <button
                            type="button"
                            @click="changeTicketStatus('resolved')"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-sm shadow-purple-600/30 transition cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span>{{ $t('Mark Resolved') }}</span>
                        </button>

                        <button
                            type="button"
                            @click="changeTicketStatus('closed')"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-white/10 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 text-xs font-semibold transition cursor-pointer"
                        >
                            <span>{{ $t('Close Ticket') }}</span>
                        </button>
                    </template>

                    <!-- If Resolved or Closed: Can Reopen -->
                    <template v-else>
                        <button
                            type="button"
                            @click="changeTicketStatus('open')"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm shadow-emerald-600/30 transition cursor-pointer"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span>{{ $t('Reopen Ticket') }}</span>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Two-Column Workspace Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Left Main Panel: Issue & Comments Thread (8 cols) -->
                <div class="lg:col-span-8 space-y-6">
                    <!-- Original Issue Card -->
                    <div class="rounded-3xl border border-slate-200/80 dark:border-white/10 bg-white dark:bg-slate-900 p-6 shadow-sm">
                        <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-white/5 mb-4">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-xl bg-primary/10 text-primary flex items-center justify-center font-bold text-xs">
                                    {{ getInitials(ticket.user?.first_name, ticket.user?.last_name) }}
                                </div>
                                <div>
                                    <div class="text-xs font-bold text-slate-900 dark:text-white">
                                        {{ ticket.user ? (ticket.user.first_name + ' ' + ticket.user.last_name) : 'User' }}
                                    </div>
                                    <div class="text-[10px] text-slate-400">
                                        {{ $t('Reported on') }} {{ formatDateTime(ticket.created_at) }}
                                    </div>
                                </div>
                            </div>

                            <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 bg-slate-100 dark:bg-white/5 px-2 py-0.5 rounded-md">
                                {{ $t('Original Ticket Description') }}
                            </span>
                        </div>

                        <div class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-wrap">
                            {{ ticket.message }}
                        </div>
                    </div>

                    <!-- Conversation Thread / Comments -->
                    <div class="space-y-4">
                        <div class="flex items-center justify-between px-1">
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white flex items-center gap-2">
                                <span>{{ $t('Conversation Activity') }}</span>
                                <span class="px-2 py-0.2 rounded-full text-[10px] bg-slate-100 dark:bg-white/10 text-slate-600 dark:text-slate-300">
                                    {{ ticket.comments_with_user?.length || 0 }}
                                </span>
                            </h3>
                        </div>

                        <!-- Comments List -->
                        <div v-if="ticket.comments_with_user?.length > 0" class="space-y-3">
                            <div
                                v-for="(comment, index) in ticket.comments_with_user"
                                :key="index"
                                class="rounded-2xl border p-4 transition"
                                :class="comment.user?.role !== 'user'
                                    ? 'bg-purple-50/50 dark:bg-purple-950/20 border-purple-200/60 dark:border-purple-800/30'
                                    : 'bg-white dark:bg-slate-900 border-slate-200/80 dark:border-white/10'"
                            >
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center gap-2.5">
                                        <div
                                            class="w-7 h-7 rounded-lg flex items-center justify-center font-bold text-xs"
                                            :class="comment.user?.role !== 'user'
                                                ? 'bg-purple-600 text-white'
                                                : 'bg-slate-200 dark:bg-white/10 text-slate-700 dark:text-slate-300'"
                                        >
                                            {{ getInitials(comment.user?.first_name, comment.user?.last_name) }}
                                        </div>
                                        <div>
                                            <span class="text-xs font-bold text-slate-900 dark:text-white">
                                                {{ comment.user?.first_name }} {{ comment.user?.last_name }}
                                            </span>
                                            <span
                                                v-if="comment.user?.role !== 'user'"
                                                class="ml-1.5 px-1.5 py-0.2 rounded text-[9px] font-bold uppercase tracking-wider bg-purple-500/10 text-purple-600 dark:text-purple-400"
                                            >
                                                {{ $t('Support Rep') }}
                                            </span>
                                        </div>
                                    </div>

                                    <span class="text-[11px] text-slate-400">
                                        {{ formatDateTime(comment.created_at) }}
                                    </span>
                                </div>

                                <div class="text-xs text-slate-700 dark:text-slate-300 leading-relaxed whitespace-pre-wrap pl-9">
                                    {{ comment.message }}
                                </div>
                            </div>
                        </div>

                        <!-- No comments yet state -->
                        <div
                            v-else
                            class="p-6 rounded-2xl border border-dashed border-slate-200 dark:border-white/10 text-center text-xs text-slate-400"
                        >
                            {{ $t('No additional comments yet. Add a reply below to update the inquiry.') }}
                        </div>
                    </div>

                    <!-- Add Comment Composer -->
                    <div
                        v-if="ticket.status === 'open' || ticket.status === 'pending'"
                        class="rounded-3xl border border-slate-200/80 dark:border-white/10 bg-white dark:bg-slate-900 p-6 shadow-sm"
                    >
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            {{ $t('Add a Response') }}
                        </h4>

                        <form @submit.prevent="submitComment">
                            <textarea
                                v-model="form.message"
                                rows="4"
                                :placeholder="$t('Type your comment or update here... (Press Ctrl + Enter to submit)')"
                                @keydown.ctrl.enter="submitComment"
                                @keydown.meta.enter="submitComment"
                                class="w-full px-4 py-3 rounded-2xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                            ></textarea>

                            <div class="flex items-center justify-between mt-3">
                                <span class="text-[11px] text-slate-400">
                                    {{ $t('Notifications will be sent automatically.') }}
                                </span>

                                <button
                                    type="submit"
                                    :disabled="form.processing || !form.message?.trim()"
                                    class="inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-primary text-white text-xs font-bold shadow-sm shadow-purple-600/30 hover:bg-primary/90 transition disabled:opacity-50 cursor-pointer"
                                >
                                    <svg v-if="form.processing" class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>{{ form.processing ? $t('Sending...') : $t('Post Comment') }}</span>
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- Ticket Closed Alert -->
                    <div
                        v-else
                        class="p-4 rounded-2xl bg-slate-100 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 text-center text-xs text-slate-500 dark:text-slate-400"
                    >
                        {{ $t('This ticket is currently') }} <span class="font-bold uppercase">{{ ticket.status }}</span>.
                        {{ $t('Reopen the ticket to add new replies.') }}
                    </div>
                </div>

                <!-- Right Side Panel: Ticket Metadata & Context (4 cols) -->
                <div class="lg:col-span-4 space-y-5">
                    <!-- Ticket Details Card -->
                    <div class="rounded-3xl border border-slate-200/80 dark:border-white/10 bg-white dark:bg-slate-900 p-6 shadow-sm space-y-4">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 pb-3 border-b border-slate-100 dark:border-white/5">
                            {{ $t('Ticket Information') }}
                        </h3>

                        <!-- Status Row -->
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">{{ $t('Status') }}</span>
                            <TicketStatusBadge :status="ticket.status" size="sm" />
                        </div>

                        <!-- Priority Selector / Row -->
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">{{ $t('Priority') }}</span>
                            <select
                                :value="ticket.priority || 'medium'"
                                @change="changePriority($event.target.value)"
                                class="px-2 py-1 rounded-lg border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-xs text-slate-800 dark:text-slate-200 font-semibold focus:outline-none focus:ring-1 focus:ring-primary"
                            >
                                <option value="low">{{ $t('Low') }}</option>
                                <option value="medium">{{ $t('Medium') }}</option>
                                <option value="high">{{ $t('High') }}</option>
                                <option value="urgent">{{ $t('Urgent') }}</option>
                            </select>
                        </div>

                        <!-- Category -->
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">{{ $t('Category') }}</span>
                            <span class="font-semibold text-slate-900 dark:text-white">
                                {{ ticket.category?.name || '—' }}
                            </span>
                        </div>

                        <!-- Created Date -->
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-500">{{ $t('Created') }}</span>
                            <span class="font-medium text-slate-700 dark:text-slate-300 text-[11px]">
                                {{ formatDateTime(ticket.created_at) }}
                            </span>
                        </div>

                        <!-- Assigned Agent -->
                        <div class="flex items-center justify-between text-xs pt-3 border-t border-slate-100 dark:border-white/5">
                            <span class="text-slate-500">{{ $t('Assigned Agent') }}</span>
                            <span v-if="ticket.agent" class="font-semibold text-cyan-600 dark:text-cyan-400">
                                {{ ticket.agent.first_name }} {{ ticket.agent.last_name }}
                            </span>
                            <span v-else class="text-slate-400 italic text-[11px]">
                                {{ $t('Pending Assignment') }}
                            </span>
                        </div>
                    </div>

                    <!-- Customer Profile Card -->
                    <div class="rounded-3xl border border-slate-200/80 dark:border-white/10 bg-white dark:bg-slate-900 p-6 shadow-sm space-y-3">
                        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 pb-3 border-b border-slate-100 dark:border-white/5">
                            {{ $t('Customer / Requester') }}
                        </h3>

                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-primary to-violet-500 text-white font-bold text-sm flex items-center justify-center shrink-0 shadow-sm">
                                {{ getInitials(ticket.user?.first_name, ticket.user?.last_name) }}
                            </div>
                            <div class="min-w-0">
                                <div class="font-bold text-xs text-slate-900 dark:text-white truncate">
                                    {{ ticket.user ? (ticket.user.first_name + ' ' + ticket.user.last_name) : 'User' }}
                                </div>
                                <div class="text-[11px] text-slate-400 truncate">
                                    {{ ticket.user?.email }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import AppLayout from '../Layout/App.vue';
import TicketStatusBadge from '@/Components/Support/TicketStatusBadge.vue';
import TicketPriorityBadge from '@/Components/Support/TicketPriorityBadge.vue';

const props = defineProps({
    ticket: {
        type: Object,
        required: true,
    },
});

const form = useForm({
    message: '',
});

function formatDateTime(value) {
    if (!value) return '—';
    try {
        const d = new Date(value);
        return d.toLocaleString('en-US', {
            month: 'short',
            day: 'numeric',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit',
        });
    } catch (e) {
        return value;
    }
}

function getInitials(first, last) {
    const f = first ? first[0] : '';
    const l = last ? last[0] : '';
    return (f + l).toUpperCase() || 'U';
}

function submitComment() {
    if (!form.message?.trim()) return;
    form.post('/support/' + props.ticket.uuid + '/comment', {
        preserveScroll: true,
        onSuccess: () => form.reset(),
    });
}

function changeTicketStatus(status) {
    router.post('/support/' + props.ticket.uuid + '/status', { status }, {
        preserveScroll: true,
    });
}

function changePriority(priority) {
    router.post('/support/' + props.ticket.uuid + '/priority', { priority }, {
        preserveScroll: true,
    });
}
</script>