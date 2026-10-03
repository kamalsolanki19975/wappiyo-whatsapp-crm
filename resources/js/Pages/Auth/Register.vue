<template>
  <div class="min-h-screen bg-slate-50 dark:bg-[#09090B] flex flex-col justify-center py-12 sm:px-6 lg:px-8 transition-colors antialiased relative overflow-hidden">
    <!-- Ambient Background Glows -->
    <div class="absolute top-0 right-1/2 translate-x-1/2 w-[700px] h-[350px] bg-gradient-to-tr from-[#22C55E]/15 via-[#10B981]/10 to-transparent blur-3xl pointer-events-none -z-10"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[300px] bg-gradient-to-tr from-[#022828]/15 to-transparent blur-3xl pointer-events-none -z-10"></div>

    <div class="sm:mx-auto sm:w-full sm:max-w-xl px-4">
      <!-- Logo -->
      <div class="flex justify-center mb-6">
        <BrandLogo :custom-logo="companyConfig?.logo" :company-name="companyConfig?.company_name || 'Wappiyo'" mode="auto" href="/" />
      </div>

      <div class="text-center space-y-1 mb-8">
        <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
          {{ currentStep === 1 ? $t('Create your workspace') : $t('Verify Your Mail ID') }}
        </h1>
        <p v-if="currentStep === 1" class="text-xs text-slate-500 dark:text-zinc-400">
          {{ $t('Already have an account?') }}
          <Link
            href="/login"
            class="font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 hover:underline"
          >
            {{ $t('Log in here') }}
          </Link>
        </p>
      </div>
    </div>

    <div class="sm:mx-auto sm:w-full sm:max-w-xl px-4">
      <!-- Pre-selected Plan Banner -->
      <div v-if="props.selectedPlan && currentStep === 1" class="mb-4 p-3 rounded-2xl bg-purple-50 dark:bg-purple-950/40 border border-purple-200 dark:border-purple-800 text-xs text-[#6C5CE7] dark:text-purple-300 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-[#6C5CE7] animate-pulse"></span>
          <span><strong>{{ $t('Plan Selected:') }}</strong> {{ $t('14-day free trial will start after setup.') }}</span>
        </div>
        <Link href="/pricing" class="font-bold underline text-[11px]">{{ $t('Change') }}</Link>
      </div>

      <div class="bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 py-8 px-6 sm:px-10 rounded-2xl shadow-xl shadow-slate-900/5 dark:shadow-black/40">
        
        <!-- STEP 1: Registration Form -->
        <form v-if="currentStep === 1" @submit.prevent="handleSendOtp" class="space-y-4">
          <!-- Duplicate email / global error banner -->
          <div v-if="globalError" class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs font-medium flex items-start gap-2.5">
            <svg class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
            </svg>
            <div>
              <p class="font-semibold">{{ globalError }}</p>
              <div v-if="isDuplicateEmail" class="mt-1">
                <Link href="/login" class="underline font-bold text-rose-800 dark:text-rose-200 hover:text-rose-900">
                  {{ $t('Click here to Log In') }} &rarr;
                </Link>
              </div>
            </div>
          </div>

          <!-- Name Row -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <FormInput 
              v-model="form.first_name" 
              :name="$t('First Name')" 
              :error="form.errors.first_name" 
              placeholder="Alex"
              class="w-full"
              required
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
              required
            />
          </div>

          <!-- Contact Row: Email & Phone -->
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <FormInput 
                v-model="form.email" 
                :name="$t('Email Address')" 
                :error="form.errors.email" 
                type="email"
                placeholder="alex@acmecorp.com"
                class="w-full"
                required
              />
            </div>
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
              required
            />
            <FormInput 
              v-model="form.password_confirmation" 
              :name="$t('Confirm Password')" 
              :error="form.errors.password_confirmation" 
              type="password" 
              placeholder="••••••••••••"
              class="w-full"
              required
            />
          </div>

          <p v-if="form.errors.recaptcha_response" class="text-xs text-rose-600 font-medium">
            {{ form.errors.recaptcha_response }}
          </p>

          <div class="pt-2">
            <button
              type="submit"
              class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-[#22C55E] via-[#16A34A] to-[#022828] hover:from-[#15803D] hover:to-[#011d1d] shadow-md shadow-emerald-500/20 active:scale-[0.98] transition-all disabled:opacity-50 cursor-pointer"
              :disabled="isLoading"
            >
              <svg v-if="isLoading" class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ isLoading ? $t('Sending Verification Code...') : $t('Continue & Verify Email') }}</span>
            </button>
          </div>
        </form>

        <!-- STEP 2: Dedicated Email OTP Verification Screen -->
        <div v-else class="space-y-6">
          <div class="text-center space-y-2">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 mb-2">
              <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
              </svg>
            </div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">
              {{ $t('Verify Your Mail ID') }}
            </h2>
            <p class="text-xs text-slate-500 dark:text-zinc-400">
              {{ $t('We have sent a verification code to:') }}
            </p>
            <p class="text-sm font-semibold text-slate-800 dark:text-zinc-100 flex items-center justify-center gap-1.5">
              <span>{{ form.email }}</span>
              <button 
                type="button" 
                @click="changeEmail" 
                class="text-xs text-emerald-600 dark:text-emerald-400 hover:underline font-medium ml-1"
              >
                ({{ $t('Change') }})
              </button>
            </p>
          </div>

          <!-- Error / Info Message -->
          <div v-if="otpError" class="p-3 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs font-medium text-center">
            {{ otpError }}
          </div>

          <div v-if="resendSuccessMsg" class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs font-medium text-center">
            {{ resendSuccessMsg }}
          </div>

          <!-- 6-digit OTP Inputs: [ _ ][ _ ][ _ ][ _ ][ _ ][ _ ] -->
          <div class="flex justify-center items-center gap-2 sm:gap-3 py-2" @paste="handlePaste">
            <template v-for="(digit, index) in otpDigits" :key="index">
              <input
                :ref="el => { if (el) otpInputRefs[index] = el; }"
                v-model="otpDigits[index]"
                type="text"
                inputmode="numeric"
                pattern="[0-9]*"
                maxlength="1"
                class="w-11 h-13 sm:w-12 sm:h-14 text-center text-xl sm:text-2xl font-bold font-mono rounded-xl border border-slate-300 dark:border-zinc-700 bg-slate-50 dark:bg-zinc-800/80 text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 transition-all shadow-inner"
                @input="handleInput(index, $event)"
                @keydown="handleKeyDown(index, $event)"
                @focus="$event.target.select()"
              />
            </template>
          </div>

          <!-- Action Button: Verify Your Mail ID -->
          <div>
            <button
              type="button"
              id="verify-mail-id-btn"
              @click="submitOtpVerification"
              :disabled="isVerifying || !isOtpComplete"
              class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-[#22C55E] via-[#16A34A] to-[#022828] hover:from-[#15803D] hover:to-[#011d1d] shadow-md shadow-emerald-500/20 active:scale-[0.98] transition-all disabled:opacity-50 cursor-pointer"
            >
              <svg v-if="isVerifying" class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              <span>{{ isVerifying ? $t('Verifying...') : $t('Verify Your Mail ID') }}</span>
            </button>
          </div>

          <!-- Resend and Expiration Countdown -->
          <div class="flex flex-col sm:flex-row items-center justify-between text-xs text-slate-500 dark:text-zinc-400 pt-2 gap-2">
            <div>
              <span>{{ $t("Didn't receive the code?") }} </span>
              <button
                type="button"
                @click="handleResendOtp"
                :disabled="resendCooldown > 0 || isResending"
                class="font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 hover:underline disabled:opacity-50 disabled:no-underline cursor-pointer"
              >
                {{ resendCooldown > 0 ? $t('Resend in {s}s', { s: resendCooldown }) : $t('Resend OTP') }}
              </button>
            </div>

            <div class="flex items-center gap-1 font-mono text-[11px] text-slate-600 dark:text-zinc-300">
              <svg class="w-3.5 h-3.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
              <span>{{ $t('OTP expires in:') }} <strong class="text-emerald-600 dark:text-emerald-400">{{ formattedTimer }}</strong></span>
            </div>
          </div>
        </div>

        <!-- Social login -->
        <div v-if="currentStep === 1 && (companyConfig?.allow_facebook_login === '1' || companyConfig?.allow_google_login === '1')" class="mt-6">
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
import { ref, computed, onUnmounted, nextTick } from 'vue';
import { Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import BrandLogo from '@/Components/UI/BrandLogo.vue';
import FormInput from '@/Components/FormInput.vue';
import FormPhoneInput from '@/Components/FormPhoneInput.vue';
import axios from 'axios';

const { t } = useI18n();

const props = defineProps({
  companyConfig: Object,
  selectedPlan: String,
});
const companyConfig = props.companyConfig;

const currentStep = ref(1);
const isLoading = ref(false);
const isVerifying = ref(false);
const isResending = ref(false);
const globalError = ref('');
const isDuplicateEmail = ref(false);
const otpError = ref('');
const resendSuccessMsg = ref('');

// 6-digit OTP state
const otpDigits = ref(['', '', '', '', '', '']);
const otpInputRefs = ref([]);

// Timers
const expirySeconds = ref(600); // 10 minutes
let expiryInterval = null;
const resendCooldown = ref(0);
let cooldownInterval = null;

const form = useForm({
  first_name: '',
  last_name: '',
  organization_name: '',
  email: '',
  phone: '',
  password: '',
  password_confirmation: '',
  plan: props.selectedPlan || '',
});

const isOtpComplete = computed(() => {
  return otpDigits.value.every(d => d.trim().length === 1);
});

const fullOtp = computed(() => {
  return otpDigits.value.join('').trim();
});

const formattedTimer = computed(() => {
  const m = Math.floor(expirySeconds.value / 60);
  const s = expirySeconds.value % 60;
  return `${m.toString().padStart(2, '0')}:${s.toString().padStart(2, '0')}`;
});

const startExpiryTimer = () => {
  if (expiryInterval) clearInterval(expiryInterval);
  expirySeconds.value = 600;
  expiryInterval = setInterval(() => {
    if (expirySeconds.value > 0) {
      expirySeconds.value--;
    } else {
      clearInterval(expiryInterval);
      otpError.value = t('Verification code has expired. Please request a new OTP.');
    }
  }, 1000);
};

const startCooldownTimer = (seconds = 60) => {
  if (cooldownInterval) clearInterval(cooldownInterval);
  resendCooldown.value = seconds;
  cooldownInterval = setInterval(() => {
    if (resendCooldown.value > 0) {
      resendCooldown.value--;
    } else {
      clearInterval(cooldownInterval);
    }
  }, 1000);
};

const handleSendOtp = async () => {
  globalError.value = '';
  isDuplicateEmail.value = false;
  form.clearErrors();

  if (!form.first_name || !form.email || !form.password || !form.password_confirmation || !form.organization_name) {
    globalError.value = t('Please fill in all required fields.');
    return;
  }

  if (form.password !== form.password_confirmation) {
    form.setError('password_confirmation', t('The password field confirmation does not match.'));
    return;
  }

  isLoading.value = true;

  try {
    const payload = {
      first_name: form.first_name,
      last_name: form.last_name,
      organization_name: form.organization_name,
      email: form.email,
      phone: form.phone,
      password: form.password,
      password_confirmation: form.password_confirmation,
      plan: form.plan,
    };

    const response = await axios.post('/send-otp', payload);

    if (response.data.success) {
      currentStep.value = 2;
      otpDigits.value = ['', '', '', '', '', ''];
      otpError.value = '';
      resendSuccessMsg.value = '';
      startExpiryTimer();
      startCooldownTimer(60);
      nextTick(() => {
        otpInputRefs.value[0]?.focus();
      });
    }
  } catch (error) {
    if (error.response?.status === 422) {
      const errors = error.response.data.errors || {};
      if (errors.email) {
        globalError.value = errors.email[0];
        isDuplicateEmail.value = errors.email[0].toLowerCase().includes('already exists');
        form.setError('email', errors.email[0]);
      } else {
        Object.keys(errors).forEach(key => {
          form.setError(key, errors[key][0]);
        });
        globalError.value = error.response.data.message || t('Validation failed. Please correct the errors.');
      }
    } else {
      globalError.value = error.response?.data?.message || t('Failed to send verification code. Please try again.');
    }
  } finally {
    isLoading.value = false;
  }
};

const handleInput = (index, event) => {
  const value = event.target.value.replace(/[^0-9]/g, '');
  if (value.length > 0) {
    otpDigits.value[index] = value.slice(-1);
    if (index < 5) {
      otpInputRefs.value[index + 1]?.focus();
    }
  } else {
    otpDigits.value[index] = '';
  }
};

const handleKeyDown = (index, event) => {
  if (event.key === 'Backspace') {
    if (!otpDigits.value[index] && index > 0) {
      otpDigits.value[index - 1] = '';
      otpInputRefs.value[index - 1]?.focus();
    } else {
      otpDigits.value[index] = '';
    }
  } else if (event.key === 'ArrowLeft' && index > 0) {
    otpInputRefs.value[index - 1]?.focus();
  } else if (event.key === 'ArrowRight' && index < 5) {
    otpInputRefs.value[index + 1]?.focus();
  }
};

const handlePaste = (event) => {
  event.preventDefault();
  const pasteData = (event.clipboardData || window.clipboardData).getData('text');
  const digits = pasteData.replace(/[^0-9]/g, '').slice(0, 6).split('');
  if (digits.length > 0) {
    for (let i = 0; i < 6; i++) {
      otpDigits.value[i] = digits[i] || '';
    }
    const nextIndex = Math.min(digits.length, 5);
    otpInputRefs.value[nextIndex]?.focus();
  }
};

const submitOtpVerification = async () => {
  if (!isOtpComplete.value) return;

  isVerifying.value = true;
  otpError.value = '';

  try {
    const payload = {
      email: form.email,
      otp: fullOtp.value,
      first_name: form.first_name,
      last_name: form.last_name,
      organization_name: form.organization_name,
      phone: form.phone,
      password: form.password,
      plan: form.plan,
    };

    const response = await axios.post('/verify-otp', payload);

    if (response.data.success) {
      if (response.data.redirect) {
        window.location.href = response.data.redirect;
      } else {
        window.location.href = '/onboarding';
      }
    } else {
      otpError.value = response.data.message || t('Invalid OTP code.');
    }
  } catch (error) {
    otpError.value = error.response?.data?.message || t('Verification failed. Please try again.');
  } finally {
    isVerifying.value = false;
  }
};

const handleResendOtp = async () => {
  if (resendCooldown.value > 0 || isResending.value) return;

  isResending.value = true;
  otpError.value = '';
  resendSuccessMsg.value = '';

  try {
    const response = await axios.post('/resend-otp', { email: form.email });
    if (response.data.success) {
      resendSuccessMsg.value = t('A new 6-digit code has been sent to your email.');
      otpDigits.value = ['', '', '', '', '', ''];
      startExpiryTimer();
      startCooldownTimer(60);
      nextTick(() => {
        otpInputRefs.value[0]?.focus();
      });
    }
  } catch (error) {
    if (error.response?.data?.seconds_remaining) {
      startCooldownTimer(error.response.data.seconds_remaining);
    }
    otpError.value = error.response?.data?.message || t('Failed to resend OTP.');
  } finally {
    isResending.value = false;
  }
};

const changeEmail = () => {
  currentStep.value = 1;
  otpDigits.value = ['', '', '', '', '', ''];
  otpError.value = '';
  resendSuccessMsg.value = '';
  if (expiryInterval) clearInterval(expiryInterval);
  if (cooldownInterval) clearInterval(cooldownInterval);
};

onUnmounted(() => {
  if (expiryInterval) clearInterval(expiryInterval);
  if (cooldownInterval) clearInterval(cooldownInterval);
});
</script>