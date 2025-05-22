<template>
  <div class="flex h-screen justify-center items-center">
    <div class="w-[20em] md:w-[30em]">
      <!-- Logo -->
      <div class="flex justify-center mb-5">
        <Link href="/">
          <template v-if="companyConfig.logo">
            <img
              class="max-w-[180px]"
              :src="`/media/${companyConfig.logo}`"
              :alt="companyConfig.company_name"
            />
          </template>
          <template v-else>
            <h4 class="text-2xl mb-2">{{ companyConfig.company_name }}</h4>
          </template>
        </Link>
      </div>

      <h1 class="text-2xl text-center">{{ $t('Create account') }}</h1>
      <p class="text-center text-sm text-slate-500">
        {{ $t('Already have an account?') }}
        <Link
          href="/login"
          class="text-primary-600 dark:text-primary-500 border-b hover:border-gray-500"
        >
          {{ $t('Login') }}
        </Link>
      </p>

      <form @submit.prevent="submitForm" class="mt-5 pb-10 space-y-5">
        <div class="grid gap-x-6 grid-cols-6">
          <FormInput v-model="form.first_name" :name="$t('First name')" :error="form.errors.first_name" class="col-span-3" />
          <FormInput v-model="form.last_name" :name="$t('Last name')" :error="form.errors.last_name" class="col-span-3" />
        </div>

        <FormInput v-model="form.organization_name" :name="$t('Organization name')" :error="form.errors.organization_name" />

        <div class="grid gap-x-6 grid-cols-6">
            <FormInput v-model="form.email" :name="$t('Email')" :error="form.errors.email" class="col-span-3" />
            <FormPhoneInput v-model="form.phone" :name="$t('Phone')" :error="form.errors.phone" :type="'text'" :class="'sm:col-span-3'"/>
        </div>

        <div class="grid gap-x-6 grid-cols-6">
          <FormInput v-model="form.password" :name="$t('Password')" :error="form.errors.password" type="password" class="col-span-3" />
          <FormInput v-model="form.password_confirmation" :name="$t('Confirm password')" :error="form.errors.password_confirmation" type="password" class="col-span-3" />
        </div>

        <p v-if="form.errors.recaptcha_response" class="text-xs text-red-700">
          {{ form.errors.recaptcha_response }}
        </p>

        <button
            v-if="!otpSent"
            type="button"
            class="rounded-md bg-primary px-3 py-3 text-sm text-white shadow-sm w-full"
            @click="sendOtp"
            :disabled="isLoadingOtp || !form.phone"
            >
            {{ isLoadingOtp ? t('Sending...') : t('Send OTP') }}
            </button>
            <!-- OTP Sent Success Message -->
            <p v-if="otpMessage" class="text-sm text-green-600">
            {{ otpMessage }}
            </p>
            <!-- OTP Input & Verify -->
            <div v-if="otpSent" class="space-y-3">
            <FormInput
                v-model="form.otp"
                :name="t('Enter 4-digit OTP')"
                :error="form.errors.otp"
                type="text"
                maxlength="4"
            />

            <button
                type="button"
                @click="verifyOtp"
                :disabled="isVerifying || !form.otp || form.otp.length !== 4"
                class="rounded-md bg-primary px-3 py-3 text-sm text-white shadow-sm w-full"
            >
                <template v-if="isVerifying">
                <svg class="animate-spin inline-block mr-2" width="20" height="20" viewBox="0 0 24 24">
                    <path fill="currentColor" d="M12 2A10 10 0 1 0 22 12A10 10 0 0 0 12 2Zm0 18a8 8 0 1 1 8-8A8 8 0 0 1 12 20Z" opacity=".5"/>
                    <path fill="currentColor" d="M20 12h2A10 10 0 0 0 12 2V4A8 8 0 0 1 20 12Z"/>
                </svg>
                {{ t('Verifying...') }}
                </template>
                <template v-else>
                {{ t('OTP Verify') }}
                </template>
            </button>
            </div>

      </form>

      <!-- Social login -->
      <div v-if="showSocialLogin" class="flex flex-col items-center space-y-4">
        <span class="text-sm text-gray-500">{{ $t('Or continue with') }}</span>
        <div class="flex justify-center gap-4">
          <a v-if="companyConfig?.allow_facebook_login === '1'" href="/social-login/facebook" class="border rounded-full p-2">
            <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 256 256"><path fill="#1877F2" d="M256 128C256 57.308 198.692 0 128 0C57.308 0 0 57.307 0 128c0 63.888 46.808 116.843 108 126.445V165H75.5v-37H108V99.8c0-32.08 19.11-49.8 48.347-49.8C170.352 50 185 52.5 185 52.5V84h-16.14C152.958 84 148 93.867 148 103.99V128h35.5l-5.675 37H148v89.445c61.192-9.602 108-62.556 108-126.445"/><path fill="#FFF" d="m177.825 165l5.675-37H148v-24.01C148 93.866 152.959 84 168.86 84H185V52.5S170.352 50 156.347 50C127.11 50 108 67.72 108 99.8V128H75.5v37H108v89.445A128.959 128.959 0 0 0 128 256a128.9 128.9 0 0 0 20-1.555V165h29.825"/></svg>
          </a>
          <a v-if="companyConfig?.allow_google_login === '1'" href="/social-login/google" class="border rounded-full p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 128 128"><path fill="#fff" d="M44.59 4.21a63.28 63.28 0 0 0 4.33 120.9a67.6 67.6 0 0 0 32.36.35a57.13 57.13 0 0 0 25.9-13.46a57.44 57.44 0 0 0 16-26.26a74.33 74.33 0 0 0 1.61-33.58H65.27v24.69h34.47a29.72 29.72 0 0 1-12.66 19.52a36.16 36.16 0 0 1-13.93 5.5a41.29 41.29 0 0 1-15.1 0A37.16 37.16 0 0 1 44 95.74a39.3 39.3 0 0 1-14.5-19.42a38.31 38.31 0 0 1 0-24.63a39.25 39.25 0 0 1 9.18-14.91A37.17 37.17 0 0 1 76.13 27a34.28 34.28 0 0 1 13.64 8q5.83-5.8 11.64-11.63c2-2.09 4.18-4.08 6.15-6.22A61.22 61.22 0 0 0 87.2 4.59a64 64 0 0 0-42.61-.38z"/><path fill="#e33629" d="M44.59 4.21a64 64 0 0 1 42.61.37a61.22 61.22 0 0 1 20.35 12.62c-2 2.14-4.11 4.14-6.15 6.22Q95.58 29.23 89.77 35a34.28 34.28 0 0 0-13.64-8a37.17 37.17 0 0 0-37.46 9.74a39.25 39.25 0 0 0-9.18 14.91L8.76 35.6A63.53 63.53 0 0 1 44.59 4.21z"/><path fill="#f8bd00" d="M3.26 51.5a62.93 62.93 0 0 1 5.5-15.9l20.73 16.09a38.31 38.31 0 0 0 0 24.63q-10.36 8-20.73 16.08a63.33 63.33 0 0 1-5.5-40.9z"/><path fill="#587dbd" d="M65.27 52.15h59.52a74.33 74.33 0 0 1-1.61 33.58a57.44 57.44 0 0 1-16 26.26c-6.69-5.22-13.41-10.4-20.1-15.62a29.72 29.72 0 0 0 12.66-19.54H65.27c-.01-8.22 0-16.45 0-24.68z"/><path fill="#319f43" d="M8.75 92.4q10.37-8 20.73-16.08A39.3 39.3 0 0 0 44 95.74a37.16 37.16 0 0 0 14.08 6.08a41.29 41.29 0 0 0 15.1 0a36.16 36.16 0 0 0 13.93-5.5c6.69 5.22 13.41 10.4 20.1 15.62a57.13 57.13 0 0 1-25.9 13.47a67.6 67.6 0 0 1-32.36-.35a63 63 0 0 1-23-11.59A63.73 63.73 0 0 1 8.75 92.4z"/></svg>
          </a>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
    import { ref } from 'vue'
    import { useForm } from '@inertiajs/vue3'
    import { useI18n } from 'vue-i18n'
    import FormInput from '@/Components/FormInput.vue'
    import FormPhoneInput from '@/Components/FormPhoneInput.vue';
    import axios from 'axios'

    const { t } = useI18n()

    const props = defineProps({ companyConfig: Object })
    const companyConfig = props.companyConfig

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
    })

    const isLoadingOtp = ref(false)
    const isVerifying = ref(false)
    const otpSent = ref(false)

    const otpMessage = ref('') // Add this

    const sendOtp = async () => {
        if (!form.phone) return
        isLoadingOtp.value = true
        form.errors.otp = null
        otpMessage.value = ''

        try {
            const response = await axios.post('/send-otp', { phone: form.phone })
            if (response.data.success) {
            otpSent.value = true
            form.otp = ''
            otpMessage.value = t('OTP sent successfully to your phone.')
            } else {
            form.errors.otp = response.data.message || t('Failed to send OTP')
            }
        } catch (error) {
            form.errors.otp = error.response?.data?.message || t('Failed to send OTP')
        } finally {
            isLoadingOtp.value = false
        }
    }


    const verifyOtp = async () => {
        if (!form.otp || form.otp.length !== 4) return
        isVerifying.value = true
        form.errors.otp = null

        try {
            const response = await axios.post('/verify-otp', {
            phone: form.phone,
            otp: form.otp,
            })
           if (response.data.success) {
                otpMessage.value = t('OTP verified successfully.')
                submitForm()
            } else {
                form.errors.otp = response.data.message || t('Invalid OTP')
            }

        } catch (error) {
            form.errors.otp = error.response?.data?.message || t('Failed to verify OTP')
        } finally {
            isVerifying.value = false
        }
    }

    const submitForm = () => {
        form.post('/signup', {
            preserveScroll: true,
            onSuccess: () => {
            // success callback
            },
            onError: () => {
            // error callback
            },
        })
    }
</script>