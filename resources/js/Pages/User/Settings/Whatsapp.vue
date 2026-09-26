<template>
    <SettingLayout :modules="props.modules">
        <Head :title="$t('WhatsApp Cloud API Configuration')" />

        <div class="space-y-6">
            <!-- 1. Connection Status Card -->
            <div v-if="isConnected" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-white/10 p-6 sm:p-8 shadow-sm space-y-6">
                <!-- Top Status Header -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-100 dark:border-white/5 pb-5">
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 flex items-center justify-center shrink-0 shadow-xs">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M19.05 4.91A9.816 9.816 0 0 0 12.04 2c-5.46 0-9.91 4.45-9.91 9.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21c5.46 0 9.91-4.45 9.91-9.91c0-2.65-1.03-5.14-2.9-7.01m-7.01 15.24c-1.48 0-2.93-.4-4.2-1.15l-.3-.18l-3.12.82l.83-3.04l-.2-.31a8.264 8.264 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24c2.2 0 4.27.86 5.82 2.42a8.183 8.183 0 0 1 2.41 5.83c.02 4.54-3.68 8.23-8.22 8.23m4.52-6.16c-.25-.12-1.47-.72-1.69-.81c-.23-.08-.39-.12-.56.12c-.17.25-.64.81-.78.97c-.14.17-.29.19-.54.06c-.25-.12-1.05-.39-1.99-1.23c-.74-.66-1.23-1.47-1.38-1.72c-.14-.25-.02-.38.11-.51c.11-.11.25-.29.37-.43s.17-.25.25-.41c.08-.17.04-.31-.02-.43s-.56-1.34-.76-1.84c-.2-.48-.41-.42-.56-.43h-.48c-.17 0-.43.06-.66.31c-.22.25-.86.85-.86 2.07c0 1.22.89 2.4 1.01 2.56c.12.17 1.75 2.67 4.23 3.74c.59.26 1.05.41 1.41.52c.59.19 1.13.16 1.56.1c.48-.07 1.47-.6 1.67-1.18c.21-.58.21-1.07.14-1.18s-.22-.16-.47-.28"/>
                            </svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <h2 class="text-base font-bold text-slate-900 dark:text-white">
                                    {{ whatsappData.verified_name || $t('WhatsApp Business Number') }}
                                </h2>
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    {{ $t('Connected') }}
                                </span>
                            </div>
                            <div class="text-xs font-semibold text-slate-500 dark:text-slate-400 mt-0.5 font-mono">
                                {{ whatsappData.display_phone_number }}
                            </div>
                        </div>
                    </div>

                    <!-- Refresh Telemetry Button -->
                    <button
                        type="button"
                        @click="refreshData"
                        :disabled="refreshLoading"
                        class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl border border-slate-200/80 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-xs font-semibold text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-white/10 transition cursor-pointer"
                    >
                        <svg class="w-3.5 h-3.5 text-slate-500" :class="{ 'animate-spin': refreshLoading }" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        <span>{{ refreshLoading ? $t('Refreshing...') : $t('Sync WhatsApp Status') }}</span>
                    </button>
                </div>

                <!-- Live Metrics Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <!-- Messaging Tier -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            {{ $t('Messaging Tier') }}
                        </div>
                        <div class="text-sm font-bold text-slate-900 dark:text-white mt-1">
                            {{ whatsappData.messaging_limit_tier || 'TIER_1K' }}
                        </div>
                    </div>

                    <!-- Quality Rating -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            {{ $t('Quality Rating') }}
                        </div>
                        <div class="text-sm font-bold text-emerald-600 dark:text-emerald-400 mt-1 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                            <span>{{ whatsappData.quality_rating || 'HIGH' }}</span>
                        </div>
                    </div>

                    <!-- Account Review -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            {{ $t('Account Status') }}
                        </div>
                        <div class="text-sm font-bold text-slate-900 dark:text-white mt-1">
                            {{ whatsappData.account_review_status || 'APPROVED' }}
                        </div>
                    </div>

                    <!-- WABA ID -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-white/5">
                        <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                            {{ $t('WABA ID') }}
                        </div>
                        <div class="text-xs font-mono font-bold text-slate-900 dark:text-white mt-1 truncate">
                            {{ whatsappData.waba_id || '—' }}
                        </div>
                    </div>
                </div>

                <!-- Update Token CTA if manual -->
                <div v-if="whatsappData.is_embedded_signup === 0" class="pt-2 flex justify-end">
                    <button
                        type="button"
                        @click="isOpenTokenModal = true"
                        class="text-xs font-bold text-primary hover:underline flex items-center gap-1"
                    >
                        <span>🔑 {{ $t('Update Permanent Access Token') }}</span>
                    </button>
                </div>
            </div>

            <!-- Not Connected State -->
            <div v-else class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-white/10 p-8 shadow-sm text-center">
                <div class="w-16 h-16 rounded-3xl bg-primary/10 text-primary flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M19.05 4.91A9.816 9.816 0 0 0 12.04 2c-5.46 0-9.91 4.45-9.91 9.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21c5.46 0 9.91-4.45 9.91-9.91c0-2.65-1.03-5.14-2.9-7.01m-7.01 15.24c-1.48 0-2.93-.4-4.2-1.15l-.3-.18l-3.12.82l.83-3.04l-.2-.31a8.264 8.264 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24c2.2 0 4.27.86 5.82 2.42a8.183 8.183 0 0 1 2.41 5.83c.02 4.54-3.68 8.23-8.22 8.23"/>
                    </svg>
                </div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-2">
                    {{ $t('Connect your WhatsApp Business Account') }}
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-6">
                    {{ $t('Integrate directly with Meta WhatsApp Cloud API to send broadcast campaigns, manage customer inquiries, and execute workflow automations.') }}
                </p>

                <div class="flex items-center justify-center gap-3">
                    <EmbeddedSignupBtn
                        v-if="embeddedSignupActive == 1"
                        :appId="props.appId"
                        :configId="props.configId"
                        :graphAPIVersion="props.graphAPIVersion"
                    />
                    <button
                        v-else
                        type="button"
                        @click="isOpenManualConfigModal = true"
                        class="px-5 py-2.5 rounded-xl bg-primary text-white text-xs font-bold shadow-md shadow-purple-600/30 hover:bg-primary/90 transition cursor-pointer"
                    >
                        {{ $t('Manual API Setup') }}
                    </button>
                </div>
            </div>

            <!-- 2. Business Profile Settings Form -->
            <div v-if="isConnected" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-white/10 p-6 sm:p-8 shadow-sm space-y-6">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">
                            {{ $t('WhatsApp Business Profile') }}
                        </h3>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        {{ $t('This information is displayed to customers in your WhatsApp chat profile.') }}
                    </p>
                </div>

                <form @submit.prevent="submitProfileForm" class="space-y-4">
                    <!-- Address -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            {{ $t('Business Address') }}
                        </label>
                        <input
                            v-model="profileForm.address"
                            type="text"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                        />
                    </div>

                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            {{ $t('Contact Email') }}
                        </label>
                        <input
                            v-model="profileForm.email"
                            type="email"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                        />
                    </div>

                    <!-- Description -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            {{ $t('Business Description / Bio') }}
                        </label>
                        <textarea
                            v-model="profileForm.description"
                            rows="3"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                        ></textarea>
                    </div>

                    <!-- Vertical / Industry -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                            {{ $t('Industry Vertical') }}
                        </label>
                        <select
                            v-model="profileForm.industry"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                        >
                            <option value="AUTO">{{ $t('Automotive') }}</option>
                            <option value="BEAUTY">{{ $t('Beauty, Spa and Salon') }}</option>
                            <option value="APPAREL">{{ $t('Clothing and Apparel') }}</option>
                            <option value="EDU">{{ $t('Education') }}</option>
                            <option value="ENTERTAIN">{{ $t('Entertainment') }}</option>
                            <option value="EVENT_PLAN">{{ $t('Event Planning and Service') }}</option>
                            <option value="FINANCE">{{ $t('Finance and Banking') }}</option>
                            <option value="GROCERY">{{ $t('Grocery') }}</option>
                            <option value="GOVT">{{ $t('Public Service') }}</option>
                            <option value="HOTEL">{{ $t('Hotel and Lodging') }}</option>
                            <option value="HEALTH">{{ $t('Medical and Health') }}</option>
                            <option value="NONPROFIT">{{ $t('Non-profit') }}</option>
                            <option value="PROF_SERVICES">{{ $t('Professional Services') }}</option>
                            <option value="RETAIL">{{ $t('Shopping and Retail') }}</option>
                            <option value="TRAVEL">{{ $t('Travel and Transportation') }}</option>
                            <option value="RESTAURANT">{{ $t('Restaurant') }}</option>
                            <option value="OTHER">{{ $t('Other') }}</option>
                        </select>
                    </div>

                    <div class="flex justify-end pt-3">
                        <button
                            type="submit"
                            :disabled="profileForm.processing"
                            class="px-5 py-2.5 rounded-xl bg-primary text-white text-xs font-bold shadow-md shadow-purple-600/30 hover:bg-primary/90 transition disabled:opacity-50 cursor-pointer"
                        >
                            {{ profileForm.processing ? $t('Saving...') : $t('Update Business Profile') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- 3. Webhook Configuration Card -->
            <div v-if="isConnected" class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-white/10 p-6 sm:p-8 shadow-sm space-y-4">
                <div>
                    <div class="flex items-center gap-2 mb-1">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                        <h3 class="text-base font-bold text-slate-900 dark:text-white">
                            {{ $t('Meta Webhook Integration') }}
                        </h3>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        {{ $t('Configure this callback URL and verification token in your Meta App Dashboard under WhatsApp &rarr; Configuration.') }}
                    </p>
                </div>

                <div class="space-y-3">
                    <!-- Webhook URL -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div class="min-w-0">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                {{ $t('Callback URL') }}
                            </div>
                            <div class="font-mono text-xs font-bold text-slate-900 dark:text-white truncate mt-0.5">
                                {{ webhookUrl }}
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="copyText(webhookUrl, 'webhook')"
                            class="shrink-0 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-800 text-xs font-semibold text-primary hover:bg-slate-50 dark:hover:bg-white/10 transition"
                        >
                            {{ copiedTarget === 'webhook' ? $t('Copied!') : $t('Copy URL') }}
                        </button>
                    </div>

                    <!-- Verify Token -->
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
                        <div class="min-w-0">
                            <div class="text-[10px] font-bold uppercase tracking-wider text-slate-400">
                                {{ $t('Verify Token') }}
                            </div>
                            <div class="font-mono text-xs font-bold text-slate-900 dark:text-white truncate mt-0.5">
                                {{ props.settings?.identifier || '—' }}
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="copyText(props.settings?.identifier, 'token')"
                            class="shrink-0 px-3 py-1.5 rounded-lg border border-slate-200 dark:border-white/10 bg-white dark:bg-slate-800 text-xs font-semibold text-primary hover:bg-slate-50 dark:hover:bg-white/10 transition"
                        >
                            {{ copiedTarget === 'token' ? $t('Copied!') : $t('Copy Token') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- 4. Danger Zone: Disconnect WhatsApp -->
            <div v-if="isConnected" class="bg-white dark:bg-slate-900 rounded-3xl border border-rose-500/20 p-6 shadow-sm flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h4 class="text-xs font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400 mb-1">
                        {{ $t('Disconnect WhatsApp Account') }}
                    </h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 max-w-lg">
                        {{ $t('Disconnecting will cease incoming and outgoing WhatsApp messages. Your historical messages and contact records will be preserved.') }}
                    </p>
                </div>

                <button
                    type="button"
                    @click="deleteIntegration"
                    class="px-4 py-2 rounded-xl bg-rose-500 hover:bg-rose-600 text-white text-xs font-bold shadow-sm shadow-rose-500/30 transition cursor-pointer shrink-0"
                >
                    {{ $t('Disconnect Account') }}
                </button>
            </div>
        </div>

        <!-- Manual Setup Modal -->
        <div
            v-if="isOpenManualConfigModal"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
            @click.self="isOpenManualConfigModal = false"
        >
            <div class="relative w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-white/10 p-6 sm:p-8 select-none">
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-4">
                    {{ $t('WhatsApp Cloud API Credentials') }}
                </h3>

                <form @submit.prevent="submitManualConfig" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            {{ $t('Meta App ID') }}
                        </label>
                        <input
                            v-model="manualForm.app_id"
                            type="text"
                            required
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            {{ $t('Permanent Access Token') }}
                        </label>
                        <input
                            v-model="manualForm.access_token"
                            type="password"
                            required
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            {{ $t('Phone Number ID') }}
                        </label>
                        <input
                            v-model="manualForm.phone_number_id"
                            type="text"
                            required
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                        />
                    </div>

                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            {{ $t('WhatsApp Business Account (WABA) ID') }}
                        </label>
                        <input
                            v-model="manualForm.waba_id"
                            type="text"
                            required
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                        />
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-white/5">
                        <button
                            type="button"
                            @click="isOpenManualConfigModal = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 dark:hover:text-white"
                        >
                            {{ $t('Cancel') }}
                        </button>

                        <button
                            type="submit"
                            :disabled="manualForm.processing"
                            class="px-5 py-2 rounded-xl bg-primary text-white text-xs font-bold shadow-md shadow-purple-600/30 hover:bg-primary/90 transition disabled:opacity-50 cursor-pointer"
                        >
                            {{ manualForm.processing ? $t('Connecting...') : $t('Save & Connect') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Update Token Modal -->
        <div
            v-if="isOpenTokenModal"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
            @click.self="isOpenTokenModal = false"
        >
            <div class="relative w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-white/10 p-6 sm:p-8 select-none">
                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2">
                    {{ $t('Update Permanent Access Token') }}
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">
                    {{ $t('If your Meta system user token has expired or permissions changed, update it here.') }}
                </p>

                <form @submit.prevent="submitTokenUpdate" class="space-y-4">
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            {{ $t('New Access Token') }}
                        </label>
                        <input
                            v-model="tokenForm.access_token"
                            type="password"
                            required
                            class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                        />
                    </div>

                    <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-white/5">
                        <button
                            type="button"
                            @click="isOpenTokenModal = false"
                            class="px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 dark:hover:text-white"
                        >
                            {{ $t('Cancel') }}
                        </button>

                        <button
                            type="submit"
                            :disabled="tokenForm.processing"
                            class="px-5 py-2 rounded-xl bg-primary text-white text-xs font-bold shadow-md shadow-purple-600/30 hover:bg-primary/90 transition disabled:opacity-50 cursor-pointer"
                        >
                            {{ tokenForm.processing ? $t('Saving...') : $t('Update Token') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </SettingLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import SettingLayout from './Layout.vue';
import EmbeddedSignupBtn from '@/Components/EmbeddedSignupBtn.vue';

const props = defineProps({
    settings: Object,
    modules: Array,
    embeddedSignupActive: [Number, String],
    appId: String,
    configId: String,
    graphAPIVersion: String,
    currentURL: String,
});

const config = ref(props.settings?.metadata);
const parsedSettings = ref(config.value ? JSON.parse(config.value) : null);
const whatsappData = computed(() => parsedSettings.value?.whatsapp || null);
const isConnected = computed(() => Boolean(whatsappData.value));

const refreshLoading = ref(false);
const isOpenManualConfigModal = ref(false);
const isOpenTokenModal = ref(false);
const copiedTarget = ref(null);

const webhookUrl = computed(() => {
    const base = props.currentURL || (typeof window !== 'undefined' ? window.location.origin : '');
    return `${base}/webhook/whatsapp/${props.settings?.identifier || ''}`;
});

const profileForm = useForm({
    address: whatsappData.value?.address || '',
    email: whatsappData.value?.email || '',
    description: whatsappData.value?.description || '',
    industry: whatsappData.value?.vertical || whatsappData.value?.industry || 'OTHER',
});

const manualForm = useForm({
    app_id: '',
    access_token: '',
    phone_number_id: '',
    waba_id: '',
});

const tokenForm = useForm({
    access_token: '',
});

function refreshData() {
    refreshLoading.value = true;
    router.get('/settings/whatsapp/refresh', {}, {
        preserveState: false,
        onFinish: () => {
            refreshLoading.value = false;
        },
    });
}

function submitProfileForm() {
    profileForm.post('/settings/whatsapp/business-profile', {
        preserveScroll: true,
    });
}

function submitManualConfig() {
    manualForm.post('/settings/whatsapp', {
        preserveScroll: true,
        onSuccess: () => {
            isOpenManualConfigModal.value = false;
            manualForm.reset();
        },
    });
}

function submitTokenUpdate() {
    tokenForm.post('/settings/whatsapp/token', {
        preserveScroll: true,
        onSuccess: () => {
            isOpenTokenModal.value = false;
            tokenForm.reset();
        },
    });
}

function deleteIntegration() {
    if (confirm('Are you sure you want to disconnect your WhatsApp account?')) {
        router.delete('/settings/whatsapp/business-profile', {
            preserveScroll: true,
        });
    }
}

function copyText(text, target) {
    if (!text) return;
    navigator.clipboard.writeText(text).then(() => {
        copiedTarget.value = target;
        setTimeout(() => {
            copiedTarget.value = null;
        }, 2000);
    });
}
</script>