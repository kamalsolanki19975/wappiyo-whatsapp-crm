<template>
    <div class="min-h-screen bg-slate-50 dark:bg-[#09090B] flex flex-col justify-center py-12 sm:px-6 lg:px-8 transition-colors antialiased relative overflow-hidden">
        <!-- Ambient Background Glows -->
        <div class="absolute top-0 left-1/2 -translate-x-1/2 w-[600px] h-[350px] bg-gradient-to-tr from-[#6C5CE7]/20 via-[#8B5CF6]/15 to-transparent blur-3xl pointer-events-none -z-10"></div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
            <!-- Brand Logo -->
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
        </div>

        <div class="sm:mx-auto sm:w-full sm:max-w-md px-4">
            <div class="bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 py-8 px-6 sm:px-10 rounded-2xl shadow-xl shadow-slate-900/5 dark:shadow-black/40 text-center space-y-5">
                <div class="w-14 h-14 rounded-2xl bg-indigo-50 dark:bg-indigo-950/50 text-[#6C5CE7] dark:text-indigo-400 mx-auto flex items-center justify-center shadow-inner">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect width="20" height="16" x="2" y="4" rx="2"></rect>
                        <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7"></path>
                    </svg>
                </div>

                <div class="space-y-1">
                    <h1 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white">
                        {{ $t('Verify your email') }}
                    </h1>
                    <p class="text-xs text-slate-500 dark:text-zinc-400 leading-relaxed">
                        {{ $t('We have dispatched a confirmation link to your email address. Please click the link inside to activate your workspace.') }}
                    </p>
                </div>

                <div class="pt-3 border-t border-slate-100 dark:border-zinc-800">
                    <button 
                        v-if="!isSending"
                        @click="resendEmail" 
                        type="button"
                        class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] hover:from-[#5b4bc4] hover:to-[#7c4deb] shadow-md shadow-indigo-500/20 active:scale-[0.98] transition-all"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 0 0-9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"></path><path d="M3 3v5h5"></path><path d="M3 12a9 9 0 0 0 9 9 9.75 9.75 0 0 0 6.74-2.74L21 16"></path><path d="M16 16h5v5"></path></svg>
                        <span>{{ $t('Resend Verification Email') }}</span>
                    </button>

                    <div v-else class="w-full inline-flex items-center justify-center gap-2 py-3 px-4 rounded-xl text-xs font-bold text-white bg-indigo-400 cursor-not-allowed">
                        <svg class="animate-spin w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ $t('Dispatching email...') }}</span>
                    </div>

                    <div class="mt-4">
                        <Link href="/login" class="text-xs font-semibold text-slate-500 dark:text-zinc-400 hover:text-[#6C5CE7] hover:underline">
                            {{ $t('Back to Sign In') }}
                        </Link>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
    import { Link, router, usePage } from "@inertiajs/vue3";
    import { ref } from 'vue';
    import { toast } from 'vue3-toastify';
    import 'vue3-toastify/dist/index.css';

    const props = defineProps(['flash', 'config', 'companyConfig']);
    const isSending = ref(false);

    const resendEmail = () => {
        isSending.value = true;
        router.visit('/email/verification-notification', {
            method: 'post',
            data: {},
            onFinish: () => {
                isSending.value = false;
                if (usePage().props.flash?.status?.message) {
                    showToast(usePage().props.flash.status.message);
                }
            },
        });
    };

    const showToast = (message) => {
        toast(message, {
            autoClose: 3000,
        });
    };
</script>