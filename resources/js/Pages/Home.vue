<script setup>
import Container from '../Components/Container.vue';
import PrimaryBtn from '../Components/PrimaryBtn.vue';
import { useForm,usePage } from '@inertiajs/vue3';
import SearchForm from '../Components/SearchForm.vue';
import { ref, computed } from 'vue';
import dayjs from 'dayjs';
import Swal from 'sweetalert2';





const props = defineProps({
  mainoperations: {
    type: Object,
    required: true,
  },
  machinetypes: {
    type: Object,
    required: true,
  },
  tasks: {
    type: Object,
    required: true,
  },
  employees: {
    type: Object,
    required: true,
  },
  machinenumber: {
    // type သတ်မှတ်ပါ
  }
});



const form = useForm({
  machine_type_id: "",
  machine_number: "",
  task_id: "",
  employee_id: "",


});


//For Main Operation Create

const createMainOperation = () => {
  form.post(route("mainoperations.store"), {
    onSuccess: () => {
      form.reset();
      Swal.fire({
        position: "top-end",
        icon: "success",
        title: "登録が成功しました！",
        showConfirmButton: false,
        timer: 1500
      });
    },
    onError: (errors) => {
      // Laravel validation error messages are available in form.errors
      if (form.hasErrors) {
        Swal.fire({
          icon: "error",
          title: "入力内容にエラーがあります",
          html: Object.values(form.errors)
            .map((e) => `<p>${e}</p>`)
            .join(""),
        });
      } else {
        Swal.fire({
          icon: "error",
          title: "エラーが発生しました",
          text: "もう一度お試しください。",
        });
      }
    }
  });
};

//end

//for complete

const completeForm = useForm({});
const completeMO = async (moId, employeeCode) => {
  // Step 1: Confirm Dialog
  const confirmResult = await Swal.fire({
    title: "この作業を完了してもよろしいですか？",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "はい、完了します",
    cancelButtonText: "キャンセル",
  });

  if (!confirmResult.isConfirmed) return;

  // Step 2: Prompt for employee code
  const { value: userInput } = await Swal.fire({
    title: "担当者コードを入力してください：",
    input: "text",
    inputPlaceholder: "担当者コード",
    showCancelButton: true,
    cancelButtonText: "キャンセル",
    confirmButtonText: "確認",
    inputAttributes:{
      autocomplete:"off"
    },
    inputValidator: (value) => {
      if (!value) {
        return "コードを入力してください。";
      }
    },
  });

  if (userInput === undefined) return;

  // Step 3: Code check
  if (userInput.trim() === employeeCode) {
    completeForm.post(route("mainoperations.complete", moId), {
      onSuccess: () => {
        Swal.fire({
          icon: "success",
          title: "作業を完了しました！",
          showConfirmButton: false,
          timer: 1500,
        });
      },
      onError: () => {
        Swal.fire({
          icon: "error",
          title: "完了に失敗しました",
          text: "もう一度お試しください。",
        });
      }
    });
  } else {
    // Step 4: Error if code mismatch
    Swal.fire({
      icon: "error",
      title: "コードが一致しません",
      text: "作業を完了できません。",
    });
  }
};

//end





//For Detlete Complete
const deleteForm = useForm({});
const deleteMO = async (moId, employeeCode) => {
  // Step 1: Confirm Dialog
  const confirmResult = await Swal.fire({
    title: "この作業を削除してもよろしいですか？",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "はい、削除します",
    cancelButtonText: "キャンセル",
  });

  if (!confirmResult.isConfirmed) return;

  // Step 2: Prompt Dialog for code input
  const { value: userInput } = await Swal.fire({
    title: "担当者コードを入力してください：",
    input: "text",
    inputPlaceholder: "担当者コード",
    inputValidator: (value) => {
      if (!value) {
        return "コードを入力してください。";
      }
    },
    showCancelButton: true,
    cancelButtonText: "キャンセル",
    confirmButtonText: "確認",
  });

  if (userInput === undefined) return; // user pressed cancel

  // Step 3: Check code
  if (userInput.trim() === employeeCode) {
    deleteForm.delete(route("mainoperations.destroy", moId), {
      onSuccess: () => {
        Swal.fire({
          icon: "success",
          title: "削除されました！",
          timer: 1500,
          showConfirmButton: false,
        });
      },
      onError: () => {
        Swal.fire({
          icon: "error",
          title: "削除に失敗しました",
          text: "もう一度お試しください。",
        });
      }
    });
  } else {
    // Step 4: Error Message
    Swal.fire({
      icon: "error",
      title: "コードが一致しません",
      text: "作業を削除できません。",
    });
  }
};

//end





//for search
const searchFilter = ref('');
const handleSearch = (search) => {
  searchFilter.value = search;
};

