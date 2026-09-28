<script setup>
import { computed } from 'vue';
import { Link, router } from '@inertiajs/vue3';

const props = defineProps({
    onboardingState: {
        type: Object,
        default: () => ({
            completed: false,
            dismissed: false,
            progress: 0,
            completedCount: 0,
            totalCount: 4,
            steps: {
                profile: false,
                whatsapp: false,
                template: false,
                team: false,
            }
        })
    }
});

const isVisible = computed(() => {
    return props.onboardingState && !props.onboardingState.completed && !props.onboardingState.dismissed;
});

const dismiss = () => {
    router.delete('/onboarding/dismiss', {
        preserveScroll: true
    });
};
</script>

<template>
    <div
        v-if="isVisible"
        class="relative overflow-hidden rounded-2xl border border-purple-200/80 dark:border-purple-900/40 bg-gradient-to-r from-purple-50/80 via-white to-indigo-50/80 dark:from-purple-950/20 dark:via-[#111113] dark:to-indigo-950/20 p-5 sm:p-6 shadow-sm shadow-purple-500/5 transition-all"
    >
        <!-- Top Ambient Glow -->
        <div class="pointer-events-none absolute -top-12 -right-12 h-36 w-36 rounded-full bg-purple-500/10 blur-2xl"></div>

        <div class="relative z-10 space-y-4">
            <!-- Header Row -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-purple-600 to-indigo-600 text-white flex items-center justify-center shrink-0 shadow-md shadow-purple-600/20">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-sm sm:text-base font-bold text-slate-900 dark:text-white">
                                {{ $t('Complete Your Workspace Onboarding') }}
                            </h3>
                            <span class="text-[10px] px-2 py-0.5 rounded-full font-bold bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300 border border-purple-200 dark:border-purple-800">
                                {{ props.onboardingState.completedCount }}/{{ props.onboardingState.totalCount }} {{ $t('Done') }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">
                            {{ $t('Follow our guided steps to finish connecting your WhatsApp instance, templates, and team.') }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center gap-2 self-end sm:self-center">
                    <button
                        type="button"
                        @click="dismiss"
                        class="text-xs text-slate-400 hover:text-slate-600 dark:hover:text-zinc-300 px-3 py-1.5 transition cursor-pointer"
                    >
                        {{ $t('Dismiss') }}
                    </button>
                    <Link
                        href="/onboarding"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-md shadow-purple-600/20 transition cursor-pointer"
                    >
                        <span>{{ $t('Continue Onboarding') }}</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
                        </svg>
                    </Link>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="space-y-1">
                <div class="flex justify-between text-[11px] font-semibold text-slate-500 dark:text-zinc-400">
                    <span>{{ $t('Setup Progress') }}</span>
                    <span class="text-purple-600 dark:text-purple-400">{{ props.onboardingState.progress }}%</span>
                </div>
                <div class="w-full bg-slate-200/80 dark:bg-zinc-800 h-2 rounded-full overflow-hidden">
                    <div
                        class="h-full bg-gradient-to-r from-purple-600 via-indigo-600 to-emerald-500 transition-all duration-500"
                        :style="{ width: `${props.onboardingState.progress}%` }"
                    ></div>
                </div>
            </div>

            <!-- Checklist Cards Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 pt-1">
                <!-- 1. Profile -->
                <Link
                    href="/onboarding"
                    class="p-3.5 rounded-xl border transition-all text-left flex items-start gap-3"
                    :class="props.onboardingState.steps.profile
                        ? 'bg-emerald-50/60 dark:bg-emerald-950/20 border-emerald-200/80 dark:border-emerald-800/40 text-emerald-900 dark:text-emerald-300'
                        : 'bg-white dark:bg-zinc-900 border-slate-200/80 dark:border-zinc-800 hover:border-purple-300 dark:hover:border-purple-800'"
                >
                    <div
                        :class="[
                            'w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5',
                            props.onboardingState.steps.profile
                                ? 'bg-emerald-500 text-white'
                                : 'border border-slate-300 dark:border-zinc-600 text-slate-400'
                        ]"
                    >
                        <span v-if="props.onboardingState.steps.profile">✓</span>
                        <span v-else>1</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate">
                            {{ $t('Company Profile') }}
                        </p>
                        <p class="text-[11px] text-slate-500 dark:text-zinc-400 truncate">
                            {{ props.onboardingState.steps.profile ? $t('Configured') : $t('Industry & details') }}
                        </p>
                    </div>
                </Link>

                <!-- 2. WhatsApp API -->
                <Link
                    href="/onboarding"
                    class="p-3.5 rounded-xl border transition-all text-left flex items-start gap-3"
                    :class="props.onboardingState.steps.whatsapp
                        ? 'bg-emerald-50/60 dark:bg-emerald-950/20 border-emerald-200/80 dark:border-emerald-800/40 text-emerald-900 dark:text-emerald-300'
                        : 'bg-white dark:bg-zinc-900 border-slate-200/80 dark:border-zinc-800 hover:border-purple-300 dark:hover:border-purple-800'"
                >
                    <div
                        :class="[
                            'w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5',
                            props.onboardingState.steps.whatsapp
                                ? 'bg-emerald-500 text-white'
                                : 'border border-slate-300 dark:border-zinc-600 text-slate-400'
                        ]"
                    >
                        <span v-if="props.onboardingState.steps.whatsapp">✓</span>
                        <span v-else>2</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate">
                            {{ $t('WhatsApp API') }}
                        </p>
                        <p class="text-[11px] text-slate-500 dark:text-zinc-400 truncate">
                            {{ props.onboardingState.steps.whatsapp ? $t('Connected') : $t('Meta Cloud API') }}
                        </p>
                    </div>
                </Link>

                <!-- 3. Templates -->
                <Link
                    href="/onboarding"
                    class="p-3.5 rounded-xl border transition-all text-left flex items-start gap-3"
                    :class="props.onboardingState.steps.template
                        ? 'bg-emerald-50/60 dark:bg-emerald-950/20 border-emerald-200/80 dark:border-emerald-800/40 text-emerald-900 dark:text-emerald-300'
                        : 'bg-white dark:bg-zinc-900 border-slate-200/80 dark:border-zinc-800 hover:border-purple-300 dark:hover:border-purple-800'"
                >
                    <div
                        :class="[
                            'w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5',
                            props.onboardingState.steps.template
                                ? 'bg-emerald-500 text-white'
                                : 'border border-slate-300 dark:border-zinc-600 text-slate-400'
                        ]"
                    >
                        <span v-if="props.onboardingState.steps.template">✓</span>
                        <span v-else>3</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate">
                            {{ $t('Message Templates') }}
                        </p>
                        <p class="text-[11px] text-slate-500 dark:text-zinc-400 truncate">
                            {{ props.onboardingState.steps.template ? $t('Ready to send') : $t('Starter greetings') }}
                        </p>
                    </div>
                </Link>

                <!-- 4. Team -->
                <Link
                    href="/onboarding"
                    class="p-3.5 rounded-xl border transition-all text-left flex items-start gap-3"
                    :class="props.onboardingState.steps.team
                        ? 'bg-emerald-50/60 dark:bg-emerald-950/20 border-emerald-200/80 dark:border-emerald-800/40 text-emerald-900 dark:text-emerald-300'
                        : 'bg-white dark:bg-zinc-900 border-slate-200/80 dark:border-zinc-800 hover:border-purple-300 dark:hover:border-purple-800'"
                >
                    <div
                        :class="[
                            'w-5 h-5 rounded-full flex items-center justify-center text-[10px] font-bold shrink-0 mt-0.5',
                            props.onboardingState.steps.team
                                ? 'bg-emerald-500 text-white'
                                : 'border border-slate-300 dark:border-zinc-600 text-slate-400'
                        ]"
                    >
                        <span v-if="props.onboardingState.steps.team">✓</span>
                        <span v-else>4</span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-bold text-slate-900 dark:text-white truncate">
                            {{ $t('Invite Team') }}
                        </p>
                        <p class="text-[11px] text-slate-500 dark:text-zinc-400 truncate">
                            {{ props.onboardingState.steps.team ? $t('Agents added') : $t('Add colleagues') }}
                        </p>
                    </div>
                </Link>
            </div>
        </div>
    </div>
</template>
