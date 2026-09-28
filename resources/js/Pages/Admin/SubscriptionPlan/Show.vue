<template>
    <AppLayout>
        <div class="pt-4 px-4 md:pt-8 md:p-8 text-slate-900 dark:text-zinc-100 h-full overflow-y-auto">
            <div class="flex items-center justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                        {{ props.plan === null ? $t('Create plan') : $t('Update plan') }}
                    </h1>
                    <p class="flex items-center gap-1.5 text-sm text-slate-500 dark:text-zinc-400 mt-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 16v-4"/><path d="M12 8h.01"/></svg>
                        <span>{{ $t('Configure subscription plan pricing, limits, and public marketing display') }}</span>
                    </p>
                </div>
                <div>
                    <Link href="/admin/plans" class="inline-flex items-center gap-2 rounded-xl bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 px-4 py-2 text-sm font-semibold text-slate-700 dark:text-zinc-200 transition-all">
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                        <span>{{ $t('Back') }}</span>
                    </Link>
                </div>
            </div>

            <form @submit.prevent="submitForm()" class="bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-sm rounded-2xl py-6 px-6 sm:px-8 mb-8">
                <!-- Name and Description -->
                <div class="sm:flex border-b border-slate-100 dark:border-zinc-800/80 py-6">
                    <div class="sm:w-[35%] mb-4 sm:mb-0 pr-4">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">{{ $t('Basic info') }}</h3>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">{{ $t('Plan name and public card description') }}</p>
                    </div>
                    <div class="sm:w-[65%]">
                        <div class="sm:w-[90%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormInput v-model="form.name" :name="$t('Plan name')" :error="form.errors.name" :type="'text'" :class="'sm:col-span-6'"/>
                            <div class="sm:col-span-6">
                                <label class="block text-xs font-semibold uppercase tracking-wider text-slate-700 dark:text-zinc-300 mb-1.5">{{ $t('Description') }}</label>
                                <textarea
                                    v-model="form.description"
                                    rows="2"
                                    class="block w-full rounded-lg border border-slate-300 dark:border-zinc-700 bg-white dark:bg-[#18181B] py-2 px-3.5 text-sm text-slate-900 dark:text-zinc-100 focus:border-[#6C5CE7] focus:ring-2 focus:ring-[#6C5CE7]/20 outline-none transition-all"
                                    :placeholder="$t('e.g. Best for growing teams needing advanced automation')"
                                ></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Status & Featured -->
                <div class="sm:flex border-b border-slate-100 dark:border-zinc-800/80 py-6">
                    <div class="sm:w-[35%] mb-4 sm:mb-0 pr-4">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">{{ $t('Status & Visibility') }}</h3>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">{{ $t('Control whether this plan is active and highlighted on the website') }}</p>
                    </div>
                    <div class="sm:w-[65%]">
                        <div class="sm:w-[90%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormSelect v-model="form.status" :options="statusOptions" :error="form.errors.status" :name="$t('Status')" :class="'sm:col-span-3'" :placeholder="$t('Select status')"/>
                            <FormInput v-model="form.sort_order" :name="$t('Sort Order')" :type="'number'" :class="'sm:col-span-3'" :placeholder="'0'"/>
                            <div class="sm:col-span-6 flex items-center justify-between p-3 rounded-xl border border-slate-200/80 dark:border-zinc-800 bg-slate-50/50 dark:bg-[#18181B]">
                                <div>
                                    <h4 class="text-xs font-semibold text-slate-900 dark:text-white">{{ $t('Featured / Recommended badge') }}</h4>
                                    <p class="text-xs text-slate-500 dark:text-zinc-400">{{ $t('Display a prominent "Most Popular" badge on the pricing page') }}</p>
                                </div>
                                <FormToggleSwitch v-model="form.featured"/>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Pricing Details -->
                <div class="sm:flex border-b border-slate-100 dark:border-zinc-800/80 py-6">
                    <div class="sm:w-[35%] mb-4 sm:mb-0 pr-4">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">{{ $t('Pricing details') }}</h3>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">{{ $t('Monthly base price and optional yearly discounted price') }}</p>
                    </div>
                    <div class="sm:w-[65%]">
                        <div class="sm:w-[90%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormInput v-model="form.price" :name="$t('Price (Monthly)')" :error="form.errors.price" :type="'number'" :class="'sm:col-span-3'"/>
                            <FormInput v-model="form.yearly_price" :name="$t('Price (Yearly per month)')" :type="'number'" :class="'sm:col-span-3'" :placeholder="$t('Optional discounted rate')"/>
                            <FormSelect v-model="form.period" :options="periodOptions" :error="form.errors.period" :name="$t('Default Period')" :class="'sm:col-span-6'" :placeholder="$t('Select period')"/>
                        </div>
                    </div>
                </div>

                <!-- Plan Limits -->
                <div class="sm:flex border-b border-slate-100 dark:border-zinc-800/80 py-6">
                    <div class="sm:w-[35%] mb-4 sm:mb-0 pr-4">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">{{ $t('Plan limits') }}</h3>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">{{ $t('Set quota limits for tenant resources') }}</p>
                    </div>
                    <div class="sm:w-[65%]">
                        <div class="bg-amber-50 dark:bg-amber-950/30 border border-amber-200 dark:border-amber-800/40 p-3 rounded-xl sm:w-[90%] mb-4 flex items-center gap-2 text-xs text-amber-800 dark:text-amber-300">
                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                            <span>{{ $t('For unlimited usage, set -1 as the value.') }}</span>
                        </div>
                        <div class="sm:w-[90%] grid gap-x-6 gap-y-4 sm:grid-cols-6">
                            <FormInput v-model="form.campaign_limit" :name="$t('Campaign limit')" :error="form.errors.campaign_limit" :type="'number'" :class="'sm:col-span-3'"/>
                            <FormInput v-model="form.message_limit" :name="$t('Message limit')" :error="form.errors.message_limit" :type="'number'" :class="'sm:col-span-3'"/>
                            <FormInput v-model="form.contacts_limit" :name="$t('Contacts limit')" :error="form.errors.contacts_limit" :type="'number'" :class="'sm:col-span-3'"/>
                            <FormInput v-model="form.canned_replies_limit" :name="$t('Canned replies limit')" :error="form.errors.canned_replies_limit" :type="'number'" :class="'sm:col-span-3'"/>
                            <FormInput v-model="form.team_limit" :name="$t('User / Team limit')" :error="form.errors.team_limit" :type="'number'" :class="'sm:col-span-3'"/>
                        </div>
                    </div>
                </div>

                <!-- Expiration behavior -->
                <div class="sm:flex py-6">
                    <div class="sm:w-[35%] mb-4 sm:mb-0 pr-4">
                        <h3 class="text-sm font-semibold text-slate-900 dark:text-white">{{ $t('Message reception after expiration') }}</h3>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">{{ $t('Allow or block inbound WhatsApp messages when subscription has ended') }}</p>
                    </div>
                    <div class="sm:w-[65%] sm:w-[90%] flex items-center">
                        <FormToggleSwitch v-model="form.receive_messages_after_expiration"/>
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
                        <span>{{ $t('Save plan') }}</span>
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>

