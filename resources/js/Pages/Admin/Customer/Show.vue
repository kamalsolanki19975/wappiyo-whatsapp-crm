<template>
    <AppLayout>
        <div class="pt-4 px-4 md:pt-8 md:p-8 text-slate-900 dark:text-zinc-100 h-full md:overflow-y-auto">
            <div v-if="props.user === null" class="flex items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ $t('Create user') }}</h1>
                    <p class="flex items-center gap-1.5 text-sm text-slate-500 dark:text-zinc-400 mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                        <span>{{ $t('Add a new customer account to the platform') }}</span>
                    </p>
                </div>
                <div>
                    <Link href="/admin/users" class="inline-flex items-center gap-2 rounded-xl bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-zinc-200 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                        <span>{{ $t('Back') }}</span>
                    </Link>
                </div>
            </div>
            <div v-if="props.user" class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
                <div class="flex items-center gap-4">
                    <img v-if="user.avatar" class="w-16 h-16 rounded-2xl object-cover border border-slate-200 dark:border-zinc-700 shadow-sm" :src="user.avatar">
                    <div v-else class="w-16 h-16 rounded-2xl bg-purple-100 dark:bg-purple-950/60 text-[#6C5CE7] dark:text-purple-300 flex items-center justify-center font-bold text-2xl shrink-0 shadow-sm border border-purple-200/50 dark:border-purple-800/40">
                        {{ props.user.first_name ? props.user.first_name[0].toUpperCase() : 'U' }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">{{ props.user.full_name }}</h1>
                        <p class="text-sm text-slate-500 dark:text-zinc-400 mt-0.5">{{ props.user.email }}</p>
                    </div>
                </div>
                <div>
                    <Link href="/admin/users" class="inline-flex items-center gap-2 rounded-xl bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-zinc-200 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                        <span>{{ $t('Back') }}</span>
                    </Link>
                </div>
            </div>
            <div v-if="props.user" class="flex border-b border-slate-200 dark:border-zinc-800 space-x-2 text-sm mb-6">
                <button
                    type="button"
                    @click="changeTab('user')"
                    :class="[
                        'cursor-pointer px-4 py-2.5 font-medium rounded-t-lg transition-colors',
                        tab === 'user'
                            ? 'border-b-2 border-[#6C5CE7] text-[#6C5CE7] dark:text-purple-400 bg-white dark:bg-[#111113]'
                            : 'text-slate-500 dark:text-zinc-400 hover:text-slate-800 dark:hover:text-zinc-200'
                    ]"
                >
                    {{ $t('User details') }}
                </button>
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
                    {{ $t('Organization details') }}
                </button>
            </div>
            <div v-if="props.user && tab === 'organization'" class="pt-2">
                <OrganizationTable :rows="props.organizations" :filters="props.filters"/>
            </div>
            <form v-if="tab === 'user'" @submit.prevent="submitForm()" class="bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-sm rounded-2xl py-6 px-6 sm:px-8 mb-8">
                <div class="sm:flex border-b border-slate-100 dark:border-zinc-800/80 py-6">
                    <div class="sm:w-[35%] mb-4 sm:mb-0 pr-4">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">{{ $t('User details') }}</h3>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">{{ $t('Basic account and profile information') }}</p>
                    </div>
                    <div class="sm:w-[65%]">
                        <div class="sm:w-[90%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormImage v-model="form.avatar" :name="$t('Avatar')" :error="form.errors.avatar" :label="$t('Upload image')" :imageUrl="props.user?.avatar ? '/media/' + props.user?.avatar : null" :class="'sm:col-span-6'"/>
                            <FormInput v-model="form.first_name" :name="$t('First name')" :error="form.errors.first_name" :type="'text'" :class="'sm:col-span-3'"/>
                            <FormInput v-model="form.last_name" :name="$t('Last name')" :error="form.errors.last_name" :type="'text'" :class="'sm:col-span-3'"/>
                            <FormInput v-model="form.email" :name="$t('Email')" :error="form.errors.email" :type="'text'" :class="'sm:col-span-3'"/>
                            <FormPhoneInput v-model="form.phone" :name="$t('Phone')" :error="form.errors.phone" :type="'text'" :class="'sm:col-span-3'"/>
                            <FormInput v-if="!props.user" v-model="form.password" :name="$t('Password')" :error="form.errors.password" :type="'password'" :class="'sm:col-span-3'"/>
                            <FormInput v-if="!props.user" v-model="form.password_confirmation" :name="$t('Confirm password')" :error="form.errors.password_confirmation" :type="'password'" :class="'sm:col-span-3'"/>
                        </div>
                    </div>
                </div>
                <div v-if="!props.user" class="sm:flex border-b border-slate-100 dark:border-zinc-800/80 py-6">
                    <div class="sm:w-[35%] mb-4 sm:mb-0 pr-4">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">{{ $t('Organization') }}</h3>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">{{ $t('Optionally create an initial organization for this user') }}</p>
                    </div>
                    <div class="sm:w-[65%]">
                        <div class="sm:w-[90%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormCheckbox  @input="toggleCreateOrganization" v-model="create_organization" :name="$t('Create organization')" :label="$t('Create organization')" :value="'organization'" :type="'checkbox'" :class="'sm:col-span-6'"/>
                            <FormInput v-if="create_organization" v-model="form.organization_name" :name="$t('Organization name')" :error="form.errors.organization_name" :type="'text'" :class="'sm:col-span-6'"/>
                        </div>
                    </div>
                </div>
                <div class="sm:flex py-6">
                    <div class="sm:w-[35%] mb-4 sm:mb-0 pr-4">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">{{ $t('Address details') }}</h3>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">{{ $t('User contact location details') }}</p>
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
</template>
<script setup>
    import AppLayout from "./../Layout/App.vue";
    import{ ref } from 'vue';
    import { Link, useForm } from "@inertiajs/vue3";
    import FormCheckbox from '@/Components/FormCheckbox.vue';
    import FormImage from '@/Components/FormImage.vue';
    import FormInput from '@/Components/FormInput.vue';
    import FormPhoneInput from '@/Components/FormPhoneInput.vue';
    import OrganizationTable from '@/Components/Tables/OrganizationTable.vue';

    const props = defineProps({ title: String, user: Object, roles: Object, organizations: Object, filters: Object });
    const create_organization = ref(false);
    const tab = ref('user');

    const getAddressDetail = (value, key) => {
        if(value){
            const address = JSON.parse(value);
            return address?.[key] ?? null;
        } else {
            return null;
        }
    };

    const toggleCreateOrganization = () => {
        if(!create_organization.value){
            form.organization_name = null;
        } else {
            form.organization_name = undefined;
        }
    }

    const form = useForm({
        first_name: props.user?.first_name,
        last_name: props.user?.last_name,
        email: props.user?.email,
        phone: props.user?.phone,
        role: props.user?.role?.uuid,
        avatar: undefined,
        street: getAddressDetail(props.user?.address, 'street'),
        city: getAddressDetail(props.user?.address, 'city'),
        state: getAddressDetail(props.user?.address, 'state'),
        zip: getAddressDetail(props.user?.address, 'zip'),
        country: getAddressDetail(props.user?.address, 'country'),
        ...(props.user ? {} : { password: null, password_confirmation: null }),
        organization_name: undefined,
    });

    const changeTab = (value) => {
        tab.value = value;
    }

    const submitForm = async () => {
        const url = props.user ? window.location.pathname : '/admin/users';

        form[props.user ? 'put' : 'post'](url, {
            preserveScroll: true,
        });
    };
</script>