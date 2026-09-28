<template>
    <footer class="bg-slate-50 dark:bg-[#09090B] border-t border-slate-200/80 dark:border-zinc-800 text-slate-600 dark:text-zinc-400 transition-colors">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-16 pb-12">
            <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-6 gap-8 mb-12">
                <!-- Brand Column -->
                <div class="col-span-2">
                    <Link href="/" class="flex items-center gap-2.5 mb-4 focus:outline-none">
                        <BrandLogo :custom-logo="companyConfig?.logo" :company-name="companyConfig?.company_name || 'Wappiyo'" mode="auto" />
                    </Link>
                    <p class="text-xs text-slate-500 dark:text-zinc-400 max-w-sm mb-5 leading-relaxed">
                        {{ $t('The modern WhatsApp CRM, broadcast messaging, and conversational automation platform designed for high-growth businesses and sales teams.') }}
                    </p>
                    
                    <!-- Operational status pill -->
                    <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800/40 text-[11px] font-semibold text-emerald-700 dark:text-emerald-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>{{ $t('All systems operational') }}</span>
                    </div>
                </div>

                <!-- Product -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-4">
                        {{ $t('Product') }}
                    </h4>
                    <ul class="space-y-2.5 text-xs">
                        <li><Link href="/product/inbox" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">{{ $t('Shared Inbox') }}</Link></li>
                        <li><Link href="/product/crm" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">{{ $t('WhatsApp CRM') }}</Link></li>
                        <li><Link href="/product/campaigns" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">{{ $t('Broadcast Campaigns') }}</Link></li>
                        <li><Link href="/product/automation" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">{{ $t('Flow Builder') }}</Link></li>
                        <li><Link href="/product/ai" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">{{ $t('AI Assistant') }}</Link></li>
                        <li><Link href="/product/analytics" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">{{ $t('Live Analytics') }}</Link></li>
                    </ul>
                </div>

                <!-- Solutions -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-4">
                        {{ $t('Solutions') }}
                    </h4>
                    <ul class="space-y-2.5 text-xs">
                        <li><Link href="/features" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">{{ $t('Customer Support') }}</Link></li>
                        <li><Link href="/features" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">{{ $t('Sales & Lead Gen') }}</Link></li>
                        <li><Link href="/features" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">{{ $t('Marketing Teams') }}</Link></li>
                        <li><Link href="/integrations" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">{{ $t('Integrations') }}</Link></li>
                        <li><Link href="/pricing" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">{{ $t('Enterprise Scaling') }}</Link></li>
                    </ul>
                </div>

                <!-- Resources -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-4">
                        {{ $t('Resources') }}
                    </h4>
                    <ul class="space-y-2.5 text-xs">
                        <li><Link href="/features" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">{{ $t('Platform Features') }}</Link></li>
                        <li><Link href="/faq" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">{{ $t('Knowledgebase & FAQ') }}</Link></li>
                        <li><Link href="/contact" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">{{ $t('Help & Support') }}</Link></li>
                        <li><Link href="/about" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">{{ $t('Platform Security') }}</Link></li>
                    </ul>
                </div>

                <!-- Company & Legal -->
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white mb-4">
                        {{ $t('Company & Legal') }}
                    </h4>
                    <ul class="space-y-2.5 text-xs">
                        <li><Link href="/about" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">{{ $t('About Us') }}</Link></li>
                        <li><Link href="/pricing" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">{{ $t('Pricing Plans') }}</Link></li>
                        <li><Link href="/contact" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">{{ $t('Contact Sales') }}</Link></li>
                        <li><Link href="/privacy" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">{{ $t('Privacy Policy') }}</Link></li>
                        <li><Link href="/terms" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">{{ $t('Terms of Service') }}</Link></li>
                        <li><Link href="/refund-policy" class="hover:text-emerald-600 dark:hover:text-emerald-400 transition-colors">{{ $t('Refund Policy') }}</Link></li>
                    </ul>
                </div>
            </div>

            <!-- Bottom Sub-Footer Bar -->
            <div class="pt-8 border-t border-slate-200 dark:border-zinc-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-slate-500 dark:text-zinc-500">
                <p>
                    &copy; {{ currentYear }} {{ companyConfig?.company_name || 'Wappiyo' }}. {{ $t('All rights reserved.') }}
                </p>

                <div class="flex items-center gap-6">
                    <Link href="/privacy" class="hover:text-slate-800 dark:hover:text-zinc-300 transition-colors">{{ $t('Privacy') }}</Link>
                    <Link href="/terms" class="hover:text-slate-800 dark:hover:text-zinc-300 transition-colors">{{ $t('Terms') }}</Link>
                    <Link href="/refund-policy" class="hover:text-slate-800 dark:hover:text-zinc-300 transition-colors">{{ $t('Refunds') }}</Link>
                    <Link href="/contact" class="hover:text-slate-800 dark:hover:text-zinc-300 transition-colors">{{ $t('Contact') }}</Link>
                </div>
            </div>
        </div>
    </footer>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import BrandLogo from '@/Components/UI/BrandLogo.vue';

const props = defineProps({
    companyConfig: {
        type: Object,
        default: () => ({}),
    },
});

const currentYear = computed(() => new Date().getFullYear());
</script>
