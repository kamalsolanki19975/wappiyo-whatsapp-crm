<script setup>
import { useForm, router } from "@inertiajs/vue3";
import { ref } from "vue";
import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle, TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
import FormInput from '@/Components/FormInput.vue';
import axios from 'axios';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    user: Object,
    organization: Object,
    role: String,
    isOpen: Boolean,
});

const isLoading = ref(false);
const isUploadingAvatar = ref(false);
const isRemovingAvatar = ref(false);
const isSendingResetLink = ref(false);
const resetLinkSent = ref(false);
const resetLinkMessage = ref('');
const resetLinkError = ref('');
const avatarError = ref('');
const avatarPreview = ref(props.user?.avatar ? (props.user.avatar.startsWith('http') ? props.user.avatar : `/storage/${props.user.avatar}`) : null);

const avatarFileInput = ref(null);

const form1 = useForm({
    first_name: props.user?.first_name || '',
    last_name: props.user?.last_name || '',
    phone: props.user?.phone || '',
});

const form3 = useForm({
    old_password: null,
    password: null,
    password_confirmation: null
});

const submitForm = async () => {
    isLoading.value = true;
    form1.put('/profile', {
        preserveScroll: true,
        onFinish: () => {
            isLoading.value = false;
        }
    });
};

const submitForm3 = async () => {
    isLoading.value = true;
    form3.put('/profile/password', {
        preserveScroll: true,
        onFinish: () => {
            isLoading.value = false;
            form3.reset();
        }
    });
};

const triggerAvatarSelect = () => {
    avatarFileInput.value?.click();
};

const handleAvatarChange = async (event) => {
    const file = event.target.files?.[0];
    if (!file) return;

    avatarError.value = '';

    // Validate size (max 2MB)
    if (file.size > 2 * 1024 * 1024) {
        avatarError.value = t('Profile picture must be less than 2MB.');
        return;
    }

    // Validate mime type
    const validMimes = ['image/jpeg', 'image/png', 'image/jpg', 'image/webp'];
    if (!validMimes.includes(file.type)) {
        avatarError.value = t('Allowed formats: JPG, PNG, WEBP.');
        return;
    }

    // Local preview immediately
    const reader = new FileReader();
    reader.onload = (e) => {
        avatarPreview.value = e.target.result;
    };
    reader.readAsDataURL(file);

    // Upload via API
    isUploadingAvatar.value = true;
    const formData = new FormData();
    formData.append('avatar', file);

    try {
        const response = await axios.post('/profile/avatar', formData, {
            headers: { 'Content-Type': 'multipart/form-data' }
        });
        if (response.data.success) {
            avatarPreview.value = response.data.avatar_url;
            router.reload({ only: ['auth'] });
        }
    } catch (err) {
        avatarError.value = err.response?.data?.message || t('Failed to upload profile picture.');
    } finally {
        isUploadingAvatar.value = false;
        if (event.target) event.target.value = '';
    }
};

const removeAvatar = async () => {
    isRemovingAvatar.value = true;
    avatarError.value = '';

    try {
        const response = await axios.delete('/profile/avatar');
        if (response.data.success) {
            avatarPreview.value = null;
            router.reload({ only: ['auth'] });
        }
    } catch (err) {
        avatarError.value = err.response?.data?.message || t('Failed to remove profile picture.');
    } finally {
        isRemovingAvatar.value = false;
    }
};

const handleSendResetLink = async () => {
    isSendingResetLink.value = true;
    resetLinkSent.value = false;
    resetLinkError.value = '';
    resetLinkMessage.value = '';

    try {
        await router.post('/profile/reset-password', {}, {
            preserveScroll: true,
            onSuccess: () => {
                resetLinkSent.value = true;
                resetLinkMessage.value = t('We will send a password reset confirmation to your registered email address.');
            },
            onError: (err) => {
                resetLinkError.value = err?.message || t('Failed to send password reset email.');
            },
            onFinish: () => {
                isSendingResetLink.value = false;
            }
        });
    } catch (e) {
        resetLinkError.value = t('Failed to send password reset email.');
        isSendingResetLink.value = false;
    }
};

