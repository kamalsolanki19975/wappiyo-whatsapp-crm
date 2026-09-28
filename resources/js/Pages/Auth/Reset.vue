<template>
    <div class="min-h-screen bg-slate-50 dark:bg-[#09090B] flex flex-col justify-center py-12 sm:px-6 lg:px-8 transition-colors antialiased relative overflow-hidden">
        <!-- Ambient Background Glows -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[350px] bg-gradient-to-tr from-[#22C55E]/15 via-[#10B981]/10 to-transparent blur-3xl pointer-events-none -z-10"></div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
            <!-- Brand Logo -->
            <div class="flex justify-center mb-6">
                <BrandLogo :custom-logo="props.companyConfig?.logo" :company-name="props.companyConfig?.company_name || 'Wappiyo'" mode="auto" href="/" />
            </div>

            <div class="text-center space-y-1 mb-8">
                <h1 class="text-2xl font-extrabold tracking-tight text-slate-900 dark:text-white">
                    {{ $t('Create new password') }}
                </h1>
                <p class="text-xs text-slate-500 dark:text-zinc-400">
                    {{ $t('Remember your password?') }} 
                    <Link href="/login" class="font-semibold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 hover:underline">
                        {{ $t('Log in here') }}
                    </Link>
                </p>
            </div>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
            <div class="bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 py-8 px-6 sm:px-10 rounded-2xl shadow-xl shadow-slate-900/5 dark:shadow-black/40">
                <form @submit.prevent="submitForm()" class="space-y-4">
                    <div v-if="props.flash?.status?.message" class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200/60 dark:border-emerald-800/60 text-emerald-700 dark:text-emerald-400 text-xs font-medium flex items-center gap-2">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"></polyline></svg>
                        <span>{{ props.flash?.status?.message }}</span>
                    </div>

                    <div>
                        <FormInput 
                            v-model="form.password" 
                            :name="$t('New Password')" 
                            :error="form.errors.password" 
                            type="password" 
                            placeholder="••••••••••••"
                            class="w-full"
                        />
                    </div>

                    <div>
                        <FormInput 
                            v-model="form.password_confirmation" 
                            :name="$t('Confirm New Password')" 
                            :error="form.errors.password_confirmation" 
                            type="password" 
                            placeholder="••••••••••••"
                            class="w-full"
                        />
                    </div>

                    <div v-if="form.errors.recaptcha_response" class="text-rose-500 text-xs font-medium">
                        {{ form.errors.recaptcha_response }}
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-3">
                        <button 
                            type="submit" 
                            :disabled="isLoading"
                            class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-[#22C55E] via-[#16A34A] to-[#022828] hover:from-[#15803D] hover:to-[#011d1d] shadow-md shadow-emerald-500/20 active:scale-[0.98] transition-all disabled:opacity-50"
                        >
                            <svg v-if="isLoading" class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ isLoading ? $t('Updating...') : $t('Update Password & Sign In') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { Link, useForm } from "@inertiajs/vue3";
    import BrandLogo from '@/Components/UI/BrandLogo.vue';
    import FormInput from '@/Components/FormInput.vue';
    import { ref, onMounted, onUnmounted } from 'vue';
    import { useRecaptcha, unMountRecaptcha } from '../../Composables/ReCaptcha';

    const props = defineProps(['flash', 'config', 'companyConfig']);
    const isLoading = ref(false);

    const form = useForm({
        password: null,
        password_confirmation: null,
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

        const currentUrl = window.location.href;

        form.post(currentUrl, {
            preserveScroll: true,
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
</script>