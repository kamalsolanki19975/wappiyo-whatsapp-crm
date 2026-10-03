<script setup>
import { ref, computed, watch, onUnmounted } from 'vue';
import axios from 'axios';
import Avatar from '@/Components/UI/Avatar.vue';
import Badge from '@/Components/UI/Badge.vue';
import Button from '@/Components/UI/Button.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    contact: {
        type: Object,
        default: null,
    },
    existingCall: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close', 'callUpdated', 'callEnded']);

const activeCall = ref(null);
const callStatus = ref('initiating'); // initiating, ringing, connecting, connected, completed, failed, missed
const duration = ref(0);
const isMuted = ref(false);
const isSpeakerOn = ref(true);
const isSubmitting = ref(false);
const errorMessage = ref('');
const showPostCallForm = ref(false);

// Post-call disposition & notes form
const disposition = ref('');
const notes = ref('');
const followUpDate = ref('');
const reminderNotes = ref('');

let timerInterval = null;
let ringAudioContext = null;
let ringOscillator = null;

const dispositionsList = [
    'Interested',
    'Not Interested',
    'Follow-up Required',
    'No Answer',
    'Busy',
    'Wrong Number',
    'Converted',
    'Callback Requested',
    'Other',
];

const formattedDuration = computed(() => {
    const d = duration.value;
    const mins = Math.floor(d / 60);
    const secs = d % 60;
    return `${mins.toString().padStart(2, '0')}:${secs.toString().padStart(2, '0')}`;
});

const statusBadgeVariant = computed(() => {
    switch (callStatus.value) {
        case 'connected': return 'success';
        case 'ringing':
        case 'connecting': return 'primary';
        case 'completed': return 'neutral';
        case 'failed':
        case 'missed': return 'danger';
        default: return 'warning';
    }
});

const statusText = computed(() => {
    switch (callStatus.value) {
        case 'initiating': return trans('Initiating WhatsApp Call...');
        case 'ringing': return trans('Ringing...');
        case 'connecting': return trans('Connecting audio...');
        case 'connected': return trans('Call Connected');
        case 'completed': return trans('Call Ended');
        case 'failed': return trans('Call Failed');
        case 'missed': return trans('Call Missed / No Answer');
        default: return callStatus.value;
    }
});

// Watch for modal opening
watch(() => props.isOpen, (newVal) => {
    if (newVal) {
        if (props.existingCall) {
            loadExistingCall(props.existingCall);
        } else if (props.contact) {
            startNewCall();
        }
    } else {
        stopTimer();
        stopRingTone();
    }
});

watch(() => props.existingCall, (newCall) => {
    if (newCall && props.isOpen) {
        loadExistingCall(newCall);
    }
});

const loadExistingCall = (call) => {
    activeCall.value = call;
    callStatus.value = call.status || 'connected';
    duration.value = call.duration || 0;
    disposition.value = call.disposition || '';
    notes.value = call.notes || '';

    if (callStatus.value === 'connected') {
        startTimer();
    } else if (['completed', 'failed', 'missed'].includes(callStatus.value)) {
        stopTimer();
        showPostCallForm.value = true;
    }
};

const startNewCall = async () => {
    if (isSubmitting.value) return;
    errorMessage.value = '';
    duration.value = 0;
    showPostCallForm.value = false;
    callStatus.value = 'initiating';
    isSubmitting.value = true;

    startRingTone();

    try {
        const response = await axios.post('/calls', {
            contact_uuid: props.contact?.uuid,
            phone: props.contact?.phone,
        });

        if (response.data.success && response.data.call) {
            activeCall.value = response.data.call;
            callStatus.value = response.data.call.status || 'ringing';

            if (callStatus.value === 'connected') {
                stopRingTone();
                startTimer();
            } else if (callStatus.value === 'failed') {
                stopRingTone();
                errorMessage.value = response.data.call.failure_reason || trans('Call failed to connect.');
                showPostCallForm.value = true;
            }
        } else {
            stopRingTone();
            callStatus.value = 'failed';
            errorMessage.value = response.data.message || trans('Unable to initiate WhatsApp call.');
            showPostCallForm.value = true;
        }
    } catch (err) {
        stopRingTone();
        callStatus.value = 'failed';
        errorMessage.value = err.response?.data?.message || err.message || trans('Error placing call.');
        showPostCallForm.value = true;
    } finally {
        isSubmitting.value = false;
    }
};