const emit = defineEmits(['close']);

function closeModal() {
    emit('close', true);
}
</script>

<template>
    <TransitionRoot appear :show="isOpen" as="template">
        <Dialog as="div" @close="closeModal" class="relative z-50">
            <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-black/60 backdrop-blur-xs" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel class="w-full max-w-lg transform overflow-hidden rounded-2xl bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 p-6 text-left align-middle shadow-2xl transition-all">
                            <!-- Header with Avatar Badge -->
                            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-zinc-800">
                                <div class="flex items-center gap-3">
                                    <div class="relative w-11 h-11 rounded-xl overflow-hidden bg-gradient-to-tr from-[#6C5CE7] to-[#8B5CF6] flex items-center justify-center text-white font-bold text-base shadow-md shadow-indigo-500/20 shrink-0">
                                        <img v-if="avatarPreview" :src="avatarPreview" alt="Profile" class="w-full h-full object-cover" />
                                        <span v-else>{{ (user?.first_name?.[0] || 'U').toUpperCase() }}</span>
                                    </div>
                                    <div>
                                        <DialogTitle as="h2" class="text-base font-bold text-slate-900 dark:text-white">
                                            {{ $t('Account & Settings') }}
                                        </DialogTitle>
                                        <p class="text-xs text-slate-500 dark:text-zinc-400">
                                            {{ user?.email }}
                                        </p>
                                    </div>
                                </div>
                                <button 
                                    @click="closeModal" 
                                    type="button" 
                                    class="text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors cursor-pointer"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </button>
                            </div>

                            <!-- Tabs -->
                            <TabGroup class="mt-4">
                                <TabList class="flex space-x-1 rounded-xl bg-slate-100 dark:bg-zinc-800 p-1">
                                    <Tab as="template" v-slot="{ selected }">
                                        <button
                                            :class="[
                                                'w-full rounded-lg py-2 text-xs font-semibold tracking-wide transition-all focus:outline-none cursor-pointer',
                                                selected
                                                    ? 'bg-white dark:bg-zinc-900 text-slate-900 dark:text-white shadow-xs'
                                                    : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white',
                                            ]"
                                        >
                                            {{ $t('User Profile') }}
                                        </button>
                                    </Tab>
                                    <Tab as="template" v-slot="{ selected }">
                                        <button
                                            :class="[
                                                'w-full rounded-lg py-2 text-xs font-semibold tracking-wide transition-all focus:outline-none cursor-pointer',
                                                selected
                                                    ? 'bg-white dark:bg-zinc-900 text-slate-900 dark:text-white shadow-xs'
                                                    : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white',
                                            ]"
                                        >
                                            {{ $t('Password & Security') }}
                                        </button>
                                    </Tab>
                                </TabList>

                                <TabPanels class="mt-4">
                                    <!-- User Profile Tab -->
                                    <TabPanel>
                                        <!-- Profile Picture Management -->
                                        <div class="mb-5 p-3.5 rounded-xl bg-slate-50 dark:bg-zinc-800/60 border border-slate-200/60 dark:border-zinc-700/60 flex items-center justify-between gap-4">
                                            <div class="flex items-center gap-3">
                                                <div class="relative w-14 h-14 rounded-full overflow-hidden border-2 border-emerald-500/50 shadow-sm shrink-0 bg-slate-200 dark:bg-zinc-700 flex items-center justify-center font-bold text-lg text-slate-700 dark:text-zinc-200">
                                                    <img v-if="avatarPreview" :src="avatarPreview" alt="Avatar" class="w-full h-full object-cover" />
                                                    <span v-else>{{ (user?.first_name?.[0] || 'U').toUpperCase() }}</span>
                                                    <div v-if="isUploadingAvatar" class="absolute inset-0 bg-black/40 flex items-center justify-center">
                                                        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                                    </div>
                                                </div>
                                                <div>
                                                    <h3 class="text-xs font-bold text-slate-800 dark:text-zinc-200">{{ $t('Profile Photo') }}</h3>
                                                    <p class="text-[11px] text-slate-500 dark:text-zinc-400">{{ $t('JPG, PNG or WEBP, max 2MB.') }}</p>
                                                    <p v-if="avatarError" class="text-[11px] text-rose-500 font-medium mt-0.5">{{ avatarError }}</p>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <input 
                                                    ref="avatarFileInput" 
                                                    type="file" 
                                                    accept="image/jpeg,image/png,image/jpg,image/webp" 
                                                    class="hidden" 
                                                    @change="handleAvatarChange" 
                                                />
                                                <button 
                                                    type="button" 
                                                    @click="triggerAvatarSelect" 
                                                    :disabled="isUploadingAvatar"
                                                    class="px-3 py-1.5 text-xs font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/50 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 rounded-lg border border-emerald-300 dark:border-emerald-800 transition-colors cursor-pointer"
                                                >
                                                    {{ avatarPreview ? $t('Change Photo') : $t('Upload Photo') }}
                                                </button>
                                                <button 
                                                    v-if="avatarPreview"
                                                    type="button" 
                                                    @click="removeAvatar" 
                                                    :disabled="isRemovingAvatar"
                                                    class="px-2.5 py-1.5 text-xs font-medium text-rose-600 hover:text-rose-700 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-950/30 rounded-lg transition-colors cursor-pointer"
                                                >
                                                    {{ $t('Remove') }}
                                                </button>
                                            </div>
                                        </div>

                                        <form @submit.prevent="submitForm()" class="space-y-4">
                                            <div class="grid grid-cols-2 gap-3">
                                                <FormInput v-model="form1.first_name" :name="$t('First Name')" :error="form1.errors.first_name" type="text" class="col-span-1" required />
                                                <FormInput v-model="form1.last_name" :name="$t('Last Name')" :error="form1.errors.last_name" type="text" class="col-span-1" />
                                            </div>

                                            <FormInput v-model="form1.phone" :name="$t('Phone Number')" :error="form1.errors.phone" type="text" class="w-full" placeholder="+1234567890" />

                                            <!-- Email Field: Strictly READ-ONLY per Requirement 19 -->
                                            <div>
                                                <label class="block text-xs font-semibold text-slate-700 dark:text-zinc-300 mb-1">
                                                    {{ $t('Email') }}
                                                </label>
                                                <div class="relative">
                                                    <input 
                                                        type="email" 
                                                        :value="user?.email" 
                                                        readonly 
                                                        disabled
                                                        class="w-full px-3.5 py-2 text-xs font-medium bg-slate-100 dark:bg-zinc-800/80 border border-slate-200 dark:border-zinc-700 rounded-xl text-slate-500 dark:text-zinc-400 cursor-not-allowed select-none shadow-xs"
                                                    />
                                                    <div class="absolute right-3 top-2.5 text-slate-400 dark:text-zinc-500" title="Email is read-only">
                                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <p class="mt-1.5 text-[11px] font-medium text-slate-500 dark:text-zinc-400 flex items-center gap-1">
                                                    <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <span>{{ $t('Email cannot be changed from profile settings.') }}</span>
                                                </p>
                                            </div>

                                            <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-zinc-800">
                                                <button type="button" @click="closeModal" class="px-4 py-2 text-xs font-medium text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-xl transition-colors cursor-pointer">
                                                    {{ $t('Cancel') }}
                                                </button>
                                                <button 
                                                    type="submit"
                                                    :disabled="isLoading"
                                                    class="inline-flex items-center gap-2 px-5 py-2 text-xs font-semibold text-white bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] hover:from-[#5b4bc4] hover:to-[#7c4deb] rounded-xl shadow-sm shadow-indigo-500/20 active:scale-[0.98] transition-all disabled:opacity-50 cursor-pointer"
                                                >
                                                    <svg v-if="isLoading" class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    <span>{{ $t('Save Changes') }}</span>
                                                </button>
                                            </div>
                                        </form>
                                    </TabPanel>

                                    <!-- Password & Security Tab -->
                                    <TabPanel class="space-y-6">
                                        <!-- Quick Password Reset Link option per Section 15 & 16 -->
                                        <div class="p-4 rounded-xl bg-indigo-50/70 dark:bg-indigo-950/30 border border-indigo-200/60 dark:border-indigo-800/60 space-y-2.5">
                                            <div class="flex items-start justify-between gap-3">
                                                <div>
                                                    <h3 class="text-xs font-bold text-indigo-950 dark:text-indigo-200">
                                                        {{ $t('Reset Password via Email') }}
                                                    </h3>
                                                    <p class="text-[11px] text-indigo-800/80 dark:text-indigo-300 mt-0.5">
                                                        {{ $t('We will send a password reset confirmation to your registered email address.') }}
                                                    </p>
                                                </div>
                                                <button
                                                    type="button"
                                                    id="btn-send-reset-link"
                                                    @click="handleSendResetLink"
                                                    :disabled="isSendingResetLink"
                                                    class="shrink-0 px-3 py-1.5 text-xs font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-lg shadow-2xs transition-colors cursor-pointer disabled:opacity-50"
                                                >
                                                    <span v-if="isSendingResetLink">{{ $t('Sending...') }}</span>
                                                    <span v-else>{{ $t('Reset Password') }}</span>
                                                </button>
                                            </div>

                                            <div v-if="resetLinkMessage" class="p-2.5 rounded-lg bg-emerald-100/80 dark:bg-emerald-950/50 border border-emerald-300 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-[11px] font-medium flex items-center gap-1.5">
                                                <svg class="w-4 h-4 shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                                <span>{{ resetLinkMessage }}</span>
                                            </div>

                                            <div v-if="resetLinkError" class="p-2.5 rounded-lg bg-rose-100/80 dark:bg-rose-950/50 border border-rose-300 dark:border-rose-800 text-rose-800 dark:text-rose-300 text-[11px] font-medium flex items-center gap-1.5">
                                                <svg class="w-4 h-4 shrink-0 text-rose-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                                                <span>{{ resetLinkError }}</span>
                                            </div>
                                        </div>

                                        <!-- Update Password Form -->
                                        <form @submit.prevent="submitForm3()" class="space-y-4">
                                            <h3 class="text-xs font-bold text-slate-800 dark:text-zinc-200">
                                                {{ $t('Or Change Password Directly') }}
                                            </h3>
                                            <FormInput v-model="form3.old_password" :name="$t('Current Password')" :error="form3.errors.old_password" type="password" class="w-full" required />
                                            <FormInput v-model="form3.password" :name="$t('New Password')" :error="form3.errors.password" type="password" class="w-full" required />
                                            <FormInput v-model="form3.password_confirmation" :name="$t('Confirm New Password')" :error="form3.errors.password_confirmation" type="password" class="w-full" required />

                                            <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-zinc-800">
                                                <button type="button" @click="closeModal" class="px-4 py-2 text-xs font-medium text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-xl transition-colors cursor-pointer">
                                                    {{ $t('Cancel') }}
                                                </button>
                                                <button 
                                                    type="submit"
                                                    :disabled="isLoading"
                                                    class="inline-flex items-center gap-2 px-5 py-2 text-xs font-semibold text-white bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] hover:from-[#5b4bc4] hover:to-[#7c4deb] rounded-xl shadow-sm shadow-indigo-500/20 active:scale-[0.98] transition-all disabled:opacity-50 cursor-pointer"
                                                >
                                                    <svg v-if="isLoading" class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    <span>{{ $t('Update Password') }}</span>
                                                </button>
                                            </div>
                                        </form>
                                    </TabPanel>
                                </TabPanels>
                            </TabGroup>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>