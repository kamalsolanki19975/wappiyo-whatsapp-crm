<script setup>
import { Link } from '@inertiajs/vue3';
import Button from '@/Components/UI/Button.vue';
</script>

<template>
    <div class="relative overflow-hidden rounded-2xl border border-purple-500/20 bg-gradient-to-br from-[#8B5CF6]/10 via-[#6C5CE7]/5 to-[#06B6D4]/10 dark:from-[#8B5CF6]/20 dark:via-[#111113] dark:to-[#06B6D4]/15 p-5 sm:p-6 shadow-card transition-all">
        <!-- Floating Ambient Glows -->
        <div class="pointer-events-none absolute -top-16 -right-16 h-40 w-40 rounded-full bg-gradient-to-br from-[#8B5CF6]/30 to-[#06B6D4]/30 blur-2xl" />

        <div class="relative z-10 space-y-4">
            <!-- Header with Badge -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <div class="flex h-8 w-8 items-center justify-center rounded-xl bg-gradient-to-tr from-[#8B5CF6] to-[#06B6D4] text-white shadow-sm shadow-purple-500/30">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"/>
                        </svg>
                    </div>
                    <h3 class="font-extrabold text-base tracking-tight text-slate-900 dark:text-white">
                        Wappiyo AI
                    </h3>
                </div>

                <span class="inline-flex items-center rounded-full bg-gradient-to-r from-purple-500/15 to-cyan-500/15 px-2.5 py-0.5 text-[10px] font-bold uppercase tracking-wider text-[#6C5CE7] dark:text-purple-300 ring-1 ring-inset ring-purple-500/20">
                    Intelligence
                </span>
            </div>

            <p class="text-xs text-slate-600 dark:text-zinc-300 leading-relaxed">
                {{ $t('Supercharge WhatsApp customer interactions with AI-generated smart replies, conversation summaries, and high-converting broadcast templates.') }}
            </p>

            <ul class="space-y-1.5 text-xs text-slate-500 dark:text-zinc-400">
                <li class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#8B5CF6]"></span>
                    <span>{{ $t('Instant contextual reply suggestions') }}</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#6C5CE7]"></span>
                    <span>{{ $t('Automated chat classification & routing') }}</span>
                </li>
                <li class="flex items-center gap-2">
                    <span class="h-1.5 w-1.5 rounded-full bg-[#06B6D4]"></span>
                    <span>{{ $t('Copywriting optimization for templates') }}</span>
                </li>
            </ul>

            <div class="pt-2 flex items-center gap-2">
                <Link href="/templates/create" class="flex-1">
                    <Button variant="ai" size="sm" class="w-full justify-center">
                        <template #icon>
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m12 3-1.9 5.8a2 2 0 0 1-1.3 1.3L3 12l5.8 1.9a2 2 0 0 1 1.3 1.3L12 21l1.9-5.8a2 2 0 0 1 1.3-1.3L21 12l-5.8-1.9a2 2 0 0 1-1.3-1.3Z"/></svg>
                        </template>
                        <span>{{ $t('Create AI Template') }}</span>
                    </Button>
                </Link>
            </div>
        </div>
    </div>
</template>
