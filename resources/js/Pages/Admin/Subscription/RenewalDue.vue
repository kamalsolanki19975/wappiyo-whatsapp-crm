<template>
  <AppLayout>
    <div class="px-6 py-6 space-y-6">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200 dark:border-zinc-800">
        <div>
          <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2.5">
            <svg class="w-6 h-6 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            <span>{{ $t('Customers With Renewal Due') }}</span>
          </h1>
          <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">
            {{ $t('Track client subscription lifecycles, automated reminder statuses, and renewal deadlines.') }}
          </p>
        </div>

        <div class="flex items-center gap-2">
          <button
            type="button"
            @click="triggerBulkInspection"
            :disabled="isInspecting"
            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-xs font-bold text-white bg-slate-900 hover:bg-slate-800 dark:bg-zinc-700 dark:hover:bg-zinc-600 transition cursor-pointer disabled:opacity-50"
          >
            <svg v-if="isInspecting" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            <span>{{ isInspecting ? $t('Inspecting...') : $t('Run Scheduler Check') }}</span>
          </button>
        </div>
      </div>

      <!-- Flash feedback banner -->
      <div v-if="$page.props.flash?.status" :class="[
        'p-4 rounded-xl border text-xs font-semibold flex items-center gap-2',
        $page.props.flash.status.type === 'success' ? 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800' : 'bg-rose-50 text-rose-800 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800'
      ]">
        <span>{{ $page.props.flash.status.message }}</span>
      </div>

      <!-- KPI Ribbon -->
      <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
        <button
          type="button"
          @click="setFilter('all')"
          :class="[
            'p-4 rounded-2xl border text-left transition shadow-xs cursor-pointer',
            activeFilter === 'all'
              ? 'bg-purple-50 dark:bg-purple-950/40 border-purple-300 dark:border-purple-800 ring-2 ring-purple-500/20'
              : 'bg-white dark:bg-zinc-900 border-slate-200 dark:border-zinc-800 hover:border-slate-300'
          ]"
        >
          <span class="text-[10px] font-bold uppercase tracking-wider text-slate-400 dark:text-zinc-500">{{ $t('All Subscriptions') }}</span>
          <p class="text-xl font-black text-slate-900 dark:text-white mt-1">{{ props.counts?.all || 0 }}</p>
        </button>

        <button
          type="button"
          @click="setFilter('due_today')"
          :class="[
            'p-4 rounded-2xl border text-left transition shadow-xs cursor-pointer',
            activeFilter === 'due_today'
              ? 'bg-rose-50 dark:bg-rose-950/40 border-rose-300 dark:border-rose-800 ring-2 ring-rose-500/20'
              : 'bg-white dark:bg-zinc-900 border-slate-200 dark:border-zinc-800 hover:border-slate-300'
          ]"
        >
          <span class="text-[10px] font-bold uppercase tracking-wider text-rose-500">{{ $t('Due Today') }}</span>
          <p class="text-xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ props.counts?.due_today || 0 }}</p>
        </button>

        <button
          type="button"
          @click="setFilter('1_7d')"
          :class="[
            'p-4 rounded-2xl border text-left transition shadow-xs cursor-pointer',
            activeFilter === '1_7d'
              ? 'bg-amber-50 dark:bg-amber-950/40 border-amber-300 dark:border-amber-800 ring-2 ring-amber-500/20'
              : 'bg-white dark:bg-zinc-900 border-slate-200 dark:border-zinc-800 hover:border-slate-300'
          ]"
        >
          <span class="text-[10px] font-bold uppercase tracking-wider text-amber-500">{{ $t('Due in 1-7 Days') }}</span>
          <p class="text-xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ props.counts?.due_1_7d || 0 }}</p>
        </button>

        <button
          type="button"
          @click="setFilter('8_30d')"
          :class="[
            'p-4 rounded-2xl border text-left transition shadow-xs cursor-pointer',
            activeFilter === '8_30d'
              ? 'bg-blue-50 dark:bg-blue-950/40 border-blue-300 dark:border-blue-800 ring-2 ring-blue-500/20'
              : 'bg-white dark:bg-zinc-900 border-slate-200 dark:border-zinc-800 hover:border-slate-300'
          ]"
        >
          <span class="text-[10px] font-bold uppercase tracking-wider text-blue-500">{{ $t('Due in 8-30 Days') }}</span>
          <p class="text-xl font-black text-blue-600 dark:text-blue-400 mt-1">{{ props.counts?.due_8_30d || 0 }}</p>
        </button>

        <button
          type="button"
          @click="setFilter('overdue')"
          :class="[
            'p-4 rounded-2xl border text-left transition shadow-xs cursor-pointer',
            activeFilter === 'overdue'
              ? 'bg-zinc-100 dark:bg-zinc-800 border-zinc-400 ring-2 ring-zinc-500/20'
              : 'bg-white dark:bg-zinc-900 border-slate-200 dark:border-zinc-800 hover:border-slate-300'
          ]"
        >
          <span class="text-[10px] font-bold uppercase tracking-wider text-slate-500">{{ $t('Past Due / Expired') }}</span>
          <p class="text-xl font-black text-slate-700 dark:text-zinc-300 mt-1">{{ props.counts?.overdue || 0 }}</p>
        </button>
      </div>

      <!-- Filter bar and search -->
      <div class="bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 rounded-3xl p-5 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-zinc-800">
          <div class="flex flex-wrap items-center gap-1.5 text-xs font-semibold">
            <button
              v-for="f in filterOptions"
              :key="f.key"
              type="button"
              @click="setFilter(f.key)"
              :class="[
                'px-3 py-1.5 rounded-xl transition cursor-pointer',
                activeFilter === f.key
                  ? 'bg-purple-600 text-white shadow-xs'
                  : 'text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800'
              ]"
            >
              {{ f.label }}
            </button>
          </div>

          <div class="w-full sm:w-64">
            <input
              v-model="searchQuery"
              @keyup.enter="handleSearch"
              type="text"
              placeholder="Search by client or plan..."
              class="w-full px-3.5 py-2 rounded-xl border border-slate-200 dark:border-zinc-700 bg-slate-50 dark:bg-zinc-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
            />
          </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50/70 dark:bg-zinc-800/40 text-slate-400 uppercase font-bold tracking-wider border-b border-slate-100 dark:border-zinc-800">
              <tr>
                <th class="py-3 px-3">{{ $t('Client / Organization') }}</th>
                <th class="py-3 px-3">{{ $t('Owner Contact') }}</th>
                <th class="py-3 px-3">{{ $t('Plan & Amount') }}</th>
                <th class="py-3 px-3">{{ $t('Status') }}</th>
                <th class="py-3 px-3">{{ $t('Renewal Date') }}</th>
                <th class="py-3 px-3">{{ $t('Remaining') }}</th>
                <th class="py-3 px-3">{{ $t('Last Reminder') }}</th>
                <th class="py-3 px-3 text-right">{{ $t('Action') }}</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 dark:divide-zinc-800 text-slate-700 dark:text-zinc-300">
              <tr v-for="sub in props.rows" :key="sub.id" class="hover:bg-slate-50/60 dark:hover:bg-zinc-800/30">
                <td class="py-3.5 px-3 font-bold text-slate-900 dark:text-white whitespace-nowrap">
                  {{ sub.organization_name }}
                </td>
                <td class="py-3.5 px-3">
                  <p class="font-semibold text-slate-800 dark:text-zinc-200">{{ sub.owner_name }}</p>
                  <p class="text-[11px] text-slate-400 font-mono">{{ sub.owner_email }}</p>
                </td>
                <td class="py-3.5 px-3 whitespace-nowrap">
                  <p class="font-semibold text-purple-600 dark:text-purple-400">{{ sub.plan_name }}</p>
                  <p class="text-[11px] text-slate-400">{{ sub.amount }}</p>
                </td>
                <td class="py-3.5 px-3 whitespace-nowrap">
                  <span
                    :class="[
                      'px-2 py-0.5 rounded-full text-[10px] font-bold uppercase',
                      sub.status === 'active' ? 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300' :
                      sub.status === 'trial' ? 'bg-purple-100 dark:bg-purple-950/60 text-purple-700 dark:text-purple-300' :
                      'bg-slate-100 dark:bg-zinc-800 text-slate-500'
                    ]"
                  >
                    {{ sub.status }}
                  </span>
                </td>
                <td class="py-3.5 px-3 font-mono font-medium whitespace-nowrap">
                  {{ sub.valid_until }}
                </td>
                <td class="py-3.5 px-3 whitespace-nowrap">
                  <span
                    :class="[
                      'font-bold',
                      sub.days_remaining < 0 ? 'text-rose-600 dark:text-rose-400' :
                      sub.days_remaining <= 3 ? 'text-rose-500' :
                      sub.days_remaining <= 7 ? 'text-amber-500' :
                      'text-emerald-600 dark:text-emerald-400'
                    ]"
                  >
                    {{ sub.days_remaining < 0 ? $t('{n}d overdue', { n: Math.abs(sub.days_remaining) }) : sub.days_remaining === 0 ? $t('Due Today') : $t('{n} days', { n: sub.days_remaining }) }}
                  </span>
                </td>
                <td class="py-3.5 px-3 whitespace-nowrap text-slate-400">
                  {{ sub.last_reminder_at }}
                </td>
                <td class="py-3.5 px-3 text-right whitespace-nowrap">
                  <button
                    type="button"
                    @click="sendManualReminder(sub)"
                    :disabled="sendingId === sub.id"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold text-emerald-700 dark:text-emerald-300 bg-emerald-50 dark:bg-emerald-950/40 hover:bg-emerald-100 dark:hover:bg-emerald-900/60 border border-emerald-300 dark:border-emerald-800 transition cursor-pointer disabled:opacity-50"
                  >
                    <svg v-if="sendingId === sub.id" class="animate-spin w-3 h-3 text-emerald-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <span>{{ sendingId === sub.id ? $t('Sending...') : $t('Send Reminder') }}</span>
                  </button>
                </td>
              </tr>
              <tr v-if="!props.rows?.length">
                <td colspan="8" class="text-center py-10 text-slate-400">
                  {{ $t('No subscriptions match the selected criteria.') }}
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../Layout/App.vue';