<script setup>
import AppLayout from "./../Layout/App.vue";
import { ref } from 'vue';
import { Link, useForm } from "@inertiajs/vue3";
import FormInput from '@/Components/FormInput.vue';
import FormSelect from '@/Components/FormSelect.vue';
import FormToggleSwitch from '@/Components/FormToggleSwitch.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({ title: String, plan: Object });

const getDetail = (value, key) => {
    if (value) {
        try {
            const item = typeof value === 'string' ? JSON.parse(value) : value;
            return item?.[key] ?? null;
        } catch (_) {
            return null;
        }
    }
    return null;
};

const form = useForm({
    name: props.plan?.name || '',
    price: props.plan?.price || '',
    yearly_price: getDetail(props.plan?.metadata, 'yearly_price') ?? '',
    description: getDetail(props.plan?.metadata, 'description') ?? '',
    featured: getDetail(props.plan?.metadata, 'featured') == 1 ? true : false,
    sort_order: getDetail(props.plan?.metadata, 'sort_order') ?? 0,
    period: props.plan?.period || 'monthly',
    status: props.plan?.status || 'active',
    campaign_limit: getDetail(props.plan?.metadata, 'campaign_limit') ?? '-1',
    message_limit: getDetail(props.plan?.metadata, 'message_limit') ?? '-1',
    contacts_limit: getDetail(props.plan?.metadata, 'contacts_limit') ?? '-1',
    canned_replies_limit: getDetail(props.plan?.metadata, 'canned_replies_limit') ?? '-1',
    team_limit: getDetail(props.plan?.metadata, 'team_limit') ?? '-1',
    receive_messages_after_expiration: getDetail(props.plan?.metadata, 'receive_messages_after_expiration') == 1 || getDetail(props.plan?.metadata, 'receive_messages_after_expiration') == null ? true : false
});

const statusOptions = ref([
    { value: 'active', label: trans('active') },
    { value: 'inactive', label: trans('inactive') }
]);

const periodOptions = ref([
    { value: 'monthly', label: trans('Monthly') },
    { value: 'yearly', label: trans('Yearly') }
]);

const submitForm = async () => {
    const url = props.plan ? `/admin/plans/${props.plan.uuid}` : '/admin/plans';

    form[props.plan ? 'put' : 'post'](url, {
        preserveScroll: true,
    });
};
</script>