<template>
    <!-- Processing Overlay -->
    <div v-if="isPaymentLoading" class="fixed inset-0 bg-black/60 backdrop-blur-xs flex items-center justify-center z-50">
        <div class="bg-white dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 p-8 rounded-2xl shadow-2xl text-center max-w-sm mx-4">
            <div class="w-16 h-16 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 text-[#6C5CE7] dark:text-indigo-400 mx-auto flex items-center justify-center mb-4">
                <svg class="animate-spin w-8 h-8" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </div>
            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">{{ $t('Processing Transaction') }}</h3>
            <p class="text-xs text-slate-500 dark:text-zinc-400 mb-5 leading-relaxed">{{ $t('Please wait while your payment confirmation is being verified...') }}</p>
            <Link href="/billing" class="text-xs font-semibold text-[#6C5CE7] hover:underline">{{ $t('Return to Billing') }}</Link>
        </div>
    </div>

    <AppLayout>
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200/80 dark:border-zinc-800 pb-6">
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white flex items-center gap-3">
                        <span>{{ $t('Billing & Subscriptions') }}</span>
                        <span 
                            class="px-2.5 py-0.5 rounded-full text-xs font-semibold uppercase tracking-wider"
                            :class="subscriptionIsActive 
                                ? 'bg-emerald-50 dark:bg-emerald-950/40 text-emerald-700 dark:text-emerald-400 border border-emerald-200/60 dark:border-emerald-800/60' 
                                : 'bg-rose-50 dark:bg-rose-950/40 text-rose-700 dark:text-rose-400 border border-rose-200/60 dark:border-rose-800/60'"
                        >
                            {{ subscriptionIsActive ? $t('Active') : $t('Action Required') }}
                        </span>
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-zinc-400 mt-1">
                        {{ $t('Manage your SaaS subscription plan, custom balance top-ups, and transaction history.') }}
                    </p>
                </div>

                <div class="flex items-center gap-3">
                    <button 
                        v-if="props.setting && props.setting['enable_custom_payment'] == 1" 
                        @click="openModal()" 
                        type="button" 
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-slate-700 dark:text-zinc-200 bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 hover:bg-slate-50 dark:hover:bg-zinc-700/60 shadow-2xs transition-all"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                            <line x1="2" y1="10" x2="22" y2="10"></line>
                        </svg>
                        <span>{{ $t('Add Payment') }}</span>
                    </button>

                    <Link 
                        href="/subscription" 
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] hover:from-[#5b4bc4] hover:to-[#7c4deb] shadow-sm shadow-indigo-500/20 active:scale-[0.98] transition-all"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                        </svg>
                        <span>{{ props.subscription?.status === 'trial' ? $t('Subscribe to Plan') : $t('Change Plan') }}</span>
                    </Link>
                </div>
            </div>

            <!-- Inactive / Expired Notice Banner -->
            <div v-if="!subscriptionIsActive" class="rounded-2xl p-5 bg-gradient-to-r from-rose-500 to-rose-600 text-white shadow-lg shadow-rose-500/20 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"></circle>
                            <line x1="12" y1="8" x2="12" y2="12"></line>
                            <line x1="12" y1="16" x2="12.01" y2="16"></line>
                        </svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-base leading-snug">
                            {{ props.subscription?.status === 'trial' ? $t('Your free trial period has concluded') : $t('Subscription payment renewal required') }}
                        </h4>
                        <p class="text-xs text-white/90 mt-0.5 leading-relaxed">
                            {{ props.subscription?.status === 'trial' 
                                ? $t('Subscribe to a plan to continue accessing live chats, broadcast campaigns, and automated workflows.') 
                                : $t('We were unable to autorenew your subscription. Please make a payment of :amount to keep services uninterrupted.', { amount: props.subscriptionDetails?.accountBalance || '' }) }}
                        </p>
                    </div>
                </div>
                <div class="shrink-0 flex items-center gap-2">
                    <Link 
                        v-if="props.subscription?.status === 'trial'" 
                        href="/subscription" 
                        class="px-4 py-2 rounded-xl bg-white text-rose-700 hover:bg-rose-50 text-xs font-bold shadow-xs transition-all"
                    >
                        {{ $t('Choose Plan') }}
                    </Link>
                    <button 
                        v-if="props.subscription?.status === 'active' && props.setting && props.setting['enable_custom_payment'] == 1" 
                        @click="openModal()" 
                        type="button" 
                        class="px-4 py-2 rounded-xl bg-white text-rose-700 hover:bg-rose-50 text-xs font-bold shadow-xs transition-all"
                    >
                        {{ $t('Make Payment') }}
                    </button>
                </div>
            </div>

            <!-- Subscription Plan & Usage Summary Card -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Current Plan Card -->
                <div class="lg:col-span-2 bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ $t('Current Subscription') }}</span>
                            <span 
                                class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-xs font-semibold capitalize"
                                :class="props.subscription?.status === 'active' 
                                    ? 'bg-purple-50 dark:bg-purple-950/40 text-[#6C5CE7] dark:text-purple-400 border border-purple-200/60 dark:border-purple-800/60' 
                                    : 'bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 border border-amber-200/60 dark:border-amber-800/60'"
                            >
                                <span class="w-1.5 h-1.5 rounded-full" :class="props.subscription?.status === 'active' ? 'bg-[#6C5CE7]' : 'bg-amber-500'"></span>
                                {{ props.subscription?.status === 'trial' ? $t('Trial Mode') : (props.subscription?.status || $t('Active')) }}
                            </span>
                        </div>

                        <div class="flex flex-col sm:flex-row sm:items-baseline gap-2 mb-6">
                            <h2 class="text-3xl font-extrabold text-slate-900 dark:text-white">
                                {{ props.subscription?.status === 'trial' ? $t('Free Trial') : (props.subscription?.plan?.name || $t('Standard Plan')) }}
                            </h2>
                            <span v-if="props.subscription?.plan?.price" class="text-slate-500 dark:text-zinc-400 text-sm">
                                • {{ props.subscription?.plan?.price }} / {{ props.subscription?.plan?.period || 'month' }}
                            </span>
                        </div>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-4 pt-4 border-t border-slate-100 dark:border-zinc-800">
                            <div>
                                <span class="text-xs text-slate-400 dark:text-zinc-500 block">{{ $t('Billing Cycle Start') }}</span>
                                <span class="text-sm font-semibold text-slate-800 dark:text-zinc-200 mt-0.5 block">
                                    {{ props.subscription?.start_date || '—' }}
                                </span>
                            </div>
                            <div>
                                <span class="text-xs text-slate-400 dark:text-zinc-500 block">
                                    {{ props.subscription?.status === 'trial' ? $t('Trial Expiration') : $t('Next Renewal Date') }}
                                </span>
                                <span class="text-sm font-semibold text-slate-800 dark:text-zinc-200 mt-0.5 block">
                                    {{ props.subscription?.valid_until || '—' }}
                                </span>
                            </div>
                            <div class="col-span-2 sm:col-span-1">
                                <span class="text-xs text-slate-400 dark:text-zinc-500 block">{{ $t('Account Balance') }}</span>
                                <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400 mt-0.5 block">
                                    {{ props.subscriptionDetails?.accountBalance || '0.00' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 mt-6 border-t border-slate-100 dark:border-zinc-800 flex items-center justify-between">
                        <span class="text-xs text-slate-400 dark:text-zinc-500">
                            {{ $t('Need higher message volume or extra seats?') }}
                        </span>
                        <Link href="/subscription" class="text-xs font-bold text-[#6C5CE7] hover:text-[#5b4bc4] dark:text-indigo-400 flex items-center gap-1">
                            <span>{{ $t('View Plan Comparison') }}</span>
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"></polyline></svg>
                        </Link>
                    </div>
                </div>

                <!-- Payment Methods & Security Card -->
                <div class="bg-gradient-to-br from-slate-900 to-zinc-950 text-white border border-zinc-800 rounded-2xl p-6 shadow-sm flex flex-col justify-between">
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-xs font-bold uppercase tracking-wider text-zinc-400">{{ $t('Payment Gateway') }}</span>
                            <div class="w-6 h-6 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            </div>
                        </div>
                        <h3 class="text-lg font-bold mb-2">{{ $t('Secure SaaS Transactions') }}</h3>
                        <p class="text-xs text-zinc-400 leading-relaxed">
                            {{ $t('All transactions are encrypted with bank-level 256-bit SSL security through trusted payment providers.') }}
                        </p>

                        <div class="mt-6 flex flex-wrap gap-2">
                            <div v-for="(method, idx) in props.methods" :key="idx" class="px-2.5 py-1 rounded-lg bg-zinc-800/80 border border-zinc-700/60 text-[11px] font-medium text-zinc-300 capitalize">
                                {{ method.name }}
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 mt-6 border-t border-zinc-800/80">
                        <button 
                            v-if="props.setting && props.setting['enable_custom_payment'] == 1"
                            @click="openModal()" 
                            type="button"
                            class="w-full py-2.5 px-4 rounded-xl text-xs font-bold text-center bg-white text-zinc-900 hover:bg-zinc-100 transition-colors shadow-sm"
                        >
                            {{ $t('Add Funds / Custom Payment') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Invoices & Transaction History -->
            <div class="bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 rounded-2xl p-6 shadow-sm space-y-6">
                <div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $t('Billing History & Invoices') }}</h3>
                    <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">
                        {{ $t('Inspect past payments, renewal records, and downloadable account invoices.') }}
                    </p>
                </div>

                <BillingTable :rows="props.rows" :filters="props.filters"/>
            </div>
        </div>

        <!-- Add Payment Modal -->
        <Modal :label="label" :isOpen="isOpenModal">
            <form @submit.prevent="submitForm()" class="space-y-4">
                <div class="border-b border-slate-100 dark:border-zinc-800 pb-3">
                    <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $t('Add Payment') }}</h2>
                    <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">{{ $t('Specify custom amount and select your preferred payment gateway.') }}</p>
                </div>

                <div class="space-y-3">
                    <FormInput 
                        v-model="form.amount" 
                        :error="form.errors.amount" 
                        :name="$t('Amount to Pay')" 
                        type="number" 
                        class="w-full"
                        :placeholder="$t('e.g. 50.00')"
                    />

                    <div>
                        <label class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400 block mb-2">{{ $t('Select Payment Gateway') }}</label>
                        <div class="grid grid-cols-2 gap-2.5">
                            <div 
                                v-for="(item, index) in props.methods" 
                                :key="index"
                                @click="selectPayment(item.name)"
                                class="flex items-center gap-3 p-3 rounded-xl border-2 cursor-pointer transition-all"
                                :class="form.method === item.name 
                                    ? 'border-[#6C5CE7] bg-indigo-50/50 dark:bg-indigo-950/20' 
                                    : 'border-slate-200 dark:border-zinc-800 hover:border-slate-300 dark:hover:border-zinc-700 bg-white dark:bg-zinc-900'"
                            >
                                <div 
                                    class="w-4 h-4 rounded-full border flex items-center justify-center shrink-0 transition-colors"
                                    :class="form.method === item.name 
                                        ? 'border-[#6C5CE7] bg-[#6C5CE7] text-white' 
                                        : 'border-slate-300 dark:border-zinc-700'"
                                >
                                    <svg v-if="form.method === item.name" class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                </div>
                                <span class="text-xs font-semibold text-slate-800 dark:text-zinc-200 capitalize">{{ item.name }}</span>
                            </div>
                        </div>
                        <div v-if="form.errors.method" class="text-rose-500 text-xs mt-1.5 font-medium">{{ form.errors.method }}</div>
                    </div>
                </div>

                <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-zinc-800">
                    <button 
                        type="button" 
                        @click="onClose" 
                        class="px-4 py-2 text-xs font-medium text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-xl transition-colors"
                    >
                        {{ $t('Cancel') }}
                    </button>
                    <button 
                        type="submit"
                        :disabled="isLoading || !form.amount || !form.method"
                        class="inline-flex items-center gap-2 px-5 py-2 text-xs font-semibold text-white bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] hover:from-[#5b4bc4] hover:to-[#7c4deb] rounded-xl shadow-sm shadow-indigo-500/20 active:scale-[0.98] transition-all disabled:opacity-50"
                    >
                        <svg v-if="isLoading" class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ isLoading ? $t('Redirecting...') : $t('Proceed to Checkout') }}</span>
                    </button>
                </div>
            </form>
        </Modal>
    </AppLayout>
