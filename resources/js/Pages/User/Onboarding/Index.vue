<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import EmbeddedSignupBtn from '@/Components/EmbeddedSignupBtn.vue';
import BrandLogo from '@/Components/UI/BrandLogo.vue';

const props = defineProps({
    organization: Object,
    whatsappConnected: Boolean,
    whatsappDetails: Object,
    useCases: Array,
    selectedUseCases: Array,
    availableAddons: Array,
    activeAddons: Array,
    starterTemplates: Array,
    selectedTemplates: Array,
    automationPresets: Object,
    notificationPreferences: Object,
    subscription: Object,
    subscriptionPlans: Array,
    industries: Array,
    currencies: Object,
    embeddedSignupActive: Number,
    appId: String,
    configId: String,
    graphAPIVersion: String,
    currentStep: {
        type: Number,
        default: 1
    },
    completedSteps: {
        type: Array,
        default: () => []
    },
    onboardingStatus: String,
    teamMembersCount: Number,
    contactsCount: Number,
    templatesCount: Number,
    timezones: Array,
});

// Step navigation
const step = ref(props.currentStep || 1);
const completedList = ref([...props.completedSteps]);

const isStepCompleted = (key) => completedList.value.includes(key);

const stepsDefinition = [
    { num: 1, key: 'welcome', label: 'Welcome', desc: 'Get Started' },
    { num: 2, key: 'company', label: 'Company Info', desc: 'Brand & Address' },
    { num: 3, key: 'use_cases', label: 'Business Profile', desc: 'Primary Goals' },
    { num: 4, key: 'whatsapp', label: 'WhatsApp API', desc: 'Cloud API & Number' },
    { num: 5, key: 'addons', label: 'Add-ons & Modules', desc: 'AI & Extensions' },
    { num: 6, key: 'team', label: 'Team Setup', desc: 'Invite Agents' },
    { num: 7, key: 'contacts', label: 'Import Contacts', desc: 'CSV or Manual' },
    { num: 8, key: 'templates', label: 'Message Templates', desc: 'Greetings & Alerts' },
    { num: 9, key: 'automation', label: 'Automations', desc: 'Instant Auto-Replies' },
    { num: 10, key: 'notifications', label: 'Notifications', desc: 'Sounds & Alerts' },
    { num: 11, key: 'subscription', label: 'Plan & Trial', desc: 'Limits & Validity' },
    { num: 12, key: 'review', label: 'Review & Launch', desc: 'Final Verification' },
];

const goToStep = (num) => {
    step.value = num;
    router.post(`/onboarding/step/${num}`, {}, { preserveScroll: true, preserveState: true });
};

// Progress percentage
const progressPercentage = computed(() => {
    return Math.round(((step.value - 1) / 11) * 100);
});

// STEP 1: WELCOME
const welcomeForm = useForm({});
const submitWelcome = () => {
    welcomeForm.post('/onboarding/welcome', {
        preserveScroll: true,
        onSuccess: () => {
            if (!completedList.value.includes('welcome')) completedList.value.push('welcome');
            step.value = 2;
        }
    });
};

// STEP 2: COMPANY INFO
const logoPreview = ref(props.organization.logo ? `/media/${props.organization.logo}` : null);
const companyForm = useForm({
    name: props.organization.name || '',
    legal_name: props.organization.legal_name || props.organization.name || '',
    industry: props.organization.industry || '',
    timezone: props.organization.timezone || 'Asia/Kolkata',
    currency: props.organization.currency || 'USD',
    support_email: props.organization.support_email || '',
    support_phone: props.organization.support_phone || '',
    website: props.organization.website || '',
    address: props.organization.address?.street || '',
    city: props.organization.address?.city || '',
    state: props.organization.address?.state || '',
    zip: props.organization.address?.zip || '',
    country: props.organization.address?.country || '',
    logo: null,
    remove_logo: false,
});

