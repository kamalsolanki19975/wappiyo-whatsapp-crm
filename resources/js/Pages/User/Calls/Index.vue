<template>
    <AppLayout>
        <Head :title="title" />

        <div class="p-4 sm:p-6 lg:p-8 max-w-7xl mx-auto space-y-6">
            
            <!-- Page Header Section -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 border-b border-slate-200/80 dark:border-zinc-800 pb-6">
                <div>
                    <div class="flex items-center gap-2.5 mb-1">
                        <span class="inline-flex items-center justify-center w-10 h-10 rounded-2xl bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-400 shadow-xs">
                            <svg class="w-5 h-5" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 0 0-1.01.24l-1.57 1.97c-2.83-1.45-5.15-3.76-6.59-6.59l1.97-1.57c.28-.28.37-.68.25-1.02A11.36 11.36 0 0 1 8.96 4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.62c0-.55-.45-1-.99-1z"/>
                            </svg>
                        </span>
                        <div>
                            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-white flex items-center gap-2">
                                <span>{{ $t('WhatsApp Calls') }}</span>
                                <span
                                    v-if="callingStatus?.enabled"
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-emerald-50 text-emerald-700 dark:bg-emerald-950/60 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800/40"
                                >
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                    {{ $t('Meta Live') }}
                                </span>
                            </h1>
                        </div>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-zinc-400">
                        {{ $t('Manage WhatsApp customer calls, review call outcomes, and track communication metrics.') }}
                    </p>
                </div>

                <div class="flex items-center gap-2.5">
                    <!-- Export CSV Button -->
                    <button
                        type="button"
                        @click="exportCsv"
                        class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-slate-200/80 dark:border-zinc-800 bg-white dark:bg-zinc-800 text-slate-700 dark:text-zinc-200 hover:bg-slate-50 dark:hover:bg-zinc-700 text-xs font-semibold shadow-xs transition cursor-pointer"
                    >
                        <svg class="w-4 h-4 text-slate-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                        </svg>
                        <span>{{ $t('Export CSV') }}</span>
                    </button>

                    <!-- New Call Button -->
                    <button
                        type="button"
                        @click="openNewCallModal"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold shadow-sm shadow-emerald-600/30 transition cursor-pointer"
                    >
                        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                            <path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 0 0-1.01.24l-1.57 1.97c-2.83-1.45-5.15-3.76-6.59-6.59l1.97-1.57c.28-.28.37-.68.25-1.02A11.36 11.36 0 0 1 8.96 4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.62c0-.55-.45-1-.99-1z"/>
                        </svg>
                        <span>{{ $t('Start Call') }}</span>
                    </button>
                </div>
            </div>

            <!-- Configuration / Eligibility Notice if Not Enabled -->
            <div
                v-if="!callingStatus?.enabled || !isCallingConfigured"
                class="rounded-2xl border border-amber-200 dark:border-amber-800/40 bg-amber-50/80 dark:bg-amber-950/20 p-4 sm:p-5 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4"
            >
                <div class="flex items-start gap-3">
                    <span class="p-2 rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400 shrink-0">
                        <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>
                        </svg>
                    </span>
                    <div>
                        <h4 class="text-sm font-bold text-amber-900 dark:text-amber-200">
                            {{ $t('WhatsApp Calling Setup Required') }}
                        </h4>
                        <p class="text-xs text-amber-700 dark:text-amber-400/90 mt-0.5">
                            {{ $t('Meta WhatsApp Business Calling requires active Cloud API credentials, an eligible phone number, and calling enabled in settings.') }}
                        </p>
                    </div>
                </div>
                <Link
                    href="/settings/whatsapp"
                    class="px-3.5 py-1.5 rounded-xl text-xs font-semibold bg-amber-600 hover:bg-amber-700 text-white shrink-0 shadow-xs transition"
                >
                    {{ $t('Configure Calling') }}
                </Link>
            </div>

            <!-- KPI Summary Cards -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Total Calls -->
                <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                            {{ $t('Total Calls') }}
                        </span>
                        <span class="p-1.5 rounded-lg bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-300">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 0 0-1.01.24l-1.57 1.97c-2.83-1.45-5.15-3.76-6.59-6.59l1.97-1.57c.28-.28.37-.68.25-1.02A11.36 11.36 0 0 1 8.96 4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.62c0-.55-.45-1-.99-1z"/>
                            </svg>
                        </span>
                    </div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white mt-2">
                        {{ analytics?.total_calls || 0 }}
                    </div>
                    <div class="text-[11px] text-slate-500 dark:text-zinc-400 mt-1 flex gap-2">
                        <span>{{ analytics?.inbound_calls || 0 }} In</span>
                        <span>•</span>
                        <span>{{ analytics?.outbound_calls || 0 }} Out</span>
                    </div>
                </div>

                <!-- Connected Calls -->
                <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-emerald-600 dark:text-emerald-400">
                            {{ $t('Connected') }}
                        </span>
                        <span class="p-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </span>
                    </div>
                    <div class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-2">
                        {{ analytics?.connected_calls || 0 }}
                    </div>
                    <div class="text-[11px] text-slate-500 dark:text-zinc-400 mt-1">
                        {{ analytics?.connection_rate || 0 }}% {{ $t('connection rate') }}
                    </div>
                </div>

                <!-- Missed / Failed Calls -->
                <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-rose-500">
                            {{ $t('Missed & Failed') }}
                        </span>
                        <span class="p-1.5 rounded-lg bg-rose-50 dark:bg-rose-950/60 text-rose-500">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                                <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                            </svg>
                        </span>
                    </div>
                    <div class="text-2xl font-black text-rose-600 dark:text-rose-400 mt-2">
                        {{ (analytics?.missed_calls || 0) + (analytics?.failed_calls || 0) }}
                    </div>
                    <div class="text-[11px] text-slate-500 dark:text-zinc-400 mt-1 flex gap-2">
                        <span>{{ analytics?.missed_calls || 0 }} Missed</span>
                        <span>•</span>
                        <span>{{ analytics?.failed_calls || 0 }} Failed</span>
                    </div>
                </div>

                <!-- Talk Time -->
                <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold uppercase tracking-wider text-purple-600 dark:text-purple-400">
                            {{ $t('Total Talk Time') }}
                        </span>
                        <span class="p-1.5 rounded-lg bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </span>
                    </div>
                    <div class="text-2xl font-black text-slate-900 dark:text-white mt-2">
                        {{ analytics?.formatted_talk_time || '0m 0s' }}
                    </div>
                    <div class="text-[11px] text-slate-500 dark:text-zinc-400 mt-1">
                        {{ $t('Avg') }}: {{ analytics?.formatted_avg_duration || '00:00' }}
                    </div>
                </div>
            </div>

            <!-- Filter & Search Toolbar -->
            <div class="p-4 rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs space-y-3">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3">
                    
                    <!-- Search Input -->
                    <div class="lg:col-span-2 relative">
                        <input
                            type="text"
                            v-model="form.search"
                            @keyup.enter="applyFilters"
                            :placeholder="$t('Search customer, phone...')"
                            class="w-full text-xs rounded-xl border border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800 px-3 py-2 text-slate-800 dark:text-zinc-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        />
                    </div>

                    <!-- Direction Filter -->
                    <div>
                        <select
                            v-model="form.direction"
                            @change="applyFilters"
                            class="w-full text-xs rounded-xl border border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800 px-3 py-2 text-slate-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                            <option value="">{{ $t('All Directions') }}</option>
                            <option value="inbound">{{ $t('Inbound') }}</option>
                            <option value="outbound">{{ $t('Outbound') }}</option>
                        </select>
                    </div>

                    <!-- Status Filter -->
                    <div>
                        <select
                            v-model="form.status"
                            @change="applyFilters"
                            class="w-full text-xs rounded-xl border border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800 px-3 py-2 text-slate-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                            <option value="">{{ $t('All Statuses') }}</option>
                            <option value="connected">{{ $t('Connected') }}</option>
                            <option value="completed">{{ $t('Completed') }}</option>
                            <option value="ringing">{{ $t('Ringing') }}</option>
                            <option value="missed">{{ $t('Missed') }}</option>
                            <option value="failed">{{ $t('Failed') }}</option>
                            <option value="busy">{{ $t('Busy') }}</option>
                        </select>
                    </div>

                    <!-- Disposition Filter -->
                    <div>
                        <select
                            v-model="form.disposition"
                            @change="applyFilters"
                            class="w-full text-xs rounded-xl border border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800 px-3 py-2 text-slate-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                            <option value="">{{ $t('All Outcomes') }}</option>
                            <option v-for="d in dispositions" :key="d" :value="d">{{ d }}</option>
                        </select>
                    </div>

                    <!-- Agent Filter -->
                    <div>
                        <select
                            v-model="form.user_id"
                            @change="applyFilters"
                            class="w-full text-xs rounded-xl border border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800 px-3 py-2 text-slate-800 dark:text-zinc-200 focus:outline-none focus:ring-2 focus:ring-emerald-500"
                        >
                            <option value="">{{ $t('All Agents') }}</option>
                            <option v-for="agent in agents" :key="agent.id" :value="agent.id">
                                {{ agent.name }}
                            </option>
                        </select>
                    </div>

                </div>

                <!-- Date Range & Reset Row -->
                <div class="flex flex-wrap items-center justify-between gap-3 pt-2 border-t border-slate-100 dark:border-zinc-800/80 text-xs">
                    <div class="flex items-center gap-2">
                        <span class="text-slate-400 font-medium">{{ $t('Date') }}:</span>
                        <input
                            type="date"
                            v-model="form.date_from"
                            @change="applyFilters"
                            class="rounded-lg border border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800 px-2 py-1 text-xs text-slate-800 dark:text-zinc-200"
                        />
                        <span class="text-slate-400">-</span>
                        <input
                            type="date"
                            v-model="form.date_to"
                            @change="applyFilters"
                            class="rounded-lg border border-slate-200 dark:border-zinc-700 bg-slate-50/50 dark:bg-zinc-800 px-2 py-1 text-xs text-slate-800 dark:text-zinc-200"
                        />
                    </div>

                    <div class="flex items-center gap-2">
                        <button
                            v-if="hasActiveFilters"
                            type="button"
                            @click="resetFilters"
                            class="text-xs text-slate-500 hover:text-slate-800 dark:hover:text-zinc-200 underline font-medium"
                        >
                            {{ $t('Reset Filters') }}
                        </button>
                        <button
                            type="button"
                            @click="applyFilters"
                            class="px-3 py-1.5 rounded-lg bg-slate-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-xs font-semibold hover:opacity-90 transition"
                        >
                            {{ $t('Apply') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Calls Table Card -->
            <div class="rounded-2xl bg-white dark:bg-[#111113] border border-slate-200/80 dark:border-zinc-800 shadow-xs overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs text-slate-600 dark:text-zinc-300">
                        <thead class="bg-slate-50 dark:bg-zinc-900/60 border-b border-slate-200/80 dark:border-zinc-800 text-[11px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">
                            <tr>
                                <th class="px-5 py-3.5">{{ $t('Direction') }}</th>
                                <th class="px-5 py-3.5">{{ $t('Customer') }}</th>
                                <th class="px-5 py-3.5">{{ $t('Status') }}</th>
                                <th class="px-5 py-3.5">{{ $t('Duration') }}</th>
                                <th class="px-5 py-3.5">{{ $t('Agent') }}</th>
                                <th class="px-5 py-3.5">{{ $t('Disposition') }}</th>
                                <th class="px-5 py-3.5">{{ $t('Date & Time') }}</th>
                                <th class="px-5 py-3.5 text-right">{{ $t('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-zinc-800/60">
                            <tr
                                v-for="call in rows.data"
                                :key="call.id"
                                @click="openCallDetails(call)"
                                class="hover:bg-slate-50/80 dark:hover:bg-zinc-900/40 cursor-pointer transition-colors"
                            >
                                <!-- Direction -->
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-1.5">
                                        <span
                                            :class="[
                                                'w-6 h-6 rounded-lg flex items-center justify-center font-bold text-white',
                                                call.direction === 'inbound' ? 'bg-blue-600' : 'bg-emerald-600'
                                            ]"
                                        >
                                            <svg v-if="call.direction === 'inbound'" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                <path d="M19 12H5M12 19l-7-7 7-7"/>
                                            </svg>
                                            <svg v-else class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                                                <path d="M5 12h14M12 5l7 7-7 7"/>
                                            </svg>
                                        </span>
                                        <span class="capitalize font-medium text-slate-800 dark:text-zinc-200">
                                            {{ call.direction }}
                                        </span>
                                    </div>
                                </td>

                                <!-- Customer -->
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <div class="flex items-center gap-3">
                                        <Avatar
                                            :name="call.contact?.full_name || call.customer_phone"
                                            size="sm"
                                        />
                                        <div>
                                            <div class="font-bold text-slate-900 dark:text-white">
                                                {{ call.contact?.full_name || $t('Unknown Contact') }}
                                            </div>
                                            <div class="text-[11px] font-mono text-slate-400 dark:text-zinc-500">
                                                {{ call.customer_phone }}
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                <!-- Status -->
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <Badge :variant="getStatusBadgeVariant(call.status)" size="xs" class="capitalize">
                                        {{ call.status }}
                                    </Badge>
                                </td>

                                <!-- Duration -->
                                <td class="px-5 py-3.5 whitespace-nowrap font-mono font-medium text-slate-800 dark:text-zinc-200">
                                    {{ call.formatted_duration || '00:00' }}
                                </td>

                                <!-- Agent -->
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <span v-if="call.user" class="font-medium text-slate-800 dark:text-zinc-200">
                                        {{ call.user.first_name }} {{ call.user.last_name || '' }}
                                    </span>
                                    <span v-else class="text-slate-400">
                                        {{ $t('Inbound / System') }}
                                    </span>
                                </td>

                                <!-- Disposition -->
                                <td class="px-5 py-3.5 whitespace-nowrap">
                                    <span
                                        v-if="call.disposition"
                                        class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold bg-purple-50 dark:bg-purple-950/50 text-[#6C5CE7] dark:text-purple-300 border border-purple-200/60 dark:border-purple-800/40"
                                    >
                                        {{ call.disposition }}
                                    </span>
                                    <span v-else class="text-slate-400 text-[11px]">—</span>
                                </td>

                                <!-- Date & Time -->
                                <td class="px-5 py-3.5 whitespace-nowrap text-slate-500 dark:text-zinc-400">
                                    {{ call.created_at_formatted || call.created_at }}
                                </td>

                                <!-- Actions -->
                                <td class="px-5 py-3.5 whitespace-nowrap text-right" @click.stop>
                                    <div class="inline-flex items-center gap-1.5">
                                        <CallButton
                                            v-if="call.contact"
                                            :contact="call.contact"
                                            :phone="call.customer_phone"
                                            variant="icon"
                                            size="xs"
                                            :showLabel="false"
                                        />
                                        <Link
                                            v-if="call.contact?.uuid"
                                            :href="`/chats/${call.contact.uuid}`"
                                            class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-lg transition"
                                            :title="$t('Open Chat')"
                                        >
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z"/></svg>
                                        </Link>
                                        <button
                                            type="button"
                                            @click="openCallDetails(call)"
                                            class="p-1.5 text-slate-400 hover:text-slate-700 dark:hover:text-zinc-200 hover:bg-slate-100 dark:hover:bg-zinc-800 rounded-lg transition"
                                            :title="$t('View Details')"
                                        >
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <circle cx="12" cy="12" r="1"/><circle cx="12" cy="5" r="1"/><circle cx="12" cy="19" r="1"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>

                            <!-- Empty State -->
                            <tr v-if="!rows.data || rows.data.length === 0">
                                <td colspan="8" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center max-w-sm mx-auto text-center space-y-3">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-zinc-800 flex items-center justify-center text-slate-400 dark:text-zinc-500">
                                            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                                                <path d="M20.01 15.38c-1.23 0-2.42-.2-3.53-.56a.977.977 0 0 0-1.01.24l-1.57 1.97c-2.83-1.45-5.15-3.76-6.59-6.59l1.97-1.57c.28-.28.37-.68.25-1.02A11.36 11.36 0 0 1 8.96 4c0-.55-.45-1-1-1H4c-.55 0-1 .45-1 1 0 9.39 7.61 17 17 17 .55 0 1-.45 1-1v-3.62c0-.55-.45-1-.99-1z"/>
                                            </svg>
                                        </div>
                                        <div>
                                            <h4 class="text-sm font-bold text-slate-800 dark:text-zinc-200">
                                                {{ $t('No calls found') }}
                                            </h4>
                                            <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">
                                                {{ $t('There are no WhatsApp call records matching your current filter criteria.') }}
                                            </p>
                                        </div>
                                        <button
                                            type="button"
                                            @click="openNewCallModal"
                                            class="px-4 py-2 rounded-xl text-xs font-semibold bg-emerald-600 hover:bg-emerald-700 text-white shadow-xs transition"
                                        >
                                            {{ $t('Place a Call') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination Component -->
                <div class="p-3 border-t border-slate-100 dark:border-zinc-800/80">
                    <Pagination :pagination="rows" />
                </div>
            </div>

        </div>

        <!-- Call Details Drawer -->
        <CallDetailsDrawer
            :is-open="isDrawerOpen"
            :call="selectedCall"
            @close="isDrawerOpen = false"
            @updated="handleCallUpdated"
        />

        <!-- Dial / Outbound Call Modal -->
        <CallModal
            :is-open="isNewCallModalOpen"
            :contact="dialContact"
            @close="isNewCallModalOpen = false"
            @call-ended="reloadCalls"
        />

    </AppLayout>
</template>

<script setup>
import { ref, reactive, computed } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import AppLayout from '@/Pages/User/Layout/App.vue';
import Avatar from '@/Components/UI/Avatar.vue';
import Badge from '@/Components/UI/Badge.vue';
import Pagination from '@/Components/Pagination.vue';
import CallButton from '@/Components/Calling/CallButton.vue';
import CallModal from '@/Components/Calling/CallModal.vue';
import CallDetailsDrawer from '@/Components/Calling/CallDetailsDrawer.vue';
import { trans } from 'laravel-vue-i18n';

const props = defineProps({
    title: { type: String, default: 'WhatsApp Calls' },
    rows: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    analytics: { type: Object, default: () => ({}) },
    isCallingConfigured: { type: Boolean, default: false },
    callingStatus: { type: Object, default: () => ({}) },
    agents: { type: Array, default: () => [] },
    dispositions: { type: Array, default: () => [] },
    organizationId: { type: Number, default: 0 },
});

const form = reactive({
    search: props.filters.search || '',
    direction: props.filters.direction || '',
    status: props.filters.status || '',
    disposition: props.filters.disposition || '',
    user_id: props.filters.user_id || '',
    date_from: props.filters.date_from || '',
    date_to: props.filters.date_to || '',
});

const isDrawerOpen = ref(false);
const selectedCall = ref(null);
const isNewCallModalOpen = ref(false);
const dialContact = ref(null);

const hasActiveFilters = computed(() => {
    return form.search || form.direction || form.status || form.disposition || form.user_id || form.date_from || form.date_to;
});

const applyFilters = () => {
    router.get('/calls', form, {
        preserveState: true,
        preserveScroll: true,
        replace: true,
    });
};

const resetFilters = () => {
    form.search = '';
    form.direction = '';
    form.status = '';
    form.disposition = '';
    form.user_id = '';
    form.date_from = '';
    form.date_to = '';
    applyFilters();
};

const openCallDetails = (call) => {
    selectedCall.value = call;
    isDrawerOpen.value = true;
};

const handleCallUpdated = (updated) => {
    if (selectedCall.value && selectedCall.value.uuid === updated.uuid) {
        selectedCall.value = { ...selectedCall.value, ...updated };
    }
    // Update in rows.data
    const index = props.rows.data?.findIndex(c => c.uuid === updated.uuid);
    if (index !== -1 && props.rows.data) {
        props.rows.data[index] = { ...props.rows.data[index], ...updated };
    }
};

const openNewCallModal = () => {
    dialContact.value = null;
    isNewCallModalOpen.value = true;
};

const reloadCalls = () => {
    router.reload({ only: ['rows', 'analytics'] });
};

const exportCsv = () => {
    const query = new URLSearchParams(form).toString();
    window.location.href = `/calls/export?${query}`;
};

const getStatusBadgeVariant = (status) => {
    switch (status) {
        case 'connected': return 'success';
        case 'completed': return 'neutral';
        case 'ringing':
        case 'connecting': return 'primary';
        case 'missed':
        case 'failed':
        case 'rejected': return 'danger';
        default: return 'warning';
    }
};
</script>
