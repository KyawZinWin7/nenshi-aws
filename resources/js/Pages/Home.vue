<script setup>
import Container from '../Components/Container.vue';
import PrimaryBtn from '../Components/PrimaryBtn.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import SearchForm from '../Components/SearchForm.vue';
import { ref, computed, watch, onMounted } from 'vue';
import dayjs from 'dayjs';
import Swal from 'sweetalert2';
import axios from 'axios'
import { initFlowbite } from 'flowbite'
// import { route } from 'ziggy-js';




onMounted(() => {
  initFlowbite();
})


const page = usePage();
console.log("Login user id:", page.props.auth.user.id);
const user = computed(() => page.props.auth.user);


const props = defineProps({
  mainoperations: { type: Object, required: true },
  mainoperation: { type: Object, required: false, default: () => ({}) },
  machinetypes: { type: Object, required: true },
  tasks: { type: Object, required: true },
  employees: { type: Object, required: true },
  plants: { type: Object, required: true },
  machinenumbers: { type: Object, required: true },
  machinenumber: { type: Object, required: false, default: () => ({}) },
});



const mainoperation = usePage().props.mainoperation?.data;



const form = useForm({
  machine_type_id: "",
  machine_number_id: "",
  task_id: "",
  employee_id: "",
  plant_id: "",
  team_ids: [],
  small_task: "",




});


const submitForm = () => {
  if (isEditing.value) {
    updateMO();
  } else {
    createMainOperation();
  }
};


