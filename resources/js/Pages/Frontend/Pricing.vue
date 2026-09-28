<template>
    <WebsiteLayout
        :companyConfig="companyConfig"
        :languages="languages"
        :currentLanguage="currentLanguage"
        title="Dynamic Subscription Plans & Add-on Pricing"
        description="Transparent WhatsApp SaaS pricing. Choose monthly or yearly billing with generous limits on contacts, messages, and team seats. 14-day free trial."
    >
        <!-- HERO -->
        <section class="relative pt-16 pb-20 sm:pt-24 sm:pb-32 overflow-hidden bg-slate-50/50 dark:bg-[#09090B]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-700 dark:text-emerald-300 mb-6">
                    {{ $t('Transparent & Predictable') }}
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight max-w-4xl mx-auto">
                    <span>{{ $t('Simple, transparent pricing that') }}</span>
                    <span class="block bg-gradient-to-r from-emerald-600 via-teal-500 to-cyan-500 bg-clip-text text-transparent">
                        {{ $t('grows with your business.') }}
                    </span>
                </h1>
                <p class="mt-6 text-base sm:text-lg text-slate-600 dark:text-zinc-400 max-w-2xl mx-auto leading-relaxed">
                    {{ $t('Get started with our 14-day full feature trial. No credit card required, cancel anytime.') }}
                </p>

                <!-- Billing Cycle Toggle -->
                <div class="mt-10 inline-flex items-center p-1 rounded-xl bg-slate-200/80 dark:bg-zinc-800 text-xs font-semibold shadow-inner">
                    <button
                        type="button"
                        @click="billingCycle = 'monthly'"
                        class="px-5 py-2.5 rounded-lg transition-all"
                        :class="billingCycle === 'monthly' ? 'bg-white dark:bg-[#18181B] text-slate-900 dark:text-white shadow-sm' : 'text-slate-600 dark:text-zinc-400'"
                    >
                        {{ $t('Monthly Billing') }}
                    </button>
                    <button
                        type="button"
                        @click="billingCycle = 'yearly'"
                        class="px-5 py-2.5 rounded-lg transition-all flex items-center gap-1.5"
                        :class="billingCycle === 'yearly' ? 'bg-white dark:bg-[#18181B] text-slate-900 dark:text-white shadow-sm' : 'text-slate-600 dark:text-zinc-400'"
                    >
                        <span>{{ $t('Yearly Billing') }}</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-bold bg-emerald-600 text-white">Save ~20%</span>
                    </button>
                </div>
            </div>
        </section>

        <!-- DYNAMIC PRICING CARDS -->
        <section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 -mt-16 relative z-10">
            <!-- If plans exist in database -->
            <div v-if="sortedPlans.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 items-stretch justify-center max-w-6xl mx-auto">
                <div
                    v-for="plan in sortedPlans"
                    :key="plan.uuid || plan.id"
                    class="relative rounded-3xl p-8 flex flex-col justify-between transition-all"
                    :class="isPlanFeatured(plan) 
                        ? 'bg-white dark:bg-[#111113] border-2 border-emerald-500 shadow-2xl shadow-emerald-600/15 ring-4 ring-emerald-500/10' 
                        : 'bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-lg'"
                >
                    <!-- Featured / Recommended Tag -->
                    <div v-if="isPlanFeatured(plan)" class="absolute -top-3.5 left-1/2 -translate-x-1/2">
                        <span class="px-4 py-1 rounded-full text-[11px] font-bold tracking-wider uppercase bg-gradient-to-r from-emerald-600 to-teal-600 text-white shadow-md">
                            {{ $t('Most Popular') }}
                        </span>
                    </div>

                    <div>
                        <!-- Plan Header -->
                        <div class="mb-4">
                            <h3 class="text-2xl font-bold text-slate-900 dark:text-white">{{ plan.name }}</h3>
                            <p class="text-xs text-slate-500 dark:text-zinc-400 mt-2 min-h-[36px] leading-relaxed">
                                {{ getPlanDescription(plan) }}
                            </p>
                        </div>

                        <!-- Price display -->
                        <div class="py-6 border-y border-slate-100 dark:border-zinc-800 mb-6">
                            <div class="flex items-baseline gap-1">
                                <span class="text-5xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                                    {{ formatPlanPrice(plan) }}
                                </span>
                                <span class="text-xs text-slate-500 dark:text-zinc-400">
                                    / {{ billingCycle === 'yearly' ? $t('year') : $t('month') }}
                                </span>
                            </div>
                            <p class="text-[11px] text-slate-400 mt-1">
                                {{ billingCycle === 'yearly' ? $t('Billed annually with bonus discount') : $t('Billed monthly, cancel anytime') }}
                            </p>
                        </div>

                        <!-- Limit Highlights -->
                        <div class="space-y-3 mb-8 text-xs text-slate-700 dark:text-zinc-300">
                            <div class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                                <span>
                                    <strong>{{ plan.contacts_limit || 'Unlimited' }}</strong> {{ $t('Stored Contacts') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                                <span>
                                    <strong>{{ plan.messages_limit || 'Unlimited' }}</strong> {{ $t('Outbound Messages/mo') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                                <span>
                                    <strong>{{ plan.team_limit || 'Unlimited' }}</strong> {{ $t('Team Members / Seats') }}
                                </span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                                <span>{{ $t('Multi-Agent Shared Inbox') }}</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                                <span>{{ $t('Flow Builder Automation Canvas') }}</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                                <span>{{ $t('Official Meta Cloud API Connection') }}</span>
                            </div>
                            <div class="flex items-center gap-2.5">
                                <span class="w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center shrink-0">✓</span>
                                <span>{{ $t('14-Day Full Access Free Trial') }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Select Plan Action -->
                    <Link
                        :href="getPlanSignupUrl(plan)"
                        class="w-full inline-flex items-center justify-center gap-2 py-4 px-6 rounded-xl font-bold text-xs transition-all text-center"
                        :class="isPlanFeatured(plan)
                            ? 'bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white shadow-xl shadow-emerald-600/25'
                            : 'bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 text-slate-900 dark:text-white'"
                    >
                        <span>{{ $t('Select') }} {{ plan.name }}</span>
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m9 18 6-6-6-6"/></svg>
                    </Link>
                </div>
            </div>

            <!-- Fallback if no active plan exists in DB -->
            <div v-else class="text-center p-12 bg-white dark:bg-[#111113] rounded-3xl border border-slate-200 dark:border-zinc-800 max-w-lg mx-auto">
                <div class="w-12 h-12 rounded-xl bg-emerald-100 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center mx-auto mb-4 text-xl">💳</div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">{{ $t('Plans Updating') }}</h3>
                <p class="text-xs text-slate-500 mb-6">{{ $t('Subscription tiers are being updated by administrators. Please reach out to our sales team for custom setup.') }}</p>
                <Link href="/contact" class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold inline-block">
                    {{ $t('Contact Sales') }}
                </Link>
            </div>
        </section>

        <!-- ADD-ONS SHOWCASE -->
        <section v-if="props.addons && props.addons.length" class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 border-t border-slate-200/80 dark:border-zinc-800 mt-16">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-2 block">
                    {{ $t('Power-Ups & Extensions') }}
                </span>
                <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    {{ $t('Available Platform Add-ons') }}
                </h2>
                <p class="mt-3 text-sm text-slate-600 dark:text-zinc-400">
                    {{ $t('Supercharge your plan with modular extensions that can be activated on demand.') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div
                    v-for="addon in props.addons"
                    :key="addon.uuid || addon.id"
                    class="p-6 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 flex flex-col justify-between"
                >
                    <div>
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 flex items-center justify-center font-bold text-sm mb-4">
                            ⚡
                        </div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1">{{ addon.name }}</h4>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 leading-relaxed">{{ addon.description || 'Verified native extension for the Wappiyo ecosystem.' }}</p>
                    </div>
                    <div class="pt-4 border-t border-slate-100 dark:border-zinc-800/80 mt-4 flex items-center justify-between text-xs">
                        <span class="font-semibold text-slate-700 dark:text-zinc-300">
                            {{ addon.status ? $t('Available') : $t('Optional') }}
                        </span>
                        <Link href="/signup" class="font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                            {{ $t('Include &rarr;') }}
                        </Link>
                    </div>
                </div>
            </div>
        </section>

        <!-- DETAILED FEATURE COMPARISON TABLE -->
        <section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 border-t border-slate-200/80 dark:border-zinc-800">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400 mb-2 block">
                    {{ $t('Full Transparency') }}
                </span>
                <h2 class="text-3xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    {{ $t('Compare Plan Capabilities') }}
                </h2>
                <p class="mt-3 text-sm text-slate-600 dark:text-zinc-400">
                    {{ $t('Review all features, limits, and technical capabilities side by side.') }}
                </p>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] shadow-md">
                <table class="w-full text-left text-xs">
                    <thead>
                        <tr class="border-b border-slate-200 dark:border-zinc-800 bg-slate-50 dark:bg-zinc-900/60">
                            <th class="p-4 sm:p-5 font-bold text-slate-900 dark:text-white w-1/3">{{ $t('Feature / Limit') }}</th>
                            <th
                                v-for="plan in sortedPlans"
                                :key="plan.uuid || plan.id"
                                class="p-4 sm:p-5 font-bold text-center text-slate-900 dark:text-white"
                            >
                                {{ plan.name }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-zinc-800 text-slate-600 dark:text-zinc-300">
                        <tr>
                            <td class="p-4 font-semibold text-slate-800 dark:text-white">{{ $t('Monthly Outbound Messages') }}</td>
                            <td v-for="p in sortedPlans" :key="p.id" class="p-4 text-center font-bold text-emerald-600 dark:text-emerald-400">
                                {{ p.messages_limit || 'Unlimited' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="p-4 font-semibold text-slate-800 dark:text-white">{{ $t('Stored Contacts Limit') }}</td>
                            <td v-for="p in sortedPlans" :key="p.id" class="p-4 text-center font-bold text-slate-800 dark:text-white">
                                {{ p.contacts_limit || 'Unlimited' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="p-4 font-semibold text-slate-800 dark:text-white">{{ $t('Team Members / Seats') }}</td>
                            <td v-for="p in sortedPlans" :key="p.id" class="p-4 text-center font-bold text-slate-800 dark:text-white">
                                {{ p.team_limit || 'Unlimited' }}
                            </td>
                        </tr>
                        <tr>
                            <td class="p-4 font-semibold text-slate-800 dark:text-white">{{ $t('Multi-Agent Shared Inbox') }}</td>
                            <td v-for="p in sortedPlans" :key="p.id" class="p-4 text-center text-emerald-500 font-bold">✓ Included</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-semibold text-slate-800 dark:text-white">{{ $t('Visual Flow Builder') }}</td>
                            <td v-for="p in sortedPlans" :key="p.id" class="p-4 text-center text-emerald-500 font-bold">✓ Included</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-semibold text-slate-800 dark:text-white">{{ $t('Meta WhatsApp Cloud API') }}</td>
                            <td v-for="p in sortedPlans" :key="p.id" class="p-4 text-center text-emerald-500 font-bold">✓ Native</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-semibold text-slate-800 dark:text-white">{{ $t('AI Smart Assistant (OpenAI)') }}</td>
                            <td v-for="p in sortedPlans" :key="p.id" class="p-4 text-center text-emerald-500 font-bold">✓ Supported</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-semibold text-slate-800 dark:text-white">{{ $t('Webhooks & Developer REST API') }}</td>
                            <td v-for="p in sortedPlans" :key="p.id" class="p-4 text-center text-emerald-500 font-bold">✓ Supported</td>
                        </tr>
                        <tr>
                            <td class="p-4 font-semibold text-slate-800 dark:text-white">{{ $t('Free Trial Access') }}</td>
                            <td v-for="p in sortedPlans" :key="p.id" class="p-4 text-center text-emerald-500 font-bold">14 Days</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>

        <!-- CTA -->
        <WebsiteCtaBanner
            :title="$t('Start your 14-day free trial on any plan')"
            :subtitle="$t('Zero Risk')"
            :description="$t('Experience the full power of Wappiyo today. No credit card required to explore.')"
        />
    </WebsiteLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link, usePage } from '@inertiajs/vue3';
import WebsiteLayout from './Layout/WebsiteLayout.vue';
import WebsiteCtaBanner from './Components/WebsiteCtaBanner.vue';

const props = defineProps({
    companyConfig: Object,
    currency: String,
    plans: {
        type: Array,
        default: () => [],
    },
    addons: {
        type: Array,
        default: () => [],
    },
    languages: Object,
    currentLanguage: String,
});

const page = usePage();
const billingCycle = ref('monthly');

const sortedPlans = computed(() => {
    return [...props.plans].sort((a, b) => {
        const orderA = parseInt(a.metadata?.sort_order ?? 0);
        const orderB = parseInt(b.metadata?.sort_order ?? 0);
        return orderA - orderB;
    });
});

const isPlanFeatured = (plan) => {
    return Boolean(plan.metadata?.featured || plan.featured);
};

const getPlanDescription = (plan) => {
    return plan.metadata?.description || plan.description || 'Full-featured WhatsApp CRM and messaging suite designed for high-growth teams.';
};

const formatPlanPrice = (plan) => {
    const symbol = props.currency || '$';
    if (billingCycle.value === 'yearly') {
        const yearly = plan.metadata?.yearly_price || (parseFloat(plan.price || 0) * 10);
        return `${symbol}${yearly}`;
    }
    return `${symbol}${plan.price || 0}`;
};

const getPlanSignupUrl = (plan) => {
    const user = page.props.auth?.user;
    if (user) {
        return `/subscription`;
    }
    return `/signup?plan=${plan.uuid || plan.id}`;
};
</script>
