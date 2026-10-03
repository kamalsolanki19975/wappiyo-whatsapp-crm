<script setup>
import { ref, watch, onUnmounted } from 'vue';
import axios from 'axios';
import Avatar from '@/Components/UI/Avatar.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    incomingCall: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close', 'accepted', 'declined']);

let audioCtx = null;
let ringInterval = null;

const playRingtone = () => {
    try {
        if (!window.AudioContext && !window.webkitAudioContext) return;
        const AudioClass = window.AudioContext || window.webkitAudioContext;
        audioCtx = new AudioClass();

        const playTone = () => {
            if (!audioCtx || audioCtx.state === 'closed') return;
            const osc = audioCtx.createOscillator();
            const gain = audioCtx.createGain();

            osc.type = 'triangle';
            osc.frequency.setValueAtTime(523.25, audioCtx.currentTime); // C5
            osc.frequency.setValueAtTime(659.25, audioCtx.currentTime + 0.15); // E5

            gain.gain.setValueAtTime(0.08, audioCtx.currentTime);
            gain.gain.exponentialRampToValueAtTime(0.001, audioCtx.currentTime + 0.6);

            osc.connect(gain);
            gain.connect(audioCtx.destination);

            osc.start();
            osc.stop(audioCtx.currentTime + 0.6);
        };

        playTone();
        ringInterval = setInterval(playTone, 2000);
    } catch (_) {}
};

const stopRingtone = () => {
    if (ringInterval) {
        clearInterval(ringInterval);
        ringInterval = null;
    }
    if (audioCtx && audioCtx.state !== 'closed') {
        try {
            audioCtx.close();
        } catch (_) {}
        audioCtx = null;
    }
};

watch(() => props.isOpen, (newVal) => {
    if (newVal) {
        playRingtone();
    } else {
        stopRingtone();
    }
});

const acceptCall = () => {
    stopRingtone();
    emit('accepted', props.incomingCall);
};

const declineCall = async () => {
    stopRingtone();
    if (props.incomingCall?.uuid) {
        try {
            await axios.post(`/calls/${props.incomingCall.uuid}/end`);
        } catch (_) {}
    }
    emit('declined', props.incomingCall);
    emit('close');
};

onUnmounted(() => {
    stopRingtone();
});
</script>

<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-md animate-fade-in"
    >
        <div
            class="relative w-full max-w-sm overflow-hidden bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 rounded-3xl shadow-2xl p-6 text-center space-y-6"
        >
            <!-- Ambient green pulse strip -->
            <div class="absolute top-0 inset-x-0 h-2 bg-gradient-to-r from-emerald-500 via-teal-400 to-emerald-500 animate-pulse" />

            <!-- WhatsApp Calling Tag -->
            <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold tracking-wide uppercase bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-ping" />
                <span>{{ $t('Incoming WhatsApp Call') }}</span>
            </div>

            <!-- Avatar -->
            <div class="relative inline-flex items-center justify-center mx-auto">
                <div class="absolute -inset-3 rounded-full bg-emerald-500/20 animate-ping" />
                <div class="relative">
                    <Avatar
                        :src="incomingCall?.contact?.avatar || null"
                        :name="incomingCall?.contact?.full_name || 'WhatsApp Caller'"
                        size="2xl"
                    />
                    <span class="absolute bottom-0 right-0 h-4 w-4 rounded-full bg-emerald-500 ring-2 ring-white dark:ring-[#111113]" />
                </div>
            </div>

            <!-- Caller Information -->
            <div class="space-y-1">
                <h3 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    {{ incomingCall?.contact?.full_name || $t('Unknown WhatsApp Contact') }}
                </h3>
                <p class="text-xs font-mono font-medium text-slate-500 dark:text-zinc-400">
                    {{ incomingCall?.formatted_phone_number || incomingCall?.customer_phone }}
                </p>
            </div>

            <!-- Accept / Decline Action Buttons -->
            <div class="flex items-center justify-center gap-8 pt-2">
                <!-- Decline Button (Red) -->
                <div class="flex flex-col items-center gap-1.5">
                    <button
                        type="button"
                        @click="declineCall"
                        class="w-14 h-14 rounded-3xl bg-rose-600 hover:bg-rose-700 active:scale-95 text-white flex items-center justify-center shadow-lg shadow-rose-600/30 transition-all cursor-pointer"
                        :title="$t('Decline Call')"
                    >
                        <svg class="w-6 h-6 rotate-[135deg]" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 0 0-1.01.24l-1.57 1.97c-2.83-1.45-5.15-3.76-6.59-6.59l1.97-1.57c.28-.28.37-.68.25-1.02A11.36 11.36 0 0 1 8.96 4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.62c0-.55-.45-1-.99-1z"/>
                        </svg>
                    </button>
                    <span class="text-[11px] font-semibold text-slate-500 dark:text-zinc-400">{{ $t('Decline') }}</span>
                </div>

                <!-- Accept Button (Green) -->
                <div class="flex flex-col items-center gap-1.5">
                    <button
                        type="button"
                        @click="acceptCall"
                        class="w-14 h-14 rounded-3xl bg-emerald-600 hover:bg-emerald-700 active:scale-95 text-white flex items-center justify-center shadow-lg shadow-emerald-600/30 transition-all cursor-pointer"
                        :title="$t('Accept Call')"
                    >
                        <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 0 0-1.01.24l-1.57 1.97c-2.83-1.45-5.15-3.76-6.59-6.59l1.97-1.57c.28-.28.37-.68.25-1.02A11.36 11.36 0 0 1 8.96 4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.62c0-.55-.45-1-.99-1z"/>
                        </svg>
                    </button>
                    <span class="text-[11px] font-semibold text-slate-500 dark:text-zinc-400">{{ $t('Accept') }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