const endCurrentCall = async () => {
    if (!activeCall.value?.uuid) {
        callStatus.value = 'completed';
        showPostCallForm.value = true;
        stopTimer();
        stopRingTone();
        return;
    }

    isSubmitting.value = true;
    stopRingTone();

    try {
        const response = await axios.post(`/calls/${activeCall.value.uuid}/end`);
        if (response.data.success) {
            activeCall.value = response.data.call;
            callStatus.value = 'completed';
            duration.value = response.data.call.duration || duration.value;
        }
    } catch (err) {
        callStatus.value = 'completed';
    } finally {
        stopTimer();
        isSubmitting.value = false;
        showPostCallForm.value = true;
        emit('callEnded', activeCall.value);
    }
};

const savePostCallDetails = async () => {
    if (!activeCall.value?.uuid) {
        closeModal();
        return;
    }

    isSubmitting.value = true;
    try {
        if (disposition.value) {
            await axios.post(`/calls/${activeCall.value.uuid}/disposition`, {
                disposition: disposition.value,
            });
        }
        if (notes.value) {
            await axios.post(`/calls/${activeCall.value.uuid}/notes`, {
                notes: notes.value,
            });
        }
        if (followUpDate.value) {
            await axios.post(`/calls/${activeCall.value.uuid}/follow-up`, {
                follow_up_at: followUpDate.value,
                reminder_notes: reminderNotes.value,
            });
        }

        emit('callUpdated', activeCall.value);
        closeModal();
    } catch (err) {
        console.error('Failed to save post-call details:', err);
    } finally {
        isSubmitting.value = false;
    }
};

const startTimer = () => {
    stopTimer();
    timerInterval = setInterval(() => {
        duration.value += 1;
    }, 1000);
};

const stopTimer = () => {
    if (timerInterval) {
        clearInterval(timerInterval);
        timerInterval = null;
    }
};

// Clean Web Audio Ring Tone Generator
const startRingTone = () => {
    try {
        if (!window.AudioContext && !window.webkitAudioContext) return;
        const AudioCtx = window.AudioContext || window.webkitAudioContext;
        ringAudioContext = new AudioCtx();

        ringOscillator = ringAudioContext.createOscillator();
        const gainNode = ringAudioContext.createGain();

        ringOscillator.type = 'sine';
        ringOscillator.frequency.setValueAtTime(425, ringAudioContext.currentTime); // Standard ring frequency

        gainNode.gain.setValueAtTime(0.04, ringAudioContext.currentTime);

        ringOscillator.connect(gainNode);
        gainNode.connect(ringAudioContext.destination);

        ringOscillator.start();
    } catch (_) {}
};

const stopRingTone = () => {
    try {
        if (ringOscillator) {
            ringOscillator.stop();
            ringOscillator.disconnect();
            ringOscillator = null;
        }
        if (ringAudioContext && ringAudioContext.state !== 'closed') {
            ringAudioContext.close();
            ringAudioContext = null;
        }
    } catch (_) {}
};

const toggleMute = () => {
    isMuted.value = !isMuted.value;
};

const toggleSpeaker = () => {
    isSpeakerOn.value = !isSpeakerOn.value;
};

const closeModal = () => {
    stopTimer();
    stopRingTone();
    emit('close');
};

onUnmounted(() => {
    stopTimer();
    stopRingTone();
});
</script>

