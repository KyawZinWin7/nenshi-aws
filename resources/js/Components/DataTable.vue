<script setup>
import { computed, ref, watch } from 'vue';
import FilterRadios from './FilterRadios.vue';
import SearchForm from './SearchForm.vue';
import FilterDropdown from './FilterDropdown.vue';
import DateFilter from './DateFilter.vue';
import dayjs from 'dayjs';
import Pagination from './Pagination.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import * as XLSX from 'xlsx';
import { saveAs } from 'file-saver';

const page = usePage();
const user = computed(() => page.props.auth.user);

const searchFilter = ref('');
const radioFilter = ref('all');
const checkboxFilter = ref([]);
const startDateFilter = ref(null);
const endDateFilter = ref(null);

const currentPage = ref(1);
const perPage = ref(20);

const props = defineProps({
  mainoperations: { type: Object, required: true },
  machinetypes: { type: Object, required: true },
  tasks: { type: Object, required: true },
});

// ✅ Watch for data reload to reset pagination or re-apply filters
watch(
  () => props.mainoperations.data,
  (newData) => {
    if (newData?.length > 0) {
      currentPage.value = 1;
      // Optionally reapply date filter
      // handleDateRangeSelected({
      //   start: startDateFilter.value,
      //   end: endDateFilter.value,
      // });
    }
  },
  { immediate: true }
);

const filteredMainOperations = computed(() => {
  let items = props.mainoperations.data ?? [];
  const search = searchFilter.value.trim().toLowerCase();
  const radio = radioFilter.value;
  const checkboxes = checkboxFilter.value || [];

  const filtered = items.filter((mainoperation) => {
    const startDate = mainoperation.start_time
      ? dayjs(mainoperation.start_time).format('YYYY-MM-DD')
      : null;

    const matchesDateRange =
      (!startDateFilter.value || !endDateFilter.value) ||
      (startDate >= startDateFilter.value && startDate <= endDateFilter.value);

    const matchesRadio =
      !radio || radio === 'all' || mainoperation.machine_type?.id === parseInt(radio);

    const matchesSearch =
      !search ||
      mainoperation.start_time?.toLowerCase().includes(search) ||
      mainoperation.end_time?.toLowerCase().includes(search) ||
      mainoperation.machine_type?.name?.toLowerCase().includes(search) ||
      mainoperation.machine_number?.toLowerCase().includes(search) ||
      mainoperation.task?.name?.toLowerCase().includes(search) ||
      mainoperation.employee?.name?.toLowerCase().includes(search);

    const taskName = mainoperation.task?.name || '';
    const matchesCheckbox =
      checkboxes.length === 0 || checkboxes.includes(taskName);

    const matchesStatus = mainoperation.status == 1;

    return (
      matchesDateRange &&
      matchesRadio &&
      matchesSearch &&
      matchesCheckbox &&
      matchesStatus
    );
  });

  return filtered.sort((a, b) => {
    const dateA = dayjs(a.start_time);
    const dateB = dayjs(b.start_time);
    if (!dateA.isValid()) return 1;
    if (!dateB.isValid()) return -1;
    return dateB.valueOf() - dateA.valueOf();
  });
});

const paginatedMainOperations = computed(() => {
  const start = (currentPage.value - 1) * perPage.value;
  return filteredMainOperations.value.slice(start, start + perPage.value);
});

const totalItems = computed(() => filteredMainOperations.value.length);

const handleSearch = (search) => {
  searchFilter.value = search;
};

const handleRadioFilter = (filter) => {
  radioFilter.value = filter;
};

const handleCheckboxFilter = (filter) => {
  checkboxFilter.value = filter;
};

const handleDateRangeSelected = (range) => {
  startDateFilter.value = range.start;
  endDateFilter.value = range.end;
};

const exportToExcel = () => {
  const data = filteredMainOperations.value.map((op) => ({
    Date: op.created_at,
    機台: op.machine_type?.name ?? '未設定',
    機台の番号: op.machine_number,
    作業: op.task?.name,
    開始時間: op.start_time,
    終了時間: op.end_time,
    担当者: op.employee?.name,
    合計時間: op.total_time,
  }));

  const worksheet = XLSX.utils.json_to_sheet(data);
  const workbook = XLSX.utils.book_new();
  XLSX.utils.book_append_sheet(workbook, worksheet, 'MainOperations');

  const excelBuffer = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });
  const fileData = new Blob([excelBuffer], { type: 'application/octet-stream' });
  saveAs(fileData, 'MainOperations.xlsx');
};

