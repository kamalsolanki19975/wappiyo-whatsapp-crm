<template>
    <AppLayout>
        <Head :title="title" />

        <div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto space-y-6">
            <!-- Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200/80 dark:border-white/10 pb-6">
                <div>
                    <div class="flex items-center gap-2.5 mb-1">
                        <span class="inline-flex items-center justify-center w-9 h-9 rounded-xl bg-primary/10 text-primary dark:bg-primary/20 shadow-sm">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                            </svg>
                        </span>
                        <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white">
                            {{ $t('Team & Agent Workspace') }}
                        </h1>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400">
                        {{ $t('Manage support agents, configure roles, and control WhatsApp conversation assignments.') }}
                    </p>
                </div>

                <div class="flex items-center gap-2.5">
                    <!-- Roles Guide Button -->
                    <button
                        type="button"
                        @click="isRolesGuideOpen = true"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200/80 dark:border-white/10 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-white/5 text-xs font-semibold shadow-xs transition cursor-pointer"
                    >
                        <svg class="w-4 h-4 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                        </svg>
                        <span>{{ $t('Roles Guide') }}</span>
                    </button>

                    <!-- Invite Button -->
                    <button
                        v-if="isOwnerOrManager"
                        type="button"
                        @click="openModal()"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-primary text-white text-xs font-bold shadow-sm shadow-purple-600/30 hover:bg-primary/90 transition cursor-pointer"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
                        </svg>
                        <span>{{ $t('Invite Member') }}</span>
                    </button>
                </div>
            </div>

            <!-- KPI Cards -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                <!-- Total -->
                <button
                    type="button"
                    @click="filterRole('all')"
                    class="p-4 rounded-2xl border transition text-left cursor-pointer"
                    :class="activeRole === 'all'
                        ? 'bg-primary/5 border-primary dark:bg-primary/10 shadow-xs ring-1 ring-primary'
                        : 'bg-white dark:bg-slate-900 border-slate-200/80 dark:border-white/10 hover:border-primary/40'"
                >
                    <div class="text-[11px] font-bold uppercase tracking-wider text-slate-400">
                        {{ $t('Total Members') }}
                    </div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white mt-1">
                        {{ counts?.total || rows?.data?.length || 0 }}
                    </div>
                </button>

                <!-- Owners -->
                <button
                    type="button"
                    @click="filterRole('owner')"
                    class="p-4 rounded-2xl border transition text-left cursor-pointer"
                    :class="activeRole === 'owner'
                        ? 'bg-purple-500/10 border-purple-500 dark:bg-purple-500/15 shadow-xs ring-1 ring-purple-500'
                        : 'bg-white dark:bg-slate-900 border-slate-200/80 dark:border-white/10 hover:border-purple-500/40'"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400">
                            {{ $t('Owner') }}
                        </span>
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span>
                    </div>
                    <div class="text-2xl font-black text-purple-600 dark:text-purple-400 mt-1">
                        {{ counts?.owner || 1 }}
                    </div>
                </button>

                <!-- Managers -->
                <button
                    type="button"
                    @click="filterRole('manager')"
                    class="p-4 rounded-2xl border transition text-left cursor-pointer"
                    :class="activeRole === 'manager'
                        ? 'bg-cyan-500/10 border-cyan-500 dark:bg-cyan-500/15 shadow-xs ring-1 ring-cyan-500'
                        : 'bg-white dark:bg-slate-900 border-slate-200/80 dark:border-white/10 hover:border-cyan-500/40'"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-cyan-600 dark:text-cyan-400">
                            {{ $t('Managers') }}
                        </span>
                        <span class="w-2 h-2 rounded-full bg-cyan-500"></span>
                    </div>
                    <div class="text-2xl font-black text-cyan-600 dark:text-cyan-400 mt-1">
                        {{ counts?.manager || 0 }}
                    </div>
                </button>

                <!-- Agents -->
                <button
                    type="button"
                    @click="filterRole('agent')"
                    class="p-4 rounded-2xl border transition text-left cursor-pointer"
                    :class="activeRole === 'agent'
                        ? 'bg-emerald-500/10 border-emerald-500 dark:bg-emerald-500/15 shadow-xs ring-1 ring-emerald-500'
                        : 'bg-white dark:bg-slate-900 border-slate-200/80 dark:border-white/10 hover:border-emerald-500/40'"
                >
                    <div class="flex items-center justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                            {{ $t('Support Agents') }}
                        </span>
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span>
                    </div>
                    <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">
                        {{ counts?.agent || 0 }}
                    </div>
                </button>
            </div>

            <!-- Filter Bar & View Toggle -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <!-- Role Filter Pills -->
                <div class="flex items-center p-1 rounded-xl bg-slate-100 dark:bg-white/5 border border-slate-200/80 dark:border-white/10 text-xs font-semibold overflow-x-auto">
                    <button
                        v-for="r in roleTabs"
                        :key="r.key"
                        type="button"
                        @click="filterRole(r.key)"
                        class="px-3 py-1.5 rounded-lg transition whitespace-nowrap"
                        :class="activeRole === r.key
                            ? 'bg-white dark:bg-slate-800 text-primary dark:text-white shadow-xs font-bold'
                            : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white'"
                    >
                        {{ r.label }}
                    </button>
                </div>

                <!-- Search & View Mode Switcher -->
                <div class="flex items-center gap-2.5">
                    <!-- Search Input -->
                    <div class="relative w-full sm:w-64">
                        <input
                            v-model="searchQuery"
                            @input="handleSearch"
                            type="text"
                            :placeholder="$t('Search by name or email...')"
                            class="w-full pl-8 pr-3 py-1.5 rounded-xl border border-slate-200/80 dark:border-white/10 bg-white dark:bg-slate-900 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary transition"
                        />
                        <svg class="w-4 h-4 absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                        </svg>
                        <button
                            v-if="searchQuery"
                            type="button"
                            @click="clearSearch"
                            class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600"
                        >
                            &times;
                        </button>
                    </div>

                    <!-- View Switcher -->
                    <div class="flex items-center p-1 rounded-xl bg-slate-100 dark:bg-white/5 border border-slate-200/80 dark:border-white/10">
                        <button
                            type="button"
                            @click="viewMode = 'table'"
                            class="p-1.5 rounded-lg transition"
                            :class="viewMode === 'table' ? 'bg-white dark:bg-slate-800 text-primary dark:text-white shadow-xs' : 'text-slate-400'"
                            :title="$t('Table View')"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                            </svg>
                        </button>
                        <button
                            type="button"
                            @click="viewMode = 'grid'"
                            class="p-1.5 rounded-lg transition"
                            :class="viewMode === 'grid' ? 'bg-white dark:bg-slate-800 text-primary dark:text-white shadow-xs' : 'text-slate-400'"
                            :title="$t('Cards Grid View')"
                        >
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Table View -->
            <TeamTable
                v-if="viewMode === 'table'"
                :rows="props.rows"
                @edit="openModal"
            />

            <!-- Cards Grid View -->
            <div
                v-else-if="rows?.data?.length > 0"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4"
            >
                <div
                    v-for="item in rows.data"
                    :key="item.uuid"
                    class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-white/10 p-5 shadow-sm hover:shadow-md transition flex flex-col justify-between"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex items-center gap-3">
                            <div class="relative w-11 h-11 rounded-2xl bg-gradient-to-tr from-primary to-violet-500 text-white font-bold text-sm flex items-center justify-center shrink-0 shadow-sm">
                                {{ getInitials(item.user?.first_name, item.user?.last_name) }}
                                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full bg-emerald-500 border-2 border-white dark:border-slate-900"></span>
                            </div>
                            <div class="min-w-0">
                                <h3 class="font-bold text-sm text-slate-900 dark:text-white capitalize truncate">
                                    {{ item.user ? (item.user.first_name + ' ' + item.user.last_name) : 'User' }}
                                </h3>
                                <p class="text-xs text-slate-400 truncate">
                                    {{ item.user?.email }}
                                </p>
                            </div>
                        </div>

                        <span
                            class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider shrink-0"
                            :class="getRoleBadgeClass(item.role)"
                        >
                            {{ item.role }}
                        </span>
                    </div>

                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-white/5 flex items-center justify-between text-xs">
                        <div class="text-[11px] text-slate-400">
                            {{ $t('Updated') }}: {{ item.updated_at }}
                        </div>

                        <div v-if="isOwner && item.role !== 'owner'" class="flex items-center gap-2">
                            <button
                                type="button"
                                @click="openModal({ id: item.uuid, role: item.role, email: item.user?.email })"
                                class="text-xs font-bold text-primary hover:underline"
                            >
                                {{ $t('Edit Role') }}
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty Grid View -->
            <div
                v-else
                class="py-16 flex flex-col items-center justify-center text-center p-6 bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-white/10"
            >
                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-white/5 text-slate-400 flex items-center justify-center mb-3">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <h4 class="text-sm font-bold text-slate-900 dark:text-white mb-1">
                    {{ $t('No team members found') }}
                </h4>
                <p class="text-xs text-slate-400 max-w-sm">
                    {{ $t('Try searching with a different name or filter by a different role.') }}
                </p>
            </div>
        </div>

        <!-- Invite / Edit Role Modal -->
        <div
            v-if="isOpenFormModal"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
            @click.self="isOpenFormModal = false"
        >
            <div class="relative w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-white/10 p-6 sm:p-8 select-none">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white">
                        {{ formMethod === 'put' ? $t('Update Member Role') : $t('Invite Team Member') }}
                    </h3>
                    <button
                        type="button"
                        @click="isOpenFormModal = false"
                        class="text-slate-400 hover:text-slate-600 text-lg leading-none"
                    >
                        &times;
                    </button>
                </div>

                <form @submit.prevent="submitForm" class="space-y-4">
                    <!-- Email -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-1">
                            {{ $t('Email Address') }}
                        </label>
                        <input
                            v-model="form.email"
                            type="email"
                            required
                            :disabled="formMethod === 'put'"
                            :placeholder="$t('colleague@company.com')"
                            class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-primary/40 focus:border-primary disabled:opacity-60 transition"
                        />
                        <p v-if="form.errors.email" class="text-xs text-rose-500 mt-1 font-medium">
                            {{ form.errors.email }}
                        </p>
                    </div>

                    <!-- Role Options Selection -->
                    <div>
                        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 dark:text-slate-300 mb-2">
                            {{ $t('Select Role & Access') }}
                        </label>

                        <div class="space-y-2.5">
                            <!-- Support Agent Option -->
                            <div
                                @click="form.role = 'agent'"
                                class="p-3.5 rounded-2xl border transition cursor-pointer flex items-start gap-3"
                                :class="form.role === 'agent'
                                    ? 'bg-emerald-500/10 border-emerald-500 dark:bg-emerald-500/15 ring-1 ring-emerald-500'
                                    : 'bg-slate-50 dark:bg-white/5 border-slate-200 dark:border-white/10 hover:border-emerald-500/40'"
                            >
                                <input
                                    type="radio"
                                    name="role"
                                    value="agent"
                                    :checked="form.role === 'agent'"
                                    class="mt-1 text-emerald-600 focus:ring-emerald-500"
                                />
                                <div>
                                    <div class="font-bold text-xs text-slate-900 dark:text-white flex items-center gap-1.5">
                                        <span>{{ $t('Support Agent') }}</span>
                                        <span class="text-[10px] px-1.5 py-0.2 rounded bg-emerald-500/20 text-emerald-600 dark:text-emerald-400">Standard</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">
                                        {{ $t('Can access live WhatsApp inbox, reply to customers, send approved templates, and manage assigned tickets.') }}
                                    </div>
                                </div>
                            </div>

                            <!-- Manager Option -->
                            <div
                                @click="form.role = 'manager'"
                                class="p-3.5 rounded-2xl border transition cursor-pointer flex items-start gap-3"
                                :class="form.role === 'manager'
                                    ? 'bg-cyan-500/10 border-cyan-500 dark:bg-cyan-500/15 ring-1 ring-cyan-500'
                                    : 'bg-slate-50 dark:bg-white/5 border-slate-200 dark:border-white/10 hover:border-cyan-500/40'"
                            >
                                <input
                                    type="radio"
                                    name="role"
                                    value="manager"
                                    :checked="form.role === 'manager'"
                                    class="mt-1 text-cyan-600 focus:ring-cyan-500"
                                />
                                <div>
                                    <div class="font-bold text-xs text-slate-900 dark:text-white flex items-center gap-1.5">
                                        <span>{{ $t('Team Manager') }}</span>
                                        <span class="text-[10px] px-1.5 py-0.2 rounded bg-cyan-500/20 text-cyan-600 dark:text-cyan-400">Elevated</span>
                                    </div>
                                    <div class="text-[11px] text-slate-500 dark:text-slate-400 mt-0.5 leading-relaxed">
                                        {{ $t('Agent capabilities plus ability to assign chats, invite team members, launch broadcast campaigns, and view analytics.') }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <p v-if="form.errors.role" class="text-xs text-rose-500 mt-1 font-medium">
                            {{ form.errors.role }}
                        </p>
                    </div>

                    <!-- Modal Actions -->
                    <div class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-white/5">
                        <button
                            type="button"
                            @click="isOpenFormModal = false"
                            class="px-4 py-2 rounded-xl text-xs font-semibold text-slate-500 hover:text-slate-800 dark:hover:text-white transition"
                        >
                            {{ $t('Cancel') }}
                        </button>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-primary text-white text-xs font-bold shadow-md shadow-purple-600/30 hover:bg-primary/90 transition disabled:opacity-50 cursor-pointer"
                        >
                            <svg v-if="form.processing" class="w-4 h-4 animate-spin text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>{{ formMethod === 'put' ? $t('Save Changes') : $t('Send Invite') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Roles & Permissions Guide Modal -->
        <div
            v-if="isRolesGuideOpen"
            class="fixed inset-0 z-50 overflow-y-auto bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4"
            @click.self="isRolesGuideOpen = false"
        >
            <div class="relative w-full max-w-2xl bg-white dark:bg-slate-900 rounded-3xl shadow-2xl border border-slate-200 dark:border-white/10 p-6 sm:p-8 select-none">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-2">
                        <span class="w-8 h-8 rounded-xl bg-primary/10 text-primary flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </span>
                        <div>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white">
                                {{ $t('Role & Permission Hierarchy') }}
                            </h3>
                            <p class="text-xs text-slate-400">
                                {{ $t('Understand what capabilities each team role provides in Wappiyo.') }}
                            </p>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="isRolesGuideOpen = false"
                        class="text-slate-400 hover:text-slate-600 text-lg leading-none"
                    >
                        &times;
                    </button>
                </div>

                <!-- Matrix Table -->
                <div class="overflow-x-auto my-4">
                    <table class="w-full text-left text-xs">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-white/5 text-slate-400 font-bold uppercase tracking-wider text-[10px]">
                                <th class="py-2.5">{{ $t('Capability') }}</th>
                                <th class="py-2.5 text-center">{{ $t('Agent') }}</th>
                                <th class="py-2.5 text-center">{{ $t('Manager') }}</th>
                                <th class="py-2.5 text-center">{{ $t('Owner') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                            <tr>
                                <td class="py-2.5 text-slate-700 dark:text-slate-300 font-medium">{{ $t('WhatsApp Inbox & Customer Replies') }}</td>
                                <td class="py-2.5 text-center text-emerald-500 font-bold">✓</td>
                                <td class="py-2.5 text-center text-emerald-500 font-bold">✓</td>
                                <td class="py-2.5 text-center text-emerald-500 font-bold">✓</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 text-slate-700 dark:text-slate-300 font-medium">{{ $t('Send WhatsApp Message Templates') }}</td>
                                <td class="py-2.5 text-center text-emerald-500 font-bold">✓</td>
                                <td class="py-2.5 text-center text-emerald-500 font-bold">✓</td>
                                <td class="py-2.5 text-center text-emerald-500 font-bold">✓</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 text-slate-700 dark:text-slate-300 font-medium">{{ $t('Assign Chats & Change Ticket State') }}</td>
                                <td class="py-2.5 text-center text-slate-300 dark:text-slate-600">—</td>
                                <td class="py-2.5 text-center text-emerald-500 font-bold">✓</td>
                                <td class="py-2.5 text-center text-emerald-500 font-bold">✓</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 text-slate-700 dark:text-slate-300 font-medium">{{ $t('Create Broadcast Campaigns') }}</td>
                                <td class="py-2.5 text-center text-slate-300 dark:text-slate-600">—</td>
                                <td class="py-2.5 text-center text-emerald-500 font-bold">✓</td>
                                <td class="py-2.5 text-center text-emerald-500 font-bold">✓</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 text-slate-700 dark:text-slate-300 font-medium">{{ $t('View Organization Analytics & Reports') }}</td>
                                <td class="py-2.5 text-center text-slate-300 dark:text-slate-600">—</td>
                                <td class="py-2.5 text-center text-emerald-500 font-bold">✓</td>
                                <td class="py-2.5 text-center text-emerald-500 font-bold">✓</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 text-slate-700 dark:text-slate-300 font-medium">{{ $t('Invite & Manage Team Members') }}</td>
                                <td class="py-2.5 text-center text-slate-300 dark:text-slate-600">—</td>
                                <td class="py-2.5 text-center text-emerald-500 font-bold">✓</td>
                                <td class="py-2.5 text-center text-emerald-500 font-bold">✓</td>
                            </tr>
                            <tr>
                                <td class="py-2.5 text-slate-700 dark:text-slate-300 font-medium">{{ $t('Manage Billing, Invoices & API Keys') }}</td>
                                <td class="py-2.5 text-center text-slate-300 dark:text-slate-600">—</td>
                                <td class="py-2.5 text-center text-slate-300 dark:text-slate-600">—</td>
                                <td class="py-2.5 text-center text-purple-600 font-bold">✓</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="flex justify-end pt-3 border-t border-slate-100 dark:border-white/5">
                    <button
                        type="button"
                        @click="isRolesGuideOpen = false"
                        class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-white/10 text-slate-700 dark:text-slate-200 text-xs font-semibold hover:bg-slate-200 dark:hover:bg-white/20 transition cursor-pointer"
                    >
                        {{ $t('Got It') }}
                    </button>
                </div>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { ref, computed } from 'vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import AppLayout from '../Layout/App.vue';
import TeamTable from '@/Components/Tables/TeamTable.vue';

const props = defineProps({
    title: {
        type: String,
        default: 'Team Management',
    },
    rows: {
        type: Object,
        required: true,
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
    counts: {
        type: Object,
        default: () => ({
            total: 0,
            owner: 0,
            manager: 0,
            agent: 0,
        }),
    },
});

const user = computed(() => usePage().props.auth?.user);
const isOwner = computed(() => user.value?.teams?.[0]?.role === 'owner');
const isOwnerOrManager = computed(() => isOwner.value || user.value?.teams?.[0]?.role === 'manager');

const activeRole = ref(props.filters?.role || 'all');
const searchQuery = ref(props.filters?.search || '');
const viewMode = ref('table');

const isOpenFormModal = ref(false);
const isRolesGuideOpen = ref(false);
const formUrl = ref('/team/invite');
const formMethod = ref('post');

const form = useForm({
    email: '',
    role: 'agent',
});

const roleTabs = [
    { key: 'all', label: 'All Members' },
    { key: 'owner', label: 'Owners' },
    { key: 'manager', label: 'Managers' },
    { key: 'agent', label: 'Agents' },
];

function filterRole(role) {
    activeRole.value = role;
    router.visit('/team', {
        method: 'get',
        data: {
            role: role === 'all' ? null : role,
            search: searchQuery.value || null,
        },
        preserveState: true,
        preserveScroll: true,
    });
}

const handleSearch = debounce(() => {
    router.visit('/team', {
        method: 'get',
        data: {
            role: activeRole.value === 'all' ? null : activeRole.value,
            search: searchQuery.value || null,
        },
        preserveState: true,
        preserveScroll: true,
    });
}, 400);

function clearSearch() {
    searchQuery.value = '';
    handleSearch();
}

function openModal(item = null) {
    if (item) {
        formUrl.value = '/team/' + item.id;
        formMethod.value = 'put';
        form.email = item.email;
        form.role = item.role || 'agent';
        isOpenFormModal.value = true;
    } else {
        formUrl.value = '/team/invite';
        formMethod.value = 'post';
        form.email = '';
        form.role = 'agent';
        isOpenFormModal.value = true;
    }
}

function submitForm() {
    if (formMethod.value === 'post') {
        form.post(formUrl.value, {
            onSuccess: () => {
                isOpenFormModal.value = false;
                form.reset();
            },
        });
    } else {
        form.put(formUrl.value, {
            onSuccess: () => {
                isOpenFormModal.value = false;
                form.reset();
            },
        });
    }
}

function getInitials(first, last) {
    const f = first ? first[0] : '';
    const l = last ? last[0] : '';
    return (f + l).toUpperCase() || 'U';
}

function getRoleBadgeClass(role) {
    switch (role?.toLowerCase()) {
        case 'owner':
            return 'bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20';
        case 'manager':
            return 'bg-cyan-500/10 text-cyan-600 dark:text-cyan-400 border border-cyan-500/20';
        case 'agent':
        default:
            return 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20';
    }
}
</script>