//For Main Operation Create
const createMainOperation = () => {
  form.post(route("mainoperations.store"), {
    onSuccess: () => {
      Swal.fire({
        position: "top-end",
        icon: "success",
        title: "登録が成功しました！",
        showConfirmButton: false,
        timer: 1500,
      }).then(() => {
        form.reset(); // 🧹 ← 登録後、フォームリセット
        window.location.href = route("home"); // 🔁 一覧へ移動
      });
    },
    onError: (errors) => {
      if (errors.error === "duplicate") {
        Swal.fire({
          icon: "warning",
          title: "未完了の作業が存在します！",
          html: `
            <p>${errors.message}</p>
            <p><b>担当者：</b>${errors.tanto}</p>
          `,
        });
      } else if (form.hasErrors) {
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
    },
  });
};



//end


//For Main Operation Edit
const isEditing = ref(false);
const editingId = ref(null);



const editMO = (mo) => {
  isEditing.value = true;
  editingId.value = mo.id;

  form.machine_type_id = mo.machine_type?.id || '';
  form.machine_number_id = mo.machine_number_id || mo.machine_number?.id || '';
  form.task_id = mo.task_id || mo.task?.id || '';
  form.employee_id = mo.employee_id || mo.employee?.id || '';
  form.plant_id = mo.plant_id || mo.plant?.id || '';
  form.small_task = mo.small_task || '';
  form.team_ids = mo.team_ids || mo.members?.map(m => m.id) || [];
};



const updateMO = () => {
  form.put(route("mainoperations.update", editingId.value), {
    onSuccess: () => {
      Swal.fire({
        position: "top-end",
        icon: "success",
        title: "更新が成功しました！",
        showConfirmButton: false,
        timer: 1500
      });
      isEditing.value = false;
      editingId.value = null;
      form.reset();
    },
    onError: () => {
      Swal.fire({
        icon: "error",
        title: "更新に失敗しました",
        text: "もう一度お試しください。",
      });
    }
  });
};


//for complete

const completeForm = useForm({});
const completeMO = async (moId) => {
  // Step 1: Confirm Dialog
  const confirmResult = await Swal.fire({
    title: "この作業を完了してもよろしいですか？",
    icon: "question",
    showCancelButton: true,
    confirmButtonText: "はい、完了します",
    cancelButtonText: "キャンセル",
  });

  if (!confirmResult.isConfirmed) return;

  // Step 2: Complete operation directly
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
};


//end





//For Detlete Complete
const deleteForm = useForm({});
const deleteMO = async (moId) => {
  // Step 1: Confirm Dialog
  const confirmResult = await Swal.fire({
    title: "この作業を削除してもよろしいですか？",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "はい、削除します",
    cancelButtonText: "キャンセル",
  });

  if (!confirmResult.isConfirmed) return;

  // Step 2: Delete operation directly
  deleteForm.delete(route("mainoperations.destroy", moId), {
    onSuccess: () => {
      Swal.fire({
        icon: "success",
        title: "削除されました！",
        showConfirmButton: false,
        timer: 1500,
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





// For member


// login user info

const loginUserId = page.props.auth.user.id;
const loginUserName = page.props.auth.user.name;

// main person (login user fixed)
const selectedMainPerson = ref(loginUserId);
const teamMembers = ref([]);

// assign login user to form.employee_id
form.employee_id = loginUserId;

// Load team members (exclude main person)
function loadTeamMembers(personId) {
  const all = props.employees.data || [];
  teamMembers.value = all.filter(e => e.id !== personId);
  form.team_ids = []; // reset previous selection
}

// Initial load
loadTeamMembers(loginUserId);

// Watch in case main person changes (optional, for future flexibility)
watch(selectedMainPerson, (newVal) => {
  if (newVal) {
    loadTeamMembers(newVal);
    form.employee_id = newVal;
  } else {
    teamMembers.value = [];
    form.employee_id = '';
  }
});



// For auto select


// ✅ Dynamic dropdown data
const machinetypes = ref([]);
const machinenumbers = ref([]);
const tasks = ref([]);


//For plant and machinetype

watch(
  () => form.plant_id,
  async (newPlantId) => {
    if (!newPlantId) {
      machinetypes.value = [];
      machinenumbers.value = [];
      form.machine_type_id = "";
      form.machine_number_id = "";
      return;
    }

    try {
      const { data } = await axios.get(`/machines/by-plant/${newPlantId}`);
      machinetypes.value = data.machineTypes;
      machinenumbers.value = data.machineNumbers;

      // 🔹 Edit mode မှာ reset မလုပ်ဘူး
      if (!isEditing.value) {
        form.machine_type_id = "";
        form.machine_number_id = "";
      }
    } catch (error) {
      console.error("Error fetching machine data:", error);
    }
  }
);

watch(
  [() => form.plant_id, () => form.machine_type_id],
  async ([plantId, typeId]) => {
    if (!plantId || !typeId) {
      machinenumbers.value = [];
      if (!isEditing.value) form.machine_number_id = "";
      return;
    }

    try {
      const { data } = await axios.get(`/machines/by-type`, {
        params: { plant_id: plantId, machine_type_id: typeId }
      });
      machinenumbers.value = data;

      if (!isEditing.value) form.machine_number_id = "";
    } catch (error) {
      console.error("Error fetching machine numbers:", error);
    }
  }
);

watch(
  () => form.machine_type_id,
  async (newTypeId) => {
    if (!newTypeId) {
      machinenumbers.value = [];
      tasks.value = [];
      if (!isEditing.value) {
        form.machine_number_id = "";
        form.task_id = "";
      }
      return;
    }

    try {
      const numbersResponse = await axios.get(`/machines/by-type`, {
        params: {
          plant_id: form.plant_id,
          machine_type_id: newTypeId
        }
      });
      machinenumbers.value = numbersResponse.data;
      if (!isEditing.value) form.machine_number_id = "";

      const tasksResponse = await axios.get(`/tasks/by-machine-type`, {
        params: { machine_type_id: newTypeId }
      });
      tasks.value = tasksResponse.data;
      if (!isEditing.value) form.task_id = "";
    } catch (error) {
      console.error("Error fetching machine numbers or tasks:", error);
    }
  }
);


const cancelEdit = () => {
  isEditing.value = false;
  editingId.value = null;
  form.reset(); // form fields reset
};




</script>

<template>

  <Head title="-ホーム " />

  <main class="p-4 sm:p-6 mx-auto min-h-screen text-xs sm:text-sm lg:text-base">
    <div class="flex flex-col items-center  gap-4">
      <!-- Left Form -->
      {{ mainoperation }}
      <div class="w-full lg:w-[30%] mb-8">
        <Container>
          <form @submit.prevent="submitForm" class="space-y-4 sm:space-y-6 text-xs sm:text-sm">



            <div>
              <label class="form-label">担当者</label>
              <select v-model="selectedMainPerson" class="select-uniform" disabled>
                <option :value="loginUserId">
                  <!-- 担当者を選択 -->
                  {{ loginUserName }}
                </option>
                <!-- <option v-for="employee in employees.data" :key="employee.id" :value="employee.id">
                  {{ employee.name }}
                </option> -->
              </select>
            </div>

            <!-- 工場 & 機台 -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="form-label">工場</label>
                <select v-model="form.plant_id" class="select-uniform">
                  <option value="">工場を選択</option>
                  <option v-for="plant in plants.data" :key="plant.id" :value="plant.id">
                    {{ plant.name }}
                  </option>
                </select>
              </div>

              <div>
                <label class="form-label">機台</label>
                <select v-model="form.machine_type_id" class="select-uniform">
                  <option value="">機台を選択</option>
                  <option v-for="mt in machinetypes" :key="mt.id" :value="mt.id">
                    {{ mt.name }}
                  </option>
                </select>
              </div>
            </div>


            <!-- 機台番号 & 作業 -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="form-label">機台番号</label>
                <select v-model="form.machine_number_id" class="select-uniform">
                  <option value="">機台番号</option>
                  <option v-for="mn in machinenumbers" :key="mn.id" :value="mn.id">
                    {{ mn.number }}
                  </option>
                </select>
              </div>

              <div>
                <label class="form-label">作業</label>
                <select v-model="form.task_id" class="select-uniform">
                  <option value="">作業を選択</option>
                  <option v-for="t in tasks" :key="t.id" :value="t.id">
                    {{ t.name }}
                  </option>
                </select>
              </div>

            </div>


            <div>
              <label class="form-label"> 小作業 </label>
              <select v-model="form.small_task" class="select-uniform">
                <option value="">選択</option>
                <option value="upper">
                  上
                </option>
                <option value="lower">
                  下
                </option>
                <!-- <option value="upper_half">
                  上半
                </option>
                <option value="lower_half">
                  下半
                </option> -->

              </select>
            </div>


            <!-- 一緒に作業する人 -->
            <div>
              <label class="form-label">一緒に作業する人</label>
              <el-select v-model="form.team_ids" multiple placeholder="メンバーを選択" class="select-uniform !p-0"
                popper-class="custom-select-dropdown">
                <el-option v-for="member in teamMembers" :key="member.id" :label="member.name" :value="member.id" />
              </el-select>
            </div>



            <!-- Submit -->
            <div class="flex flex-col sm:flex-row gap-2">
              <!-- Submit button -->
              <PrimaryBtn class="w-full sm:flex-1 py-2 sm:py-2.5 text-xs sm:text-sm">
                {{ isEditing ? '更新' : 'スタート' }}
              </PrimaryBtn>

              <!-- Cancel button (only in edit mode) -->
              <button v-if="isEditing" type="button" @click="cancelEdit"
                class="w-full sm:flex-1 py-2 sm:py-2.5 text-xs sm:text-sm bg-gray-400 text-white rounded hover:bg-gray-500">
                キャンセル
              </button>
            </div>



          </form>

        </Container>
      </div>

      <!-- Right Table -->
      <!-- Right Table -->
      <div class="w-full overflow-x-auto">
        <table class="min-w-[700px] sm:min-w-full divide-y divide-gray-300 text-[11px] sm:text-sm">
          <thead class="bg-gray-50 text-left">
            <tr>
              <th class="px-2 sm:px-4 py-2">Date</th>
              <th class="px-2 sm:px-4 py-2">工場</th>
              <th class="px-2 sm:px-4 py-2">機台</th>
              <th class="px-2 sm:px-4 py-2">機号</th>
              <th class="px-2 sm:px-4 py-2">作業</th>
              <th class="px-2 sm:px-4 py-2">開始</th>
              <th class="px-2 sm:px-4 py-2">終了</th>
              <th class="px-2 sm:px-4 py-2">担当者</th>
              <th class="px-2 sm:px-4 py-2">合計時間</th>
              <th class="px-2 sm:px-4 py-2">メンバー</th>
              <th class="px-2 sm:px-4 py-2">操作</th>
            </tr>
          </thead>

          <tbody class="bg-white divide-y divide-gray-200" v-for="mo in filteredMainOperations" :key="mo.id">
            <tr>
              <td class="px-2 sm:px-4 py-2 whitespace-nowrap">{{ mo.created_at || 'No Date' }}</td>
              <td class="px-2 sm:px-4 py-2 whitespace-nowrap">{{ mo.plant?.name ?? '未設定' }}</td>
              <td class="px-2 sm:px-4 py-2 whitespace-nowrap">{{ mo.machine_type.name }}</td>
              <td class="px-2 sm:px-4 py-2 whitespace-nowrap">{{ mo.machine_number?.number ?? '未設定' }}</td>
              <td class="px-2 sm:px-4 py-2 whitespace-nowrap">{{ mo.task.name }}</td>
              <td class="px-2 sm:px-4 py-2 whitespace-nowrap">{{ mo.start_time }}</td>
              <td class="px-2 sm:px-4 py-2 whitespace-nowrap">{{ mo.end_time }}</td>
              <td class="px-2 sm:px-4 py-2 whitespace-nowrap">{{ mo.employee.name }}</td>
              <td class="px-2 sm:px-4 py-2 whitespace-nowrap">{{ mo.total_time }}</td>
              <td class="px-2 sm:px-4 py-2">
                <div class="flex flex-col gap-1">
                  <span v-for="member in mo.members" :key="member.id" class="sm:text-xs whitespace-nowrap">
                    {{ member.name }}
                  </span>
                </div>
              </td>
              <td class="px-2 sm:px-4 py-2 flex gap-1 sm:gap-2 whitespace-nowrap">
                <button @click="completeMO(mo.id)"
                   v-if="['admin', 'superadmin'].includes(user.role) || user.id === mo.employee.id || mo.members.some(m => m.id === user.id)"
                  class="px-2 sm:px-3 py-1 bg-green-600 text-white rounded text-[11px] sm:text-sm hover:bg-green-700">
                  完了
                </button>

                <button @click="editMO(mo)"
                  v-if="['admin', 'superadmin'].includes(user.role) || user.id === mo.employee.id || mo.members.some(m => m.id === user.id)"
                  class="px-2 sm:px-3 py-1 bg-blue-600 text-white rounded text-[11px] sm:text-sm hover:bg-blue-700">
                  編集
                </button>


                <button @click="deleteMO(mo.id)"
                   v-if="['admin', 'superadmin'].includes(user.role) || user.id === mo.employee.id || mo.members.some(m => m.id === user.id)"
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

<style scoped>
.select-uniform {
  @apply mt-1 block w-full border border-gray-300 rounded-md py-2 px-3 text-xs sm:text-sm focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none bg-white;
}

.form-label {
  @apply block text-xs sm:text-sm font-medium text-gray-700;
}

/* Element Plus dropdown padding fix */
.custom-select-dropdown {
  @apply text-xs sm:text-sm;
}
</style>
