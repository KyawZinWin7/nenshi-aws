<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';

const show = ref(false);
const dropdownRef = ref(null); // reference to the dropdown element

const props = defineProps({
  tasks: {
    type: Object,
    required: true,
  },
});
// console.log(props.tasks);

const emit = defineEmits(['filter']);

const taskNames = computed(() => {
  return [...new Set(props.tasks.data.map(task => task.name))];
});

const checkedTasks = ref([]);

const filter = (e) => {
  const value = e.target.value;
  if (e.target.checked) {
    if (!checkedTasks.value.includes(value)) {
      checkedTasks.value.push(value);
    }
  } else {
    checkedTasks.value = checkedTasks.value.filter(item => item !== value);
  }
  emit('filter', checkedTasks.value);
};

// click outside => hide dropdown
const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    show.value = false;
  }
};

onMounted(() => {
  document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
});
</script>

<template>
  <div class="relative flex items-center px-4" ref="dropdownRef">
    <button
      @click="show = !show"
      class="w-full flex items-center justify-between py-2 px-4
             text-sm font-medium text-gray-900 
             bg-white border border-gray-300 rounded-lg 
             shadow-sm hover:bg-gray-50 focus:outline-none
             transition duration-150 min-w-[180px]"
    >
      <span v-if="checkedTasks.length">
        {{ checkedTasks.join(', ') }}
      </span>
      <span v-else>
        フィルター
      </span>
      <svg
        class="ml-2 h-4 w-4 text-gray-500"
        xmlns="http://www.w3.org/2000/svg"
        fill="none" viewBox="0 0 24 24" stroke="currentColor"
      >
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <div
      v-if="show"
      class="absolute top-12 right-0 z-20 w-48 bg-white rounded-lg shadow-lg p-4"
    >
      <h6 class="mb-3 text-sm font-semibold text-gray-700">作業</h6>
      <ul class="space-y-2 text-sm text-gray-700 max-h-48 overflow-auto">
        <li v-for="(task, index) in taskNames" :key="index" class="flex items-center">
          <input
            :id="`filter_option_${index}`"
            type="checkbox"
            :value="task"
            @change="filter"
            :checked="checkedTasks.includes(task)"
            class="mr-2 w-4 h-4 bg-gray-100 border border-gray-300 rounded cursor-pointer"
          />
          <label
            :for="`filter_option_${index}`"
            class="ml-2 text-sm font-medium text-gray-900 cursor-pointer select-none"
          >
            {{ task }}
          </label>
        </li>
      </ul>
    </div>
  </div>
</template>
