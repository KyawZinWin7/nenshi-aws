<script setup>
import { computed } from 'vue';

const props = defineProps({
  total: { type: Number, required: true },
  perPage: { type: Number, required: true },
  currentPage: { type: Number, required: true }
});

const emit = defineEmits(['update:currentPage']);

const totalPages = computed(() => Math.ceil(props.total / props.perPage));

// start / end စာကြောင်းတွက်ချက်
const start = computed(() => (props.currentPage - 1) * props.perPage + 1);
const end = computed(() => {
  const e = props.currentPage * props.perPage;
  return e > props.total ? props.total : e;
});

const changePage = (page) => {
  if (page > 0 && page <= totalPages.value) {
    emit('update:currentPage', page);
  }
};

// number buttons logic (with "...")
const pages = computed(() => {
  const total = totalPages.value;
  const current = props.currentPage;
  const delta = 2;
  const range = [];
  const rangeWithDots = [];
  let l;

  for (let i = 1; i <= total; i++) {
    if (i === 1 || i === total || (i >= current - delta && i <= current + delta)) {
      range.push(i);
    }
  }

  for (let i of range) {
    if (l) {
      if (i - l === 2) rangeWithDots.push(l + 1);
      else if (i - l !== 1) rangeWithDots.push('...');
    }
    rangeWithDots.push(i);
    l = i;
  }
  return rangeWithDots;
});
</script>

<template>
  <div
    class="flex flex-col sm:flex-row sm:items-center sm:justify-between py-3 space-y-2 sm:space-y-0 sm:space-x-4"
  >
    <!-- Showing text -->
    <p class="text-sm text-gray-700 px-2 text-center sm:text-left">
      全 <span class="font-medium">{{ total }}</span> 件中
      <span class="font-medium">{{ start }}</span> ～ <span class="font-medium">{{ end }}</span> 件を表示
    </p>

    <!-- Pagination numbers -->
    <div
      class="flex flex-wrap justify-center items-center space-x-1 space-y-1 px-2 sm:space-y-0 sm:flex-nowrap"
    >
      <button
        class="px-2 py-1 sm:px-3 sm:py-1 border rounded-md text-sm min-w-[32px]"
        :class="{ 'opacity-50 cursor-not-allowed': props.currentPage === 1 }"
        :disabled="props.currentPage === 1"
        @click="changePage(props.currentPage - 1)"
        aria-label="Previous page"
      >
        ‹
      </button>

      <button
        v-for="(page, i) in pages"
        :key="i"
        class="px-2 py-1 sm:px-3 sm:py-1 border rounded-md text-sm min-w-[32px]"
        :class="{
          'bg-indigo-600 text-white': page === props.currentPage,
          'cursor-default': page==='...'
        }"
        @click="page !== '...' && changePage(page)"
        :aria-current="page === props.currentPage ? 'page' : false"
      >
        {{ page }}
      </button>

      <button
        class="px-2 py-1 sm:px-3 sm:py-1 border rounded-md text-sm min-w-[32px]"
        :class="{ 'opacity-50 cursor-not-allowed': props.currentPage === totalPages }"
        :disabled="props.currentPage === totalPages"
        @click="changePage(props.currentPage + 1)"
        aria-label="Next page"
      >
        ›
      </button>
    </div>
  </div>
</template>
