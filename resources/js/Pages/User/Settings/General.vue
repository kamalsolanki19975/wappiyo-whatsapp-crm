<template>
    <SettingLayout :modules="props.modules">
        <Head :title="$t('General Settings')" />

        <div class="space-y-6">
            <form @submit.prevent="submitForm">
                <!-- 1. Organization Details Card -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-white/10 p-6 sm:p-8 shadow-sm space-y-6 mb-6">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2 h-2 rounded-full bg-primary"></span>
                            <h2 class="text-base font-bold text-slate-900 dark:text-white">
                                {{ $t('Organization Information') }}
                            </h2>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $t('Specify the legal name and physical address for your business workspace.') }}
                        </p>
                    </div>

                    <div class="space-y-4">
                        <!-- Organization Name -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                {{ $t('Business / Organization Name') }} <span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.organization_name"
                                type="text"
                                :placeholder="$t('e.g., Acme Corporation')"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                                :class="{ 'border-rose-500': form.errors.organization_name }"
                            />
                            <p v-if="form.errors.organization_name" class="text-xs text-rose-500 mt-1 font-medium">
                                {{ form.errors.organization_name }}
                            </p>
                        </div>

                        <!-- Address -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                {{ $t('Street Address') }}
                            </label>
                            <input
                                v-model="form.address"
                                type="text"
                                :placeholder="$t('123 Innovation Blvd, Suite 400')"
                                class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                            />
                        </div>

                        <!-- City, State, Zip, Country Grid -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    {{ $t('City') }}
                                </label>
                                <input
                                    v-model="form.city"
                                    type="text"
                                    class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    {{ $t('State / Province') }}
                                </label>
                                <input
                                    v-model="form.state"
                                    type="text"
                                    class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    {{ $t('Postal / Zip Code') }}
                                </label>
                                <input
                                    v-model="form.zip"
                                    type="text"
                                    class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                                />
                            </div>

                            <div>
                                <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                    {{ $t('Country') }}
                                </label>
                                <select
                                    v-model="form.country"
                                    class="w-full px-3 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                                >
                                    <option :value="null">{{ $t('Select Country') }}</option>
                                    <option v-for="c in countries" :key="c.value || c" :value="c.value || c">
                                        {{ c.label || c }}
                                    </option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 2. Regional & Timezone Card -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-white/10 p-6 sm:p-8 shadow-sm space-y-4 mb-6">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                            <h2 class="text-base font-bold text-slate-900 dark:text-white">
                                {{ $t('Regional & Timezone Settings') }}
                            </h2>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $t('Campaign scheduled send times and message delivery reports are calculated in this timezone.') }}
                        </p>
                    </div>

                    <div class="max-w-md">
                        <select
                            v-model="form.timezone"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                        >
                            <option v-for="tz in timezones" :key="tz.value || tz" :value="tz.value || tz">
                                {{ tz.label || tz }}
                            </option>
                        </select>
                    </div>
                </div>

                <!-- 3. Notification Sounds Card -->
                <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-white/10 p-6 sm:p-8 shadow-sm space-y-5 mb-6">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                            <h2 class="text-base font-bold text-slate-900 dark:text-white">
                                {{ $t('Inbox Notification Alerts') }}
                            </h2>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            {{ $t('Hear an audio chime whenever a new incoming customer message is received.') }}
                        </p>
                    </div>

                    <!-- Enable Sound Toggle -->
                    <div class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-200/60 dark:border-white/5">
                        <div>
                            <div class="text-xs font-bold text-slate-900 dark:text-white">
                                {{ $t('Incoming Chat Chime') }}
                            </div>
                            <div class="text-[11px] text-slate-400 mt-0.5">
                                {{ $t('Play sound when a customer sends a new message in live chat') }}
                            </div>
                        </div>

                        <FormToggleSwitch v-model="form.enable_sound_notification" />
                    </div>

                    <!-- Sound Tone & Volume (if enabled) -->
                    <div v-if="form.enable_sound_notification" class="grid grid-cols-1 sm:grid-cols-2 gap-4 pt-2">
                        <!-- Tone Selector -->
                        <div>
                            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1.5">
                                {{ $t('Alert Tone') }}
                            </label>
                            <select
                                v-model="form.tone"
                                @change="playSound(form.tone)"
                                class="w-full px-4 py-2 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                            >
                                <option v-for="snd in sounds" :key="snd.value || snd" :value="snd.value || snd">
                                    {{ snd.label || snd }}
                                </option>
                            </select>
                        </div>

                        <!-- Volume Slider with Play Button -->
                        <div>
                            <div class="flex items-center justify-between mb-1.5">
                                <label class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300">
                                    {{ $t('Volume') }} ({{ Math.round(form.volume * 100) }}%)
                                </label>
                                <button
                                    type="button"
                                    @click="playSound(form.tone)"
                                    class="text-[11px] font-bold text-primary hover:underline flex items-center gap-1"
                                >
                                    <span>▶ {{ $t('Test Sound') }}</span>
                                </button>
                            </div>
                            <input
                                v-model="form.volume"
                                type="range"
                                min="0"
                                max="1"
                                step="0.05"
                                @change="playSound(form.tone)"
                                class="w-full accent-primary cursor-pointer"
                            />
                        </div>
                    </div>
                </div>

                <!-- Hidden audio player for previewing sounds -->
                <audio ref="audioPlayer"></audio>

                <!-- Action Bar -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-primary text-white text-xs font-bold shadow-md shadow-purple-600/30 hover:bg-primary/90 transition disabled:opacity-50 cursor-pointer"
                    >
                        <svg v-if="form.processing" class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ form.processing ? $t('Saving...') : $t('Save Changes') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </SettingLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import SettingLayout from './Layout.vue';
import FormToggleSwitch from '@/Components/FormToggleSwitch.vue';

const props = defineProps({
    settings: Object,
    timezones: Array,
    modules: Array,
    organization: Object,
    countries: Array,
    sounds: Array,
});

const config = ref(props.settings?.metadata);
const parsedSettings = ref(config.value ? JSON.parse(config.value) : null);
const audioPlayer = ref(null);

function getAddressDetail(value, key) {
    if (!value) return null;
    try {
        const address = JSON.parse(value);
        return address?.[key] ?? null;
    } catch (e) {
        return null;
    }
}

const form = useForm({
    organization_name: props.settings?.name || '',
    address: getAddressDetail(props.settings?.address, 'street') || '',
    city: getAddressDetail(props.settings?.address, 'city') || '',
    state: getAddressDetail(props.settings?.address, 'state') || '',
    zip: getAddressDetail(props.settings?.address, 'zip') || '',
    country: getAddressDetail(props.settings?.address, 'country') || null,
    timezone: parsedSettings.value?.timezone || 'UTC',
    enable_sound_notification: parsedSettings.value?.notifications?.enable_sound ?? false,
    volume: parsedSettings.value?.notifications?.volume ?? 1,
    tone: parsedSettings.value?.notifications?.tone || (props.sounds?.[0]?.value || null),
});

function playSound(fileUrl) {
    if (!fileUrl || !audioPlayer.value) return;
    audioPlayer.value.src = fileUrl;
    audioPlayer.value.volume = form.volume;
    audioPlayer.value.play().catch(() => {});
}

function submitForm() {
    form.put('/profile/organization', {
        preserveScroll: true,
    });
}
</script>