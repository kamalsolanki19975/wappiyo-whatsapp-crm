<template>
    <SettingLayout :modules="props.modules">
        <div class="max-w-4xl mx-auto space-y-6 pb-20">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200/80 dark:border-zinc-800 pb-5">
                <div>
                    <h2 class="text-xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                        <span>{{ $t('Contact Fields & Attributes') }}</span>
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-zinc-400 mt-1">
                        {{ $t('Configure custom CRM attributes and field placement on customer profiles.') }}
                    </p>
                </div>
                <div>
                    <button 
                        @click="openModal()" 
                        type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] hover:from-[#5b4bc4] hover:to-[#7c4deb] shadow-sm shadow-indigo-500/20 active:scale-[0.98] transition-all"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                            <line x1="12" y1="5" x2="12" y2="19"></line>
                            <line x1="5" y1="12" x2="19" y2="12"></line>
                        </svg>
                        <span>{{ $t('Add Custom Field') }}</span>
                    </button>
                </div>
            </div>

            <!-- Card 1: Field Placement / Settings -->
            <div class="bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 rounded-2xl p-6 shadow-sm">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="max-w-md">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-950/50 flex items-center justify-center text-[#6C5CE7] dark:text-indigo-400">
                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="8" y1="6" x2="21" y2="6"></line>
                                    <line x1="8" y1="12" x2="21" y2="12"></line>
                                    <line x1="8" y1="18" x2="21" y2="18"></line>
                                    <line x1="3" y1="6" x2="3.01" y2="6"></line>
                                    <line x1="3" y1="12" x2="3.01" y2="12"></line>
                                    <line x1="3" y1="18" x2="3.01" y2="18"></line>
                                </svg>
                            </div>
                            <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $t('Contact Fields Location') }}</h3>
                        </div>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1 pl-10">
                            {{ $t('Choose whether custom fields appear before or after the address section on contact profiles.') }}
                        </p>
                    </div>
                    <div class="sm:w-64">
                        <FormSelect 
                            v-model="location" 
                            :options="locationOptions" 
                            :name="''" 
                            :error="form2.errors.location" 
                            :placeholder="$t('Select Location')"
                            class="w-full"
                        />
                    </div>
                </div>
            </div>

            <!-- Card 2: Custom Contact Fields Table -->
            <div class="bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 rounded-2xl p-6 shadow-sm space-y-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-base font-semibold text-slate-900 dark:text-white">{{ $t('Defined Custom Fields') }}</h3>
                        <p class="text-xs text-slate-500 dark:text-zinc-400 mt-0.5">
                            {{ $t('Drag fields to reorder how they appear in contact detail views.') }}
                        </p>
                    </div>
                </div>

                <div class="w-full">
                    <ContactFieldTable :rows="props.rows" @edit="openModal" @delete="openAlert" />
                </div>
            </div>
        </div>

        <!-- Add/Edit Custom Field Modal -->
        <Modal :label="label" :isOpen="isOpenFormModal">
            <div class="mt-4">
                <form @submit.prevent="submitForm()" class="space-y-4">
                    <div class="grid grid-cols-1 gap-y-4">
                        <FormInput 
                            v-model="form.name" 
                            :name="$t('Field Label')" 
                            :error="form.errors.name" 
                            type="text" 
                            class="col-span-1"
                            :placeholder="$t('e.g. VIP Status, Alternate Phone, Birthday')"
                        />
                        <FormSelect 
                            v-model="form.component" 
                            :options="componentOptions" 
                            :name="$t('Input Component')" 
                            :error="form.errors.component" 
                            class="col-span-1" 
                            :placeholder="$t('Select component type')"
                        />
                        <FormSelect 
                            v-if="form.component === 'input'" 
                            v-model="form.type" 
                            :options="inputTypeOptions" 
                            :name="$t('Data Type')" 
                            :error="form.errors.type" 
                            class="col-span-1" 
                            :placeholder="$t('Select data type')"
                        />

                        <!-- Select Options with Drag-and-Drop -->
                        <div v-if="form.component === 'select'" class="col-span-1">
                            <div class="flex items-center justify-between pb-2">
                                <span class="text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">{{ $t('Options List') }}</span>
                                <button 
                                    type="button"
                                    class="inline-flex items-center gap-1 text-xs font-medium text-[#6C5CE7] hover:text-[#5b4bc4] dark:text-indigo-400 px-2 py-1 rounded-lg hover:bg-indigo-50 dark:hover:bg-indigo-950/40 transition-colors" 
                                    @click="add"
                                >
                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    <span>{{ $t('Add Option') }}</span>
                                </button>
                            </div>
                            <div class="bg-slate-50 dark:bg-zinc-800/60 rounded-xl p-3 border border-slate-200/80 dark:border-zinc-700/60 space-y-2">
                                <draggable tag="div" :list="form.options" class="space-y-2 w-full" handle=".handle" item-key="id">
                                    <template #item="{ element, index }">
                                        <div class="flex items-center w-full gap-2 bg-white dark:bg-zinc-900 p-1.5 rounded-lg border border-slate-200 dark:border-zinc-700 shadow-2xs">
                                            <span class="handle cursor-grab active:cursor-grabbing text-slate-400 hover:text-slate-600 dark:hover:text-zinc-200 px-1">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                                    <circle cx="9" cy="6" r="1.5"/><circle cx="15" cy="6" r="1.5"/>
                                                    <circle cx="9" cy="12" r="1.5"/><circle cx="15" cy="12" r="1.5"/>
                                                    <circle cx="9" cy="18" r="1.5"/><circle cx="15" cy="18" r="1.5"/>
                                                </svg>
                                            </span>

                                            <input 
                                                v-model="element.value" 
                                                type="text" 
                                                class="flex-1 text-sm bg-transparent outline-none text-slate-900 dark:text-zinc-100 placeholder-slate-400"
                                                :placeholder="$t('Option label')" 
                                                required
                                            />

                                            <button 
                                                v-if="index !== 0" 
                                                type="button"
                                                @click="removeAt(index)" 
                                                class="text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-950/40 p-1 rounded-md transition-colors"
                                            >
                                                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 6L6 18M6 6l12 12"/></svg>
                                            </button>
                                        </div>
                                    </template>
                                </draggable>
                            </div>
                        </div>

                        <!-- Is Required Toggle -->
                        <div class="flex items-center justify-between py-2 border-t border-slate-100 dark:border-zinc-800">
                            <div>
                                <span class="text-sm font-medium text-slate-800 dark:text-zinc-200 block">{{ $t('Required Field') }}</span>
                                <span class="text-xs text-slate-400 dark:text-zinc-500">{{ $t('Must be filled before saving contact details') }}</span>
                            </div>
                            <button
                                type="button"
                                role="switch"
                                :aria-checked="form.required !== 0"
                                class="w-12 h-6 flex items-center rounded-full p-1 transition-colors duration-200 focus:outline-none"
                                :class="form.required !== 0 ? 'bg-[#6C5CE7]' : 'bg-slate-300 dark:bg-zinc-700'"
                                @click="toggleRequiredInput()"
                            >
                                <div 
                                    class="bg-white w-4 h-4 rounded-full shadow-md transform duration-200 ease-in-out" 
                                    :class="{ 'translate-x-6': form.required !== 0 }"
                                ></div>
                            </button>
                        </div>
                    </div>
                    
                    <div class="pt-4 flex items-center justify-end gap-3 border-t border-slate-100 dark:border-zinc-800">
                        <button 
                            type="button" 
                            @click="isOpenFormModal = false" 
                            class="px-4 py-2 text-sm font-medium text-slate-600 dark:text-zinc-300 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-xl transition-colors"
                        >
                            {{ $t('Cancel') }}
                        </button>
                        <button 
                            type="submit"
                            :disabled="isLoading"
                            class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white bg-gradient-to-r from-[#6C5CE7] to-[#8B5CF6] hover:from-[#5b4bc4] hover:to-[#7c4deb] rounded-xl shadow-sm shadow-indigo-500/20 active:scale-[0.98] transition-all disabled:opacity-50"
                        >
                            <svg v-if="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ $t('Save Field') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </Modal>
    </SettingLayout>
</template>

<script setup>
    import SettingLayout from "./Layout.vue";
    import axios from "axios";
    import { ref, watch } from 'vue';
    import { useForm } from "@inertiajs/vue3";
    import { trans } from 'laravel-vue-i18n';
    import draggable from 'vuedraggable';
    import Modal from '@/Components/Modal.vue';
    import FormInput from '@/Components/FormInput.vue';
    import FormSelect from '@/Components/FormSelect.vue';
    import ContactFieldTable from '@/Components/Tables/ContactFieldTable.vue';

    const props = defineProps(['rows', 'filters', 'settings', 'modules']);
    const isOpenFormModal = ref(false);
    const label = ref(trans('Add contact field'));
    const formUrl = ref('/contact-fields');
    const formMethod = ref('post');
    const config = ref(props.settings?.metadata);
    const settings = ref(config.value ? JSON.parse(config.value) : null);
    const isLoading = ref(false);
    const location = ref(settings?.value?.contacts?.location ? settings?.value?.contacts?.location : null);
    let id = 0;

    const form = useForm({
        name: null,
        component: null,
        type: null,
        required: 0,
        options: [
            { value: "", id: 0 },
        ],
    });

    const form2 = useForm({
        location: null,
    });

    const openModal = (key) => {
        label.value = trans('Add contact field');
        formUrl.value = '/contact-fields';
        formMethod.value = 'post';
        
        if (key != null) {
            label.value = trans('Edit contact field');
            formUrl.value = '/contact-fields/' + key;
            formMethod.value = 'put';
            getRow();
        } else {
            id = 0;
            form.name = null;
            form.type = null;
            form.component = null;
            form.required = 0;
            form.options = [
                { value: "", id: 0 },
            ];
            isOpenFormModal.value = true;
        }
    };

    function getRow() {
        axios.get(formUrl.value).then((response) => {
            const { data } = response;

            if (data.item.type === 'select') {
                form['name'] = data.item.name;
                form['component'] = data.item.type;
                form['required'] = data.item.required;

                const inputString = data.item.value || '';
                const transformedArray = inputString.split(', ').map((value, index) => ({
                    id: index,
                    value: value
                }));
                id = transformedArray.length - 1;
                form['options'] = transformedArray;
            } else if (data.item.type === 'input') {
                form['name'] = data.item.name;
                form['component'] = data.item.type;
                form['type'] = data.item.value;
                form['required'] = data.item.required;
            } else {
                form['name'] = data.item.name;
                form['component'] = data.item.type;
                form['required'] = data.item.required;
            }

            isOpenFormModal.value = true;
        })
        .catch(() => {});
    }

    const inputTypeOptions = [
        { label: trans('Text'), value: 'text' },
        { label: trans('Number'), value: 'number' },
        { label: trans('Email'), value: 'email' },
        { label: trans('URL'), value: 'url' },
        { label: trans('Date'), value: 'date' },
        { label: trans('Time'), value: 'time' },
        { label: trans('Date & time'), value: 'datetime-local' },
    ];

    const componentOptions = [
        { label: trans('Input'), value: 'input' },
        { label: trans('Select box'), value: 'select' },
        { label: trans('Text area'), value: 'textarea' },
        { label: trans('Checkbox'), value: 'checkbox' },
    ];

    const locationOptions = [
        { label: trans('Before address'), value: 'before' },
        { label: trans('After address'), value: 'after' },
    ];

    const toggleRequiredInput = () => {
        form.required = form.required === 0 ? 1 : 0;
    };

    const removeAt = (idx) => {
        form.options.splice(idx, 1);
    };

    const add = () => {
        id++;
        form.options.push({ id, value: "" });
    };

    const submitForm = async () => {
        isLoading.value = true;

        if (formMethod.value === 'post') {
            form.post(formUrl.value, {
                preserveScroll: true,
                onFinish: () => {
                    isLoading.value = false;
                },
                onSuccess: () => {
                    isOpenFormModal.value = false;
                }
            });
        } else {
            form.put(formUrl.value, {
                preserveScroll: true,
                onFinish: () => {
                    isLoading.value = false;
                },
                onSuccess: () => {
                    isOpenFormModal.value = false;
                }
            });
        }
    };

    watch(location, (newValue, oldValue) => {  
        if (newValue !== oldValue) {
            form2.location = location.value;
            submitForm2();
        }
    });

    const submitForm2 = async () => {
        form2.post('/settings/contacts', {
            preserveScroll: true,
        });
    };
</script>