<template>
    <AppLayout>
        <div class="p-4 sm:p-6 lg:p-8 max-w-5xl mx-auto space-y-6 text-slate-900 dark:text-zinc-100 overflow-y-auto">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200/80 dark:border-zinc-800 pb-5">
                <div>
                    <h1 class="text-xl sm:text-2xl font-bold tracking-tight text-slate-900 dark:text-white mb-1">
                        {{ props.role ? $t('Update role') : $t('Create role') }}
                    </h1>
                    <p class="flex items-center text-xs sm:text-sm text-slate-500 dark:text-zinc-400">
                        <svg class="w-4 h-4 mr-1.5 shrink-0 text-slate-400 dark:text-zinc-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 11v5m0 5a9 9 0 1 1 0-18a9 9 0 0 1 0 18Zm.05-13v.1h-.1V8h.1Z"/>
                        </svg>
                        <span>{{ $t('Create roles for administrative users and assign system privileges') }}</span>
                    </p>
                </div>
                <div>
                    <Link
                        href="/admin/team/roles"
                        class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-slate-100 dark:bg-zinc-800 hover:bg-slate-200 dark:hover:bg-zinc-700 text-slate-700 dark:text-zinc-200 text-xs font-semibold border border-slate-200/80 dark:border-zinc-700 transition-colors shadow-2xs"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m15 18-6-6 6-6"/></svg>
                        <span>{{ $t('Back') }}</span>
                    </Link>
                </div>
            </div>

            <!-- Form Card -->
            <form @submit.prevent="submitForm()" class="bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800/80 rounded-2xl p-6 sm:p-8 shadow-xs space-y-6">
                <!-- Role Details Section -->
                <div class="sm:flex border-b border-slate-100 dark:border-zinc-800/80 pb-6 gap-6">
                    <div class="sm:w-[35%] mb-4 sm:mb-0">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-zinc-300">{{ $t('Role Name') }}</h2>
                        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1 leading-relaxed">{{ $t('Specify a clear title for this administrative access tier.') }}</p>
                    </div>
                    <div class="sm:w-[65%]">
                        <FormInput
                            v-model="form.name"
                            :name="$t('Role Name')"
                            :placeholder="$t('e.g. Super Admin, Support Manager, Billing Lead')"
                            :type="'text'"
                            :error="form.errors.name"
                            :class="'w-full'"
                            :labelClass="'mb-1'"
                            required
                        />
                    </div>
                </div>

                <!-- Permissions Section -->
                <div class="sm:flex pb-4 gap-6">
                    <div class="sm:w-[35%] mb-4 sm:mb-0">
                        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-zinc-300">{{ $t('Permissions') }}</h2>
                        <p class="text-xs text-slate-400 dark:text-zinc-500 mt-1 leading-relaxed">{{ $t('Choose the appropriate permissions and operational actions for this role.') }}</p>
                        
                        <!-- Global Select All / Clear All -->
                        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-zinc-800/80 space-y-2">
                            <div class="text-[11px] font-semibold text-slate-500 dark:text-zinc-400 uppercase tracking-wider">{{ $t('Quick Actions') }}</div>
                            <div class="flex items-center gap-2">
                                <button
                                    type="button"
                                    @click="toggleAllPermissions(true)"
                                    class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/40 dark:text-emerald-300 hover:bg-emerald-100 transition-colors"
                                >
                                    {{ $t('Select All') }}
                                </button>
                                <button
                                    type="button"
                                    @click="toggleAllPermissions(false)"
                                    class="px-2.5 py-1 rounded-lg text-xs font-semibold bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400 hover:bg-slate-200 dark:hover:bg-zinc-700 transition-colors"
                                >
                                    {{ $t('Deselect All') }}
                                </button>
                            </div>
                        </div>

                        <!-- Search Filter -->
                        <div class="mt-4 relative">
                            <input
                                v-model="searchQuery"
                                type="text"
                                :placeholder="$t('Filter permissions...')"
                                class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-200 dark:border-zinc-700 bg-slate-50 dark:bg-zinc-900 text-xs text-slate-900 dark:text-zinc-100 placeholder:text-slate-400 dark:placeholder:text-zinc-500 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                            />
                            <svg class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 dark:text-zinc-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                            <button
                                v-if="searchQuery"
                                type="button"
                                @click="searchQuery = ''"
                                class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-zinc-300 text-xs"
                            >
                                &times;
                            </button>
                        </div>
                    </div>

                    <div class="sm:w-[65%] space-y-4">
                        <div
                            v-for="(item, index) in filteredModules"
                            :key="index"
                            class="p-4 rounded-xl border border-slate-200/80 dark:border-zinc-800 bg-slate-50/50 dark:bg-zinc-900/40 hover:border-indigo-500/30 transition-colors"
                        >
                            <!-- Module Header & Select All per module -->
                            <div class="flex items-center justify-between pb-3 mb-3 border-b border-slate-200/60 dark:border-zinc-800">
                                <div class="flex items-center gap-2">
                                    <span class="w-2 h-2 rounded-full bg-primary"></span>
                                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-900 dark:text-white">
                                        {{ $t(removeUnderscoreAndCapitalize(item.name)) }}
                                    </h3>
                                </div>
                                <button
                                    type="button"
                                    @click="toggleModule(item.name, !isModuleAllSelected(item.name))"
                                    class="text-[11px] font-semibold text-primary hover:text-primary/80 dark:text-purple-400 transition-colors"
                                >
                                    {{ isModuleAllSelected(item.name) ? $t('Deselect module') : $t('Select all') }}
                                </button>
                            </div>

                            <!-- Actions Checkbox Grid -->
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-2.5">
                                <div
                                    v-for="value in separateValues(item.actions)"
                                    :key="value"
                                    class="relative flex items-center p-2 rounded-lg bg-white dark:bg-[#111113] border border-slate-200/70 dark:border-zinc-800 hover:border-indigo-500/40 transition-colors cursor-pointer select-none"
                                    @click="form.permissions[item.name][value] = !form.permissions[item.name][value]"
                                >
                                    <div class="flex h-5 items-center mr-2.5">
                                        <input
                                            :checked="!!form.permissions[item.name]?.[value]"
                                            :id="'permission[' + item.name + '|' + value + ']'"
                                            :name="'permission[' + item.name + '|' + value + ']'"
                                            type="checkbox"
                                            @click.stop
                                            @change="form.permissions[item.name][value] = $event.target.checked"
                                            class="h-4 w-4 rounded border-slate-300 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-[#6C5CE7] focus:ring-[#6C5CE7]/30 transition-colors cursor-pointer"
                                        >
                                    </div>
                                    <div class="text-xs font-medium text-slate-700 dark:text-zinc-200 leading-tight">
                                        <label :for="'permission[' + item.name + '|' + value + ']'" class="cursor-pointer">
                                            {{ $t(removeUnderscoreAndCapitalize(value)) }}
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div v-if="filteredModules.length === 0" class="p-8 text-center rounded-xl border border-dashed border-slate-200 dark:border-zinc-800 text-xs text-slate-400 dark:text-zinc-500">
                            {{ $t('No permissions match your search query.') }}
                        </div>
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100 dark:border-zinc-800/80">
                    <Link
                        href="/admin/team/roles"
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
    import { ref, computed, onMounted } from 'vue';
    import { Link, useForm } from "@inertiajs/vue3";
    import FormInput from '@/Components/FormInput.vue';

    const props = defineProps({ title: String, role: Object, modules: Object, permissions: Object });
    const searchQuery = ref('');

    const form = useForm({
        name: props.role?.name || '',
        permissions: Object.fromEntries(props.modules.map(item => [item.name, {}]))
    });

    const separateValues = (value) => {
        return value ? value.split(',') : [];
    };

    const removeUnderscoreAndCapitalize = (inputString) => {
        if (!inputString) return '';
        const words = inputString.split('_');
        const capitalizedWords = words.map(word => word.charAt(0).toUpperCase() + word.slice(1));
        return capitalizedWords.join(' ');
    };

    const initializeCheckboxValues = () => {
        // First ensure every action is initialized to false if not set
        props.modules.forEach(item => {
            if (!form.permissions[item.name]) {
                form.permissions[item.name] = {};
            }
            separateValues(item.actions).forEach(action => {
                form.permissions[item.name][action] = false;
            });
        });

        // If editing, mark existing permissions as true
        if (props.permissions && Array.isArray(props.permissions)) {
            props.modules.forEach(item => {
                const moduleName = item.name;
                const modulePermissions = props.permissions.filter(permission => permission.module === moduleName);

                modulePermissions.forEach(permission => {
                    const actionName = permission.action;
                    if (form.permissions[moduleName]) {
                        form.permissions[moduleName][actionName] = true;
                    }
                });
            });
        }
    };

    const isModuleAllSelected = (moduleName) => {
        const item = props.modules.find(m => m.name === moduleName);
        if (!item) return false;
        const actions = separateValues(item.actions);
        return actions.length > 0 && actions.every(action => !!form.permissions[moduleName]?.[action]);
    };

    const toggleModule = (moduleName, selectAll) => {
        const item = props.modules.find(m => m.name === moduleName);
        if (!item) return;
        if (!form.permissions[moduleName]) {
            form.permissions[moduleName] = {};
        }
        separateValues(item.actions).forEach(action => {
            form.permissions[moduleName][action] = selectAll;
        });
    };

    const toggleAllPermissions = (selectAll) => {
        props.modules.forEach(item => {
            toggleModule(item.name, selectAll);
        });
    };

    const filteredModules = computed(() => {
        if (!searchQuery.value) return props.modules;
        const query = searchQuery.value.toLowerCase();
        return props.modules.filter(item => {
            const moduleMatches = item.name.toLowerCase().includes(query);
            const actionsMatch = separateValues(item.actions).some(action => action.toLowerCase().includes(query));
            return moduleMatches || actionsMatch;
        });
    });

    const submitForm = async () => {
        const url = props.role ? window.location.pathname : '/admin/team/roles';

        form[props.role ? 'put' : 'post'](url, {
            preserveScroll: true,
        });
    };

    onMounted(() => {
        initializeCheckboxValues();
    });
</script>