</template>

<script setup>
    import AppLayout from "./../Layout/App.vue";
    import BillingTable from '@/Components/Tables/BillingTable.vue';
    import { Link, useForm, router } from "@inertiajs/vue3";
    import Modal from '@/Components/Modal.vue';
    import FormInput from '@/Components/FormInput.vue';
    import { ref, onMounted } from 'vue';
    import Echo from 'laravel-echo';
    import Pusher from 'pusher-js';

    const props = defineProps([
        'subscription',
        'rows', 
        'filters', 
        'subscriptionIsActive', 
        'subscriptionDetails', 
        'methods', 
        'isPaymentLoading',
        'pusherSettings',
        'organizationId',
        'setting'
    ]);

    const isOpenModal = ref(false);
    const isLoading = ref(false);
    const label = ref('Add Payment');
    
    const form = useForm({
        'amount': null,
        'method': null
    });

    const selectPayment = (method) => {
        form.method = method;
    };

    function openModal() {
        isOpenModal.value = true;
    }

    function onClose() {
        isOpenModal.value = false;
    }

    const submitForm = async () => {
        isLoading.value = true;
        form.post('/pay', {
            preserveScroll: true,
            onFinish: () => { 
                isLoading.value = false;
            },
        });
    };

    onMounted(() => {
        if (props.pusherSettings?.['pusher_app_key'] && props.pusherSettings?.['pusher_app_cluster']) {
            window.Pusher = Pusher;

            window.Echo = new Echo({
                broadcaster: 'pusher',
                key: props.pusherSettings['pusher_app_key'],
                cluster: props.pusherSettings['pusher_app_cluster'],
                encrypted: true,
            });

            window.Echo.channel('payments.ch' + props.organizationId).listen('NewPaymentEvent', () => {
                router.visit('/billing', {});
            });
        }
    });
</script>