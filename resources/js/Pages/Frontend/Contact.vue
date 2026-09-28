<template>
    <WebsiteLayout
        :companyConfig="companyConfig"
        :languages="languages"
        :currentLanguage="currentLanguage"
        title="Contact Sales & Support"
        description="Get in touch with the Wappiyo team for product inquiries, enterprise custom pricing, onboarding questions, and partner integrations."
    >
        <!-- HERO -->
        <section class="relative pt-16 pb-16 sm:pt-20 sm:pb-24 overflow-hidden bg-slate-50/50 dark:bg-[#09090B]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs font-semibold text-emerald-700 dark:text-emerald-300 mb-4">
                    {{ $t('Get in Touch') }}
                </span>
                <h1 class="text-4xl sm:text-5xl font-extrabold tracking-tight text-slate-900 dark:text-white leading-tight">
                    <span>{{ $t("Let's talk about your") }}</span>
                    <span class="block bg-gradient-to-r from-emerald-600 via-teal-500 to-cyan-500 bg-clip-text text-transparent">
                        {{ $t('WhatsApp operations.') }}
                    </span>
                </h1>
                <p class="mt-4 text-base text-slate-600 dark:text-zinc-400 max-w-xl mx-auto">
                    {{ $t('Have a question about high-volume broadcasts, custom enterprise plans, or API capabilities? Send us a message.') }}
                </p>
            </div>
        </section>

        <!-- CONTACT FORM & COMPANY INFO -->
        <section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12">
                <!-- Contact Details Column -->
                <div class="lg:col-span-5 space-y-8">
                    <div class="p-8 rounded-3xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-sm space-y-6">
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white">{{ $t('Direct Communication') }}</h3>
                        
                        <!-- Email -->
                        <div v-if="companyConfig?.email" class="flex items-start gap-3.5 text-xs">
                            <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect width="20" height="16" x="2" y="4" rx="2"/><path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"/></svg>
                            </div>
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $t('Email Us') }}</span>
                                <a :href="'mailto:' + companyConfig.email" class="text-slate-500 dark:text-zinc-400 hover:text-emerald-600 transition-colors">
                                    {{ companyConfig.email }}
                                </a>
                            </div>
                        </div>

                        <!-- Phone -->
                        <div v-if="companyConfig?.phone" class="flex items-start gap-3.5 text-xs">
                            <div class="w-9 h-9 rounded-xl bg-cyan-100 dark:bg-cyan-950 text-[#06B6D4] flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"/></svg>
                            </div>
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $t('Direct Phone') }}</span>
                                <a :href="'tel:' + companyConfig.phone" class="text-slate-500 dark:text-zinc-400 hover:text-[#06B6D4] transition-colors">
                                    {{ companyConfig.phone }}
                                </a>
                            </div>
                        </div>

                        <!-- Office Address -->
                        <div v-if="companyConfig?.address" class="flex items-start gap-3.5 text-xs">
                            <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-950 text-emerald-600 flex items-center justify-center shrink-0">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 10c0 6-8 12-8 12s-8-6-8-12a8 8 0 0 1 16 0Z"/><circle cx="12" cy="10" r="3"/></svg>
                            </div>
                            <div>
                                <span class="font-bold text-slate-900 dark:text-white block">{{ $t('Headquarters') }}</span>
                                <p class="text-slate-500 dark:text-zinc-400 leading-relaxed">{{ companyConfig.address }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Fast FAQ prompt -->
                    <div class="p-6 rounded-3xl bg-emerald-50/50 dark:bg-emerald-950/20 border border-emerald-200/60 dark:border-emerald-800/40 text-xs space-y-2">
                        <h4 class="font-bold text-emerald-800 dark:text-emerald-300">{{ $t('Looking for instant answers?') }}</h4>
                        <p class="text-slate-600 dark:text-zinc-400 leading-relaxed">
                            {{ $t('Check out our searchable Knowledgebase & FAQ section covering setup, WhatsApp API limits, and billing details.') }}
                        </p>
                        <Link href="/faq" class="font-bold text-emerald-600 dark:text-emerald-400 hover:underline inline-block pt-1">
                            {{ $t('Explore FAQs &rarr;') }}
                        </Link>
                    </div>
                </div>

                <!-- Contact Form Column -->
                <div class="lg:col-span-7">
                    <div class="p-8 sm:p-10 rounded-3xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xl">
                        <!-- Flash Notification if present -->
                        <div v-if="$page.props.flash?.status" class="mb-6 p-4 rounded-2xl flex items-center gap-3 text-xs" :class="$page.props.flash.status.type === 'success' ? 'bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300' : 'bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-300'">
                            <svg v-if="$page.props.flash.status.type === 'success'" xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0 text-emerald-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                            <svg v-else xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 shrink-0 text-rose-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>{{ $page.props.flash.status.message }}</span>
                        </div>

                        <form @submit.prevent="submitForm" class="space-y-5">
                            <!-- Anti-bot honeypot hidden field -->
                            <input
                                type="text"
                                name="website_hp"
                                v-model="form.website_hp"
                                tabindex="-1"
                                autocomplete="off"
                                class="opacity-0 absolute -z-10 w-0 h-0 pointer-events-none"
                            />

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Name -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 dark:text-zinc-300 mb-1.5">{{ $t('Your Name') }} *</label>
                                    <input
                                        type="text"
                                        v-model="form.name"
                                        required
                                        placeholder="Alex Morgan"
                                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-xs text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all"
                                    />
                                    <p v-if="form.errors.name" class="text-rose-500 text-[10px] mt-1">{{ form.errors.name }}</p>
                                </div>

                                <!-- Email -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 dark:text-zinc-300 mb-1.5">{{ $t('Business Email') }} *</label>
                                    <input
                                        type="email"
                                        v-model="form.email"
                                        required
                                        placeholder="alex@company.com"
                                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-xs text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all"
                                    />
                                    <p v-if="form.errors.email" class="text-rose-500 text-[10px] mt-1">{{ form.errors.email }}</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <!-- Phone -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 dark:text-zinc-300 mb-1.5">{{ $t('Phone Number') }}</label>
                                    <input
                                        type="tel"
                                        v-model="form.phone"
                                        placeholder="+1 (555) 000-0000"
                                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-xs text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all"
                                    />
                                    <p v-if="form.errors.phone" class="text-rose-500 text-[10px] mt-1">{{ form.errors.phone }}</p>
                                </div>

                                <!-- Company -->
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 dark:text-zinc-300 mb-1.5">{{ $t('Company / Organization') }}</label>
                                    <input
                                        type="text"
                                        v-model="form.company"
                                        placeholder="Acme Corp"
                                        class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-xs text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all"
                                    />
                                    <p v-if="form.errors.company" class="text-rose-500 text-[10px] mt-1">{{ form.errors.company }}</p>
                                </div>
                            </div>

                            <!-- Subject -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-zinc-300 mb-1.5">{{ $t('Subject') }} *</label>
                                <input
                                    type="text"
                                    v-model="form.subject"
                                    required
                                    placeholder="Enterprise deployment & custom message volumes"
                                    class="w-full px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-xs text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all"
                                />
                                <p v-if="form.errors.subject" class="text-rose-500 text-[10px] mt-1">{{ form.errors.subject }}</p>
                            </div>

                            <!-- Message -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 dark:text-zinc-300 mb-1.5">{{ $t('Your Message') }} *</label>
                                <textarea
                                    v-model="form.message"
                                    rows="5"
                                    required
                                    placeholder="Tell us about your team size, expected message volumes, and any specific requirements..."
                                    class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-[#18181B] border border-slate-200 dark:border-zinc-800 text-xs text-slate-900 dark:text-white placeholder:text-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all"
                                ></textarea>
                                <p v-if="form.errors.message" class="text-rose-500 text-[10px] mt-1">{{ form.errors.message }}</p>
                            </div>

                            <button
                                type="submit"
                                :disabled="form.processing"
                                class="w-full py-3.5 px-6 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white font-bold text-xs shadow-lg shadow-emerald-600/20 transition-all disabled:opacity-50 cursor-pointer flex items-center justify-center gap-2"
                            >
                                <span v-if="form.processing" class="w-3.5 h-3.5 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                <span>{{ form.processing ? $t('Sending Message...') : $t('Send Message') }}</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </section>
    </WebsiteLayout>
</template>

<script setup>
import { onMounted } from 'vue';
import { useForm, Link } from '@inertiajs/vue3';
import WebsiteLayout from './Layout/WebsiteLayout.vue';

const props = defineProps({
    companyConfig: Object,
    languages: Object,
    currentLanguage: String,
});

const form = useForm({
    name: '',
    email: '',
    phone: '',
    company: '',
    subject: '',
    message: '',
    website_hp: '',
    form_type: 'contact',
    source: 'Website Contact Form',
    page_url: '',
    referrer: '',
    utm_source: '',
    utm_medium: '',
    utm_campaign: '',
    utm_term: '',
    utm_content: '',
});

onMounted(() => {
    if (typeof window !== 'undefined') {
        const params = new URLSearchParams(window.location.search);
        
        // Attribution capture
        form.page_url = window.location.href;
        form.referrer = document.referrer || '';
        form.utm_source = params.get('utm_source') || '';
        form.utm_medium = params.get('utm_medium') || '';
        form.utm_campaign = params.get('utm_campaign') || '';
        form.utm_term = params.get('utm_term') || '';
        form.utm_content = params.get('utm_content') || '';

        // Form Type & Subject contextual prefill
        const type = params.get('type');
        const subjectParam = params.get('subject');
        if (type === 'demo') {
            form.form_type = 'demo';
            form.source = 'Book Demo Button';
            form.subject = subjectParam || 'Request a Product Demo';
        } else if (type === 'sales') {
            form.form_type = 'sales';
            form.source = 'Contact Sales Button';
            form.subject = subjectParam || 'Sales & Custom Enterprise Inquiry';
        } else if (subjectParam) {
            form.subject = subjectParam;
        }
    }
});

const submitForm = () => {
    form.post('/contact', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('name', 'email', 'phone', 'company', 'subject', 'message', 'website_hp');
        },
    });
};
</script>
