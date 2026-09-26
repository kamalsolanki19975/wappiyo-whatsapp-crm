<template>
  <div v-if="pagination && pagination.last_page > 1" class="flex flex-col sm:flex-row justify-between items-center gap-3 py-3 px-2 text-sm">
    <!-- Item summary -->
    <div class="text-xs text-slate-500 dark:text-zinc-400 font-medium">
      Showing page <span class="font-semibold text-slate-800 dark:text-zinc-200">{{ page }}</span> of <span class="font-semibold text-slate-800 dark:text-zinc-200">{{ pagination.last_page }}</span>
      <span v-if="pagination.total" class="ml-1">({{ pagination.total }} items)</span>
    </div>

    <!-- Controls -->
    <div class="inline-flex items-center gap-1 bg-white dark:bg-[#111113] p-1 rounded-xl border border-slate-200/80 dark:border-zinc-800 shadow-sm">
      <!-- First Page -->
      <button
        :disabled="noPreviousPage"
        :class="{'opacity-30 cursor-not-allowed': noPreviousPage}"
        @click="loadPage(1)"
        title="First page"
        class="inline-flex justify-center items-center w-8 h-8 rounded-lg text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 hover:text-slate-900 dark:hover:text-white transition-colors"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="11 17 6 12 11 7"/><polyline points="18 17 13 12 18 7"/></svg>
      </button>

      <!-- Previous Page -->
      <button
        :disabled="noPreviousPage"
        :class="{'opacity-30 cursor-not-allowed': noPreviousPage}"
        @click="loadPage(pagination.current_page - 1)"
        title="Previous page"
        class="inline-flex justify-center items-center w-8 h-8 rounded-lg text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 hover:text-slate-900 dark:hover:text-white transition-colors"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
      </button>

      <!-- Current Page Badge -->
      <div class="px-3 py-1 text-xs font-semibold text-[#6C5CE7] dark:text-purple-400 bg-purple-50 dark:bg-purple-950/50 rounded-lg border border-purple-200/60 dark:border-purple-800/40">
        {{ page }} / {{ pagination.last_page }}
      </div>

      <!-- Next Page -->
      <button
        :disabled="noNextPage"
        :class="{'opacity-30 cursor-not-allowed': noNextPage}"
        @click="loadPage(pagination.current_page + 1)"
        title="Next page"
        class="inline-flex justify-center items-center w-8 h-8 rounded-lg text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 hover:text-slate-900 dark:hover:text-white transition-colors"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 18 15 12 9 6"/></svg>
      </button>

      <!-- Last Page -->
      <button
        :disabled="noNextPage"
        :class="{'opacity-30 cursor-not-allowed': noNextPage}"
        @click="loadPage(pagination.last_page)"
        title="Last page"
        class="inline-flex justify-center items-center w-8 h-8 rounded-lg text-slate-600 dark:text-zinc-400 hover:bg-slate-100 dark:hover:bg-zinc-800 hover:text-slate-900 dark:hover:text-white transition-colors"
      >
        <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="13 17 18 12 13 7"/><polyline points="6 17 11 12 6 7"/></svg>
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Pagination',
  props: {
    pagination: {
      type: Object,
      default: () => ({ current_page: 1, last_page: 1, total: 0 }),
    },
  },
  data() {
    return {
      page: this.pagination ? this.pagination.current_page : 1
    }
  },
  watch: {
    'pagination.current_page': function(page) {
      this.page = page;
    }
  },
  methods: {
    loadPage(page) {
      this.$inertia.get(this.$page.url, {page: page}, {
        preserveState: true
      });
    }
  },
  computed: {
    noPreviousPage() {
      return !this.pagination || this.pagination.current_page - 1 <= 0;
    },
    noNextPage() {
      return !this.pagination || this.pagination.current_page + 1 > this.pagination.last_page;
    }
  }
};
</script>