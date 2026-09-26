<template>
    <AppLayout>
        <div class="h-full flex flex-col overflow-hidden bg-slate-50/70 dark:bg-[#09090B] transition-colors">
            <!-- Top Sticky Navigation Bar -->
            <div class="sticky top-0 z-20 px-4 sm:px-6 lg:px-8 py-3.5 glass-header border-b border-slate-200/80 dark:border-zinc-800 flex items-center justify-between shrink-0">
                <div class="flex items-center gap-3">
                    <Link
                        href="/campaigns"
                        class="p-1.5 -ml-1 text-slate-500 hover:text-slate-800 dark:hover:text-zinc-200 rounded-lg hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors"
                        title="Back to campaigns"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                    </Link>
                    <div>
                        <h1 class="text-base font-bold text-slate-900 dark:text-white">
                            {{ $t('Create WhatsApp Broadcast Campaign') }}
                        </h1>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">
                            {{ $t('Build message, bind customer variables, select audience, and schedule launch.') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <Link href="/campaigns">
                        <Button variant="secondary" size="xs">
                            {{ $t('Cancel') }}
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- Campaign Builder Form -->
            <div class="flex-1 overflow-hidden">
                <CampaignForm
                    :templates="templates || []"
                    :contactGroups="contactGroups || []"
                    :settings="settings"
                    :isCampaignFlow="true"
                />
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import CampaignForm from '@/Components/CampaignForm.vue';
import Button from '@/Components/UI/Button.vue';
import { Link } from "@inertiajs/vue3";

const props = defineProps(['templates', 'contactGroups', 'settings', 'title']);
</script>