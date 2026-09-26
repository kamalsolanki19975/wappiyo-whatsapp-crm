<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-8">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200/80 dark:border-zinc-800 pb-6">
                <div>
                    <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white flex items-center gap-3">
                        <span>{{ $t('Subscription Plans') }}</span>
                    </h1>
                    <p class="text-sm text-slate-500 dark:text-zinc-400 mt-1">
                        {{ $t('Choose the plan that fits your business scale. Upgrade or change your tier at any time.') }}
                    </p>
                </div>

                <div>
                    <Link 
                        href="/billing" 
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-semibold text-slate-700 dark:text-zinc-300 bg-white dark:bg-zinc-800 border border-slate-200 dark:border-zinc-700 hover:bg-slate-50 dark:hover:bg-zinc-700/60 transition-all shadow-2xs"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"></polyline></svg>
                        <span>{{ $t('Back to Billing') }}</span>
                    </Link>
                </div>
            </div>

            <!-- Two-Column Layout: Plans Grid (Left) + Order Summary & Payment (Right) -->
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                <!-- Left: Plans Grid -->
                <div class="lg:col-span-8 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div 
                            v-for="(item, index) in props.plans?.data" 
                            :key="index" 
                            @click="selectPlan(item.id, item.name, item.period, item.price)"
                            class="relative flex flex-col justify-between p-6 rounded-2xl border-2 cursor-pointer transition-all duration-200 bg-white dark:bg-zinc-900 shadow-sm hover:shadow-md"
                            :class="form.plan === item.id 
                                ? 'border-[#6C5CE7] ring-4 ring-[#6C5CE7]/10' 
                                : 'border-slate-200/80 dark:border-zinc-800 hover:border-slate-300 dark:hover:border-zinc-700'"
                        >
                            <!-- Plan Header -->
                            <div>
                                <div class="flex items-center justify-between mb-3">
                                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ item.name }}</h3>
                                    <div 
                                        class="w-5 h-5 rounded-full border-2 flex items-center justify-center transition-colors"
                                        :class="form.plan === item.id 
                                            ? 'border-[#6C5CE7] bg-[#6C5CE7] text-white' 
                                            : 'border-slate-300 dark:border-zinc-700'"
                                    >
                                        <svg v-if="form.plan === item.id" class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Pricing -->
                                <div class="flex items-baseline gap-1 mb-5">
                                    <span class="text-3xl font-extrabold text-slate-900 dark:text-white">{{ item.price }}</span>
                                    <span class="text-xs font-medium text-slate-500 dark:text-zinc-400 capitalize">/ {{ item.period }}</span>
                                </div>

                                <!-- Feature List -->
                                <div class="space-y-2.5 pt-4 border-t border-slate-100 dark:border-zinc-800">
                                    <div class="flex items-center gap-2 text-xs text-slate-700 dark:text-zinc-300">
                                        <div class="w-4 h-4 rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </div>
                                        <span>
                                            <strong class="font-bold text-slate-900 dark:text-white">
                                                {{ getDetail(item?.metadata, 'campaign_limit') == '-1' ? $t('Unlimited') : getDetail(item?.metadata, 'campaign_limit') }}
                                            </strong> {{ $t('Campaigns') }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2 text-xs text-slate-700 dark:text-zinc-300">
                                        <div class="w-4 h-4 rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </div>
                                        <span>
                                            <strong class="font-bold text-slate-900 dark:text-white">
                                                {{ getDetail(item?.metadata, 'message_limit') == '-1' ? $t('Unlimited') : getDetail(item?.metadata, 'message_limit') }}
                                            </strong> {{ $t('Messages') }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2 text-xs text-slate-700 dark:text-zinc-300">
                                        <div class="w-4 h-4 rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </div>
                                        <span>
                                            <strong class="font-bold text-slate-900 dark:text-white">
                                                {{ getDetail(item?.metadata, 'contacts_limit') == '-1' ? $t('Unlimited') : getDetail(item?.metadata, 'contacts_limit') }}
                                            </strong> {{ $t('Contacts') }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2 text-xs text-slate-700 dark:text-zinc-300">
                                        <div class="w-4 h-4 rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </div>
                                        <span>
                                            <strong class="font-bold text-slate-900 dark:text-white">
                                                {{ getDetail(item?.metadata, 'canned_replies_limit') == '-1' ? $t('Unlimited') : getDetail(item?.metadata, 'canned_replies_limit') }}
                                            </strong> {{ $t('Canned replies') }}
                                        </span>
                                    </div>

                                    <div class="flex items-center gap-2 text-xs text-slate-700 dark:text-zinc-300">
                                        <div class="w-4 h-4 rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-2.5 h-2.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg>
                                        </div>
                                        <span>
                                            <strong class="font-bold text-slate-900 dark:text-white">
                                                {{ getDetail(item?.metadata, 'team_limit') == '-1' ? $t('Unlimited') : getDetail(item?.metadata, 'team_limit') }}
                                            </strong> {{ $t('User Seats') }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-6 mt-6 border-t border-slate-100 dark:border-zinc-800">
                                <button 
                                    type="button" 
                                    class="w-full py-2 px-3 rounded-xl text-xs font-semibold text-center transition-all"
                                    :class="form.plan === item.id 
                                        ? 'bg-[#6C5CE7] text-white shadow-sm shadow-indigo-500/20' 
                                        : 'bg-slate-100 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 hover:bg-slate-200 dark:hover:bg-zinc-700'"
                                >
                                    {{ form.plan === item.id ? $t('Selected Plan') : $t('Select This Plan') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Order Summary & Checkout -->
                <div class="lg:col-span-4 sticky top-6">
                    <div class="bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 rounded-2xl p-6 shadow-sm space-y-5">
                        <div class="border-b border-slate-100 dark:border-zinc-800 pb-3">
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ $t('Subscription Summary') }}</h3>
                            <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">{{ $t('Review your selection and calculate applicable charges.') }}</p>
                        </div>

                        <!-- Empty State if no plan chosen -->
                        <div v-if="!form.plan" class="p-8 border-2 border-dashed border-slate-200 dark:border-zinc-800 rounded-xl text-center">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-8 h-8 text-slate-400 dark:text-zinc-500 mx-auto mb-2" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                                <circle cx="12" cy="12" r="10"></circle>
                                <polyline points="12 6 12 12 16 14"></polyline>
                            </svg>
                            <p class="text-xs font-semibold text-slate-600 dark:text-zinc-400">{{ $t('Select a plan to preview invoice calculation') }}</p>
                        </div>

                        <!-- Selected Plan Calculations -->
                        <div v-else class="space-y-4">
                            <!-- Plan Name & Base Price -->
                            <div class="flex items-center justify-between pb-3 border-b border-slate-100 dark:border-zinc-800">
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900 dark:text-white">{{ selectedPlan.name }}</h4>
                                    <span class="inline-flex px-2 py-0.5 rounded-md text-[10px] font-semibold bg-indigo-50 dark:bg-indigo-950/50 text-[#6C5CE7] dark:text-indigo-400 capitalize">
                                        {{ selectedPlan.period }}
                                    </span>
                                </div>
                                <span class="text-sm font-bold text-slate-900 dark:text-white">{{ basePrice || selectedPlan.amount }}</span>
                            </div>

                            <!-- Line items: Gross -->
                            <div class="flex justify-between text-xs text-slate-600 dark:text-zinc-400">
                                <span>{{ $t('Gross Total') }}</span>
                                <span class="font-medium text-slate-900 dark:text-white">{{ grossAmount }}</span>
                            </div>

                            <!-- Tax Breakdown -->
                            <div v-if="taxRates && taxRates.length > 0" class="p-3 bg-slate-50 dark:bg-zinc-800/60 rounded-xl space-y-2 border border-slate-200/60 dark:border-zinc-700/60">
                                <div class="text-[11px] font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">{{ $t('Taxes & Levies') }}</div>
                                <div v-for="(item, index) in taxRates" :key="index" class="flex justify-between text-xs">
                                    <span class="text-slate-700 dark:text-zinc-300">{{ item.name }} ({{ item.percentage }}%)</span>
                                    <span class="font-medium text-slate-900 dark:text-white">{{ item.amount }}</span>
                                </div>
                            </div>

                            <!-- Applicable Credits -->
                            <div v-if="credit && credit.total > 0" class="flex justify-between text-xs text-emerald-600 dark:text-emerald-400 font-medium">
                                <span>{{ $t('Applied Credits') }}</span>
                                <span>- {{ credit.total }}</span>
                            </div>

                            <!-- Debits Due -->
                            <div v-if="debit && debit.total > 0" class="flex justify-between text-xs text-rose-600 dark:text-rose-400 font-medium">
                                <span>{{ $t('Outstanding Debits') }}</span>
                                <span>+ {{ debit.total }}</span>
                            </div>

                            <!-- Total Due -->
                            <div class="pt-3 border-t border-slate-200/80 dark:border-zinc-800 flex items-baseline justify-between">
                                <span class="text-sm font-bold text-slate-900 dark:text-white">{{ $t('Total Due') }}</span>
                                <span class="text-2xl font-extrabold text-[#6C5CE7] dark:text-indigo-400">{{ amountDue }}</span>
                            </div>

                            <!-- Payment Gateways (if amountDue > 0) -->
                            <div v-if="parseInt(amountDue) > 0" class="pt-2 space-y-2">
                                <label class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400 block">{{ $t('Payment Gateway') }}</label>
                                <div class="grid grid-cols-2 gap-2">
                                    <div 
                                        v-for="(item, index) in props.methods" 
                                        :key="index"
                                        @click="selectPayment(item.name)"
                                        class="flex items-center gap-2.5 p-2.5 rounded-xl border-2 cursor-pointer transition-all"
                                        :class="form.method === item.name 
                                            ? 'border-[#6C5CE7] bg-indigo-50/50 dark:bg-indigo-950/20' 
                                            : 'border-slate-200 dark:border-zinc-800 hover:border-slate-300 dark:hover:border-zinc-700 bg-white dark:bg-zinc-900'"
                                    >
                                        <div 
                                            class="w-3.5 h-3.5 rounded-full border flex items-center justify-center shrink-0 transition-colors"
                                            :class="form.method === item.name 
                                                ? 'border-[#6C5CE7] bg-[#6C5CE7] text-white' 
                                                : 'border-slate-300 dark:border-zinc-700'"
                                        >
                                            <svg v-if="form.method === item.name" class="w-2 h-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <span class="text-xs font-semibold text-slate-800 dark:text-zinc-200 capitalize truncate">{{ item.name }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit CTA Button -->
                            <div class="pt-3">
                                <button 
                                    v-if="(!buttonLoading && form.method != null) || (!buttonLoading && amountDue <= 0)" 
                                    @click="submitForm()" 
                                    type="button" 
                                    class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] hover:from-[#5b4bc4] hover:to-[#7c4deb] shadow-md shadow-indigo-500/20 active:scale-[0.98] transition-all"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                                        <line x1="2" y1="10" x2="22" y2="10"></line>
                                    </svg>
                                    <span>{{ $t('Confirm & Subscribe') }}</span>
                                </button>

                                <button 
                                    v-else-if="buttonLoading"
                                    type="button"
                                    disabled
                                    class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-bold text-white bg-indigo-400 cursor-not-allowed opacity-80"
                                >
                                    <svg class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>{{ $t('Redirecting to Checkout...') }}</span>
                                </button>

                                <button 
                                    v-else 
                                    type="button" 
                                    disabled
                                    class="w-full py-3 px-4 rounded-xl text-xs font-bold text-center bg-slate-100 dark:bg-zinc-800 text-slate-400 dark:text-zinc-600 cursor-not-allowed"
                                >
                                    {{ !form.plan ? $t('Select a plan above') : $t('Select a payment method') }}
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
    import AppLayout from "./../Layout/App.vue";
    import { router, useForm, Link } from "@inertiajs/vue3";
    import { ref } from 'vue';

    const props = defineProps({ plans: Object, methods: Object, subscription: Object, subscriptionDetails: Object });

    const form = useForm({
        'plan': props.subscription?.plan_id,
        'method': null,
    });

    const buttonLoading = ref(false);

    const selectedPlan = ref({
        name: props.subscription?.plan?.name,
        period: props.subscription?.plan?.period,
        amount: props.subscription?.plan?.price,
    });

    const grossAmount = ref(props.subscriptionDetails?.grossAmount);
    const netAmount = ref(props.subscriptionDetails?.netAmount);
    const amountDue = ref(props.subscriptionDetails?.amountDue);
    const taxRates = ref(props.subscriptionDetails?.taxRates);
    const credit = ref(props.subscriptionDetails?.credit);
    const debit = ref(props.subscriptionDetails?.debit);
    const basePrice = ref(props.subscriptionDetails?.basePrice);

    const selectPlan = (plan, name, period, amount) => {
        form.plan = plan;
        selectedPlan.value = { name, period, amount };
        getPlanDetails(plan);
    };

    const selectPayment = (method) => {
        form.method = method;
    };

    const getDetail = (value, key) => {
        if (value) {
            try {
                const item = JSON.parse(value);
                return item?.[key] ?? null;
            } catch (_) {
                return null;
            }
        }
        return null;
    };

    const getPlanDetails = (planId) => {
        router.visit('/subscription/' + planId, {
            method: 'get',
            preserveState: true,
            onSuccess: (response) => {
                form.plan = planId;
                const res = response.props.response_data?.data || {};
                grossAmount.value = res.grossAmount;
                taxRates.value = res.taxRates;
                netAmount.value = res.netAmount;
                credit.value = res.credit;
                debit.value = res.debit;
                basePrice.value = res.basePrice;
                amountDue.value = res.amountDue;
            },
        });
    };

    const submitForm = async () => {
        buttonLoading.value = true;
        form.post('/subscription', {
            preserveScroll: true,
        });
    };
</script>