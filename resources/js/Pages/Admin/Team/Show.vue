<template>
    <AppLayout>
        <div class="p-4 sm:p-6 lg:p-8 max-w-5xl mx-auto space-y-6 text-slate-900 dark:text-zinc-100 overflow-y-auto">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200/80 dark:border-zinc-800 pb-5">
                <div>
                    <h1 v-if="props.user === null" class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white mb-1">
                        {{ $t('Create user') }}
                    </h1>
                    <h1 v-else class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white mb-1">
                        {{ $t('Update user') }}
                    </h1>
                    <p class="flex items-center text-xs sm:text-sm text-slate-500 dark:text-zinc-400">
                        <svg class="w-4 h-4 mr-1.5 shrink-0 text-slate-400 dark:text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"/>
                        </svg>
                        <span v-if="props.user === null">{{ $t('Create administrative user and assign role') }}</span>
                        <span v-else>{{ $t('Update administrative user and assign role') }}</span>
                    </p>
                </div>
                <div>
                    <Link
                        href="/admin/team/users"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 text-slate-700 dark:text-zinc-200 text-xs font-semibold border border-slate-200/80 dark:border-zinc-700 transition-colors shadow-2xs"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                        <span>{{ $t('Back') }}</span>
                    </Link>
                </div>
            </div>

            <!-- Form Card -->
            <form @submit.prevent="submitForm()" class="bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800/80 rounded-2xl p-6 sm:p-8 shadow-xs space-y-6">
                <!-- Section 1: Personally Identifiable Information -->
                <div class="sm:flex border-b border-slate-100 dark:border-zinc-800/80 pb-6 gap-6">
                    <div class="sm:w-[35%] mb-4 sm:mb-0">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-zinc-300">{{ $t('Personally identifiable information') }}</h2>
                        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1 leading-relaxed">{{ $t('Basic profile information and account access credentials.') }}</p>
                    </div>
                    <div class="sm:w-[65%]">
                        <div class="grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormImage v-model="form.avatar" :name="'Avatar'" :error="form.errors.avatar" :label="$t('Upload image')" :imageUrl="props.user?.avatar ? '/media/' + props.user?.avatar : null" :class="'sm:col-span-6'"/>
                            <FormInput v-model="form.first_name" :name="$t('First name')" :error="form.errors.first_name" :type="'text'" :class="'sm:col-span-3'"/>
                            <FormInput v-model="form.last_name" :name="$t('Last name')" :error="form.errors.last_name" :type="'text'" :class="'sm:col-span-3'"/>
                            <FormInput v-model="form.email" :name="$t('Email')" :error="form.errors.email" :type="'text'" :class="'sm:col-span-3'"/>
                            <FormPhoneInput v-model="form.phone" :name="$t('Phone')" :error="form.errors.phone" :type="'text'" :class="'sm:col-span-3'"/>
                            <FormSelect v-model="form.role" :name="$t('Role')" :error="form.errors.role" :options="roleOptions()" :type="'text'" :class="'sm:col-span-6'"/>
                            <FormInput v-if="!props.user" v-model="form.password" :name="$t('Password')" :error="form.errors.password" :type="'password'" :class="'sm:col-span-3'"/>
                            <FormInput v-if="!props.user" v-model="form.password_confirmation" :name="$t('Confirm password')" :error="form.errors.password_confirmation" :type="'password'" :class="'sm:col-span-3'"/>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Address details -->
                <div class="sm:flex border-b border-slate-100 dark:border-zinc-800/80 pb-6 gap-6">
                    <div class="sm:w-[35%] mb-4 sm:mb-0">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-zinc-300">{{ $t('Address details') }}</h2>
                        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1 leading-relaxed">{{ $t('Physical address and regional contact coordinates.') }}</p>
                    </div>
                    <div class="sm:w-[65%]">
                        <div class="grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormInput v-model="form.street" :name="$t('Street')" :error="form.errors.street" :type="'text'" :class="'sm:col-span-6'"/>
                            <FormInput v-model="form.city" :name="$t('City')" :error="form.errors.city" :type="'text'" :class="'sm:col-span-3'"/>
                            <FormInput v-model="form.state" :name="$t('State')" :error="form.errors.state" :type="'text'" :class="'sm:col-span-3'"/>
                            <FormInput v-model="form.zip" :name="$t('Zip code')" :error="form.errors.zip" :type="'text'" :class="'sm:col-span-3'"/>
                            <FormInput v-model="form.country" :name="$t('Country')" :error="form.errors.country" :type="'text'" :class="'sm:col-span-3'"/>
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="flex items-center justify-end gap-3 pt-2">
                    <Link
                        href="/admin/team/users"
                        class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-600 hover:text-slate-900 dark:text-zinc-400 dark:hover:text-white transition-colors"
                    >
                        {{ $t('Cancel') }}
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-md shadow-emerald-600/20 active:scale-[0.98] transition-all disabled:opacity-50 cursor-pointer"
                    >
                        <svg v-if="form.processing" class="w-3.5 h-3.5 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>{{ form.processing ? $t('Saving...') : $t('Save') }}</span>
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
    import FormImage from '@/Components/FormImage.vue';
    import FormInput from '@/Components/FormInput.vue';
    import FormPhoneInput from '@/Components/FormPhoneInput.vue';
    import FormSelect from '@/Components/FormSelect.vue';

    const props = defineProps({ title: String, user: Object, roles: Object });
    const fileUrl = ref(null);

    const getAddressDetail = (value, key) => {
        if(value){
            const address = JSON.parse(value);
            return address?.[key] ?? null;
        } else {
            return null;
        }
    }

    const form = useForm({
        first_name: props.user?.first_name,
        last_name: props.user?.last_name,
        email: props.user?.email,
        phone: props.user?.phone,
        role: props.user?.role?.uuid,
        password: null,
        password_confirmation: null,
        avatar: null,
        street: getAddressDetail(props.user?.address, 'street'),
        city: getAddressDetail(props.user?.address, 'city'),
        state: getAddressDetail(props.user?.address, 'state'),
        zip: getAddressDetail(props.user?.address, 'zip'),
        country: getAddressDetail(props.user?.address, 'country')
    })

    const roleOptions = () => {
        return props.roles.map((option) => ({
            value: option.uuid,
            label: option.name,
        }));
    };

    const submitForm = async () => {
        const url = props.user ? window.location.pathname : '/admin/team/users';

        form[props.user ? 'put' : 'post'](url, {
            preserveScroll: true,
        });
    };
</script>