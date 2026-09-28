<template>
    <WebsiteLayout
        :companyConfig="companyConfig"
        :languages="languages"
        :currentLanguage="currentLanguage"
        :title="pageTitle"
        :description="`Legal policies and regulatory compliance guidelines for ${companyName}.`"
    >
        <!-- HERO -->
        <section class="relative pt-16 pb-12 sm:pt-20 sm:pb-16 overflow-hidden bg-slate-50/50 dark:bg-[#09090B] border-b border-slate-200/80 dark:border-zinc-800">
            <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-700 dark:text-emerald-300 mb-4">
                    {{ $t('Legal & Compliance') }}
                </span>
                <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight">
                    {{ pageTitle }}
                </h1>
                <p class="mt-2 text-xs text-slate-500 dark:text-zinc-400">
                    {{ $t('Last updated: March 2026') }}
                </p>

                <!-- Policy Selector Tabs -->
                <div class="mt-8 flex flex-wrap justify-center gap-2">
                    <Link
                        href="/privacy"
                        class="px-4 py-2 rounded-xl text-xs font-semibold transition-all"
                        :class="currentType === 'privacy' 
                            ? 'bg-emerald-600 text-white shadow-sm' 
                            : 'bg-white dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white'"
                    >
                        {{ $t('Privacy Policy') }}
                    </Link>
                    <Link
                        href="/terms"
                        class="px-4 py-2 rounded-xl text-xs font-semibold transition-all"
                        :class="currentType === 'terms' 
                            ? 'bg-emerald-600 text-white shadow-sm' 
                            : 'bg-white dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white'"
                    >
                        {{ $t('Terms of Service') }}
                    </Link>
                    <Link
                        href="/refund-policy"
                        class="px-4 py-2 rounded-xl text-xs font-semibold transition-all"
                        :class="currentType === 'refund' 
                            ? 'bg-emerald-600 text-white shadow-sm' 
                            : 'bg-white dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white'"
                    >
                        {{ $t('Refund Policy') }}
                    </Link>
                </div>
            </div>
        </section>

        <!-- DOCUMENT CONTENT -->
        <section class="py-16 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="prose prose-slate dark:prose-invert max-w-none text-xs sm:text-sm leading-relaxed space-y-8 bg-white dark:bg-[#111113] p-8 sm:p-12 rounded-3xl border border-slate-200/80 dark:border-zinc-800 shadow-sm">
                
                <!-- PRIVACY POLICY -->
                <div v-if="currentType === 'privacy'" class="space-y-6">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-2">1. Overview & Information Collection</h2>
                        <p class="text-slate-600 dark:text-zinc-400">
                            {{ companyName }} ("we", "our", or "us") respects your privacy and is dedicated to safeguarding user information. This Privacy Policy describes how we collect, store, and process personal and organizational data when using our software, shared inbox, broadcast tools, and Meta Cloud API integrations.
                        </p>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-2">2. WhatsApp Data & Messaging Privacy</h2>
                        <p class="text-slate-600 dark:text-zinc-400">
                            We process message content, customer contact identifiers, phone numbers, and delivery statuses solely for the purpose of delivering requested services to your organization. Customer messages are transmitted using TLS encryption directly via Meta Cloud API endpoints. We do not sell or monetize customer conversation contents or phone numbers.
                        </p>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-2">3. Data Retention & Tenant Isolation</h2>
                        <p class="text-slate-600 dark:text-zinc-400">
                            All tenant data is strictly isolated by organizational identifier. You maintain ownership of your contact records and conversation histories. Organizations can request data exports or permanent account deletion at any time by contacting our support team.
                        </p>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-2">4. Third-Party Integrations</h2>
                        <p class="text-slate-600 dark:text-zinc-400">
                            Depending on your enabled addons, data may be processed by certified service providers including Meta Platforms, OpenAI, Stripe, and Razorpay for payment processing and AI assistant services.
                        </p>
                    </div>
                </div>

                <!-- TERMS OF SERVICE -->
                <div v-else-if="currentType === 'terms'" class="space-y-6">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-2">1. Acceptance of Terms</h2>
                        <p class="text-slate-600 dark:text-zinc-400">
                            By creating an account or accessing {{ companyName }}, you agree to be bound by these Terms of Service. If you do not agree to these terms, you may not access or use the platform.
                        </p>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-2">2. Compliance with WhatsApp & Meta Business Policies</h2>
                        <p class="text-slate-600 dark:text-zinc-400">
                            Users are strictly required to adhere to all Meta WhatsApp Business and Commerce policies. Spamming, transmitting unsolicited commercial communications without explicit user opt-in, or distributing malicious content is strictly prohibited and grounds for immediate termination without refund.
                        </p>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-2">3. Subscription, Billing & Account Responsibility</h2>
                        <p class="text-slate-600 dark:text-zinc-400">
                            Subscriptions are billed on a recurring monthly or annual basis as configured by the customer. You are responsible for maintaining the confidentiality of your login credentials and for all activities occurring under your account.
                        </p>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-2">4. Service Availability & SLA</h2>
                        <p class="text-slate-600 dark:text-zinc-400">
                            While we strive for 99.9% uptime, services may occasionally experience brief maintenance windows. We are not liable for upstream outages originating directly from Meta Platforms or underlying telecommunication networks.
                        </p>
                    </div>
                </div>

                <!-- REFUND POLICY -->
                <div v-else-if="currentType === 'refund'" class="space-y-6">
                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-2">1. 14-Day Money-Back Guarantee</h2>
                        <p class="text-slate-600 dark:text-zinc-400">
                            We want you to be completely satisfied with {{ companyName }}. If you determine that our platform does not suit your business needs within 14 days of your initial paid subscription, you may request a 100% full refund with no questions asked.
                        </p>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-2">2. Cancellation Policy</h2>
                        <p class="text-slate-600 dark:text-zinc-400">
                            You may cancel your subscription at any time directly through your billing portal. Upon cancellation, your access remains active until the end of your current paid billing period, and no further recurring charges will occur.
                        </p>
                    </div>

                    <div>
                        <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-2">3. Meta Conversation Fees Notice</h2>
                        <p class="text-slate-600 dark:text-zinc-400">
                            Please note that usage-based conversation fees billed directly by Meta Platforms (WhatsApp Cloud API utility and marketing conversations) are governed by Meta's billing terms and are non-refundable by {{ companyName }}.
                        </p>
                    </div>
                </div>

                <!-- Contact Note -->
                <div class="pt-6 border-t border-slate-100 dark:border-zinc-800 text-xs text-slate-500">
                    <p>
                        For any legal inquiries, data protection officer contact, or formal requests, reach out to:
                        <a v-if="companyConfig?.email" :href="'mailto:' + companyConfig.email" class="text-emerald-600 dark:text-emerald-400 underline font-medium ml-1">
                            {{ companyConfig.email }}
                        </a>
                        <span v-else class="text-emerald-600 dark:text-emerald-400 font-medium ml-1">support@wappiyo.io</span>
                    </p>
                </div>
            </div>
        </section>
    </WebsiteLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import WebsiteLayout from './Layout/WebsiteLayout.vue';

const props = defineProps({
    companyConfig: Object,
    languages: Object,
    currentLanguage: String,
    type: {
        type: String,
        default: 'privacy',
    },
    title: {
        type: String,
        default: 'Privacy Policy',
    },
});

const currentType = computed(() => props.type || 'privacy');
const companyName = computed(() => props.companyConfig?.company_name || 'Wappiyo');
const pageTitle = computed(() => props.title || 'Legal Information');
</script>
