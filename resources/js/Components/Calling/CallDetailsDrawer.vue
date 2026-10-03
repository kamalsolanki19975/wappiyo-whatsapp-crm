<script setup>
import { ref, computed, watch } from 'vue';
import axios from 'axios';
import Avatar from '@/Components/UI/Avatar.vue';
import Badge from '@/Components/UI/Badge.vue';
import Button from '@/Components/UI/Button.vue';
import CallButton from '@/Components/Calling/CallButton.vue';
import { Link } from '@inertiajs/vue3';
import { trans } from 'laravel-vue-i18n';
import { toast } from 'vue3-toastify';

const props = defineProps({
    isOpen: {
        type: Boolean,
        default: false,
    },
    call: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits(['close', 'updated']);

const currentCall = ref(null);
const disposition = ref('');
const notes = ref('');
const followUpDate = ref('');
const reminderNotes = ref('');
const isSaving = ref(false);

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

watch(() => props.call, (newVal) => {
    if (newVal) {
        currentCall.value = { ...newVal };
        disposition.value = newVal.disposition || '';
        notes.value = newVal.notes || '';
        followUpDate.value = newVal.follow_up_at ? newVal.follow_up_at.substring(0, 16) : '';
    } else {
        currentCall.value = null;
    }
}, { immediate: true });

const statusVariant = computed(() => {
    switch (currentCall.value?.status) {
        case 'connected': return 'success';
        case 'completed': return 'neutral';
        case 'ringing':
        case 'connecting': return 'primary';
        case 'missed':
        case 'failed':
        case 'rejected': return 'danger';
        default: return 'warning';
    }
});

const closeDrawer = () => {
    emit('close');
};

const saveDisposition = async () => {
    if (!currentCall.value?.uuid) return;
    try {
        isSaving.value = true;
        const res = await axios.post(`/calls/${currentCall.value.uuid}/disposition`, {
            disposition: disposition.value,
        });
        if (res.data.success) {
            toast(trans('Disposition saved successfully'), { autoClose: 2000, type: 'success' });
            emit('updated', res.data.call);
        }
    } catch (err) {
        toast(err.response?.data?.message || trans('Failed to update disposition'), { autoClose: 3000, type: 'error' });
    } finally {
        isSaving.value = false;
    }
};

const saveNotes = async () => {
    if (!currentCall.value?.uuid) return;
    try {
        isSaving.value = true;
        const res = await axios.post(`/calls/${currentCall.value.uuid}/notes`, {
            notes: notes.value,
        });
        if (res.data.success) {
            toast(trans('Call notes saved successfully'), { autoClose: 2000, type: 'success' });
            emit('updated', res.data.call);
        }
    } catch (err) {
        toast(err.response?.data?.message || trans('Failed to update notes'), { autoClose: 3000, type: 'error' });
    } finally {
        isSaving.value = false;
    }
};

const saveFollowUp = async () => {
    if (!currentCall.value?.uuid || !followUpDate.value) return;
    try {
        isSaving.value = true;
        const res = await axios.post(`/calls/${currentCall.value.uuid}/follow-up`, {
            follow_up_at: followUpDate.value,
            reminder_notes: reminderNotes.value,
        });
        if (res.data.success) {
            toast(trans('Follow-up scheduled successfully'), { autoClose: 2000, type: 'success' });
            emit('updated', res.data.call);
        }
    } catch (err) {
        toast(err.response?.data?.message || trans('Failed to schedule follow-up'), { autoClose: 3000, type: 'error' });
    } finally {
        isSaving.value = false;
    }
};
</script>

<template>
    <div>
        <!-- Backdrop -->
        <transition
            enter-active-class="transition-opacity ease-linear duration-300"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition-opacity ease-linear duration-300"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="isOpen"
                class="fixed inset-0 z-40 bg-slate-900/60 dark:bg-black/80 backdrop-blur-xs"
                @click="closeDrawer"
            />
        </transition>

        <!-- Slide-over Drawer -->
        <transition
            enter-active-class="transform transition ease-in-out duration-300"
            enter-from-class="translate-x-full"
            enter-to-class="translate-x-0"
            leave-active-class="transform transition ease-in-out duration-300"
            leave-from-class="translate-x-0"
            leave-to-class="translate-x-full"
        >
            <div
                v-if="isOpen && currentCall"
                class="fixed inset-y-0 right-0 z-50 flex max-w-full pl-10"
            >
                <div class="w-screen max-w-md bg-white dark:bg-[#111113] shadow-2xl border-l border-slate-200 dark:border-zinc-800 flex flex-col">
                    
                    <!-- Header -->
                    <div class="px-6 py-5 border-b border-slate-200 dark:border-zinc-800 flex items-center justify-between bg-slate-50/50 dark:bg-zinc-900/40">
                        <div class="flex items-center gap-3">
                            <div
                                :class="[
                                    'w-10 h-10 rounded-xl flex items-center justify-center font-bold text-white shadow-xs',
                                    currentCall.direction === 'inbound'
                                        ? 'bg-blue-600 shadow-blue-500/20'
                                        : 'bg-emerald-600 shadow-emerald-500/20'
                                ]"
                            >
                                <svg v-if="currentCall.direction === 'inbound'" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                                </svg>
                                <svg v-else class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white capitalize">
                                    {{ currentCall.direction }} {{ $t('WhatsApp Call') }}
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-zinc-400">
                                    {{ currentCall.created_at_formatted || currentCall.created_at }}
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            @click="closeDrawer"
                            class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 rounded-xl hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors"
                        >
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                        </button>
                    </div>

                    <!-- Body Content (Scrollable) -->
                    <div class="flex-1 overflow-y-auto px-6 py-5 space-y-6">
                        
                        <!-- Status and Duration Card -->
                        <div class="p-4 rounded-2xl bg-slate-50 dark:bg-zinc-900/60 border border-slate-200/80 dark:border-zinc-800 flex items-center justify-between">
                            <div>
                                <span class="text-xs text-slate-500 dark:text-zinc-400 block mb-1 font-medium">{{ $t('Status') }}</span>
                                <Badge :variant="statusVariant" size="sm" class="capitalize">
                                    {{ currentCall.status }}
                                </Badge>
                            </div>
                            <div class="text-right">
                                <span class="text-xs text-slate-500 dark:text-zinc-400 block mb-1 font-medium">{{ $t('Duration') }}</span>
                                <span class="text-sm font-bold font-mono text-slate-900 dark:text-zinc-100">
                                    {{ currentCall.formatted_duration || '00:00' }}
                                </span>
                            </div>
                        </div>

                        <!-- Customer Details -->
                        <div class="rounded-2xl border border-slate-200/80 dark:border-zinc-800 p-4 space-y-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                                {{ $t('Customer') }}
                            </h4>
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <Avatar
                                        :name="currentCall.contact?.full_name || currentCall.customer_phone"
                                        size="md"
                                    />
                                    <div>
                                        <h5 class="text-sm font-bold text-slate-900 dark:text-white">
                                            {{ currentCall.contact?.full_name || $t('Unknown Contact') }}
                                        </h5>
                                        <p class="text-xs font-mono text-slate-500 dark:text-zinc-400">
                                            {{ currentCall.customer_phone }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5">
                                    <CallButton
                                        v-if="currentCall.contact"
                                        :contact="currentCall.contact"
                                        :phone="currentCall.customer_phone"
                                        variant="green"
                                        size="xs"
                                        :showLabel="false"
                                    />
                                    <Link
                                        v-if="currentCall.contact?.uuid"
                                        :href="`/chats/${currentCall.contact.uuid}`"
                                        class="p-2 rounded-xl bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-300 hover:bg-slate-200 transition-colors"
                                        :title="$t('Open Chat')"
                                    >
                                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/></svg>
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- Agent & Call Timing -->
                        <div class="rounded-2xl border border-slate-200/80 dark:border-zinc-800 p-4 space-y-3">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                                {{ $t('Call Information') }}
                            </h4>
                            <dl class="divide-y divide-slate-100 dark:divide-zinc-800/60 text-xs">
                                <div class="py-2 flex justify-between">
                                    <dt class="text-slate-500 dark:text-zinc-400">{{ $t('Agent') }}</dt>
                                    <dd class="font-medium text-slate-800 dark:text-zinc-200">
                                        {{ currentCall.user ? `${currentCall.user.first_name} ${currentCall.user.last_name || ''}` : $t('System / Inbound') }}
                                    </dd>
                                </div>
                                <div class="py-2 flex justify-between" v-if="currentCall.connected_at">
                                    <dt class="text-slate-500 dark:text-zinc-400">{{ $t('Connected At') }}</dt>
                                    <dd class="text-slate-700 dark:text-zinc-300">{{ currentCall.connected_at }}</dd>
                                </div>
                                <div class="py-2 flex justify-between" v-if="currentCall.ended_at">
                                    <dt class="text-slate-500 dark:text-zinc-400">{{ $t('Ended At') }}</dt>
                                    <dd class="text-slate-700 dark:text-zinc-300">{{ currentCall.ended_at }}</dd>
                                </div>
                                <div class="py-2 flex justify-between" v-if="currentCall.failure_reason">
                                    <dt class="text-rose-500 font-semibold">{{ $t('Failure Reason') }}</dt>
                                    <dd class="text-rose-600 dark:text-rose-400 font-medium text-right max-w-[200px]">
                                        {{ currentCall.failure_reason }}
                                    </dd>
                                </div>
                                <div class="py-2 flex justify-between" v-if="currentCall.provider_call_id">
                                    <dt class="text-slate-500 dark:text-zinc-400">{{ $t('Meta Call ID') }}</dt>
                                    <dd class="font-mono text-[10px] text-slate-600 dark:text-zinc-400 text-right max-w-[200px] truncate" :title="currentCall.provider_call_id">
                                        {{ currentCall.provider_call_id }}
                                    </dd>
                                </div>
                            </dl>
                        </div>

                        <!-- Call Outcome / Disposition -->
                        <div class="rounded-2xl border border-slate-200/80 dark:border-zinc-800 p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                                    {{ $t('Outcome / Disposition') }}
                                </h4>
                                <Button
                                    size="xs"
                                    variant="secondary"
                                    :disabled="isSaving"
                                    @click="saveDisposition"
                                >
                                    {{ $t('Save') }}
                                </Button>
                            </div>
                            <select
                                v-model="disposition"
                                class="w-full text-xs font-medium rounded-xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-slate-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            >
                                <option value="">{{ $t('Select an outcome...') }}</option>
                                <option v-for="d in dispositionsList" :key="d" :value="d">
                                    {{ d }}
                                </option>
                            </select>
                        </div>

                        <!-- Call Notes -->
                        <div class="rounded-2xl border border-slate-200/80 dark:border-zinc-800 p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                                    {{ $t('Call Notes') }}
                                </h4>
                                <Button
                                    size="xs"
                                    variant="secondary"
                                    :disabled="isSaving"
                                    @click="saveNotes"
                                >
                                    {{ $t('Save Notes') }}
                                </Button>
                            </div>
                            <textarea
                                v-model="notes"
                                rows="3"
                                :placeholder="$t('Add notes about conversation details, customer requests...')"
                                class="w-full text-xs rounded-xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 p-3 text-slate-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 placeholder-slate-400"
                            ></textarea>
                        </div>

                        <!-- Follow-up Scheduler -->
                        <div class="rounded-2xl border border-slate-200/80 dark:border-zinc-800 p-4 space-y-3">
                            <div class="flex items-center justify-between">
                                <h4 class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                                    {{ $t('Schedule Follow-up') }}
                                </h4>
                                <Button
                                    size="xs"
                                    variant="secondary"
                                    :disabled="isSaving || !followUpDate"
                                    @click="saveFollowUp"
                                >
                                    {{ $t('Schedule') }}
                                </Button>
                            </div>
                            <input
                                type="datetime-local"
                                v-model="followUpDate"
                                class="w-full text-xs rounded-xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-slate-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                            />
                            <input
                                v-if="followUpDate"
                                type="text"
                                v-model="reminderNotes"
                                :placeholder="$t('Follow-up reminder note...')"
                                class="w-full text-xs rounded-xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 px-3 py-2 text-slate-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-emerald-500 placeholder-slate-400"
                            />
                        </div>

                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 border-t border-slate-200 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-900/40 flex justify-end">
                        <Button variant="secondary" size="sm" @click="closeDrawer">
                            {{ $t('Close') }}
                        </Button>
                    </div>

                </div>
            </div>
        </transition>
    </div>
</template>