const filteredMainOperations = computed(() => {
  let items = props.mainoperations.data ?? [];
  const search = searchFilter.value.trim().toLowerCase();

  // 1. Filter
  const filtered = items.filter(mainoperation => {
    const matchesStatus = mainoperation.status == 0;

    const matchesSearch = (
      mainoperation.created_at?.toLowerCase().includes(search) ||
      mainoperation.machine_type?.name?.toLowerCase().includes(search) ||
      mainoperation.machine_number?.toLowerCase().includes(search) ||
      mainoperation.task?.name?.toLowerCase().includes(search) ||
      mainoperation.employee?.name?.toLowerCase().includes(search)
    );

    return matchesStatus && matchesSearch;
  });

  // 2. Sort by start_time descending (latest time first)
  return filtered.sort((a, b) => {
    return dayjs(b.start_time).valueOf() - dayjs(a.start_time).valueOf();
  });
});



</script>

<template>
  <Head title="-ホーム " />

  <main class="p-4 sm:p-6 mx-auto min-h-screen text-xs sm:text-sm lg:text-base">
    <div class="flex flex-col lg:flex-row gap-4 lg:gap-x-8">
      <!-- Left Form -->
      <div class="w-full lg:w-[30%]">
        <Container>
          <form @submit.prevent="createMainOperation" class="space-y-4 sm:space-y-6 text-xs sm:text-sm">
            
            <!-- Employee -->
            <div>
              <label class="block text-xs sm:text-sm font-medium text-gray-700">担当者</label>
              <select v-model="form.employee_id"
                class="mt-1 block w-full border rounded-md py-1.5 sm:py-2 px-2 sm:px-3 text-xs sm:text-sm focus:ring focus:outline-none">
                <option value="">担当者を選択</option>
                <option v-for="employee in employees.data" :key="employee.id" :value="employee.id">
                  {{ employee.name }}
                </option>
              </select>
            </div>

            <!-- Machine Type & Number -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700">機台</label>
                <select v-model="form.machine_type_id"
                  class="mt-1 block w-full border rounded-md py-1.5 sm:py-2 px-2 sm:px-3 text-xs sm:text-sm">
                  <option value="">機台を選択</option>
                  <option v-for="mt in machinetypes.data" :key="mt.id" :value="mt.id">{{ mt.name }}</option>
                </select>
              </div>
              <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700">機台の番号</label>
                <select v-model="form.machine_number"
                  class="mt-1 block w-full border rounded-md py-1.5 sm:py-2 px-2 sm:px-3 text-xs sm:text-sm">
                  <option value="">機台の番号</option>
                  <option v-for="num in 50" :key="num" :value="num">{{ num }}</option>
                </select>
              </div>
            </div>

            <!-- Task -->
            <div>
              <label class="block text-xs sm:text-sm font-medium text-gray-700">作業</label>
              <select v-model="form.task_id"
                class="mt-1 block w-full border rounded-md py-1.5 sm:py-2 px-2 sm:px-3 text-xs sm:text-sm">
                <option value="">作業を選択</option>
                <option v-for="task in tasks.data" :key="task.id" :value="task.id">{{ task.name }}</option>
              </select>
            </div>

            <!-- Submit -->
            <PrimaryBtn class="w-full py-2 sm:py-2.5 text-xs sm:text-sm">スタート</PrimaryBtn>
          </form>
        </Container>
      </div>

      <!-- Right Table -->
      <div class="overflow-x-auto">
        <table class="min-w-[700px] sm:min-w-full divide-y divide-gray-300 text-[11px] sm:text-sm">
          <thead class="bg-gray-50 text-left">
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
          <tbody class="bg-white divide-y divide-gray-200" v-for="mo in filteredMainOperations" :key="mo.id">
            <tr>
              <td class="px-2 sm:px-4 py-2">{{ mo.created_at || 'No Date' }}</td>
              <td class="px-2 sm:px-4 py-2">{{ mo.machine_type.name }}</td>
              <td class="px-2 sm:px-4 py-2">{{ mo.machine_number }}</td>
              <td class="px-2 sm:px-4 py-2">{{ mo.task.name }}</td>
              <td class="px-2 sm:px-4 py-2">{{ mo.start_time }}</td>
              <td class="px-2 sm:px-4 py-2">{{ mo.end_time }}</td>
              <td class="px-2 sm:px-4 py-2">{{ mo.employee.name }}</td>
              <td class="px-2 sm:px-4 py-2">{{ mo.total_time }}</td>
              <td class="px-2 sm:px-4 py-2 flex gap-1 sm:gap-2">
                <button @click="completeMO(mo.id, mo.employee.employee_code)"
                  class="px-2 sm:px-3 py-1 bg-green-600 text-white rounded text-[11px] sm:text-sm hover:bg-green-700">
                  完了
                </button>
                <button @click="deleteMO(mo.id,mo.employee.employee_code)"
                  class="px-2 sm:px-3 py-1 bg-red-600 text-white rounded text-[11px] sm:text-sm hover:bg-red-700">
                  削除
                </button>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>
  </main>
</template>

