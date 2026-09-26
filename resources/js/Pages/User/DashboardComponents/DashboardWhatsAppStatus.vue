<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import EmbeddedSignupBtn from '@/Components/EmbeddedSignupBtn.vue';
import Button from '@/Components/UI/Button.vue';
import Badge from '@/Components/UI/Badge.vue';

const props = defineProps({
    setupWhatsapp: {
        type: Boolean,
        default: false,
    },
    embeddedSignupActive: {
        type: [Number, String],
        default: 0,
    },
    appId: {
        type: String,
        default: '',
    },
    configId: {
        type: String,
        default: '',
    },
    graphAPIVersion: {
        type: String,
        default: '',
    },
    isOwner: {
        type: Boolean,
        default: false,
    },
});

const isConnected = computed(() => !props.setupWhatsapp);
</script>

<template>
    <div class="relative overflow-hidden rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] p-5 sm:p-6 shadow-card transition-all">
        <!-- Glow accent on hover -->
        <div
            class="pointer-events-none absolute -top-12 -right-12 h-36 w-36 rounded-full blur-2xl transition-opacity"
            :class="isConnected ? 'bg-emerald-500/10 dark:bg-emerald-500/15' : 'bg-amber-500/15 dark:bg-amber-500/20'"
        />

        <div class="relative z-10 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <!-- Left Info -->
            <div class="flex items-start gap-4">
                <div
                    class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl ring-1 shadow-sm transition-transform"
                    :class="isConnected
                        ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 ring-emerald-500/20'
                        : 'bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 ring-amber-500/20'"
                >
                    <svg class="h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor">
                        <path d="M12.042 2C6.556 2 2.084 6.446 2.084 9.911c0 1.739.458 3.447 1.33 4.954l-1.23 4.466a.4.4 0 0 0 .487.494l4.607-1.204a10 10 0 0 0 4.76 1.207h.004c5.486 0 9.958-4.447 9.958-9.912A9.828 9.828 0 0 0 12.042 2Zm4.774 13.968c-.218.6-1.267 1.176-1.742 1.22l-.135.016c-.436.052-.988.12-2.956-.655c-2.426-.954-4.027-3.32-4.35-3.799a2.768 2.768 0 0 0-.053-.076l-.006-.008c-.147-.197-1.048-1.402-1.048-2.646c0-1.19.587-1.81.854-2.092l.047-.05a.95.95 0 0 1 .687-.32c.173 0 .347 0 .495.005c.183.005.386.015.579.443c.128.285.343.81.519 1.238c.137.333.249.607.277.663c.064.128.104.275.02.448l-.028.058a1.43 1.43 0 0 1-.23.37a9.386 9.386 0 0 0-.143.17c-.085.104-.17.206-.242.278c-.129.128-.262.266-.114.522c.149.256.668 1.098 1.435 1.777a6.634 6.634 0 0 0 1.903 1.2c.07.03.127.055.17.076c.257.128.41.108.558-.064c.149-.173.643-.749.817-1.005c.168-.256.34-.216.578-.128c.238.089 1.504.71 1.761.837l.143.07c.179.085.3.144.352.23c.064.109.064.62-.148 1.222Z"/>
                    </svg>
                </div>

                <div>
                    <div class="flex items-center gap-2">
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">
                            {{ $t('WhatsApp Connection') }}
                        </h3>
                        <Badge :variant="isConnected ? 'success' : 'warning'" size="sm" dot>
                            {{ isConnected ? $t('Connected & Active') : $t('Setup Required') }}
                        </Badge>
                    </div>

                    <p class="mt-1 text-xs sm:text-sm text-slate-500 dark:text-zinc-400 max-w-xl">
                        <span v-if="isConnected">
                            {{ $t('Your WhatsApp Cloud API instance is connected. Live chats, webhooks, and broadcasts are operational.') }}
                        </span>
                        <span v-else>
                            {{ $t('Configure your WhatsApp Business Cloud API to start dispatching broadcasts and receiving customer inquiries.') }}
                        </span>
                    </p>
                </div>
            </div>

            <!-- Right Actions -->
            <div class="shrink-0 flex items-center gap-2">
                <template v-if="!isConnected && isOwner">
                    <EmbeddedSignupBtn
                        v-if="embeddedSignupActive == 1"
                        :appId="appId"
                        :configId="configId"
                        :graphAPIVersion="graphAPIVersion"
                    />
                    <Link v-else href="/settings/whatsapp">
                        <Button variant="primary" size="sm">
                            <template #icon>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                                </svg>
                            </template>
                            {{ $t('Connect WhatsApp') }}
                        </Button>
                    </Link>
                </template>

                <template v-else>
                    <Link href="/settings/whatsapp">
                        <Button variant="secondary" size="sm">
                            <span>{{ $t('Manage Connection') }}</span>
                            <template #iconRight>
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-3.5 w-3.5 text-slate-400 group-hover:translate-x-0.5 transition-transform" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                            </template>
                        </Button>
                    </Link>
                </template>
            </div>
        </div>
    </div>
</template>
