<script setup>
import { ref } from 'vue';
import AppLayout from "./../Layout/App.vue";
import { router, Link } from '@inertiajs/vue3';
import TemplateTable from '@/Components/Tables/TemplateTable.vue';
import Button from '@/Components/UI/Button.vue';
import axios from 'axios';
import { toast } from 'vue3-toastify';
import 'vue3-toastify/dist/index.css';

const props = defineProps({
    title: String,
    allowCreate: Boolean,
    rows: Object,
    stats: Object,
    filters: Object,
});

const isSyncActive = ref(false);

const syncTemplates = () => {
    isSyncActive.value = true;
    axios.get('/templates/sync')
        .then(function (response) {
            router.reload({
                only: ['rows', 'stats'],
                onSuccess: () => {
                    toast.success('WhatsApp templates synchronized with Meta Cloud API', {
                        autoClose: 3000,
                    });
                },
                onFinish: () => {
                    setTimeout(() => {
                        isSyncActive.value = false;
                    }, 800);
                }
            });
        })
        .catch(function (error) {
            isSyncActive.value = false;
            const message = error.response?.data?.message || 'Failed to synchronize with WhatsApp Cloud API';
            toast.error(message, {
                autoClose: 4000,
            });
        });
};
</script>

<template>
    <AppLayout>
        <div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto space-y-6">
            <!-- PAGE HEADER -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 pb-2 border-b border-slate-200/80 dark:border-zinc-800/80">
                <div class="space-y-1">
                    <div class="flex items-center gap-2.5">
                        <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-900 dark:text-white">
                            {{ $t('Message Templates') }}
                        </h1>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold tracking-wide uppercase bg-gradient-to-r from-purple-500/10 to-cyan-500/10 text-[#6C5CE7] dark:text-purple-300 ring-1 ring-inset ring-purple-500/20">
                            {{ $t('Meta Cloud API') }}
                        </span>
                    </div>
                    <p class="text-xs sm:text-sm text-slate-500 dark:text-zinc-400 max-w-2xl leading-relaxed">
                        {{ $t('Build, submit, and manage verified WhatsApp message templates for marketing broadcasts, transactional updates, and quick customer notifications.') }}
                    </p>
                </div>

                <!-- TOP ACTIONS -->
                <div class="flex items-center gap-3 shrink-0">
                    <!-- Sync Button -->
                    <Button
                        type="button"
                        variant="secondary"
                        size="sm"
                        @click="syncTemplates"
                        :disabled="isSyncActive"
                    >
                        <template #icon>
                            <svg 
                                class="w-4 h-4 text-slate-600 dark:text-zinc-300"
                                :class="isSyncActive ? 'animate-spin text-[#6C5CE7]' : ''" 
                                viewBox="0 0 24 24" 
                                fill="none" 
                                stroke="currentColor" 
                                stroke-width="2"
                            >
                                <path d="M21.5 2v6h-6M21.34 15.57a10 10 0 1 1-.57-8.38l5.67-5.67"/>
                            </svg>
                        </template>
                        <span>{{ isSyncActive ? $t('Syncing with Meta...') : $t('Sync Templates') }}</span>
                    </Button>

                    <!-- Create Template Button -->
                    <Link href="/templates/create">
                        <Button
                            type="button"
                            variant="primary"
                            size="sm"
                        >
                            <template #icon>
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                            </template>
                            <span>{{ $t('Create Template') }}</span>
                        </Button>
                    </Link>
                </div>
            </div>

            <!-- MAIN TEMPLATES TABLE & FILTERS COMPONENT -->
            <TemplateTable
                :rows="props.rows"
                :stats="props.stats"
                :filters="props.filters"
                @sync="syncTemplates"
            />
        </div>
    </AppLayout>
</template>