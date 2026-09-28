<template>
    <AppLayout>
        <div class="pt-4 px-4 md:pt-8 md:p-8 text-slate-900 dark:text-zinc-100 h-full overflow-y-auto">
            <div v-if="props.organization === null" class="flex items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $t('Create organization') }}</h1>
                    <p class="flex items-center gap-1.5 text-sm text-slate-500 dark:text-zinc-400 mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                        <span>{{ $t('Add a new organization account to the platform') }}</span>
                    </p>
                </div>
                <div>
                    <Link href="/admin/organizations" class="inline-flex items-center gap-2 rounded-xl bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-zinc-200 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                        <span>{{ $t('Back') }}</span>
                    </Link>
                </div>
            </div>
            <div v-if="props.organization" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <div class="w-16 h-16 rounded-2xl bg-purple-100 dark:bg-purple-950/60 text-[#6C5CE7] dark:text-purple-300 flex items-center justify-center font-bold text-2xl shrink-0 shadow-sm border border-purple-200/50 dark:border-purple-800/40">
                        {{ props.organization.name ? props.organization.name[0].toUpperCase() : 'O' }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ props.organization.name }}</h1>
                        <div class="flex flex-wrap items-center gap-2 mt-1 text-xs text-slate-500 dark:text-zinc-400">
                            <span>{{ $t('Subscription plan') }}: <strong class="text-slate-700 dark:text-zinc-200">{{ props.organization?.subscription?.plan?.name ?? 'Not set' }}</strong></span>
                            <span v-if="props.organization?.subscription?.status === 'trial'" class="bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 font-semibold text-[10px] py-0.5 px-2 rounded-md border border-amber-200 dark:border-amber-800/40">{{ $t('Trial period') }}</span>
                            <span class="text-slate-300 dark:text-zinc-600">•</span>
                            <span>{{ $t('Valid until') }}: <strong class="text-slate-700 dark:text-zinc-200">{{ props.organization?.subscription?.valid_until ?? 'Not set' }}</strong></span>
                        </div>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button type="button" @click="toggleFormModal()" class="inline-flex items-center gap-2 rounded-xl bg-[#6C5CE7] hover:bg-[#5B46D6] px-4 py-2 text-sm font-semibold text-white shadow-sm shadow-purple-600/30 transition-all cursor-pointer">
                        {{ $t('Create transaction') }}
                    </button>
                    <Link href="/admin/organizations" class="inline-flex items-center gap-2 rounded-xl bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-zinc-200 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                        <span>{{ $t('Back') }}</span>
                    </Link>
                </div>
            </div>
            <div v-if="props.organization" class="flex border-b border-slate-200 dark:border-zinc-800 space-x-2 text-sm mb-6">
                <button
                    type="button"
                    @click="changeTab('organization')"
                    :class="[
                        'cursor-pointer px-4 py-2.5 font-medium rounded-t-lg transition-colors',
                        tab === 'organization'
                            ? 'border-b-2 border-[#6C5CE7] text-[#6C5CE7] dark:text-purple-400 bg-white dark:bg-[#111113]'
                            : 'text-slate-500 dark:text-zinc-400 hover:text-slate-800 dark:hover:text-zinc-200'
                    ]"
                >
                    {{ $t('User details') }}
                </button>
                <button
                    type="button"
                    @click="changeTab('team')"
                    :class="[
                        'cursor-pointer px-4 py-2.5 font-medium rounded-t-lg transition-colors',
                        tab === 'team'
                            ? 'border-b-2 border-[#6C5CE7] text-[#6C5CE7] dark:text-purple-400 bg-white dark:bg-[#111113]'
                            : 'text-slate-500 dark:text-zinc-400 hover:text-slate-800 dark:hover:text-zinc-200'
                    ]"
                >
                    {{ $t('Team') }}
                </button>
                <button
                    type="button"
                    @click="changeTab('billing')"
                    :class="[
                        'cursor-pointer px-4 py-2.5 font-medium rounded-t-lg transition-colors',
                        tab === 'billing'
                            ? 'border-b-2 border-[#6C5CE7] text-[#6C5CE7] dark:text-purple-400 bg-white dark:bg-[#111113]'
                            : 'text-slate-500 dark:text-zinc-400 hover:text-slate-800 dark:hover:text-zinc-200'
                    ]"
                >
                    {{ $t('Billing history') }}
                </button>
            </div>
            <div v-if="props.organization && tab === 'team'" class="pt-2">
                <UserTable :rows="props.users" :filters="props.filters" :type="'user'" :showRole="true" :showDeleteBtn="false"/>
            </div>
            <div v-if="props.organization && tab === 'billing'" class="pt-2">
                <BillingTable :rows="props.invoices" :filters="props.filters" :uuid="props.organization.uuid"/>
            </div>
            <form v-if="tab === 'organization'" @submit.prevent="submitForm()" class="bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-sm rounded-2xl py-6 px-6 sm:px-8 mb-8">
                <div class="sm:flex border-b border-slate-100 dark:border-zinc-800/80 py-6">
                    <div class="sm:w-[35%] mb-4 sm:mb-0 pr-4">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">{{ $t('Organization details') }}</h3>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">{{ $t('Basic details for the organization') }}</p>
                    </div>
                    <div class="sm:w-[65%]">
                        <div class="sm:w-[90%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormInput v-model="form.name" :name="$t('Name')" :error="form.errors.name" :type="'text'" :class="'sm:col-span-6'"/>
                            <FormSelect v-model="form.plan" :name="$t('Subscription plan')" :error="form.errors.plan" :options="roleOptions()" :type="'text'" :class="'sm:col-span-6'"/>
                        </div>
                    </div>
                </div>
                <div v-if="props.organization === null" class="sm:flex border-b border-slate-100 dark:border-zinc-800/80 py-6">
                    <div class="sm:w-[35%] mb-4 sm:mb-0 pr-4">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">{{ $t('User details') }}</h3>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">{{ $t('Enter the details of the main administrative user of this organization') }}</p>
                    </div>
                    <div class="sm:w-[65%]">
                        <div class="sm:w-[90%] flex p-1 bg-slate-100 dark:bg-zinc-800/80 rounded-xl mb-5 border border-slate-200/60 dark:border-zinc-700/50">
                            <button
                                type="button"
                                class="w-1/2 py-2 text-xs font-semibold rounded-lg transition-all"
                                :class="form.create_user === 1 ? 'bg-white dark:bg-[#18181B] text-[#6C5CE7] dark:text-purple-300 shadow-sm' : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white'"
                                @click="switchUserType(1)"
                            >
                                {{ $t('Add user') }}
                            </button>
                            <button
                                type="button"
                                class="w-1/2 py-2 text-xs font-semibold rounded-lg transition-all"
                                :class="form.create_user === 0 ? 'bg-white dark:bg-[#18181B] text-[#6C5CE7] dark:text-purple-300 shadow-sm' : 'text-slate-600 dark:text-zinc-400 hover:text-slate-900 dark:hover:text-white'"
                                @click="switchUserType(0)"
                            >
                                {{ $t('Select existing user') }}
                            </button>
                        </div>
                        <div v-if="form.create_user === 1" class="sm:w-[90%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormInput v-model="form.first_name" :name="$t('First name')" :error="form.errors.first_name" :type="'text'" :class="'sm:col-span-3'"/>
                            <FormInput v-model="form.last_name" :name="$t('Last name')" :error="form.errors.last_name" :type="'text'" :class="'sm:col-span-3'"/>
                            <FormInput v-model="form.email" :name="$t('Email')" :error="form.errors.email" :type="'text'" :class="'sm:col-span-3'"/>
                            <FormPhoneInput v-model="form.phone" :name="$t('Phone')" :error="form.errors.phone" :type="'text'" :class="'sm:col-span-3'"/>
                            <FormInput v-model="form.password" :name="$t('Password')" :error="form.errors.password" :type="'password'" :class="'sm:col-span-3'"/>
                            <FormInput v-model="form.password_confirmation" :name="$t('Confirm password')" :error="form.errors.password_confirmation" :type="'password'" :class="'sm:col-span-3'"/>
                        </div>
                        <div v-else class="sm:w-[90%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormInput v-model="form.email" :name="$t('Email')" :error="form.errors.email" :type="'text'" :class="'sm:col-span-6'"/>
                        </div>
                    </div>
                </div>
                <div class="sm:flex py-6">
                    <div class="sm:w-[35%] mb-4 sm:mb-0 pr-4">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">{{ $t('Address details') }}</h3>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">{{ $t('Physical or mailing address for the organization') }}</p>
                    </div>
                    <div class="sm:w-[65%]">
                        <div class="sm:w-[90%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormInput v-model="form.street" :name="$t('Street')" :error="form.errors.street" :type="'text'" :class="'sm:col-span-6'"/>
                            <FormInput v-model="form.city" :name="$t('City')" :error="form.errors.city" :type="'text'" :class="'sm:col-span-3'"/>
                            <FormInput v-model="form.state" :name="$t('State')" :error="form.errors.state" :type="'text'" :class="'sm:col-span-3'"/>
                            <FormInput v-model="form.zip" :name="$t('Zip code')" :error="form.errors.zip" :type="'text'" :class="'sm:col-span-3'"/>
                            <FormInput v-model="form.country" :name="$t('Country')" :error="form.errors.country" :type="'text'" :class="'sm:col-span-3'"/>
                        </div>
                    </div>
                </div>
                <div class="pt-6 border-t border-slate-100 dark:border-zinc-800/80 flex justify-end">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 rounded-xl bg-[#6C5CE7] hover:bg-[#5B46D6] px-6 py-2.5 text-sm font-semibold text-white shadow-sm shadow-purple-600/30 transition-all disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer"
                    >
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ $t('Save') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
    <Modal :label="$t('Create transaction')" :isOpen="isOpenFormModal" @close="isOpenFormModal = false">
        <div class="mt-4">
            <form @submit.prevent="submitForm1()">
                <div class="grid grid-cols-1 gap-x-6 gap-y-4 sm:grid-cols-6">
                    <FormSelect v-model="form1.type" :name="$t('Transaction type')" :error="form1.errors.type" :options="typeOptions" :class="'sm:col-span-3'"/>
                    <FormInput v-model="form1.amount" :name="$t('Amount')" :error="form1.errors.amount" :type="'number'" :class="'sm:col-span-3'"/>
                    <FormSelect v-if="form1.type === 'payment'" v-model="form1.method" :name="$t('Payment method')" :error="form1.errors.method" :options="paymentOptions" :class="'sm:col-span-6'"/>
                    <FormInput v-else v-model="form1.description" :name="$t('Description')" :error="form1.errors.description" :type="'text'" :class="'sm:col-span-6'"/>
                </div>

                <div class="bg-rose-50 dark:bg-rose-950/30 border border-rose-200 dark:border-rose-900/40 p-3 rounded-xl mt-6">
                    <p class="text-xs flex items-center gap-2 text-rose-700 dark:text-rose-400">
                        <svg class="w-4 h-4 shrink-0" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M8.485 2.495c.673-1.167 2.357-1.167 3.03 0l6.28 10.875c.673 1.167-.17 2.625-1.516 2.625H3.72c-1.347 0-2.189-1.458-1.515-2.625zM10 5a.75.75 0 0 1 .75.75v3.5a.75.75 0 0 1-1.5 0v-3.5A.75.75 0 0 1 10 5m0 9a1 1 0 1 0 0-2a1 1 0 0 0 0 2" clip-rule="evenodd"/></svg>
                        <span>{{ $t('You can\'t undo this transaction once you save it') }}</span>
                    </p>
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button" @click="toggleFormModal()" class="px-4 py-2 text-xs font-semibold text-slate-700 dark:text-zinc-300 bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 rounded-xl transition-colors">{{ $t('Cancel') }}</button>
                    <button 
                        type="submit"
                        :class="['inline-flex items-center justify-center rounded-xl bg-[#6C5CE7] hover:bg-[#5B46D6] px-5 py-2 text-xs font-semibold text-white shadow-sm shadow-purple-600/30 transition-all cursor-pointer', { 'opacity-50': isLoading }]"
                        :disabled="isLoading">
                        <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span>{{ $t('Save') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </Modal>
</template>
<script setup>
    import AppLayout from "./../Layout/App.vue";
    import{ ref } from 'vue';
    import { Link, useForm } from "@inertiajs/vue3";
    import FormInput from '@/Components/FormInput.vue';
    import FormPhoneInput from '@/Components/FormPhoneInput.vue';
    import FormSelect from '@/Components/FormSelect.vue';
    import BillingTable from '@/Components/Tables/BillingTable.vue';
    import Modal from '@/Components/Modal.vue';
    import UserTable from '@/Components/Tables/UserTable.vue';
    import { trans } from 'laravel-vue-i18n';

    const props = defineProps({ 
        showAddBtn: {
            type: Boolean,
            default: true
        }, 
        title: String, 
        organization: Object, 
        users: Object, 
        invoices: Object, 
        plans: Object, 
        filters: Object, 
        mode: String 
    });

    const tab = ref('organization');

    const getAddressDetail = (value, key) => {
        if(value){
            const address = JSON.parse(value);
            return address?.[key] ?? null;
        } else {
            return null;
        }
    };

    const form = useForm({
        name: props.organization?.name,
        plan: props.organization?.subscription?.plan?.uuid,
        create_user: 1,
        first_name: null,
        last_name: null,
        email: null,
        phone: null,
        password: null, 
        password_confirmation: null,
        street: getAddressDetail(props.organization?.address, 'street'),
        city: getAddressDetail(props.organization?.address, 'city'),
        state: getAddressDetail(props.organization?.address, 'state'),
        zip: getAddressDetail(props.organization?.address, 'zip'),
        country: getAddressDetail(props.organization?.address, 'country')
    });

    const typeOptions = ref([
        { value: 'credit', label: trans('Credit') },
        { value: 'debit', label: trans('Debit') },
        { value: 'payment', label: trans('Payment') },
    ])

    const paymentOptions = ref([
        { value: 'manual', label: trans('Manual') },
        { value: 'bank', label: trans('Bank') },
    ])

    const form1 = useForm({
        uuid: props.organization?.uuid,
        type: null,
        amount: null,
        method: null,
        description: null,
    })

    const roleOptions = () => {
        return props.plans.map((option) => ({
            value: option.uuid,
            label: option.name,
        }));
    };

    const isOpenFormModal = ref(false);
    
    const isLoading = ref(false);

    const changeTab = (value) => {
        tab.value = value;
    }

    const switchUserType = (value) => {
        form.create_user = value;
        if(value === 0){
            form.first_name = null;
            form.last_name = null;
            form.email = null;
            form.phone = null;
            form.password = null;
            form.password_confirmation = null;
        } else {
            form.email = null;
        }
    }

    const submitForm = async () => {
        const url = props.organization ? window.location.pathname : '/admin/organizations';

        form[props.organization ? 'put' : 'post'](url, {
            preserveScroll: true,
        });
    };

    const toggleFormModal = () => {
        isOpenFormModal.value = !isOpenFormModal.value;
    }

    const submitForm1 = async () => {
        form1.post('/admin/billing', {
            preserveScroll: true,
            onSuccess: () => {
                toggleFormModal()
                changeTab('billing')
            },
        })
    };
</script>