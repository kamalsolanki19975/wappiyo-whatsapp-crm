<template>
  <AppLayout>
    <div class="px-6 py-6 space-y-6">
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-200 dark:border-zinc-800">
        <div>
          <h1 class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white flex items-center gap-2.5">
            <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            <span>{{ $t('Admin Notifications') }}</span>
          </h1>
          <p class="text-xs text-slate-500 dark:text-zinc-400 mt-1">
            {{ $t('Broadcast announcements or alert individual workspace owners and users.') }}
          </p>
        </div>
      </div>

      <!-- Flash feedback banner -->
      <div v-if="$page.props.flash?.status" :class="[
        'p-4 rounded-xl border text-xs font-semibold flex items-center gap-2',
        $page.props.flash.status.type === 'success' ? 'bg-emerald-50 text-emerald-800 border-emerald-200 dark:bg-emerald-950/40 dark:text-emerald-300 dark:border-emerald-800' : 'bg-rose-50 text-rose-800 border-rose-200 dark:bg-rose-950/40 dark:text-rose-300 dark:border-rose-800'
      ]">
        <span>{{ $page.props.flash.status.message }}</span>
      </div>

      <!-- Main Layout: Grid Composer + History -->
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Notification Composer Form (5 Cols) -->
        <div class="lg:col-span-5 bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 rounded-2xl p-6 shadow-sm">
          <div class="pb-3 border-b border-slate-100 dark:border-zinc-800 mb-4">
            <h2 class="text-base font-bold text-slate-900 dark:text-white">
              {{ $t('Create Notification') }}
            </h2>
            <p class="text-xs text-slate-500 dark:text-zinc-400">
              {{ $t('Deliver instant in-app alerts directly to user workspaces.') }}
            </p>
          </div>

          <form @submit.prevent="submitNotification" class="space-y-4">
            <!-- Audience Selection -->
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-zinc-300 mb-2">
                {{ $t('Audience') }}
              </label>
              <div class="grid grid-cols-2 gap-3">
                <label
                  :class="[
                    'flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition text-xs font-semibold',
                    form.audience === 'all'
                      ? 'border-purple-600 bg-purple-50/60 dark:bg-purple-950/30 text-purple-700 dark:text-purple-300'
                      : 'border-slate-200 dark:border-zinc-700 text-slate-600 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800'
                  ]"
                >
                  <input type="radio" v-model="form.audience" value="all" class="text-purple-600 focus:ring-purple-500" />
                  <span>{{ $t('All Users') }}</span>
                </label>

                <label
                  :class="[
                    'flex items-center gap-2.5 p-3 rounded-xl border cursor-pointer transition text-xs font-semibold',
                    form.audience === 'specific'
                      ? 'border-purple-600 bg-purple-50/60 dark:bg-purple-950/30 text-purple-700 dark:text-purple-300'
                      : 'border-slate-200 dark:border-zinc-700 text-slate-600 dark:text-zinc-400 hover:bg-slate-50 dark:hover:bg-zinc-800'
                  ]"
                >
                  <input type="radio" v-model="form.audience" value="specific" class="text-purple-600 focus:ring-purple-500" />
                  <span>{{ $t('Specific User') }}</span>
                </label>
              </div>
            </div>

            <!-- Specific User Picker -->
            <div v-if="form.audience === 'specific'">
              <label class="block text-xs font-bold text-slate-700 dark:text-zinc-300 mb-1">
                {{ $t('Select Recipient User') }} *
              </label>
              <select
                v-model="form.user_id"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:outline-none"
                required
              >
                <option value="" disabled>{{ $t('Choose an active user...') }}</option>
                <option v-for="u in props.users" :key="u.id" :value="u.id">
                  {{ u.first_name }} {{ u.last_name || '' }} ({{ u.email }})
                </option>
              </select>
            </div>

            <!-- Type -->
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-zinc-300 mb-1">
                {{ $t('Notification Type') }}
              </label>
              <select
                v-model="form.type"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:outline-none"
              >
                <option value="info">{{ $t('Information') }}</option>
                <option value="announcement">{{ $t('System Announcement') }}</option>
                <option value="warning">{{ $t('Warning / Important') }}</option>
                <option value="success">{{ $t('Success / Milestone') }}</option>
              </select>
            </div>

            <!-- Title -->
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-zinc-300 mb-1">
                {{ $t('Title') }} *
              </label>
              <input
                v-model="form.title"
                type="text"
                maxlength="150"
                placeholder="e.g. Scheduled Maintenance or New Feature"
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:outline-none"
                required
              />
            </div>

            <!-- Message -->
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-zinc-300 mb-1">
                {{ $t('Message') }} *
              </label>
              <textarea
                v-model="form.message"
                rows="4"
                maxlength="1000"
                placeholder="Type your notification message content here..."
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:outline-none"
                required
              ></textarea>
            </div>

            <!-- Action Link -->
            <div>
              <label class="block text-xs font-bold text-slate-700 dark:text-zinc-300 mb-1">
                {{ $t('Target URL (Optional)') }}
              </label>
              <input
                v-model="form.url"
                type="text"
                placeholder="/dashboard or https://..."
                class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-xs text-slate-900 dark:text-white focus:ring-2 focus:ring-purple-500 focus:outline-none"
              />
            </div>

            <div class="pt-2">
              <button
                type="submit"
                :disabled="isSubmitting"
                class="w-full py-3 px-4 rounded-xl text-xs font-bold text-white bg-gradient-to-r from-purple-600 to-indigo-600 hover:from-purple-700 hover:to-indigo-700 shadow-md shadow-purple-500/20 active:scale-[0.98] transition cursor-pointer disabled:opacity-50"
              >
                <span v-if="isSubmitting">{{ $t('Broadcasting...') }}</span>
                <span v-else>{{ $t('Send Notification') }}</span>
              </button>
            </div>
          </form>
        </div>

        <!-- Notification History Log (7 Cols) -->
        <div class="lg:col-span-7 bg-white dark:bg-zinc-900 border border-slate-200/80 dark:border-zinc-800 rounded-2xl p-6 shadow-sm space-y-4">
          <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-100 dark:border-zinc-800">
            <div>
              <h2 class="text-base font-bold text-slate-900 dark:text-white">
                {{ $t('Notification History') }}
              </h2>
              <p class="text-xs text-slate-500 dark:text-zinc-400">
                {{ $t('Recent notifications delivered to users.') }}
              </p>
            </div>

            <!-- Search input -->
            <div class="w-full sm:w-60">
              <input
                v-model="searchQuery"
                @keyup.enter="handleSearch"
                type="text"
                placeholder="Search logs..."
                class="w-full px-3 py-1.5 rounded-lg border border-slate-200 dark:border-zinc-700 bg-slate-50 dark:bg-zinc-800 text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-purple-500"
              />
            </div>
          </div>

          <!-- Table -->
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead class="border-b border-slate-100 dark:border-zinc-800 text-slate-500 dark:text-zinc-400 bg-slate-50/50 dark:bg-zinc-800/40">
                <tr>
                  <th class="py-2.5 px-3 font-semibold">{{ $t('Recipient') }}</th>
                  <th class="py-2.5 px-3 font-semibold">{{ $t('Title & Message') }}</th>
                  <th class="py-2.5 px-3 font-semibold">{{ $t('Status') }}</th>
                  <th class="py-2.5 px-3 font-semibold">{{ $t('Delivered') }}</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-100 dark:divide-zinc-800 text-slate-700 dark:text-zinc-300">
                <tr v-for="item in props.rows.data" :key="item.id" class="hover:bg-slate-50/80 dark:hover:bg-zinc-800/50">
                  <td class="py-3 px-3 font-medium whitespace-nowrap">
                    <span v-if="item.user">
                      {{ item.user.first_name }} {{ item.user.last_name || '' }}
                      <span class="block text-[11px] text-slate-400 font-mono">{{ item.user.email }}</span>
                    </span>
                    <span v-else class="text-slate-400 italic">{{ $t('Unknown User') }}</span>
                  </td>
                  <td class="py-3 px-3 max-w-xs">
                    <p class="font-bold text-slate-900 dark:text-white truncate">{{ item.title }}</p>
                    <p class="text-[11px] text-slate-500 dark:text-zinc-400 line-clamp-2">{{ item.comment }}</p>
                  </td>
                  <td class="py-3 px-3 whitespace-nowrap">
                    <span
                      :class="[
                        'px-2 py-0.5 rounded-full text-[10px] font-bold',
                        item.seen
                          ? 'bg-slate-100 dark:bg-zinc-800 text-slate-600 dark:text-zinc-400'
                          : 'bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-300'
                      ]"
                    >
                      {{ item.seen ? $t('Seen') : $t('Unread') }}
                    </span>
                  </td>
                  <td class="py-3 px-3 text-[11px] text-slate-500 whitespace-nowrap">
                    {{ new Date(item.created_at).toLocaleDateString() }}
                  </td>
                </tr>
                <tr v-if="!props.rows.data?.length">
                  <td colspan="4" class="text-center py-8 text-slate-400">
                    {{ $t('No notifications found.') }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>

          <!-- Pagination -->
          <div v-if="props.rows.links?.length > 3" class="flex justify-center gap-1 pt-3">
            <template v-for="(link, i) in props.rows.links" :key="i">
              <Link
                v-if="link.url"
                :href="link.url"
                v-html="link.label"
                :class="[
                  'px-3 py-1 rounded-lg text-xs font-medium transition',
                  link.active
                    ? 'bg-purple-600 text-white'
                    : 'bg-slate-100 dark:bg-zinc-800 text-slate-700 dark:text-zinc-300 hover:bg-slate-200'
                ]"
              />
              <span
                v-else
                v-html="link.label"
                class="px-3 py-1 text-xs text-slate-400 opacity-50"
              />
            </template>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

<script setup>
import { ref } from 'vue';
import { Link, useForm, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppLayout from '../Layout/App.vue';

const { t } = useI18n();

const props = defineProps({
  rows: Object,
  users: Array,
  filters: Object,
});

const isSubmitting = ref(false);
const searchQuery = ref(props.filters?.search || '');

const form = useForm({
  audience: 'all',
  user_id: '',
  type: 'info',
  title: '',
  message: '',
  url: '/dashboard',
});

const submitNotification = () => {
  if (isSubmitting.value) return;

  if (form.audience === 'specific' && !form.user_id) {
    alert(t('Please select a recipient user.'));
    return;
  }

  isSubmitting.value = true;
  form.post('/admin/notifications/send', {
    preserveScroll: true,
    onSuccess: () => {
      form.reset('title', 'message', 'user_id');
      form.url = '/dashboard';
    },
    onFinish: () => {
      isSubmitting.value = false;
    }
  });
};

const handleSearch = () => {
  router.get('/user-logs/notifications', { search: searchQuery.value }, {
    preserveState: true,
    replace: true,
  });
};
</script>
