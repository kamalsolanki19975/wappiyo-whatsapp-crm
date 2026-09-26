<template>
    <div class="min-h-screen bg-slate-50 dark:bg-[#09090B] flex flex-col justify-center py-12 sm:px-6 lg:px-8 transition-colors antialiased relative overflow-hidden">
        <!-- Ambient Background Glows -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[350px] bg-gradient-to-tr from-[#6C5CE7]/20 via-[#8B5CF6]/15 to-transparent blur-3xl pointer-events-none -z-10"></div>
        <div class="absolute bottom-0 right-0 w-[400px] h-[300px] bg-gradient-to-tl from-[#06B6D4]/10 to-transparent blur-3xl pointer-events-none -z-10"></div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
            <!-- Brand Logo / Identity -->
            <div class="flex justify-center mb-6">
                <Link href="/" class="flex items-center gap-3">
                    <img 
                        v-if="props.companyConfig?.logo" 
                        class="max-h-10 object-contain" 
                        :src="'/media/' + props.companyConfig.logo" 
                        :alt="props.companyConfig.company_name"
                    >
                    <div v-else class="flex items-center gap-2.5">
                        <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-[#6C5CE7] to-[#8B5CF6] flex items-center justify-center text-white font-extrabold text-lg shadow-lg shadow-indigo-500/25">
                            W
                        </div>
                        <span class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                            {{ props.companyConfig?.company_name || 'Wappiyo' }}
                        </span>
                    </div>
                </Link>
            </div>

            <div class="text-center space-y-1 mb-8">
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    {{ $t('Sign in to your workspace') }}
                </h1>
                <p class="text-xs text-slate-500 dark:text-zinc-400">
                    {{ $t('Don\'t have an account?') }} 
                    <Link href="/signup" class="font-semibold text-[#6C5CE7] hover:text-[#5b4bc4] dark:text-indigo-400 hover:underline">
                        {{ $t('Create one here') }}
                    </Link>
                </p>
            </div>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
            <div class="bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 py-8 px-6 sm:px-10 rounded-2xl shadow-xl shadow-slate-900/5 dark:shadow-black/40">
                <form @submit.prevent="submitForm()" class="space-y-4">
                    <div>
                        <FormInput 
                            v-model="form.email" 
                            :name="$t('Email Address')" 
                            :error="form.errors.email" 
                            type="email" 
                            placeholder="name@company.com"
                            class="w-full"
                        />
                    </div>

                    <div>
                        <FormInput 
                            v-model="form.password" 
                            :name="$t('Password')" 
                            :error="form.errors.password" 
                            type="password" 
                            placeholder="••••••••••••"
                            class="w-full"
                        />
                    </div>

                    <div v-if="form.errors.recaptcha_response" class="text-rose-500 text-xs font-medium">
                        {{ form.errors.recaptcha_response }}
                    </div>

                    <!-- Remember me & Forgot Password -->
                    <div class="flex items-center justify-between text-xs pt-1">
                        <label class="flex items-center gap-2 cursor-pointer select-none text-slate-600 dark:text-zinc-400">
                            <input 
                                id="remember" 
                                type="checkbox" 
                                class="w-4 h-4 rounded border-slate-300 dark:border-zinc-700 text-[#6C5CE7] focus:ring-[#6C5CE7]/30 bg-slate-50 dark:bg-zinc-800"
                            >
                            <span>{{ $t('Remember me') }}</span>
                        </label>

                        <Link href="/forgot-password" class="font-semibold text-[#6C5CE7] hover:text-[#5b4bc4] dark:text-indigo-400 hover:underline">
                            {{ $t('Forgot password?') }}
                        </Link>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-3">
                        <button 
                            type="submit" 
                            :disabled="isLoading"
                            class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] hover:from-[#5b4bc4] hover:to-[#7c4deb] shadow-md shadow-indigo-500/20 active:scale-[0.98] transition-all disabled:opacity-50"
                        >
                            <svg v-if="isLoading" class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ isLoading ? $t('Signing in...') : $t('Sign in to Account') }}</span>
                        </button>
                    </div>
                </form>

                <!-- Social OAuth Login -->
                <div v-if="props.companyConfig?.allow_facebook_login === '1' || props.companyConfig?.allow_google_login === '1'" class="mt-6">
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
                            v-if="props.companyConfig?.allow_google_login === '1'" 
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
                            v-if="props.companyConfig?.allow_facebook_login === '1'" 
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
    import { Link, useForm, usePage } from "@inertiajs/vue3";
    import FormInput from '@/Components/FormInput.vue';
    import { ref, onMounted, onUnmounted, watch } from 'vue';
    import { useRecaptcha, unMountRecaptcha } from '../../Composables/ReCaptcha';
    import { toast } from 'vue3-toastify';

    const props = defineProps(['flash', 'config', 'companyConfig']);
    const isLoading = ref(false);

    const form = useForm({
        email: null,
        password: null,
        recaptcha_response: null,
    });

    const getValueByKey = (key) => {
        if (!props.config || !Array.isArray(props.config)) return '';
        const found = props.config.find(item => item.key === key);
        return found ? found.value : '';
    };

    const submitForm = async () => {
        isLoading.value = true;

        if (getValueByKey('recaptcha_active') === '1') {
            const token = await getRecaptchaToken();
            form.recaptcha_response = token;
        }

        form.post('/login', {
            onSuccess: () => form.reset(),
            onFinish: () => {
                isLoading.value = false;
            }
        });
    };

    const getRecaptchaToken = () => {
        return new Promise((resolve) => {
            if (typeof grecaptcha !== 'undefined' && grecaptcha.ready) {
                grecaptcha.ready(() => {
                    grecaptcha.execute(getValueByKey('recaptcha_site_key'), { action: 'submit' })
                    .then((token) => {
                        resolve(token);
                    });
                });
            } else {
                resolve(null);
            }
        });
    };

    onMounted(() => {
        if (getValueByKey('recaptcha_active') === '1') {
            useRecaptcha(getValueByKey('recaptcha_site_key'));
        }
    });

    onUnmounted(() => {
        unMountRecaptcha(getValueByKey('recaptcha_site_key'));
    });

    watch(() => [usePage().props.flash, { deep: true }], () => {
        if (usePage().props.flash?.status != null) {
            toast(usePage().props.flash.status.message, {
                autoClose: 3000,
            });
        }
    });
</script>