<template>
    <div class="min-h-screen bg-slate-50 dark:bg-[#09090B] flex flex-col justify-center py-12 sm:px-6 lg:px-8 transition-colors antialiased relative overflow-hidden">
        <!-- Ambient Background Glows -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[350px] bg-gradient-to-tr from-[#22C55E]/15 via-[#10B981]/10 to-transparent blur-3xl pointer-events-none -z-10"></div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
            <!-- Brand Logo -->
            <div class="flex justify-center mb-6">
                <BrandLogo :custom-logo="props.companyConfig?.logo" :company-name="props.companyConfig?.company_name || 'Wappiyo'" mode="auto" href="/" />
            </div>
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
            <!-- Active Invitation Form -->
            <div v-if="!hasLinkExpired" class="bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 py-8 px-6 sm:px-10 rounded-2xl shadow-xl shadow-slate-900/5 dark:shadow-black/40 space-y-6">
                <div class="text-center space-y-1">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/50 text-emerald-600 dark:text-emerald-400 text-xs font-semibold uppercase tracking-wider mb-2">
                        {{ $t('Team Invitation') }}
                    </div>
                    <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">
                        <span v-if="props.user">{{ $t('Welcome,') }} {{ props.user?.first_name }}</span>
                        <span v-else>{{ $t('You\'re invited to join') }}</span>
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-zinc-400">
                        {{ $t('Join') }} <strong class="font-bold text-slate-800 dark:text-zinc-200">{{ props?.organization?.name }}</strong> {{ $t('as a team member.') }}
                    </p>
                </div>

                <!-- Registration form if new user -->
                <form v-if="!props.user" @submit.prevent="submitForm()" class="space-y-4">
                    <div class="grid grid-cols-2 gap-3">
                        <FormInput 
                            v-model="form.first_name" 
                            :name="$t('First Name')" 
                            :error="form.errors.first_name" 
                            type="text" 
                            class="col-span-1"
                        />
                        <FormInput 
                            v-model="form.last_name" 
                            :name="$t('Last Name')" 
                            :error="form.errors.last_name" 
                            type="text" 
                            class="col-span-1"
                        />
                    </div>

                    <div>
                        <FormInput 
                            v-model="form.email" 
                            :name="$t('Invited Email')" 
                            :disabled="true" 
                            :error="form.errors.email" 
                            type="email" 
                            class="w-full"
                        />
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <FormInput 
                            v-model="form.password" 
                            :name="$t('Password')" 
                            :error="form.errors.password" 
                            type="password" 
                            placeholder="••••••••••••"
                            class="col-span-1"
                        />
                        <FormInput 
                            v-model="form.password_confirmation" 
                            :name="$t('Confirm Password')" 
                            :error="form.errors.password_confirmation" 
                            type="password" 
                            placeholder="••••••••••••"
                            class="col-span-1"
                        />
                    </div>

                    <div v-if="form.errors.recaptcha_response" class="text-rose-500 text-xs font-medium">
                        {{ form.errors.recaptcha_response }}
                    </div>

                    <div class="pt-2">
                        <button 
                            type="submit" 
                            :disabled="isLoading"
                            class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-[#22C55E] via-[#16A34A] to-[#022828] hover:from-[#15803D] hover:to-[#011d1d] shadow-md shadow-emerald-500/20 active:scale-[0.98] transition-all disabled:opacity-50"
                        >
                            <svg v-if="isLoading" class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ isLoading ? $t('Creating account...') : $t('Accept & Join Team') }}</span>
                        </button>
                    </div>
                </form>

                <!-- Accept form if user already exists -->
                <form v-else @submit.prevent="submitForm()" class="space-y-4">
                    <div class="pt-2">
                        <button 
                            type="submit" 
                            :disabled="isLoading"
                            class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-[#22C55E] via-[#16A34A] to-[#022828] hover:from-[#15803D] hover:to-[#011d1d] shadow-md shadow-emerald-500/20 active:scale-[0.98] transition-all disabled:opacity-50"
                        >
                            <svg v-if="isLoading" class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ isLoading ? $t('Joining...') : $t('Accept Invitation & Proceed') }}</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- Expired Link State -->
            <div v-else class="bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 py-8 px-6 sm:px-10 rounded-2xl shadow-xl shadow-slate-900/5 dark:shadow-black/40 text-center space-y-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-50 dark:bg-amber-950/40 text-amber-600 dark:text-amber-400 mx-auto flex items-center justify-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                </div>
                <h2 class="text-lg font-bold text-slate-900 dark:text-white">{{ $t('Invitation Link Expired') }}</h2>
                <p class="text-xs text-slate-500 dark:text-zinc-400 leading-relaxed">
                    {{ $t('This invite link has reached its expiration date. Please request the organization administrator to re-invite you.') }}
                </p>
                <div class="pt-2">
                    <Link href="/login" class="inline-flex items-center justify-center w-full py-2.5 px-4 rounded-xl text-xs font-semibold bg-slate-100 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 hover:bg-slate-200 dark:hover:bg-zinc-700 transition-colors">
                        {{ $t('Return to Sign In') }}
                    </Link>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { Link, useForm } from "@inertiajs/vue3";
    import BrandLogo from '@/Components/UI/BrandLogo.vue';
    import FormInput from '@/Components/FormInput.vue';
    import { computed, ref, onMounted, onUnmounted } from 'vue';
    import { useRecaptcha, unMountRecaptcha } from '../../Composables/ReCaptcha';

    const props = defineProps(['flash', 'config', 'organization', 'companyConfig', 'invite', 'user', 'code']);
    const expireTime = ref(props.invite?.expire_at);
    const currentTime = ref(new Date().toISOString());
    const isLoading = ref(false);

    const form = useForm({
        first_name: props.user?.first_name || '',
        last_name: props.user?.last_name || '',
        email: props.invite?.email || '',
        password: null,
        password_confirmation: null,
        code: props.code,
        recaptcha_response: null,
    });

    const getValueByKey = (key) => {
        if (!props.config || !Array.isArray(props.config)) return '';
        const found = props.config.find(item => item.key === key);
        return found ? found.value : '';
    };

    if (props.user) {
        delete form.password;
        delete form.password_confirmation;
    }

    const hasLinkExpired = computed(() => {
        if (!expireTime.value) return false;
        const dbTime = new Date(expireTime.value);
        const current = new Date(currentTime.value);
        return dbTime < current;
    });

    const submitForm = async () => {
        isLoading.value = true;

        if (getValueByKey('recaptcha_active') === '1') {
            const token = await getRecaptchaToken();
            form.recaptcha_response = token;
        }

        form.post('/invite', {
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