const { t } = useI18n();

const props = defineProps({
  rows: Array,
  counts: Object,
  filters: Object,
});

const activeFilter = ref(props.filters?.filter || 'all');
const searchQuery = ref(props.filters?.search || '');
const sendingId = ref(null);
const isInspecting = ref(false);

const filterOptions = [
  { key: 'all', label: t('All') },
  { key: 'due_today', label: t('Due Today') },
  { key: '1_7d', label: t('1-7 Days') },
  { key: '8_30d', label: t('8-30 Days') },
  { key: 'overdue', label: t('Overdue') },
  { key: 'trial', label: t('Trial') },
  { key: 'active', label: t('Active') },
];

const setFilter = (key) => {
  activeFilter.value = key;
  navigate();
};

const handleSearch = () => {
  navigate();
};

const navigate = () => {
  router.get('/admin/subscriptions/renewal-due', {
    filter: activeFilter.value,
    search: searchQuery.value,
  }, {
    preserveState: true,
    replace: true,
  });
};

const sendManualReminder = (sub) => {
  if (sendingId.value) return;
  sendingId.value = sub.id;

  router.post(`/admin/subscriptions/${sub.id}/send-reminder`, {}, {
    preserveScroll: true,
    onFinish: () => {
      sendingId.value = null;
    }
  });
};

const triggerBulkInspection = () => {
  if (isInspecting.value) return;
  isInspecting.value = true;
  router.get('/admin/subscriptions/renewal-due', { filter: activeFilter.value }, {
    preserveScroll: true,
    onFinish: () => {
      isInspecting.value = false;
    }
  });
};
</script>
