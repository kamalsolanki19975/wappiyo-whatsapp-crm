<template>
    <WebsiteLayout
        :companyConfig="companyConfig"
        :languages="languages"
        :currentLanguage="currentLanguage"
        title="Knowledgebase & Frequently Asked Questions"
        description="Find answers to all your questions about Wappiyo WhatsApp CRM, Meta Cloud API setup, pricing tiers, broadcast templates, and automation workflows."
    >
        <!-- HERO -->
        <section class="relative pt-16 pb-16 sm:pt-20 sm:pb-24 overflow-hidden bg-slate-50/50 dark:bg-[#09090B]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-700 dark:text-emerald-300 mb-4">
                    {{ $t('Knowledge & Answers') }}
                </span>
                <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight">
                    <span>{{ $t('Frequently Asked') }}</span>
                    <span class="block bg-gradient-to-r from-emerald-600 via-teal-500 to-cyan-500 bg-clip-text text-transparent">
                        {{ $t('Questions & Docs.') }}
                    </span>
                </h1>
                <p class="mt-4 text-base text-slate-600 dark:text-zinc-400 max-w-xl mx-auto">
                    {{ $t('Everything you need to know about getting started, connecting Meta Cloud API, and maximizing your team productivity.') }}
                </p>

                <!-- Search Input -->
                <div class="mt-8 max-w-lg mx-auto relative">
                    <input
                        type="text"
                        v-model="searchQuery"
                        placeholder="Search answers (e.g. Meta Cloud API, templates, billing)..."
                        class="w-full px-4 py-3 pl-11 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 shadow-sm"
                    />
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-4 top-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                </div>

                <!-- Categories -->
                <div class="mt-6 flex flex-wrap justify-center gap-2">
                    <button
                        v-for="cat in categories"
                        :key="cat"
                        @click="selectedCategory = cat"
                        class="px-3.5 py-1.5 rounded-lg text-xs font-semibold transition-all cursor-pointer"
                        :class="selectedCategory === cat 
                            ? 'bg-emerald-600 text-white shadow-sm' 
                            : 'bg-white dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white'"
                    >
                        {{ cat }}
                    </button>
                </div>
            </div>
        </section>

        <!-- ACCORDION CONTENT -->
        <section class="py-16 max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div v-if="filteredFaqs.length > 0" class="space-y-4">
                <div
                    v-for="(item, idx) in filteredFaqs"
                    :key="idx"
                    class="rounded-2xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-[#111113] overflow-hidden transition-all shadow-sm"
                >
                    <button
                        type="button"
                        @click="toggleItem(idx)"
                        class="w-full px-6 py-4 flex items-center justify-between text-left text-sm font-semibold text-slate-900 dark:text-white hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors cursor-pointer"
                    >
                        <div class="flex items-center gap-3">
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 shrink-0">
                                {{ item.category }}
                            </span>
                            <span>{{ item.q }}</span>
                        </div>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            class="w-4 h-4 transition-transform text-slate-400 shrink-0 ml-4"
                            :class="openIndex === idx ? 'rotate-180 text-emerald-600' : ''"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </button>
                    <div
                        v-show="openIndex === idx"
                        class="px-6 pb-5 pt-1 text-xs text-slate-600 dark:text-zinc-400 leading-relaxed border-t border-slate-100 dark:border-zinc-800/60"
                    >
                        {{ item.a }}
                    </div>
                </div>
            </div>

            <div v-else class="text-center py-16">
                <p class="text-slate-500 text-sm">No FAQs match your search query.</p>
                <button @click="searchQuery = ''; selectedCategory = 'All'" class="mt-2 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline cursor-pointer">
                    Reset search filters
                </button>
            </div>

            <!-- Need More Help Banner -->
            <div class="mt-16 p-8 rounded-3xl bg-slate-50 dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 text-center">
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">{{ $t('Still have questions?') }}</h3>
                <p class="text-xs text-slate-500 dark:text-zinc-400 max-w-md mx-auto mb-6">
                    {{ $t('Our support engineering team is available to assist with onboarding, technical integrations, or custom requirements.') }}
                </p>
                <Link href="/contact" class="px-6 py-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold inline-block transition-colors">
                    {{ $t('Contact Support Team') }}
                </Link>
            </div>
        </section>
    </WebsiteLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import WebsiteLayout from './Layout/WebsiteLayout.vue';