<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/60 backdrop-blur-sm transition-opacity"
        @click.self="callStatus === 'completed' || callStatus === 'failed' ? closeModal() : null"
    >
        <div
            class="relative w-full max-w-md overflow-hidden bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 rounded-3xl shadow-2xl transition-all duration-200"
        >
            <!-- Top Gradient Status Ambient Strip -->
            <div
                class="h-2 w-full transition-colors duration-300"
                :class="[
                    callStatus === 'connected' ? 'bg-gradient-to-r from-emerald-500 to-teal-400' :
                    callStatus === 'ringing' || callStatus === 'initiating' ? 'bg-gradient-to-r from-[#6C5CE7] to-purple-400 animate-pulse' :
                    callStatus === 'failed' || callStatus === 'missed' ? 'bg-rose-500' : 'bg-slate-400'
                ]"
            />

            <!-- Close / Minimize Top Bar -->
            <div class="flex items-center justify-between px-5 pt-4">
                <div class="flex items-center gap-2">
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold tracking-wide uppercase bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                        <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M19.05 4.91A9.816 9.816 0 0 0 12.04 2c-5.46 0-9.91 4.45-9.91 9.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38c1.45.79 3.08 1.21 4.74 1.21c5.46 0 9.91-4.45 9.91-9.91c0-2.65-1.03-5.14-2.9-7.01m-7.01 15.24c-1.48 0-2.93-.4-4.2-1.15l-.3-.18l-3.12.82l.83-3.04l-.2-.31a8.264 8.264 0 0 1-1.26-4.38c0-4.54 3.7-8.24 8.24-8.24c2.2 0 4.27.86 5.82 2.42a8.183 8.183 0 0 1 2.41 5.83c.02 4.54-3.68 8.23-8.22 8.23"/>
                        </svg>
                        <span>WhatsApp Calling</span>
                    </span>
                </div>

                <button
                    type="button"
                    @click="closeModal"
                    class="p-1.5 rounded-xl text-slate-400 hover:text-slate-700 dark:hover:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors"
                >
                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>

            <!-- Call Main Body -->
            <div class="p-6 text-center space-y-5">
                <!-- Avatar with animated glowing rings when active -->
                <div class="relative inline-flex items-center justify-center mx-auto">
                    <div
                        v-if="callStatus === 'ringing' || callStatus === 'connecting'"
                        class="absolute -inset-3 rounded-full bg-purple-500/20 animate-ping"
                    />
                    <div
                        v-if="callStatus === 'connected'"
                        class="absolute -inset-2 rounded-full bg-emerald-500/20 animate-pulse"
                    />
                    <div class="relative">
                        <Avatar
                            :src="contact?.avatar || null"
                            :name="contact?.full_name || 'Contact'"
                            size="2xl"
                        />
                        <span
                            class="absolute bottom-0 right-0 h-4 w-4 rounded-full ring-2 ring-white dark:ring-[#111113]"
                            :class="callStatus === 'connected' ? 'bg-emerald-500' : 'bg-purple-500'"
                        />
                    </div>
                </div>

                <!-- Customer Details -->
                <div class="space-y-1">
                    <h2 class="text-lg sm:text-xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        {{ contact?.full_name || trans('Customer Call') }}
                    </h2>
                    <p class="text-xs font-mono font-medium text-slate-500 dark:text-zinc-400">
                        {{ contact?.formatted_phone_number || contact?.phone || activeCall?.customer_phone }}
                    </p>
                </div>

                <!-- Call Status & Duration Badge -->
                <div class="flex items-center justify-center gap-2">
                    <Badge :variant="statusBadgeVariant" size="md">
                        <span class="flex items-center gap-1.5">
                            <span
                                v-if="callStatus === 'connected'"
                                class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"
                            />
                            <span>{{ statusText }}</span>
                        </span>
                    </Badge>
                    <span v-if="callStatus === 'connected'" class="text-sm font-mono font-bold text-slate-800 dark:text-zinc-200">
                        {{ formattedDuration }}
                    </span>
                </div>

                <!-- Error Alert Box -->
                <div
                    v-if="errorMessage"
                    class="p-3.5 rounded-2xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/50 text-xs text-rose-700 dark:text-rose-300 text-left"
                >
                    <p class="font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-rose-500 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                        <span>{{ $t('Calling Notice') }}</span>
                    </p>
                    <p class="mt-1 text-[11px] leading-relaxed">{{ errorMessage }}</p>
                </div>

                <!-- Active Call Control Buttons (When in call) -->
                <div
                    v-if="!showPostCallForm && (callStatus === 'initiating' || callStatus === 'ringing' || callStatus === 'connecting' || callStatus === 'connected')"
                    class="flex items-center justify-center gap-4 pt-3"
                >
                    <!-- Mute / Unmute Button -->
                    <button
                        type="button"
                        @click="toggleMute"
                        :class="[
                            'w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-150',
                            isMuted
                                ? 'bg-amber-100 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-300 dark:border-amber-800'
                                : 'bg-slate-100 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 hover:bg-slate-200 dark:hover:bg-zinc-700'
                        ]"
                        :title="isMuted ? 'Unmute microphone' : 'Mute microphone'"
                    >
                        <svg v-if="!isMuted" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"/><path d="M19 10v2a7 7 0 0 1-14 0v-2"/><line x1="12" y1="19" x2="12" y2="22"/></svg>
                        <svg v-else class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="2" y1="2" x2="22" y2="22"/><path d="M18.89 13.23A7.12 7.12 0 0 0 19 12v-2"/><path d="M5 10v2a7 7 0 0 0 12 5"/><path d="M15 9.34V5a3 3 0 0 0-5.68-1.33"/><path d="M9 9v3a3 3 0 0 0 5.12 2.12"/><line x1="12" y1="19" x2="12" y2="22"/></svg>
                    </button>

                    <!-- End Call Button (Big Red) -->
                    <button
                        type="button"
                        @click="endCurrentCall"
                        :disabled="isSubmitting"
                        class="w-14 h-14 rounded-3xl bg-rose-600 hover:bg-rose-700 active:scale-95 text-white flex items-center justify-center shadow-lg shadow-rose-600/30 transition-all cursor-pointer"
                        :title="$t('End Call')"
                    >
                        <svg class="w-6 h-6 rotate-[135deg]" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 0 0-1.01.24l-1.57 1.97c-2.83-1.45-5.15-3.76-6.59-6.59l1.97-1.57c.28-.28.37-.68.25-1.02A11.36 11.36 0 0 1 8.96 4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.62c0-.55-.45-1-.99-1z"/>
                        </svg>
                    </button>

                    <!-- Speaker Output Toggle -->
                    <button
                        type="button"
                        @click="toggleSpeaker"
                        :class="[
                            'w-12 h-12 rounded-2xl flex items-center justify-center transition-all duration-150',
                            isSpeakerOn
                                ? 'bg-purple-100 dark:bg-purple-950/60 text-[#6C5CE7] dark:text-purple-300 border border-purple-300 dark:border-purple-800'
                                : 'bg-slate-100 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 hover:bg-slate-200 dark:hover:bg-zinc-700'
                        ]"
                        :title="isSpeakerOn ? 'Speaker On' : 'Speaker Off'"
                    >
                        <svg v-if="isSpeakerOn" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14"/><path d="M15.54 8.46a5 5 0 0 1 0 7.07"/></svg>
                        <svg v-else class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polygon points="11 5 6 9 2 9 2 15 6 15 11 19 11 5"/><line x1="23" y1="9" x2="17" y2="15"/><line x1="17" y1="9" x2="23" y2="15"/></svg>
                    </button>
                </div>

                <!-- POST-CALL CRM FORM: Disposition, Notes & Follow-up -->
                <div v-if="showPostCallForm" class="pt-2 text-left space-y-4 border-t border-slate-100 dark:border-zinc-800/80">
                    <div class="flex items-center justify-between">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-zinc-400">
                            {{ $t('Call Outcome & Notes') }}
                        </h4>
                        <span class="text-[11px] font-mono text-slate-400">
                            Duration: {{ formattedDuration }}
                        </span>
                    </div>

                    <!-- Disposition Select -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-zinc-300 mb-1">
                            {{ $t('Disposition / Outcome') }}
                        </label>
                        <select
                            v-model="disposition"
                            class="w-full text-xs bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-xl px-3 py-2 text-slate-800 dark:text-zinc-200 focus:outline-none focus:border-[#6C5CE7]"
                        >
                            <option value="">{{ $t('Select call disposition...') }}</option>
                            <option v-for="disp in dispositionsList" :key="disp" :value="disp">
                                {{ disp }}
                            </option>
                        </select>
                    </div>

                    <!-- Call Notes Textarea -->
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 dark:text-zinc-300 mb-1">
                            {{ $t('Call Notes') }}
                        </label>
                        <textarea
                            v-model="notes"
                            rows="2"
                            placeholder="Add internal notes about conversation, requirements, next steps..."
                            class="w-full text-xs bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-xl p-2.5 text-slate-800 dark:text-zinc-200 focus:outline-none focus:border-[#6C5CE7] placeholder-slate-400"
                        />
                    </div>

                    <!-- Follow-up Date & Reminder -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-zinc-400 mb-1">
                                {{ $t('Follow-up Date') }}
                            </label>
                            <input
                                v-model="followUpDate"
                                type="datetime-local"
                                class="w-full text-xs bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-xl px-2.5 py-1.5 text-slate-800 dark:text-zinc-200 focus:outline-none focus:border-[#6C5CE7]"
                            />
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-600 dark:text-zinc-400 mb-1">
                                {{ $t('Reminder Note') }}
                            </label>
                            <input
                                v-model="reminderNotes"
                                type="text"
                                placeholder="e.g. Send quote..."
                                class="w-full text-xs bg-slate-50 dark:bg-zinc-900 border border-slate-200 dark:border-zinc-800 rounded-xl px-2.5 py-1.5 text-slate-800 dark:text-zinc-200 focus:outline-none focus:border-[#6C5CE7]"
                            />
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center justify-end gap-2 pt-2">
                        <Button
                            type="button"
                            variant="secondary"
                            size="sm"
                            @click="closeModal"
                        >
                            {{ $t('Skip') }}
                        </Button>
                        <Button
                            type="button"
                            variant="primary"
                            size="sm"
                            :disabled="isSubmitting"
                            @click="savePostCallDetails"
                        >
                            {{ isSubmitting ? $t('Saving...') : $t('Save Activity') }}
                        </Button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
