<template>
    <WebsiteLayout
        :companyConfig="companyConfig"
        :languages="languages"
        :currentLanguage="currentLanguage"
        title="Verified Integrations & Ecosystem"
        description="Connect Wappiyo with your favorite tools. Official Meta WhatsApp Cloud API, OpenAI, Stripe, Razorpay, Webhooks, Google reCAPTCHA, and REST APIs."
    >
        <!-- HERO -->
        <section class="relative pt-16 pb-20 sm:pt-24 sm:pb-32 overflow-hidden bg-slate-50/50 dark:bg-[#09090B]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-700 dark:text-emerald-300 mb-6">
                    {{ $t('Extensible Ecosystem') }}
                </span>
                <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight max-w-4xl mx-auto">
                    <span>{{ $t('Connect Wappiyo to your') }}</span>
                    <span class="block bg-gradient-to-r from-emerald-600 via-teal-500 to-cyan-500 bg-clip-text text-transparent">
                        {{ $t('mission-critical tech stack.') }}
                    </span>
                </h1>
                <p class="mt-6 text-base sm:text-lg text-slate-600 dark:text-zinc-400 max-w-2xl mx-auto leading-relaxed">
                    {{ $t('Seamlessly sync customer conversations, trigger outbound workflows, process subscriptions, and enrich leads with our verified native integrations.') }}
                </p>

                <!-- Search & Filter Controls -->
                <div class="mt-10 max-w-xl mx-auto flex flex-col sm:flex-row gap-3">
                    <div class="relative flex-1">
                        <input
                            type="text"
                            v-model="searchQuery"
                            placeholder="Search integrations (e.g. Meta, Stripe, OpenAI)..."
                            class="w-full px-4 py-3 pl-10 rounded-xl bg-white dark:bg-[#111113] border border-slate-200 dark:border-zinc-800 text-xs text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        />
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 absolute left-3.5 top-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
                    </div>
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

        <!-- INTEGRATIONS DIRECTORY GRID -->
        <section class="py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div
                    v-for="item in filteredIntegrations"
                    :key="item.name"
                    class="p-6 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 hover:border-emerald-300 dark:hover:border-emerald-800 transition-all hover:shadow-lg space-y-4 flex flex-col justify-between"
                >
                    <div>
                        <div class="flex items-center justify-between mb-4">
                            <div class="w-12 h-12 rounded-xl flex items-center justify-center font-bold text-lg" :class="item.bgClass">
                                <span>{{ item.icon }}</span>
                            </div>
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-300">
                                {{ item.category }}
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1">{{ item.name }}</h3>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 leading-relaxed">{{ item.description }}</p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-zinc-800/80 flex items-center justify-between text-xs">
                        <span class="inline-flex items-center gap-1.5 font-semibold text-emerald-600 dark:text-emerald-400 text-[11px]">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            <span>Native Support</span>
                        </span>
                        <Link href="/signup" class="font-bold text-emerald-600 dark:text-emerald-400 hover:underline">
                            Connect &rarr;
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Empty state if search returns nothing -->
            <div v-if="filteredIntegrations.length === 0" class="text-center py-16">
                <p class="text-slate-500 text-sm">No integrations match your search criteria.</p>
                <button @click="searchQuery = ''; selectedCategory = 'All'" class="mt-2 text-xs font-bold text-emerald-600 dark:text-emerald-400 hover:underline cursor-pointer">
                    Reset search filters
                </button>
            </div>
        </section>

        <!-- CTA -->
        <WebsiteCtaBanner
            :title="$t('Need a custom integration or webhook pipeline?')"
            :subtitle="$t('Developer-Friendly')"
            :description="$t('Use our comprehensive REST API and real-time outbound webhooks to build anything you need.')"
        />
    </WebsiteLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import WebsiteLayout from './Layout/WebsiteLayout.vue';
import WebsiteCtaBanner from './Components/WebsiteCtaBanner.vue';

const props = defineProps({
    companyConfig: Object,
    languages: Object,
    currentLanguage: String,
});

const searchQuery = ref('');
const selectedCategory = ref('All');

const categories = ['All', 'Messaging', 'AI & Automation', 'Payments', 'Security & Maps', 'Webhooks & APIs'];

const integrationsList = [
    {
        name: 'Meta WhatsApp Cloud API',
        category: 'Messaging',
        icon: '💬',
        bgClass: 'bg-emerald-100 dark:bg-emerald-950 text-emerald-600',
        description: 'Official direct connection to WhatsApp Business Platform. Verified phone numbers, interactive templates, and 99.9% delivery reliability.'
    },
    {
        name: 'OpenAI GPT Models',
        category: 'AI & Automation',
        icon: '🤖',
        bgClass: 'bg-purple-100 dark:bg-purple-950 text-[#8B5CF6]',
        description: 'Empower agents with intelligent suggestions, automatic multilingual translations, and instant conversation summaries.'
    },
    {
        name: 'Stripe Billing',
        category: 'Payments',
        icon: '💳',
        bgClass: 'bg-indigo-100 dark:bg-indigo-950 text-indigo-600',
        description: 'Automated subscription billing, recurring invoice collection, customer payment methods, and international multi-currency checkouts.'
    },
    {
        name: 'Razorpay Gateway',
        category: 'Payments',
        icon: '🇮🇳',
        bgClass: 'bg-blue-100 dark:bg-blue-950 text-blue-600',
        description: 'Native subscription and one-time payment processing supporting UPI, credit cards, debit cards, and Indian netbanking.'
    },
    {
        name: 'Outbound & Inbound Webhooks',
        category: 'Webhooks & APIs',
        icon: '⚡',
        bgClass: 'bg-amber-100 dark:bg-amber-950 text-amber-600',
        description: 'Receive instant real-time HTTP POST notifications whenever a message is sent, delivered, or read, or when a contact updates.'
    },
    {
        name: 'Google reCAPTCHA v2 / v3',
        category: 'Security & Maps',
        icon: '🛡️',
        bgClass: 'bg-slate-100 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300',
        description: 'Enterprise bot protection preventing spam account registrations, unauthorized API attacks, and brute-force inquiries.'
    },
    {
        name: 'Google Maps API',
        category: 'Security & Maps',
        icon: '📍',
        bgClass: 'bg-rose-100 dark:bg-rose-950 text-rose-600',
        description: 'Render accurate customer location pins and address autocomplete within WhatsApp CRM profiles.'
    },
    {
        name: 'Google & Facebook Social Login',
        category: 'Security & Maps',
        icon: '🔐',
        bgClass: 'bg-cyan-100 dark:bg-cyan-950 text-[#06B6D4]',
        description: 'One-click friction-free authentication for customer and team accounts with OAuth 2.0 security.'
    },
    {
        name: 'Developer REST API',
        category: 'Webhooks & APIs',
        icon: '🔌',
        bgClass: 'bg-violet-100 dark:bg-violet-950 text-[#8B5CF6]',
        description: 'Full programmatic control to send messages, manage contacts, query analytics, and trigger automated flows from external software.'
    }
];

const filteredIntegrations = computed(() => {
    return integrationsList.filter(item => {
        const matchesCategory = selectedCategory.value === 'All' || item.category === selectedCategory.value;
        const query = searchQuery.value.toLowerCase().trim();
        const matchesQuery = !query || 
            item.name.toLowerCase().includes(query) || 
            item.description.toLowerCase().includes(query) ||
            item.category.toLowerCase().includes(query);
        return matchesCategory && matchesQuery;
    });
});
</script>