const uncompleteForm = useForm({});
const uncompleteMO = async (moId) => {
  const result = await Swal.fire({
    title: 'この作業を未完了にしてもよろしいですか？',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'はい、未完了にする',
    cancelButtonText: 'キャンセル',
  });

  if (result.isConfirmed) {
    uncompleteForm.post(route('mainoperations.uncomplete', moId), {
      onSuccess: () => {
        Swal.fire({
          icon: 'success',
          title: '未完了に変更されました！',
          showConfirmButton: false,
          timer: 1500,
        });
      },
      onError: () => {
        Swal.fire({
          icon: 'error',
          title: '変更に失敗しました',
          text: 'もう一度お試しください。',
        });
      },
    });
  }
};

const deleteForm = useForm({});
const deleteMO = (moId) => {
  Swal.fire({
    title: '本当に削除しますか？',
    text: 'この作業を削除すると元に戻せません。',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonColor: '#e3342f',
    cancelButtonColor: '#6c757d',
    confirmButtonText: 'はい、削除します',
    cancelButtonText: 'キャンセル',
  }).then((result) => {
    if (result.isConfirmed) {
      deleteForm.delete(route('mainoperations.destroy', moId));
    }
  });
};

const refreshData = () => {
  location.reload();
};
</script>


<template>
  <div class="bg-white relative border rounded-lg overflow-x-auto">
    <div class="flex flex-wrap items-center justify-between gap-4 px-4 py-2">
      <!-- Search -->
      <SearchForm @search="handleSearch" />

      <button
        @click="refreshData"
        class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 flex items-center gap-1"
      >
        <!-- Lucide RefreshCcw Icon -->
        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
          viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
          <path d="M3 2v6h6"></path>
          <path d="M21 12a9 9 0 1 0-3 6.7L21 22M21 16v6h-6"></path>
        </svg>
        更新
      </button>

      <!-- Date Filter -->
      <DateFilter @date-range-selected="handleDateRangeSelected" />

      <!-- Filters -->
      <div class="flex flex-wrap items-center gap-2 text-sm font-semibold">
        <FilterRadios :machinetypes="machinetypes" :tasks="tasks" @filter="handleRadioFilter" />
        <FilterDropdown :tasks="tasks" @filter="handleCheckboxFilter" />
      </div>

      <!-- Export Button -->
      <button
        v-if="user"
        @click="exportToExcel"
        class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700"
      >
        Excel にエクスポート
      </button>
    </div>

    <!-- Responsive Table Wrapper -->
    <div class="overflow-x-auto">
      <table
        class="min-w-[800px] sm:min-w-full text-[11px] sm:text-sm text-left text-gray-500"
      >
        <thead
          class="text-[10px] sm:text-xs text-gray-700 uppercase bg-gray-50"
        >
          <tr>
            <th class="px-2 sm:px-4 py-2">Date</th>
            <th class="px-2 sm:px-4 py-2">機台</th>
            <th class="px-2 sm:px-4 py-2">機台の番号</th>
            <th class="px-2 sm:px-4 py-2">作業</th>
            <th class="px-2 sm:px-4 py-2">開始時間</th>
            <th class="px-2 sm:px-4 py-2">終了時間</th>
            <th class="px-2 sm:px-4 py-2">担当者</th>
            <th class="px-2 sm:px-4 py-2">合計時間</th>
            <th class="px-2 sm:px-4 py-2">操作</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="mainoperation in paginatedMainOperations"
            :key="mainoperation.id"
            class="border-b"
          >
            <td class="px-2 sm:px-4 py-2">{{ mainoperation.created_at }}</td>
            <td class="px-2 sm:px-4 py-2">
              {{ mainoperation.machine_type?.name ?? '未設定' }}
            </td>
            <td class="px-2 sm:px-4 py-2">{{ mainoperation.machine_number }}</td>
            <td class="px-2 sm:px-4 py-2">{{ mainoperation.task?.name }}</td>
            <td class="px-2 sm:px-4 py-2">{{ mainoperation.start_time }}</td>
            <td class="px-2 sm:px-4 py-2">{{ mainoperation.end_time }}</td>
            <td class="px-2 sm:px-4 py-2">{{ mainoperation.employee?.name }}</td>
            <td class="px-2 sm:px-4 py-2">{{ mainoperation.total_time }}</td>
            <td class="px-2 sm:px-4 py-2" v-if="user">
              <div class="flex items-center justify-center gap-1 sm:gap-2">
                <button
                  @click="uncompleteMO(mainoperation.id)"
                  class="px-2 sm:px-3 py-1 bg-blue-600 text-white rounded text-[10px] sm:text-sm hover:bg-blue-700"
                >
                  未完了
                </button>
                <button
                  @click="deleteMO(mainoperation.id)"
                  class="px-2 sm:px-3 py-1 bg-red-600 text-white rounded text-[10px] sm:text-sm hover:bg-red-700"
                >
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
      v-model:current-page="currentPage"
    />
  </div>
</template>