const props = defineProps({
    companyConfig: Object,
    languages: Object,
    currentLanguage: String,
});

const searchQuery = ref('');
const selectedCategory = ref('All');
const openIndex = ref(0);

const categories = [
    'All',
    'Getting Started',
    'WhatsApp API',
    'Shared Inbox',
    'Campaigns',
    'Automation',
    'Billing & Subscriptions',
    'Security'
];

const toggleItem = (idx) => {
    openIndex.value = openIndex.value === idx ? null : idx;
};

const faqData = [
    {
        category: 'Getting Started',
        q: 'How quickly can we get started with Wappiyo?',
        a: 'You can register in 30 seconds and connect your WhatsApp Business account using our Meta Embedded Signup flow. Most teams are sending and receiving messages within 10 minutes.'
    },
    {
        category: 'Getting Started',
        q: 'Do I need technical skills or coding experience?',
        a: 'No coding is required. Our Shared Inbox, Contact CRM, and Flow Builder are completely visual and designed for non-technical sales and support teams.'
    },
    {
        category: 'WhatsApp API',
        q: 'Does Wappiyo use the Official Meta Cloud API?',
        a: 'Yes, 100%. Wappiyo connects directly through Meta’s Official Cloud API infrastructure. This guarantees maximum delivery speed, official green tick support eligibility, and full compliance with Meta business policies.'
    },
    {
        category: 'WhatsApp API',
        q: 'Can I keep my existing WhatsApp number?',
        a: 'Yes! You can migrate an existing phone number or activate a new landline, toll-free, or mobile number. If the number is currently on the consumer WhatsApp app, you can delete that account and register it on Cloud API seamlessly.'
    },
    {
        category: 'Shared Inbox',
        q: 'How does agent collision detection work?',
        a: 'When an agent opens a conversation, a real-time presence lock notifies other team members. If another agent begins typing, a warning banner appears to prevent duplicate customer replies.'
    },
    {
        category: 'Campaigns',
        q: 'How do message templates and approvals work?',
        a: 'Meta requires pre-approval for outbound marketing and utility templates. You can submit templates directly through Wappiyo; approval typically takes between 1 minute and 24 hours.'
    },
    {
        category: 'Automation',
        q: 'Can chatbots transfer conversations to human agents?',
        a: 'Yes. In the visual flow builder, you can add a "Transfer to Agent" node at any step or configure trigger words like "agent" or "human" to instantly alert your team.'
    },
    {
        category: 'Billing & Subscriptions',
        q: 'Can I upgrade, downgrade, or cancel at any time?',
        a: 'Yes. You have full control from your billing portal. Upgrades apply immediately with prorated credits, and you can cancel anytime with zero cancellation fees.'
    },
    {
        category: 'Security',
        q: 'How is customer conversation data protected?',
        a: 'Wappiyo enforces strict tenant isolation, role-based access control (RBAC), end-to-end TLS encryption, and secure webhook signing. Your data is never shared with third parties.'
    }
];

const filteredFaqs = computed(() => {
    return faqData.filter(item => {
        const matchesCategory = selectedCategory.value === 'All' || item.category === selectedCategory.value;
        const query = searchQuery.value.toLowerCase().trim();
        const matchesQuery = !query || 
            item.q.toLowerCase().includes(query) || 
            item.a.toLowerCase().includes(query) ||
            item.category.toLowerCase().includes(query);
        return matchesCategory && matchesQuery;
    });
});
</script>
