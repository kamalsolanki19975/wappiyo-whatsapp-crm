<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import Badge from '@/Components/UI/Badge.vue';
import Button from '@/Components/UI/Button.vue';

const props = defineProps({
    subscription: {
        type: Object,
        default: () => ({}),
    },
    subscriptionIsActive: {
        type: Boolean,
        default: true,
    },
    subscriptionDetails: {
        type: Object,
        default: () => ({}),
    },
    isOwner: {
        type: Boolean,
        default: false,
    },
});

const isTrial = computed(() => props.subscription?.status === 'trial');
const planName = computed(() => props.subscription?.plan?.name || (isTrial.value ? 'Free Trial' : 'Professional Plan'));
const expiryDate = computed(() => props.subscription?.valid_until || '—');
</script>

<template>
    <div
        class="relative overflow-hidden rounded-2xl border p-5 sm:p-6 shadow-card transition-all"
        :class="!subscriptionIsActive
            ? 'border-rose-300 dark:border-rose-900/60 bg-rose-50/70 dark:bg-rose-950/30 text-rose-950 dark:text-rose-100'
            : 'border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113]'"
    >
        <!-- Header -->
        <div class="flex items-center justify-between border-b pb-4" :class="!subscriptionIsActive ? 'border-rose-200/60 dark:border-rose-900/50' : 'border-slate-100 dark:border-zinc-800/80'">
            <div class="flex items-center gap-2.5">
                <div
                    class="flex h-10 w-10 items-center justify-center rounded-xl"
                    :class="!subscriptionIsActive
                        ? 'bg-rose-100 dark:bg-rose-900/60 text-rose-600 dark:text-rose-300'
                        : 'bg-purple-50 dark:bg-purple-950/40 text-[#6C5CE7]'"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <rect width="20" height="14" x="2" y="5" rx="2"/>
                        <line x1="2" y1="10" x2="22" y2="10"/>
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold" :class="!subscriptionIsActive ? 'text-rose-950 dark:text-rose-100' : 'text-slate-900 dark:text-white'">
                        {{ planName }}
                    </h3>
                    <p class="text-xs" :class="!subscriptionIsActive ? 'text-rose-700 dark:text-rose-300' : 'text-slate-400 dark:text-zinc-500'">
                        {{ isTrial ? $t('Trial Period') : $t('Subscription Plan') }}
                    </p>
                </div>
            </div>

            <Badge
                :variant="!subscriptionIsActive ? 'danger' : isTrial ? 'warning' : 'success'"
                size="sm"
                dot
            >
                {{ !subscriptionIsActive ? $t('Expired') : isTrial ? $t('Trial') : $t('Active') }}
            </Badge>
        </div>

        <!-- Body -->
        <div class="mt-4 space-y-3">
            <!-- Inactive Warning -->
            <div v-if="!subscriptionIsActive" class="rounded-xl bg-white/80 dark:bg-zinc-900/80 p-3.5 border border-rose-200 dark:border-rose-900/60 text-xs">
                <p v-if="isTrial" class="font-semibold text-rose-700 dark:text-rose-300">
                    {{ $t('Your trial period is over. Please subscribe to continue using your WhatsApp CRM.') }}
                </p>
                <p v-else class="font-semibold text-rose-700 dark:text-rose-300">
                    {{ $t('We were unable to autorenew your subscription.') }}
                    <span v-if="subscriptionDetails?.accountBalance">
                        {{ $t('Outstanding amount:') }} {{ subscriptionDetails.accountBalance }}
                    </span>
                </p>
            </div>

            <!-- Active details -->
            <div v-else class="flex items-center justify-between text-xs py-1">
                <span class="text-slate-500 dark:text-zinc-400">
                    {{ isTrial ? $t('Trial expires on') : $t('Renewal date') }}
                </span>
                <span class="font-semibold text-slate-800 dark:text-zinc-200">
                    {{ expiryDate }}
                </span>
            </div>

            <!-- Actions -->
            <div class="pt-2 flex items-center justify-between">
                <template v-if="isOwner">
                    <Link v-if="isTrial" href="/subscription">
                        <Button :variant="!subscriptionIsActive ? 'danger' : 'primary'" size="sm">
                            {{ $t('Upgrade Plan') }}
                        </Button>
                    </Link>

                    <Link v-else-if="!subscriptionIsActive" href="/billing">
                        <Button variant="danger" size="sm">
                            {{ $t('Make Payment') }}
                        </Button>
                    </Link>

                    <Link v-else href="/billing">
                        <Button variant="secondary" size="sm">
                            {{ $t('Billing & Invoices') }}
                        </Button>
                    </Link>
                </template>

                <Link
                    v-if="subscriptionIsActive"
                    href="/subscription"
                    class="text-xs font-semibold text-[#6C5CE7] dark:text-purple-400 hover:underline"
                >
                    {{ $t('View plan details →') }}
                </Link>
            </div>
        </div>
    </div>
</template>