const onLogoSelected = (e) => {
    const file = e.target.files[0];
    if (file) {
        companyForm.logo = file;
        companyForm.remove_logo = false;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const removeLogo = () => {
    companyForm.logo = null;
    companyForm.remove_logo = true;
    logoPreview.value = null;
};

const submitCompany = () => {
    companyForm.post('/onboarding/company', {
        preserveScroll: true,
        onSuccess: () => {
            if (!completedList.value.includes('company')) completedList.value.push('company');
            step.value = 3;
        }
    });
};

// STEP 3: BUSINESS PROFILE / USE CASES
const chosenUseCases = ref([...props.selectedUseCases]);
const toggleUseCase = (id) => {
    const idx = chosenUseCases.value.indexOf(id);
    if (idx > -1) {
        chosenUseCases.value.splice(idx, 1);
    } else {
        chosenUseCases.value.push(id);
    }
};

const useCasesForm = useForm({
    use_cases: [],
});

const submitUseCases = () => {
    useCasesForm.use_cases = chosenUseCases.value;
    useCasesForm.post('/onboarding/use-cases', {
        preserveScroll: true,
        onSuccess: () => {
            if (!completedList.value.includes('use_cases')) completedList.value.push('use_cases');
            step.value = 4;
        }
    });
};

// STEP 4: WHATSAPP SETUP
const whatsappMethod = ref(props.embeddedSignupActive == 1 ? 'embedded' : 'manual');
const whatsappForm = useForm({
    access_token: '',
    phone_number_id: '',
    waba_id: '',
    app_id: '',
    skip: false,
});

const submitWhatsapp = () => {
    whatsappForm.skip = false;
    whatsappForm.post('/onboarding/whatsapp', {
        preserveScroll: true,
        onSuccess: () => {
            if (!completedList.value.includes('whatsapp')) completedList.value.push('whatsapp');
            step.value = 5;
        }
    });
};

const skipWhatsapp = () => {
    whatsappForm.skip = true;
    whatsappForm.post('/onboarding/whatsapp', {
        preserveScroll: true,
        onSuccess: () => {
            if (!completedList.value.includes('whatsapp')) completedList.value.push('whatsapp');
            step.value = 5;
        }
    });
};

// STEP 5: ADD-ONS / MODULES
const chosenAddons = ref([...props.activeAddons]);
const toggleAddon = (name) => {
    const idx = chosenAddons.value.indexOf(name);
    if (idx > -1) {
        chosenAddons.value.splice(idx, 1);
    } else {
        chosenAddons.value.push(name);
    }
};

const addonsForm = useForm({
    addons: [],
});

const submitAddons = () => {
    addonsForm.addons = chosenAddons.value;
    addonsForm.post('/onboarding/addons', {
        preserveScroll: true,
        onSuccess: () => {
            if (!completedList.value.includes('addons')) completedList.value.push('addons');
            step.value = 6;
        }
    });
};

// STEP 6: TEAM SETUP
const teamForm = useForm({
    invites: [
        { email: '', role: 'agent' }
    ],
    skip: false,
});

const addTeamRow = () => {
    teamForm.invites.push({ email: '', role: 'agent' });
};

const removeTeamRow = (index) => {
    if (teamForm.invites.length > 1) {
        teamForm.invites.splice(index, 1);
    } else {
        teamForm.invites[0].email = '';
    }
};

const submitTeam = () => {
    teamForm.skip = false;
    teamForm.invites = teamForm.invites.filter(i => i.email && i.email.trim() !== '');
    if (teamForm.invites.length === 0) {
        skipTeam();
        return;
    }
    teamForm.post('/onboarding/team', {
        preserveScroll: true,
        onSuccess: () => {
            if (!completedList.value.includes('team')) completedList.value.push('team');
            step.value = 7;
        }
    });
};

const skipTeam = () => {
    teamForm.skip = true;
    teamForm.post('/onboarding/team', {
        preserveScroll: true,
        onSuccess: () => {
            if (!completedList.value.includes('team')) completedList.value.push('team');
            step.value = 7;
        }
    });
};

// STEP 7: IMPORT CONTACTS
const contactMode = ref('manual');
const contactsForm = useForm({
    file: null,
    test_name: '',
    test_phone: '',
    skip: false,
});

const onContactFileChange = (e) => {
    contactsForm.file = e.target.files[0];
};

const submitContacts = () => {
    contactsForm.skip = false;
    contactsForm.post('/onboarding/contacts', {
        preserveScroll: true,
        onSuccess: () => {
            if (!completedList.value.includes('contacts')) completedList.value.push('contacts');
            step.value = 8;
        }
    });
};

const skipContacts = () => {
    contactsForm.skip = true;
    contactsForm.post('/onboarding/contacts', {
        preserveScroll: true,
        onSuccess: () => {
            if (!completedList.value.includes('contacts')) completedList.value.push('contacts');
            step.value = 8;
        }
    });
};

// STEP 8: STARTER TEMPLATES
const chosenTemplates = ref([...props.selectedTemplates]);
const toggleTemplate = (id) => {
    const idx = chosenTemplates.value.indexOf(id);
    if (idx > -1) {
        chosenTemplates.value.splice(idx, 1);
    } else {
        chosenTemplates.value.push(id);
    }
};

const templatesForm = useForm({
    templates: [],
    skip: false,
});

const submitTemplates = () => {
    templatesForm.skip = false;
    templatesForm.templates = chosenTemplates.value;
    templatesForm.post('/onboarding/templates', {
        preserveScroll: true,
        onSuccess: () => {
            if (!completedList.value.includes('templates')) completedList.value.push('templates');
            step.value = 9;
        }
    });
};

const skipTemplates = () => {
    templatesForm.skip = true;
    templatesForm.post('/onboarding/templates', {
        preserveScroll: true,
        onSuccess: () => {
            if (!completedList.value.includes('templates')) completedList.value.push('templates');
            step.value = 9;
        }
    });
};

// STEP 9: AUTOMATION
const automationForm = useForm({
    welcome_bot: props.automationPresets?.welcome_bot ?? true,
    support_bot: props.automationPresets?.support_bot ?? true,
    skip: false,
});

const submitAutomation = () => {
    automationForm.skip = false;
    automationForm.post('/onboarding/automation', {
        preserveScroll: true,
        onSuccess: () => {
            if (!completedList.value.includes('automation')) completedList.value.push('automation');
            step.value = 10;
        }
    });
};

const skipAutomation = () => {
    automationForm.skip = true;
    automationForm.post('/onboarding/automation', {
        preserveScroll: true,
        onSuccess: () => {
            if (!completedList.value.includes('automation')) completedList.value.push('automation');
            step.value = 10;
        }
    });
};

// STEP 10: NOTIFICATIONS
const notificationsForm = useForm({
    enable_sound: props.notificationPreferences?.enable_sound ?? true,
    tone: props.notificationPreferences?.tone || 'bell',
    volume: props.notificationPreferences?.volume || 80,
    email_inbound: props.notificationPreferences?.email_inbound ?? true,
    email_assignment: props.notificationPreferences?.email_assignment ?? true,
});

const playTestTone = () => {
    try {
        const audio = new Audio(`/media/sounds/${notificationsForm.tone}.mp3`);
        audio.volume = notificationsForm.volume / 100;
        audio.play().catch(() => {});
    } catch (e) {}
};

const submitNotifications = () => {
    notificationsForm.post('/onboarding/notifications', {
        preserveScroll: true,
        onSuccess: () => {
            if (!completedList.value.includes('notifications')) completedList.value.push('notifications');
            step.value = 11;
        }
    });
};

// STEP 11: SUBSCRIPTION
const subscriptionForm = useForm({});
const submitSubscription = () => {
    subscriptionForm.post('/onboarding/subscription', {
        preserveScroll: true,
        onSuccess: () => {
            if (!completedList.value.includes('subscription')) completedList.value.push('subscription');
            step.value = 12;
        }
    });
};

// STEP 12: COMPLETE & LAUNCH
const completeOnboarding = () => {
    router.post('/onboarding/complete');
};
</script>

<template>
    <Head :title="$t('Workspace Onboarding — Wappiyo')" />

    <div class="min-h-screen bg-slate-50 dark:bg-[#09090b] text-slate-900 dark:text-zinc-100 flex flex-col transition-colors selection:bg-emerald-500 selection:text-white">
        <!-- Top Ambient Glow -->
        <div class="fixed top-0 left-1/2 -translate-x-1/2 w-[900px] h-[350px] bg-gradient-to-b from-emerald-500/15 via-[#022828]/10 to-transparent blur-3xl pointer-events-none -z-10"></div>

        <!-- Sticky Navigation Header -->
        <header class="sticky top-0 z-40 bg-white/85 dark:bg-[#111113]/85 backdrop-blur-md border-b border-slate-200/80 dark:border-zinc-800 px-4 sm:px-8 py-3.5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <Link href="/" class="focus:outline-none flex items-center gap-2.5">
                    <BrandLogo mode="auto" />
                </Link>
                <div class="h-6 w-px bg-slate-200 dark:bg-zinc-800 hidden sm:block"></div>
                <div>
                    <div class="flex items-center gap-2">
                        <span class="text-[11px] px-2 py-0.5 rounded-full font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800/40">
                            {{ $t('Company Onboarding') }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500 dark:text-zinc-400">
                        {{ props.organization.name || $t('Setting up workspace') }}
                    </p>
                </div>
            </div>

            <div class="flex items-center gap-4">
                <div class="hidden sm:flex items-center gap-2 text-xs text-slate-500 dark:text-zinc-400">
                    <span>{{ $t('Step') }} {{ step }} {{ $t('of') }} 12</span>
                    <span class="inline-block w-1 h-1 rounded-full bg-slate-300 dark:bg-zinc-700"></span>
                    <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ progressPercentage }}% {{ $t('Complete') }}</span>
                </div>
                <Link
                    href="/dashboard"
                    class="text-xs font-semibold px-3.5 py-1.5 rounded-xl border border-slate-200 dark:border-zinc-700 text-slate-600 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800 transition cursor-pointer"
                >
                    {{ $t('Save & Exit') }}
                </Link>
            </div>
        </header>

        <!-- Top Thin Progress Bar -->
        <div class="w-full bg-slate-200/80 dark:bg-zinc-800 h-1">
            <div 
                class="bg-gradient-to-r from-emerald-600 via-teal-500 to-emerald-400 h-1 transition-all duration-500 ease-out"
                :style="{ width: `${progressPercentage}%` }"
            ></div>
        </div>

        <!-- 2-COLUMN DEDICATED ONBOARDING SHELL -->
        <div class="flex-1 max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 py-6 sm:py-10 grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            
            <!-- LEFT COLUMN: PROGRESS SIDEBAR (DESKTOP) -->
            <aside class="hidden lg:block lg:col-span-4 bg-white dark:bg-[#111113] rounded-2xl border border-slate-200/80 dark:border-zinc-800 p-5 shadow-card sticky top-24 space-y-4">
                <div class="pb-3 border-b border-slate-100 dark:border-zinc-800">
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                        {{ $t('Setup Journey') }}
                    </p>
                    <h3 class="text-sm font-extrabold text-slate-900 dark:text-white mt-0.5">
                        {{ props.organization.name }}
                    </h3>
                </div>

                <nav class="space-y-1 max-h-[calc(100vh-250px)] overflow-y-auto pr-1">
                    <button
                        v-for="s in stepsDefinition"
                        :key="s.num"
                        type="button"
                        @click="goToStep(s.num)"
                        :class="[
                            'w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-left transition-all cursor-pointer',
                            step === s.num
                                ? 'bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-500/40 text-emerald-900 dark:text-emerald-300 font-bold shadow-sm'
                                : isStepCompleted(s.key)
                                    ? 'hover:bg-slate-50 dark:hover:bg-zinc-800/60 text-slate-700 dark:text-zinc-300'
                                    : 'opacity-60 hover:opacity-100 text-slate-500 dark:text-zinc-400'
                        ]"
                    >
                        <!-- Step Badge -->
                        <div
                            :class="[
                                'w-6 h-6 rounded-lg flex items-center justify-center text-[11px] font-bold shrink-0 transition-colors',
                                step === s.num
                                    ? 'bg-emerald-600 text-white'
                                    : isStepCompleted(s.key)
                                        ? 'bg-emerald-500 text-white'
                                        : 'bg-slate-100 dark:bg-zinc-800 text-slate-500 dark:text-zinc-400'
                            ]"
                        >
                            <svg v-if="isStepCompleted(s.key) && step !== s.num" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" />
                            </svg>
                            <span v-else>{{ s.num }}</span>
                        </div>

                        <!-- Step Info -->
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-semibold truncate leading-tight">
                                {{ $t(s.label) }}
                            </p>
                            <p class="text-[10px] text-slate-400 dark:text-zinc-500 truncate">
                                {{ $t(s.desc) }}
                            </p>
                        </div>
                    </button>
                </nav>
            </aside>

            <!-- RIGHT COLUMN: CURRENT STEP CONTENT CARD -->
            <main class="lg:col-span-8 bg-white dark:bg-[#111113] rounded-2xl border border-slate-200/80 dark:border-zinc-800 shadow-xl shadow-slate-900/5 p-6 sm:p-10 transition-all">
                
                <!-- ============================================== -->
                <!-- STEP 1: WELCOME & OVERVIEW                     -->
                <!-- ============================================== -->
                <div v-if="step === 1" class="space-y-8 animate-fadeIn">
                    <div class="space-y-3">
                        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-xs font-bold text-emerald-700 dark:text-emerald-300">
                            <span>Step 1 of 12</span>
                            <span>•</span>
                            <span>{{ $t('Setup Overview') }}</span>
                        </div>
                        <h2 class="text-3xl font-black tracking-tight text-slate-900 dark:text-white">
                            {{ $t('Welcome to Wappiyo') }} 👋
                        </h2>
                        <p class="text-sm text-slate-600 dark:text-zinc-400 leading-relaxed max-w-2xl">
                            {{ $t('Let’s set up your new workspace for') }} <span class="font-bold text-slate-900 dark:text-white">{{ props.organization.name }}</span>. {{ $t('We will guide you through connecting your WhatsApp Cloud API, choosing add-ons, importing contacts, and configuring instant automations. It only takes about 3 minutes.') }}
                        </p>
                    </div>

                    <!-- Setup Pillars Grid -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-900/40 flex items-start gap-3.5">
                            <div class="w-8 h-8 rounded-lg bg-purple-500/10 text-purple-600 flex items-center justify-center shrink-0">
                                💬
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white">{{ $t('WhatsApp Cloud API') }}</h4>
                                <p class="text-[11px] text-slate-500 dark:text-zinc-400">{{ $t('Link your official Meta business number.') }}</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-900/40 flex items-start gap-3.5">
                            <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-600 flex items-center justify-center shrink-0">
                                ⚡
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white">{{ $t('Automations & AI') }}</h4>
                                <p class="text-[11px] text-slate-500 dark:text-zinc-400">{{ $t('Activate instant greetings and AI copilot.') }}</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-900/40 flex items-start gap-3.5">
                            <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-600 flex items-center justify-center shrink-0">
                                👥
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white">{{ $t('Team & Contacts') }}</h4>
                                <p class="text-[11px] text-slate-500 dark:text-zinc-400">{{ $t('Invite your support agents and upload lists.') }}</p>
                            </div>
                        </div>

                        <div class="p-4 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-900/40 flex items-start gap-3.5">
                            <div class="w-8 h-8 rounded-lg bg-amber-500/10 text-amber-600 flex items-center justify-center shrink-0">
                                📊
                            </div>
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white">{{ $t('Broadcasts & Templates') }}</h4>
                                <p class="text-[11px] text-slate-500 dark:text-zinc-400">{{ $t('Launch high-converting messaging templates.') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100 dark:border-zinc-800 flex justify-end">
                        <button
                            type="button"
                            @click="submitWelcome"
                            class="inline-flex items-center gap-2 px-7 py-3 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-lg shadow-purple-600/30 transition cursor-pointer"
                        >
                            <span>{{ $t('Get Started') }}</span>
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- STEP 2: COMPANY INFORMATION                    -->
                <!-- ============================================== -->
                <div v-else-if="step === 2" class="space-y-6 animate-fadeIn">
                    <div class="space-y-1 border-b border-slate-100 dark:border-zinc-800 pb-4">
                        <span class="text-xs font-bold text-purple-600 dark:text-purple-400">Step 2 of 12</span>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white">{{ $t('Company & Brand Information') }}</h2>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $t('Enter your official business profile, logo, and timezone.') }}</p>
                    </div>

                    <form @submit.prevent="submitCompany" class="space-y-5">
                        <!-- Logo Upload -->
                        <div class="flex items-center gap-5 p-4 rounded-xl bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800">
                            <div class="w-16 h-16 rounded-xl border-2 border-dashed border-slate-300 dark:border-zinc-700 flex items-center justify-center overflow-hidden bg-white dark:bg-zinc-800 shrink-0">
                                <img v-if="logoPreview" :src="logoPreview" alt="Logo" class="w-full h-full object-contain" />
                                <span v-else class="text-xs text-slate-400 font-bold">LOGO</span>
                            </div>
                            <div class="space-y-1.5">
                                <p class="text-xs font-bold text-slate-800 dark:text-zinc-200">{{ $t('Workspace Logo') }}</p>
                                <div class="flex items-center gap-3">
                                    <label class="px-3 py-1.5 rounded-lg border border-slate-300 dark:border-zinc-700 text-xs font-semibold hover:bg-slate-100 dark:hover:bg-zinc-800 cursor-pointer transition">
                                        {{ $t('Upload Image') }}
                                        <input type="file" accept="image/*" class="hidden" @change="onLogoSelected" />
                                    </label>
                                    <button v-if="logoPreview" type="button" @click="removeLogo" class="text-xs text-rose-500 hover:underline">
                                        {{ $t('Remove') }}
                                    </button>
                                </div>
                                <p class="text-[10px] text-slate-400">PNG, JPG up to 5MB.</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-zinc-300 mb-1">{{ $t('Company Name') }} *</label>
                                <input v-model="companyForm.name" type="text" required class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800/50 text-xs focus:ring-2 focus:ring-purple-500" />
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-zinc-300 mb-1">{{ $t('Legal Entity Name') }}</label>
                                <input v-model="companyForm.legal_name" type="text" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800/50 text-xs focus:ring-2 focus:ring-purple-500" />
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-zinc-300 mb-1">{{ $t('Industry') }}</label>
                                <select v-model="companyForm.industry" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800/50 text-xs focus:ring-2 focus:ring-purple-500">
                                    <option value="">{{ $t('Select Industry') }}</option>
                                    <option v-for="ind in props.industries" :key="ind" :value="ind">{{ ind }}</option>
                                </select>
                            </div>

                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-bold text-slate-700 dark:text-zinc-300">{{ $t('Primary Timezone') }}</label>
                                    <span class="text-[10px] font-semibold px-2 py-0.5 rounded-full bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        {{ $t('Default: India Standard Time (UTC+05:30)') }}
                                    </span>
                                </div>
                                <select
                                    v-model="companyForm.timezone"
                                    class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800/50 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500"
                                >
                                    <option v-for="tz in props.timezones" :key="tz.value || tz" :value="tz.value || tz">
                                        {{ tz.label || tz }}
                                    </option>
                                </select>
                                <p class="text-[11px] text-slate-400 mt-1">
                                    {{ $t('Broadcasts, scheduled automations, and analytics are calculated in this timezone.') }}
                                </p>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-zinc-300 mb-1">{{ $t('Default Currency') }}</label>
                                <select v-model="companyForm.currency" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800/50 text-xs focus:ring-2 focus:ring-purple-500">
                                    <option v-for="(label, code) in props.currencies" :key="code" :value="code">{{ label }}</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-zinc-300 mb-1">{{ $t('Website') }}</label>
                                <input v-model="companyForm.website" type="url" placeholder="https://example.com" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800/50 text-xs focus:ring-2 focus:ring-purple-500" />
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-zinc-300 mb-1">{{ $t('Support Email') }}</label>
                                <input v-model="companyForm.support_email" type="email" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800/50 text-xs focus:ring-2 focus:ring-purple-500" />
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-zinc-300 mb-1">{{ $t('Support Phone') }}</label>
                                <input v-model="companyForm.support_phone" type="text" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800/50 text-xs focus:ring-2 focus:ring-purple-500" />
                            </div>
                        </div>

                        <div class="pt-4 border-t border-slate-100 dark:border-zinc-800 flex justify-between items-center">
                            <button type="button" @click="step = 1" class="text-xs text-slate-500 hover:text-slate-800">← {{ $t('Back') }}</button>
                            <button type="submit" :disabled="companyForm.processing" class="px-6 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-md cursor-pointer">
                                {{ $t('Save & Continue') }} →
                            </button>
                        </div>
                    </form>
                </div>

                <!-- ============================================== -->
                <!-- STEP 3: BUSINESS PROFILE & USE CASES           -->
                <!-- ============================================== -->
                <div v-else-if="step === 3" class="space-y-6 animate-fadeIn">
                    <div class="space-y-1 border-b border-slate-100 dark:border-zinc-800 pb-4">
                        <span class="text-xs font-bold text-purple-600 dark:text-purple-400">Step 3 of 12</span>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white">{{ $t('What are you using Wappiyo for?') }}</h2>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $t('Select all applicable business goals to personalize your workspace.') }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div
                            v-for="uc in props.useCases"
                            :key="uc.id"
                            @click="toggleUseCase(uc.id)"
                            :class="[
                                'p-4 rounded-xl border transition-all cursor-pointer flex items-start gap-3',
                                chosenUseCases.includes(uc.id)
                                    ? 'bg-purple-50/60 dark:bg-purple-950/30 border-purple-500 text-purple-950 dark:text-purple-200'
                                    : 'bg-slate-50/40 dark:bg-zinc-900/40 border-slate-200 dark:border-zinc-800 opacity-80 hover:opacity-100'
                            ]"
                        >
                            <div
                                :class="[
                                    'w-5 h-5 rounded-md flex items-center justify-center text-xs shrink-0 mt-0.5',
                                    chosenUseCases.includes(uc.id)
                                        ? 'bg-purple-600 text-white'
                                        : 'border border-slate-300 dark:border-zinc-600'
                                ]"
                            >
                                <svg v-if="chosenUseCases.includes(uc.id)" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="space-y-0.5">
                                <h4 class="text-xs font-bold">{{ uc.title }}</h4>
                                <p class="text-[11px] text-slate-500 dark:text-zinc-400">{{ uc.desc }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-zinc-800 flex justify-between items-center">
                        <button type="button" @click="step = 2" class="text-xs text-slate-500 hover:text-slate-800">← {{ $t('Back') }}</button>
                        <button type="button" @click="submitUseCases" class="px-6 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-md cursor-pointer">
                            {{ $t('Continue to WhatsApp Setup') }} →
                        </button>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- STEP 4: WHATSAPP SETUP                         -->
                <!-- ============================================== -->
                <div v-else-if="step === 4" class="space-y-6 animate-fadeIn">
                    <div class="space-y-1 border-b border-slate-100 dark:border-zinc-800 pb-4">
                        <span class="text-xs font-bold text-purple-600 dark:text-purple-400">Step 4 of 12</span>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white">{{ $t('WhatsApp Business Cloud API') }}</h2>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $t('Connect your official Meta phone number.') }}</p>
                    </div>

                    <!-- Connected Banner -->
                    <div v-if="props.whatsappConnected" class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/30 border border-emerald-200 dark:border-emerald-800/40 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500 text-white flex items-center justify-center shrink-0">✓</div>
                        <div class="min-w-0 flex-1">
                            <p class="text-xs font-bold text-emerald-900 dark:text-emerald-300">{{ $t('WhatsApp Account Connected!') }}</p>
                            <p class="text-[11px] text-emerald-700 dark:text-emerald-400 truncate">
                                {{ props.whatsappDetails.display_phone_number || props.whatsappDetails.phone_number_id }}
                                <span v-if="props.whatsappDetails.verified_name"> ({{ props.whatsappDetails.verified_name }})</span>
                            </p>
                        </div>
                    </div>

                    <!-- Method Tabs -->
                    <div class="flex items-center gap-2 border-b border-slate-200 dark:border-zinc-800 pb-2">
                        <button
                            v-if="props.embeddedSignupActive == 1"
                            type="button"
                            @click="whatsappMethod = 'embedded'"
                            :class="['px-3 py-1.5 text-xs font-bold rounded-lg cursor-pointer', whatsappMethod === 'embedded' ? 'bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300' : 'text-slate-500']"
                        >
                            {{ $t('1-Click Embedded Signup') }}
                        </button>
                        <button
                            type="button"
                            @click="whatsappMethod = 'manual'"
                            :class="['px-3 py-1.5 text-xs font-bold rounded-lg cursor-pointer', whatsappMethod === 'manual' ? 'bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300' : 'text-slate-500']"
                        >
                            {{ $t('Manual API Credentials') }}
                        </button>
                    </div>

                    <!-- Embedded Signup -->
                    <div v-if="whatsappMethod === 'embedded' && props.embeddedSignupActive == 1" class="p-5 rounded-xl bg-purple-50/50 dark:bg-purple-950/20 border border-purple-200 dark:border-purple-800/40 space-y-3">
                        <h4 class="text-xs font-bold text-purple-900 dark:text-purple-300">{{ $t('Login with Meta Facebook Account') }}</h4>
                        <p class="text-xs text-slate-600 dark:text-zinc-400 leading-relaxed">{{ $t('Select your WhatsApp Business Account and phone number in the Meta popup.') }}</p>
                        <div class="pt-2">
                            <EmbeddedSignupBtn :appId="props.appId" :configId="props.configId" :graphAPIVersion="props.graphAPIVersion" />
                        </div>
                    </div>

                    <!-- Manual Credentials -->
                    <form v-else @submit.prevent="submitWhatsapp" class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-zinc-300 mb-1">{{ $t('System User Access Token') }} *</label>
                            <input v-model="whatsappForm.access_token" type="password" required placeholder="EAA..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800/50 text-xs focus:ring-2 focus:ring-purple-500" />
                        </div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-zinc-300 mb-1">{{ $t('Phone Number ID') }} *</label>
                                <input v-model="whatsappForm.phone_number_id" type="text" required placeholder="1029384756..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800/50 text-xs focus:ring-2 focus:ring-purple-500" />
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-zinc-300 mb-1">{{ $t('WABA ID') }} *</label>
                                <input v-model="whatsappForm.waba_id" type="text" required placeholder="9876543210..." class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800/50 text-xs focus:ring-2 focus:ring-purple-500" />
                            </div>
                        </div>
                        <div class="flex justify-end">
                            <button type="submit" :disabled="whatsappForm.processing" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold cursor-pointer">
                                {{ $t('Verify & Save') }}
                            </button>
                        </div>
                    </form>

                    <div class="pt-4 border-t border-slate-100 dark:border-zinc-800 flex justify-between items-center">
                        <button type="button" @click="step = 3" class="text-xs text-slate-500 hover:text-slate-800">← {{ $t('Back') }}</button>
                        <div class="flex items-center gap-3">
                            <button type="button" @click="skipWhatsapp" class="text-xs text-slate-500 hover:text-slate-700">
                                {{ $t('Skip for now & connect later') }}
                            </button>
                            <button type="button" @click="step = 5" class="px-6 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-md cursor-pointer">
                                {{ $t('Continue') }} →
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- STEP 5: ADD-ONS & MODULES                      -->
                <!-- ============================================== -->
                <div v-else-if="step === 5" class="space-y-6 animate-fadeIn">
                    <div class="space-y-1 border-b border-slate-100 dark:border-zinc-800 pb-4">
                        <span class="text-xs font-bold text-purple-600 dark:text-purple-400">Step 5 of 12</span>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white">{{ $t('Customize Workspace Modules') }}</h2>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $t('Enable the features and add-ons you want active for your team.') }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3.5">
                        <div
                            v-for="addon in props.availableAddons"
                            :key="addon.id"
                            @click="toggleAddon(addon.name)"
                            :class="[
                                'p-4 rounded-xl border transition-all cursor-pointer flex items-start gap-3',
                                chosenAddons.includes(addon.name)
                                    ? 'bg-purple-50/60 dark:bg-purple-950/30 border-purple-500 text-purple-950 dark:text-purple-200'
                                    : 'bg-slate-50/40 dark:bg-zinc-900/40 border-slate-200 dark:border-zinc-800 opacity-80 hover:opacity-100'
                            ]"
                        >
                            <div
                                :class="[
                                    'w-5 h-5 rounded-md flex items-center justify-center text-xs shrink-0 mt-0.5',
                                    chosenAddons.includes(addon.name)
                                        ? 'bg-purple-600 text-white'
                                        : 'border border-slate-300 dark:border-zinc-600'
                                ]"
                            >
                                <svg v-if="chosenAddons.includes(addon.name)" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="space-y-0.5">
                                <div class="flex items-center gap-2">
                                    <h4 class="text-xs font-bold">{{ addon.name }}</h4>
                                    <span class="text-[9px] px-1.5 py-0.5 rounded bg-slate-200 dark:bg-zinc-700 uppercase font-semibold">{{ addon.category }}</span>
                                </div>
                                <p class="text-[11px] text-slate-500 dark:text-zinc-400">{{ addon.description }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-zinc-800 flex justify-between items-center">
                        <button type="button" @click="step = 4" class="text-xs text-slate-500 hover:text-slate-800">← {{ $t('Back') }}</button>
                        <button type="button" @click="submitAddons" class="px-6 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-md cursor-pointer">
                            {{ $t('Save Add-ons & Continue') }} →
                        </button>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- STEP 6: TEAM SETUP                             -->
                <!-- ============================================== -->
                <div v-else-if="step === 6" class="space-y-6 animate-fadeIn">
                    <div class="space-y-1 border-b border-slate-100 dark:border-zinc-800 pb-4">
                        <span class="text-xs font-bold text-purple-600 dark:text-purple-400">Step 6 of 12</span>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white">{{ $t('Invite Team Members') }}</h2>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $t('Add agents and teammates to your shared WhatsApp inbox.') }}</p>
                    </div>

                    <div class="space-y-3">
                        <div v-for="(invite, idx) in teamForm.invites" :key="idx" class="flex items-center gap-3">
                            <input v-model="invite.email" type="email" placeholder="colleague@company.com" class="flex-1 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800/50 text-xs focus:ring-2 focus:ring-purple-500" />
                            <select v-model="invite.role" class="w-32 px-3 py-2 rounded-xl border border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800/50 text-xs focus:ring-2 focus:ring-purple-500">
                                <option value="agent">{{ $t('Agent') }}</option>
                                <option value="manager">{{ $t('Manager') }}</option>
                                <option value="admin">{{ $t('Admin') }}</option>
                            </select>
                            <button type="button" @click="removeTeamRow(idx)" class="p-2 text-slate-400 hover:text-rose-500">✕</button>
                        </div>
                        <button type="button" @click="addTeamRow" class="text-xs font-bold text-purple-600 hover:underline">
                            + {{ $t('Add Another Team Member') }}
                        </button>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-zinc-800 flex justify-between items-center">
                        <button type="button" @click="step = 5" class="text-xs text-slate-500 hover:text-slate-800">← {{ $t('Back') }}</button>
                        <div class="flex items-center gap-3">
                            <button type="button" @click="skipTeam" class="text-xs text-slate-500 hover:text-slate-700">{{ $t('Skip for now') }}</button>
                            <button type="button" @click="submitTeam" :disabled="teamForm.processing" class="px-6 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-md cursor-pointer">
                                {{ $t('Send Invites & Continue') }} →
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- STEP 7: IMPORT CONTACTS                        -->
                <!-- ============================================== -->
                <div v-else-if="step === 7" class="space-y-6 animate-fadeIn">
                    <div class="space-y-1 border-b border-slate-100 dark:border-zinc-800 pb-4">
                        <span class="text-xs font-bold text-purple-600 dark:text-purple-400">Step 7 of 12</span>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white">{{ $t('Import Contacts') }}</h2>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $t('Upload your customer list via CSV or add a test contact.') }}</p>
                    </div>

                    <div class="flex items-center gap-2 border-b border-slate-200 dark:border-zinc-800 pb-2">
                        <button type="button" @click="contactMode = 'manual'" :class="['px-3 py-1.5 text-xs font-bold rounded-lg cursor-pointer', contactMode === 'manual' ? 'bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300' : 'text-slate-500']">
                            {{ $t('Add Test Contact') }}
                        </button>
                        <button type="button" @click="contactMode = 'csv'" :class="['px-3 py-1.5 text-xs font-bold rounded-lg cursor-pointer', contactMode === 'csv' ? 'bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300' : 'text-slate-500']">
                            {{ $t('Upload CSV / Excel') }}
                        </button>
                    </div>

                    <div v-if="contactMode === 'manual'" class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-xl bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800">
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-zinc-300 mb-1">{{ $t('Contact Name') }}</label>
                            <input v-model="contactsForm.test_name" type="text" placeholder="Alex Morgan" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-xs" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-700 dark:text-zinc-300 mb-1">{{ $t('Phone Number (with country code)') }}</label>
                            <input v-model="contactsForm.test_phone" type="text" placeholder="+1 555 123 4567" class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-xs" />
                        </div>
                    </div>

                    <div v-else class="p-6 rounded-xl border-2 border-dashed border-slate-300 dark:border-zinc-700 text-center space-y-2">
                        <input type="file" accept=".csv, .xlsx, .xls" class="hidden" id="csv-upload" @change="onContactFileChange" />
                        <label for="csv-upload" class="inline-flex px-4 py-2 rounded-xl bg-purple-50 dark:bg-purple-950 text-purple-700 dark:text-purple-300 text-xs font-bold border border-purple-200 dark:border-purple-800 cursor-pointer hover:bg-purple-100">
                            {{ contactsForm.file ? contactsForm.file.name : $t('Select CSV or Excel File') }}
                        </label>
                        <p class="text-[11px] text-slate-400">{{ $t('Columns: First Name, Last Name, Phone Number, Email') }}</p>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-zinc-800 flex justify-between items-center">
                        <button type="button" @click="step = 6" class="text-xs text-slate-500 hover:text-slate-800">← {{ $t('Back') }}</button>
                        <div class="flex items-center gap-3">
                            <button type="button" @click="skipContacts" class="text-xs text-slate-500 hover:text-slate-700">{{ $t('Skip for now') }}</button>
                            <button type="button" @click="submitContacts" :disabled="contactsForm.processing" class="px-6 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-md cursor-pointer">
                                {{ $t('Save Contacts & Continue') }} →
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- STEP 8: MESSAGE TEMPLATES                      -->
                <!-- ============================================== -->
                <div v-else-if="step === 8" class="space-y-6 animate-fadeIn">
                    <div class="space-y-1 border-b border-slate-100 dark:border-zinc-800 pb-4">
                        <span class="text-xs font-bold text-purple-600 dark:text-purple-400">Step 8 of 12</span>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white">{{ $t('Starter WhatsApp Templates') }}</h2>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $t('Install verified template blueprints for your customer conversations.') }}</p>
                    </div>

                    <div class="space-y-3">
                        <div
                            v-for="tpl in props.starterTemplates"
                            :key="tpl.id"
                            @click="toggleTemplate(tpl.id)"
                            :class="[
                                'p-4 rounded-xl border transition-all cursor-pointer flex items-start gap-3',
                                chosenTemplates.includes(tpl.id)
                                    ? 'bg-purple-50/60 dark:bg-purple-950/30 border-purple-500 text-purple-950 dark:text-purple-200'
                                    : 'bg-slate-50/40 dark:bg-zinc-900/40 border-slate-200 dark:border-zinc-800 opacity-80 hover:opacity-100'
                            ]"
                        >
                            <div
                                :class="[
                                    'w-5 h-5 rounded-md flex items-center justify-center text-xs shrink-0 mt-0.5',
                                    chosenTemplates.includes(tpl.id)
                                        ? 'bg-purple-600 text-white'
                                        : 'border border-slate-300 dark:border-zinc-600'
                                ]"
                            >
                                <svg v-if="chosenTemplates.includes(tpl.id)" class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="space-y-1">
                                <h4 class="text-xs font-bold">{{ tpl.title }}</h4>
                                <p class="text-[11px] text-slate-500 dark:text-zinc-400">{{ tpl.description }}</p>
                                <div class="p-2.5 bg-white dark:bg-zinc-800 rounded-lg border border-slate-200 dark:border-zinc-700 text-[11px] font-mono">
                                    "{{ tpl.preview }}"
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-zinc-800 flex justify-between items-center">
                        <button type="button" @click="step = 7" class="text-xs text-slate-500 hover:text-slate-800">← {{ $t('Back') }}</button>
                        <div class="flex items-center gap-3">
                            <button type="button" @click="skipTemplates" class="text-xs text-slate-500 hover:text-slate-700">{{ $t('Skip for now') }}</button>
                            <button type="button" @click="submitTemplates" :disabled="templatesForm.processing" class="px-6 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-md cursor-pointer">
                                {{ $t('Install & Continue') }} →
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- STEP 9: AUTOMATION SETUP                       -->
                <!-- ============================================== -->
                <div v-else-if="step === 9" class="space-y-6 animate-fadeIn">
                    <div class="space-y-1 border-b border-slate-100 dark:border-zinc-800 pb-4">
                        <span class="text-xs font-bold text-purple-600 dark:text-purple-400">Step 9 of 12</span>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white">{{ $t('Conversational Automation') }}</h2>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $t('Automate common customer inquiries with instant auto-replies.') }}</p>
                    </div>

                    <div class="space-y-4">
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-900/40 flex items-start justify-between gap-4">
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white">{{ $t('Instant Welcome Greeting') }}</h4>
                                <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5">{{ $t('Automatically greet incoming customers saying "Hi", "Hello", or "Hey".') }}</p>
                            </div>
                            <input v-model="automationForm.welcome_bot" type="checkbox" class="w-4 h-4 rounded text-purple-600 focus:ring-purple-500" />
                        </div>

                        <div class="p-4 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-900/40 flex items-start justify-between gap-4">
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white">{{ $t('Support Auto-Responder') }}</h4>
                                <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5">{{ $t('Acknowledge support requests with an instant confirmation response.') }}</p>
                            </div>
                            <input v-model="automationForm.support_bot" type="checkbox" class="w-4 h-4 rounded text-purple-600 focus:ring-purple-500" />
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-zinc-800 flex justify-between items-center">
                        <button type="button" @click="step = 8" class="text-xs text-slate-500 hover:text-slate-800">← {{ $t('Back') }}</button>
                        <div class="flex items-center gap-3">
                            <button type="button" @click="skipAutomation" class="text-xs text-slate-500 hover:text-slate-700">{{ $t('Skip for now') }}</button>
                            <button type="button" @click="submitAutomation" class="px-6 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-md cursor-pointer">
                                {{ $t('Save Automations & Continue') }} →
                            </button>
                        </div>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- STEP 10: NOTIFICATION PREFERENCES             -->
                <!-- ============================================== -->
                <div v-else-if="step === 10" class="space-y-6 animate-fadeIn">
                    <div class="space-y-1 border-b border-slate-100 dark:border-zinc-800 pb-4">
                        <span class="text-xs font-bold text-purple-600 dark:text-purple-400">Step 10 of 12</span>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white">{{ $t('Sound & Notification Preferences') }}</h2>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $t('Configure audio chimes and email alerts for inbound conversations.') }}</p>
                    </div>

                    <div class="space-y-4">
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-900/40 flex items-center justify-between gap-4">
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white">{{ $t('Enable Sound Notifications') }}</h4>
                                <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5">{{ $t('Play audio chime when a new message arrives.') }}</p>
                            </div>
                            <input v-model="notificationsForm.enable_sound" type="checkbox" class="w-4 h-4 rounded text-purple-600 focus:ring-purple-500" />
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-zinc-300 mb-1">{{ $t('Notification Tone') }}</label>
                                <div class="flex items-center gap-2">
                                    <select v-model="notificationsForm.tone" class="flex-1 px-3.5 py-2 rounded-xl border border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800/50 text-xs">
                                        <option value="bell">{{ $t('Classic Bell') }}</option>
                                        <option value="chime">{{ $t('Modern Chime') }}</option>
                                        <option value="pop">{{ $t('Subtle Pop') }}</option>
                                    </select>
                                    <button type="button" @click="playTestTone" class="px-3 py-2 rounded-xl border border-slate-300 dark:border-zinc-700 text-xs font-bold hover:bg-slate-100">
                                        🔊
                                    </button>
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-slate-700 dark:text-zinc-300 mb-1">
                                    {{ $t('Volume') }} ({{ notificationsForm.volume }}%)
                                </label>
                                <input v-model.number="notificationsForm.volume" type="range" min="0" max="100" class="w-full mt-2 accent-purple-600" />
                            </div>
                        </div>

                        <div class="p-4 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-900/40 flex items-center justify-between gap-4">
                            <div>
                                <h4 class="text-xs font-bold text-slate-900 dark:text-white">{{ $t('Email Alerts on Inbound Chats') }}</h4>
                                <p class="text-[11px] text-slate-500 dark:text-zinc-400 mt-0.5">{{ $t('Notify agents via email when tickets are assigned.') }}</p>
                            </div>
                            <input v-model="notificationsForm.email_inbound" type="checkbox" class="w-4 h-4 rounded text-purple-600 focus:ring-purple-500" />
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-zinc-800 flex justify-between items-center">
                        <button type="button" @click="step = 9" class="text-xs text-slate-500 hover:text-slate-800">← {{ $t('Back') }}</button>
                        <button type="button" @click="submitNotifications" class="px-6 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-md cursor-pointer">
                            {{ $t('Save Preferences & Continue') }} →
                        </button>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- STEP 11: SUBSCRIPTION & TRIAL                  -->
                <!-- ============================================== -->
                <div v-else-if="step === 11" class="space-y-6 animate-fadeIn">
                    <div class="space-y-1 border-b border-slate-100 dark:border-zinc-800 pb-4">
                        <span class="text-xs font-bold text-purple-600 dark:text-purple-400">Step 11 of 12</span>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white">{{ $t('Your Workspace Plan & Trial') }}</h2>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $t('Review your active tier and business usage limits.') }}</p>
                    </div>

                    <div class="p-5 rounded-2xl bg-gradient-to-tr from-purple-50 to-indigo-50 dark:from-purple-950/20 dark:to-indigo-950/20 border border-purple-200 dark:border-purple-800/40 space-y-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="text-[10px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400">{{ $t('Active Subscription') }}</span>
                                <h3 class="text-lg font-black text-slate-900 dark:text-white">{{ props.subscription.plan_name }}</h3>
                            </div>
                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-100 dark:bg-emerald-950 text-emerald-700 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-800">
                                {{ props.subscription.status === 'trial' ? `${props.subscription.trial_days_remaining} Days Trial Remaining` : $t('Active Plan') }}
                            </span>
                        </div>

                        <!-- Limits -->
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 pt-2">
                            <div class="p-3 bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800">
                                <p class="text-[10px] text-slate-400 uppercase font-bold">{{ $t('Campaigns') }}</p>
                                <p class="text-sm font-extrabold text-slate-900 dark:text-white">{{ props.subscription.limits?.campaign_limit || '1,000' }} / mo</p>
                            </div>
                            <div class="p-3 bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800">
                                <p class="text-[10px] text-slate-400 uppercase font-bold">{{ $t('Messages') }}</p>
                                <p class="text-sm font-extrabold text-slate-900 dark:text-white">{{ props.subscription.limits?.message_limit || '50,000' }} / mo</p>
                            </div>
                            <div class="p-3 bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800">
                                <p class="text-[10px] text-slate-400 uppercase font-bold">{{ $t('Contacts') }}</p>
                                <p class="text-sm font-extrabold text-slate-900 dark:text-white">{{ props.subscription.limits?.contacts_limit || '10,000' }}</p>
                            </div>
                            <div class="p-3 bg-white dark:bg-zinc-900 rounded-xl border border-slate-200 dark:border-zinc-800">
                                <p class="text-[10px] text-slate-400 uppercase font-bold">{{ $t('Team Seats') }}</p>
                                <p class="text-sm font-extrabold text-slate-900 dark:text-white">{{ props.subscription.limits?.team_limit || '10' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-100 dark:border-zinc-800 flex justify-between items-center">
                        <button type="button" @click="step = 10" class="text-xs text-slate-500 hover:text-slate-800">← {{ $t('Back') }}</button>
                        <button type="button" @click="submitSubscription" class="px-6 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-md cursor-pointer">
                            {{ $t('Proceed to Final Review') }} →
                        </button>
                    </div>
                </div>

                <!-- ============================================== -->
                <!-- STEP 12: REVIEW & LAUNCH                       -->
                <!-- ============================================== -->
                <div v-else-if="step === 12" class="space-y-6 animate-fadeIn">
                    <div class="space-y-1 border-b border-slate-100 dark:border-zinc-800 pb-4">
                        <span class="text-xs font-bold text-purple-600 dark:text-purple-400">Step 12 of 12</span>
                        <h2 class="text-2xl font-black text-slate-900 dark:text-white">{{ $t('Review Your Configuration') }} 🎉</h2>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $t('Verify your setup summary before launching the workspace.') }}</p>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <!-- Company -->
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-900/40 space-y-1">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold text-slate-700 dark:text-zinc-300">{{ $t('Company Identity') }}</h4>
                                <button type="button" @click="step = 2" class="text-[10px] text-purple-600 font-bold hover:underline">{{ $t('Edit') }}</button>
                            </div>
                            <p class="text-sm font-extrabold text-slate-900 dark:text-white">{{ props.organization.name }}</p>
                            <p class="text-[11px] text-slate-500">{{ props.organization.industry || 'General Business' }} • {{ props.organization.timezone }}</p>
                        </div>

                        <!-- WhatsApp -->
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-900/40 space-y-1">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold text-slate-700 dark:text-zinc-300">{{ $t('WhatsApp Connection') }}</h4>
                                <button type="button" @click="step = 4" class="text-[10px] text-purple-600 font-bold hover:underline">{{ $t('Edit') }}</button>
                            </div>
                            <p class="text-sm font-extrabold" :class="props.whatsappConnected ? 'text-emerald-600' : 'text-amber-500'">
                                {{ props.whatsappConnected ? $t('Connected & Active') : $t('Pending Connection') }}
                            </p>
                            <p class="text-[11px] text-slate-500">{{ props.whatsappDetails.display_phone_number || $t('Configure in Settings') }}</p>
                        </div>

                        <!-- Team & Contacts -->
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-900/40 space-y-1">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold text-slate-700 dark:text-zinc-300">{{ $t('Team & Audience') }}</h4>
                                <button type="button" @click="step = 6" class="text-[10px] text-purple-600 font-bold hover:underline">{{ $t('Edit') }}</button>
                            </div>
                            <p class="text-sm font-extrabold text-slate-900 dark:text-white">
                                {{ props.teamMembersCount }} {{ $t('Team Member(s)') }} • {{ props.contactsCount }} {{ $t('Contact(s)') }}
                            </p>
                            <p class="text-[11px] text-slate-500">{{ $t('Shared team inbox configured') }}</p>
                        </div>

                        <!-- Messaging & Automations -->
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-900/40 space-y-1">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold text-slate-700 dark:text-zinc-300">{{ $t('Templates & Automations') }}</h4>
                                <button type="button" @click="step = 8" class="text-[10px] text-purple-600 font-bold hover:underline">{{ $t('Edit') }}</button>
                            </div>
                            <p class="text-sm font-extrabold text-slate-900 dark:text-white">
                                {{ chosenTemplates.length }} {{ $t('Templates') }} • {{ $t('Auto-Replies Active') }}
                            </p>
                            <p class="text-[11px] text-slate-500">{{ chosenAddons.length }} {{ $t('Add-on modules enabled') }}</p>
                        </div>
                    </div>

                    <!-- Final Action -->
                    <div class="pt-6 border-t border-slate-100 dark:border-zinc-800 flex items-center justify-between">
                        <button type="button" @click="step = 11" class="text-xs text-slate-500 hover:text-slate-800">← {{ $t('Back') }}</button>
                        <button
                            type="button"
                            @click="completeOnboarding"
                            class="inline-flex items-center gap-3 px-8 py-3.5 rounded-2xl bg-gradient-to-r from-purple-600 via-indigo-600 to-emerald-600 hover:opacity-95 text-white text-sm font-extrabold shadow-xl shadow-purple-600/30 transition transform hover:-translate-y-0.5 cursor-pointer"
                        >
                            <span>{{ $t('Complete Onboarding & Launch Workspace') }}</span>
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 7l5 5m0 0l-5 5m5-5H6" />
                            </svg>
                        </button>
                    </div>
                </div>

            </main>
        </div>
    </div>
</template>

<style scoped>
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(6px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
.animate-fadeIn {
    animation: fadeIn 0.25s cubic-bezier(0.16, 1, 0.3, 1) forwards;
}
</style>
