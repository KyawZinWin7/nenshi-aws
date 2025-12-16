<script setup>
import { computed, ref, watch } from 'vue';
import FilterRadios from './FilterRadios.vue';
import SearchForm from './SearchForm.vue';
import FilterDropdown from './FilterDropdown.vue';
import Pagination from './Pagination.vue';
import dayjs from 'dayjs';
import { useForm, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const page = usePage();
const user = computed(() => page.props.auth.user);

const searchFilter = ref('');
const radioFilter = ref('all');
const checkboxFilter = ref([]);

const currentPage = ref(1);
const perPage = ref(50);

const props = defineProps({
  mainoperations: { type: Object, required: true },
  machinetypes: { type: Object, required: true },
  tasks: { type: Object, required: true },
});

watch(
  () => props.mainoperations.data,
  () => {
    currentPage.value = 1;
  },
  { immediate: true }
);

const filteredMainOperations = computed(() => {
  let items = props.mainoperations.data ?? [];
  const search = searchFilter.value.toLowerCase();

  return items
    .filter((m) => {
      const matchesSearch =
        !search ||
        m.start_time?.toLowerCase().includes(search) ||
        m.end_time?.toLowerCase().includes(search) ||
        m.machine_type?.name?.toLowerCase().includes(search) ||
        m.machine_number?.number?.toLowerCase().includes(search) ||
        m.task?.name?.toLowerCase().includes(search) ||
        m.employee?.name?.toLowerCase().includes(search);

      const matchesRadio =
        radioFilter.value === 'all' ||
        m.machine_type?.id === parseInt(radioFilter.value);

      const matchesCheckbox =
        checkboxFilter.value.length === 0 ||
        checkboxFilter.value.includes(m.task?.name);

      return matchesSearch && matchesRadio && matchesCheckbox && m.status === 1;
    })
    .sort((a, b) => dayjs(b.start_time).valueOf() - dayjs(a.start_time).valueOf());
});

const paginatedMainOperations = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  return filteredMainOperations.value.slice(start, start + perPage.value);
});

const handleSearch = (val) => (searchFilter.value = val);
const handleRadioFilter = (val) => (radioFilter.value = val);
const handleCheckboxFilter = (val) => (checkboxFilter.value = val);

const uncompleteForm = useForm({});
const uncompleteMO = async (id) => {
  const result = await Swal.fire({
    title: 'この作業を未完了にしますか？',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'はい',
  });

  if (result.isConfirmed) {
    uncompleteForm.post(route('mainoperations.uncomplete', id));
  }
};

const deleteForm = useForm({});
const deleteMO = (id) => {
  Swal.fire({
    title: '削除しますか？',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: '削除',
  }).then((r) => {
    if (r.isConfirmed) {
      deleteForm.delete(route('mainoperations.destroy', id));
    }
  });
};

const refreshData = () => location.reload();
</script>

<template>
  <div class="bg-white border rounded-lg">

    <!-- Filters -->
    <div class="flex flex-wrap gap-3 p-3">
      <SearchForm @search="handleSearch" />

      <button
        @click="refreshData"
        class="bg-blue-600 text-white px-3 py-2 rounded hover:bg-blue-700 text-sm">
        更新
      </button>

      <FilterRadios
        :machinetypes="machinetypes"
        :tasks="tasks"
        @filter="handleRadioFilter" />

      <FilterDropdown
        :tasks="tasks"
        @filter="handleCheckboxFilter" />
    </div>

    <!-- Scroll Hint -->
    <div class="sm:hidden text-xs text-gray-400 px-3 pb-1">
      ← 横にスクロールできます →
    </div>

    <!-- Horizontal Scroll Table -->
    <div class="w-full overflow-x-auto">
      <table
        class="w-full min-w-[1200px] table-fixed whitespace-nowrap
               text-[11px] sm:text-sm text-left text-gray-500">

        <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
          <tr>
            <th class="px-2 py-2">Date</th>
            <th class="px-2 py-2">工場</th>
            <th class="px-2 py-2">機台</th>
            <th class="px-2 py-2">機号</th>
            <th class="px-2 py-2">作業</th>
            <th class="px-2 py-2">小作業</th>
            <th class="px-2 py-2">開始</th>
            <th class="px-2 py-2">終了</th>
            <th class="px-2 py-2">担当者</th>
            <th class="px-2 py-2">合計時間</th>
            <th class="px-2 py-2">メンバー</th>
            <th class="px-2 py-2 text-center">操作</th>
          </tr>
        </thead>

        <tbody>
          <tr
            v-for="m in paginatedMainOperations"
            :key="m.id"
            class="border-b">

            <td class="px-2 py-2">{{ m.created_at }}</td>
            <td class="px-2 py-2">{{ m.plant?.name ?? '未設定' }}</td>
            <td class="px-2 py-2">{{ m.machine_type?.name ?? '未設定' }}</td>
            <td class="px-2 py-2">{{ m.machine_number?.number ?? '未設定' }}</td>
            <td class="px-2 py-2">{{ m.task?.name }}</td>
            <td class="px-2 py-2">{{ m.small_task?.name ?? '未設定' }}</td>
            <td class="px-2 py-2">{{ m.start_time }}</td>
            <td class="px-2 py-2">{{ m.end_time }}</td>
            <td class="px-2 py-2">{{ m.employee?.name }}</td>
            <td class="px-2 py-2">{{ m.total_time }}</td>

            <td class="px-2 py-2">
              <div class="flex flex-col text-[10px]">
                <span v-for="mem in m.members" :key="mem.id">
                  {{ mem.name }}
                </span>
              </div>
            </td>

            <td class="px-2 py-2 text-center">
              <div
                v-if="['admin','superadmin'].includes(user.role)
                  || user.id === m.employee.id
                  || m.members.some(mem => mem.id === user.id)"
                class="flex gap-1 justify-center">

                <button
                  @click="uncompleteMO(m.id)"
                  class="px-2 py-1 text-xs bg-blue-600 text-white rounded">
                  未完了
                </button>

                <button
                  @click="deleteMO(m.id)"
                  class="px-2 py-1 text-xs bg-red-600 text-white rounded">
                  削除
                </button>
              </div>
            </td>

          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <Pagination
      :total="filteredMainOperations.length"
      :per-page="perPage"
      v-model:current-page="currentPage" />
  </div>
</template>
