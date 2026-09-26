<template>
    <AppLayout>
        <Head :title="title" />

        <div class="p-4 sm:p-6 lg:p-8 max-w-6xl mx-auto space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-slate-200/80 dark:border-white/10 pb-6">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <Link
                            href="/support"
                            class="inline-flex items-center justify-center w-8 h-8 rounded-xl border border-slate-200 dark:border-white/10 text-slate-500 hover:text-slate-900 dark:hover:text-white transition"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                            </svg>
                        </Link>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                            {{ $t('Create Support Ticket') }}
                        </h1>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        {{ $t('Submit an inquiry to our support engineers. We will investigate and follow up promptly.') }}
                    </p>
                </div>

                <Link
                    href="/support"
                    class="hidden sm:inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200/80 dark:border-white/10 text-xs font-semibold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-white/5 transition"
                >
                    &larr; {{ $t('Back to Tickets') }}
                </Link>
            </div>

            <!-- Form + Guidance 2-Column Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Left: Form Card -->
                <div class="lg:col-span-8 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-white/10 p-6 sm:p-8 shadow-sm">
                    <form @submit.prevent="submitForm" class="space-y-6">
                        <!-- Subject Line -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                {{ $t('Subject') }} <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.subject"
                                type="text"
                                :placeholder="$t('e.g., WhatsApp template approval delay or webhook error')"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                                :class="{ 'border-rose-500': form.errors.subject }"
                            />
                            <p v-if="form.errors.subject" class="text-xs text-rose-500 mt-1 font-medium">
                                {{ form.errors.subject }}
                            </p>
                        </div>

                        <!-- Category Selector -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                {{ $t('Category') }} <span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.category"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                                :class="{ 'border-rose-500': form.errors.category }"
                            >
                                <option :value="null" disabled>{{ $t('Select an issue category...') }}</option>
                                <option
                                    v-for="cat in categories"
                                    :key="cat.id"
                                    :value="cat.id"
                                >
                                    {{ cat.name }}
                                </option>
                            </select>
                            <p v-if="form.errors.category" class="text-xs text-rose-500 mt-1 font-medium">
                                {{ form.errors.category }}
                            </p>
                        </div>

                        <!-- Detailed Description -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                {{ $t('Detailed Description') }} <span class="text-rose-500">*</span>
                            </label>
                            <textarea
                                v-model="form.message"
                                rows="6"
                                :placeholder="$t('Please describe the issue, steps to reproduce, relevant error codes, or contact numbers involved...')"
                                class="w-full px-4 py-3 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                                :class="{ 'border-rose-500': form.errors.message }"
                            ></textarea>
                            <p v-if="form.errors.message" class="text-xs text-rose-500 mt-1 font-medium">
                                {{ form.errors.message }}
                            </p>
                        </div>

                        <!-- Form Actions -->
                        <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-white/5">
                            <Link
                                href="/support"
                                class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-800 dark:hover:text-white transition"
                            >
                                {{ $t('Cancel') }}
                            </Link>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-xs font-bold shadow-md shadow-purple-600/30 hover:bg-primary/90 transition disabled:opacity-50 cursor-pointer"
                            >
                                <svg v-if="form.processing" class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>{{ form.processing ? $t('Submitting...') : $t('Submit Support Ticket') }}</span>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Right: Helpful Guidelines -->
                <div class="lg:col-span-4 space-y-5">
                    <!-- What happens next -->
                    <div class="rounded-3xl border border-slate-200/80 dark:border-white/10 bg-white dark:bg-slate-900 p-6 shadow-sm">
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-7 h-7 rounded-lg bg-primary/10 text-primary flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                            </span>
                            <h3 class="text-sm font-bold text-slate-900 dark:text-white">
                                {{ $t('What happens next?') }}
                            </h3>
                        </div>
                        <ul class="space-y-3 text-xs text-slate-500 dark:text-slate-400">
                            <li class="flex items-start gap-2">
                                <span class="w-4 h-4 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">1</span>
                                <span>{{ $t('A unique reference number is assigned to your ticket.') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="w-4 h-4 rounded-full bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">2</span>
                                <span>{{ $t('A dedicated technical support agent reviews the issue.') }}</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="w-4 h-4 rounded-full bg-purple-500/10 text-purple-600 dark:text-purple-400 font-bold text-[10px] flex items-center justify-center shrink-0 mt-0.5">3</span>
                                <span>{{ $t('You can converse directly with the agent through the ticket thread.') }}</span>
                            </li>
                        </ul>
                    </div>

                    <!-- Live Chat Alternative -->
                    <div class="rounded-3xl border border-emerald-500/20 bg-emerald-500/5 p-6">
                        <div class="flex items-center gap-2 mb-2">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                            <h4 class="text-xs font-bold uppercase tracking-wider text-emerald-700 dark:text-emerald-400">
                                {{ $t('Need Immediate Assistance?') }}
                            </h4>
                        </div>
                        <p class="text-xs text-slate-600 dark:text-slate-300 leading-relaxed mb-4">
                            {{ $t('If your inquiry is regarding live customer conversations or urgent broadcasts, check the Live WhatsApp Inbox.') }}
                        </p>
                        <Link
                            href="/chats?status=open"
                            class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline"
                        >
                            <span>{{ $t('Go to WhatsApp Chats') }}</span>
                            &rarr;
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import AppLayout from '../Layout/App.vue';

const props = defineProps({
    title: {
        type: String,
        default: 'Create Ticket',
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

const form = useForm({
    subject: '',
    category: null,
    message: '',
});

function submitForm() {
    form.post('/support');
}
</script>