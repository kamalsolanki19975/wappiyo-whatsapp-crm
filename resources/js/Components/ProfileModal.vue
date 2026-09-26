<script setup>
    import { useForm } from "@inertiajs/vue3";
    import { ref } from "vue";
    import { TransitionRoot, TransitionChild, Dialog, DialogPanel, DialogTitle, TabGroup, TabList, Tab, TabPanels, TabPanel } from '@headlessui/vue';
    import FormInput from '@/Components/FormInput.vue';

    const props = defineProps({
        user: Object,
        organization: Object,
        role: String,
        isOpen: Boolean,
    });

    const isLoading = ref(false);

    const form1 = useForm({
        first_name: props.user?.first_name || '',
        last_name: props.user?.last_name || '',
        email: props.user?.email || ''
    });

    const form3 = useForm({
        old_password: null,
        password: null,
        password_confirmation: null
    });

    const submitForm = async () => {
        isLoading.value = true;
        form1.put('/profile', {
            preserveScroll: true,
            onFinish: () => {
                isLoading.value = false;
            }
        });
    };

    const submitForm3 = async () => {
        isLoading.value = true;
        form3.put('/profile/password', {
            preserveScroll: true,
            onFinish: () => {
                isLoading.value = false;
                form3.reset();
            }
        });
    };

    const emit = defineEmits(['close']);

    function closeModal() {
        emit('close', true);
    }
</script>

<template>
    <TransitionRoot appear :show="isOpen" as="template">
        <Dialog as="div" @close="closeModal" class="relative z-50">
            <TransitionChild
                as="template"
                enter="duration-300 ease-out"
                enter-from="opacity-0"
                enter-to="opacity-100"
                leave="duration-200 ease-in"
                leave-from="opacity-100"
                leave-to="opacity-0"
            >
                <div class="fixed inset-0 bg-black/60 backdrop-blur-xs" />
            </TransitionChild>

            <div class="fixed inset-0 overflow-y-auto">
                <div class="flex min-h-full items-center justify-center p-4 text-center">
                    <TransitionChild
                        as="template"
                        enter="duration-300 ease-out"
                        enter-from="opacity-0 scale-95"
                        enter-to="opacity-100 scale-100"
                        leave="duration-200 ease-in"
                        leave-from="opacity-100 scale-100"
                        leave-to="opacity-0 scale-95"
                    >
                        <DialogPanel class="w-full max-w-md transform overflow-hidden rounded-2xl bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 p-6 text-left align-middle shadow-2xl transition-all">
                            <!-- Header with Avatar Badge -->
                            <div class="flex items-center justify-between pb-4 border-b border-slate-100 dark:border-zinc-800">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-xl bg-gradient-to-tr from-[#6C5CE7] to-[#8B5CF6] flex items-center justify-center text-white font-bold text-sm shadow-md shadow-indigo-500/20">
                                        {{ (user?.first_name?.[0] || 'U').toUpperCase() }}
                                    </div>
                                    <div>
                                        <DialogTitle as="h2" class="text-base font-bold text-slate-900 dark:text-white">
                                            {{ $t('Account & Security') }}
                                        </DialogTitle>
                                        <p class="text-xs text-slate-500 dark:text-zinc-400">
                                            {{ user?.email }}
                                        </p>
                                    </div>
                                </div>
                                <button 
                                    @click="closeModal" 
                                    type="button" 
                                    class="text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-zinc-800 transition-colors"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg>
                                </button>
                            </div>

                            <!-- Tabs -->
                            <TabGroup class="mt-4">
                                <TabList class="flex space-x-1 rounded-xl bg-slate-100 dark:bg-zinc-800 p-1">
                                    <Tab as="template" v-slot="{ selected }">
                                        <button
                                            :class="[
                                                'w-full rounded-lg py-2 text-xs font-semibold tracking-wide transition-all focus:outline-none',
                                                selected
                                                    ? 'bg-white dark:bg-zinc-900 text-slate-900 dark:text-white shadow-xs'
                                                    : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white',
                                            ]"
                                        >
                                            {{ $t('My Profile') }}
                                        </button>
                                    </Tab>
                                    <Tab as="template" v-slot="{ selected }">
                                        <button
                                            :class="[
                                                'w-full rounded-lg py-2 text-xs font-semibold tracking-wide transition-all focus:outline-none',
                                                selected
                                                    ? 'bg-white dark:bg-zinc-900 text-slate-900 dark:text-white shadow-xs'
                                                    : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white',
                                            ]"
                                        >
                                            {{ $t('Password & Security') }}
                                        </button>
                                    </Tab>
                                </TabList>

                                <TabPanels class="mt-4">
                                    <!-- Profile Tab -->
                                    <TabPanel>
                                        <form @submit.prevent="submitForm()" class="space-y-4">
                                            <div class="grid grid-cols-2 gap-3">
                                                <FormInput v-model="form1.first_name" :name="$t('First name')" :error="form1.errors.first_name" type="text" class="col-span-1"/>
                                                <FormInput v-model="form1.last_name" :name="$t('Last name')" :error="form1.errors.last_name" type="text" class="col-span-1"/>
                                            </div>
                                            <FormInput v-model="form1.email" :name="$t('Email Address')" :error="form1.errors.email" type="email" class="w-full"/>

                                            <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-zinc-800">
                                                <button type="button" @click="closeModal" class="px-4 py-2 text-xs font-medium text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-xl transition-colors">
                                                    {{ $t('Cancel') }}
                                                </button>
                                                <button 
                                                    type="submit"
                                                    :disabled="isLoading"
                                                    class="inline-flex items-center gap-2 px-5 py-2 text-xs font-semibold text-white bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] hover:from-[#5b4bc4] hover:to-[#7c4deb] rounded-xl shadow-sm shadow-indigo-500/20 active:scale-[0.98] transition-all disabled:opacity-50"
                                                >
                                                    <svg v-if="isLoading" class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    <span>{{ $t('Save Changes') }}</span>
                                                </button>
                                            </div>
                                        </form>
                                    </TabPanel>

                                    <!-- Security Tab -->
                                    <TabPanel>
                                        <form @submit.prevent="submitForm3()" class="space-y-4">
                                            <FormInput v-model="form3.old_password" :name="$t('Current Password')" :error="form3.errors.old_password" type="password" class="w-full"/>
                                            <FormInput v-model="form3.password" :name="$t('New Password')" :error="form3.errors.password" type="password" class="w-full"/>
                                            <FormInput v-model="form3.password_confirmation" :name="$t('Confirm New Password')" :error="form3.errors.password_confirmation" type="password" class="w-full"/>

                                            <div class="pt-3 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-zinc-800">
                                                <button type="button" @click="closeModal" class="px-4 py-2 text-xs font-medium text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-xl transition-colors">
                                                    {{ $t('Cancel') }}
                                                </button>
                                                <button 
                                                    type="submit"
                                                    :disabled="isLoading"
                                                    class="inline-flex items-center gap-2 px-5 py-2 text-xs font-semibold text-white bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] hover:from-[#5b4bc4] hover:to-[#7c4deb] rounded-xl shadow-sm shadow-indigo-500/20 active:scale-[0.98] transition-all disabled:opacity-50"
                                                >
                                                    <svg v-if="isLoading" class="animate-spin -ml-1 mr-1 h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    <span>{{ $t('Update Password') }}</span>
                                                </button>
                                            </div>
                                        </form>
                                    </TabPanel>
                                </TabPanels>
                            </TabGroup>
                        </DialogPanel>
                    </TransitionChild>
                </div>
            </div>
        </Dialog>
    </TransitionRoot>
</template>