<template>
  <div class="min-h-screen bg-slate-50 dark:bg-[#09090B] flex flex-col justify-center py-12 sm:px-6 lg:px-8 transition-colors antialiased relative overflow-hidden">
    <!-- Ambient Background Glows -->
    <div class="absolute top-0 right-1/2 translate-x-1/2 w-[700px] h-[350px] bg-gradient-to-tr from-[#6C5CE7]/15 via-[#8B5CF6]/10 to-transparent blur-3xl pointer-events-none -z-10"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[300px] bg-gradient-to-tr from-[#EC4899]/10 to-transparent blur-3xl pointer-events-none -z-10"></div>

    <div class="sm:mx-auto sm:w-full sm:max-w-xl px-4">
      <!-- Logo -->
      <div class="flex justify-center mb-6">
        <Link href="/" class="flex items-center gap-3">
          <template v-if="companyConfig?.logo">
            <img
              class="max-h-10 object-contain"
              :src="`/media/${companyConfig.logo}`"
              :alt="companyConfig.company_name"
            />
          </template>
          <template v-else>
            <div class="flex items-center gap-2.5">
              <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-[#6C5CE7] to-[#8B5CF6] flex items-center justify-center text-white font-extrabold text-lg shadow-lg shadow-indigo-500/25">
                W
              </div>
              <span class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                {{ companyConfig?.company_name || 'Wappiyo' }}
              </span>
            </div>
          </template>
        </Link>
      </div>

      <div class="text-center space-y-1 mb-8">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
          {{ $t('Create your workspace') }}
        </h1>
        <p class="text-xs text-slate-500 dark:text-zinc-400">
          {{ $t('Already have an account?') }}
          <Link
            href="/login"
            class="font-semibold text-[#6C5CE7] hover:text-[#5b4bc4] dark:text-indigo-400 hover:underline"
          >
            {{ $t('Log in here') }}
          </Link>
        </p>
      </div>
    </div>

    <div class="sm:mx-auto sm:w-full sm:max-w-xl px-4">
      <div class="bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 py-8 px-6 sm:px-10 rounded-2xl shadow-xl shadow-slate-900/5 dark:shadow-black/40">
        <form @submit.prevent="submitForm" class="space-y-4">
          <!-- Name Row -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <FormInput 
              v-model="form.first_name" 
              :name="$t('First Name')" 
              :error="form.errors.first_name" 
              placeholder="Alex"
              class="w-full"
            />
            <FormInput 
              v-model="form.last_name" 
              :name="$t('Last Name')" 
              :error="form.errors.last_name" 
              placeholder="Morgan"
              class="w-full"
            />
          </div>

          <!-- Organization Name -->
          <div>
            <FormInput 
              v-model="form.organization_name" 
              :name="$t('Organization / Business Name')" 
              :error="form.errors.organization_name" 
              placeholder="Acme Corp"
              class="w-full"
            />
          </div>

          <!-- Contact Row: Email & Phone -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <FormInput 
              v-model="form.email" 
              :name="$t('Email Address')" 
              :error="form.errors.email" 
              type="email"
              placeholder="alex@acmecorp.com"
              class="w-full"
            />
            <FormPhoneInput 
              v-model="form.phone" 
              :name="$t('Phone Number')" 
              :error="form.errors.phone" 
              type="text" 
              class="w-full"
            />
          </div>

          <!-- Password Row -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <FormInput 
              v-model="form.password" 
              :name="$t('Password')" 
              :error="form.errors.password" 
              type="password" 
              placeholder="••••••••••••"
              class="w-full"
            />
            <FormInput 
              v-model="form.password_confirmation" 
              :name="$t('Confirm Password')" 
              :error="form.errors.password_confirmation" 
              type="password" 
              placeholder="••••••••••••"
              class="w-full"
            />
          </div>

          <p v-if="form.errors.recaptcha_response" class="text-xs text-rose-600 font-medium">
            {{ form.errors.recaptcha_response }}
          </p>

          <!-- OTP Step 1: Send OTP -->
          <div v-if="!otpSent" class="pt-2">
            <button
              type="button"
              class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] hover:from-[#5b4bc4] hover:to-[#7c4deb] shadow-md shadow-indigo-500/20 active:scale-[0.98] transition-all disabled:opacity-50"
              @click="sendOtp"
              :disabled="isLoadingOtp || !form.phone"
            >
              <svg v-if="isLoadingOtp" class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ isLoadingOtp ? t('Sending OTP...') : t('Send Phone Verification OTP') }}</span>
            </button>
          </div>

          <!-- OTP Sent Feedback -->
          <div v-if="otpMessage" class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/60 dark:border-emerald-800/60 text-emerald-700 dark:text-emerald-400 text-xs font-medium flex items-center gap-2">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
            <span>{{ otpMessage }}</span>
          </div>

          <!-- OTP Step 2: Verify & Submit -->
          <div v-if="otpSent" class="space-y-3 pt-2">
            <FormInput
              v-model="form.otp"
              :name="t('Enter 4-Digit OTP Code')"
              :error="form.errors.otp"
              type="text"
              maxlength="4"
              placeholder="1234"
              class="w-full text-center tracking-widest text-lg font-mono font-bold"
            />

            <button
              type="button"
              @click="verifyOtp"
              :disabled="isVerifying || !form.otp || form.otp.length !== 4"
              class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-[#22C55E] to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 shadow-md shadow-emerald-500/20 active:scale-[0.98] transition-all disabled:opacity-50"
            >
              <svg v-if="isVerifying" class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ isVerifying ? t('Verifying...') : t('Verify OTP & Create Workspace') }}</span>
            </button>
          </div>
        </form>

        <!-- Social login -->
        <div v-if="companyConfig?.allow_facebook_login === '1' || companyConfig?.allow_google_login === '1'" class="mt-6">
          <div class="relative">
            <div class="absolute inset-0 flex items-center">
              <div class="w-full border-t border-slate-200/80 dark:border-zinc-800"></div>
            </div>
            <div class="relative flex justify-center text-xs">
              <span class="bg-white dark:bg-zinc-900 px-3 text-slate-400 dark:text-zinc-500 uppercase tracking-wider font-semibold">
                {{ $t('Or continue with') }}
              </span>
            </div>
          </div>

          <div class="mt-5 grid grid-cols-2 gap-3">
            <a 
              v-if="companyConfig?.allow_google_login === '1'" 
              href="/social-login/google" 
              class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 hover:bg-slate-50 dark:hover:bg-zinc-700/60 text-xs font-semibold text-slate-700 dark:text-zinc-200 shadow-2xs transition-all"
            >
              <svg class="w-4 h-4" viewBox="0 0 24 24">
                <path fill="#4285F4" d="M23.745 12.27c0-.7-.06-1.4-.19-2.07H12v4.51h6.6c-.29 1.52-1.14 2.82-2.4 3.68v3.05h3.88c2.27-2.09 3.665-5.17 3.665-9.17Z"/>
                <path fill="#34A853" d="M12 24c3.24 0 5.95-1.08 7.93-2.91l-3.88-3.05c-1.08.72-2.45 1.16-4.05 1.16-3.12 0-5.77-2.1-6.72-4.93H1.25v3.15C3.26 21.36 7.33 24 12 24Z"/>
                <path fill="#FBBC05" d="M5.28 14.27c-.25-.72-.38-1.49-.38-2.27s.14-1.55.38-2.27V6.58H1.25C.45 8.18 0 9.99 0 12s.45 3.82 1.25 5.42l4.03-3.15Z"/>
                <path fill="#EA4335" d="M12 4.75c1.77 0 3.35.61 4.6 1.8l3.42-3.42C17.95 1.19 15.24 0 12 0 7.33 0 3.26 2.64 1.25 6.58l4.03 3.15c.95-2.83 3.6-4.98 6.72-4.98Z"/>
              </svg>
              <span>Google</span>
            </a>

            <a 
              v-if="companyConfig?.allow_facebook_login === '1'" 
              href="/social-login/facebook" 
              class="flex items-center justify-center gap-2 py-2.5 px-3 rounded-xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 hover:bg-slate-50 dark:hover:bg-zinc-700/60 text-xs font-semibold text-slate-700 dark:text-zinc-200 shadow-2xs transition-all"
            >
              <svg class="w-4 h-4 text-[#1877F2]" fill="currentColor" viewBox="0 0 24 24">
                <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z"/>
              </svg>
              <span>Facebook</span>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
    import { ref } from 'vue';
    import { Link, useForm } from '@inertiajs/vue3';
    import { useI18n } from 'vue-i18n';
    import FormInput from '@/Components/FormInput.vue';
    import FormPhoneInput from '@/Components/FormPhoneInput.vue';
    import axios from 'axios';

    const { t } = useI18n();

    const props = defineProps({ companyConfig: Object });
    const companyConfig = props.companyConfig;

    const form = useForm({
        first_name: '',
        last_name: '',
        organization_name: '',
        email: '',
        phone: '',
        password: '',
        password_confirmation: '',
        otp: '',
        recaptcha_response: '',
    });

    const isLoadingOtp = ref(false);
    const isVerifying = ref(false);
    const otpSent = ref(false);
    const otpMessage = ref('');

    const sendOtp = async () => {
        if (!form.phone) return;
        isLoadingOtp.value = true;
        form.errors.otp = null;
        otpMessage.value = '';

        try {
            const response = await axios.post('/send-otp', { phone: form.phone });
            if (response.data.success) {
                otpSent.value = true;
                form.otp = '';
                otpMessage.value = t('OTP sent successfully to your phone.');
            } else {
                form.errors.otp = response.data.message || t('Failed to send OTP');
            }
        } catch (error) {
            form.errors.otp = error.response?.data?.message || t('Failed to send OTP');
        } finally {
            isLoadingOtp.value = false;
        }
    };

    const verifyOtp = async () => {
        if (!form.otp || form.otp.length !== 4) return;
        isVerifying.value = true;
        form.errors.otp = null;

        try {
            const response = await axios.post('/verify-otp', {
                phone: form.phone,
                otp: form.otp,
            });
            if (response.data.success) {
                otpMessage.value = t('OTP verified successfully.');
                submitForm();
            } else {
                form.errors.otp = response.data.message || t('Invalid OTP');
            }
        } catch (error) {
            form.errors.otp = error.response?.data?.message || t('Failed to verify OTP');
        } finally {
            isVerifying.value = false;
        }
    };

    const submitForm = () => {
        form.post('/signup', {
            preserveScroll: true,
        });
    };
</script>