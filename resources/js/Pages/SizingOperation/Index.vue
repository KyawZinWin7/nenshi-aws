<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import Container from '../../Components/Container.vue'
import PrimaryBtn from '../../Components/PrimaryBtn.vue';
import { ref, onMounted, watch } from "vue";
import axios from 'axios'


const props = defineProps({
    employees: Object,
    plants: Object,
    machinetypes: Object,
    tasks: Object,
    machinenumbers: Object,
});


const form = useForm({
    employee_id: '', // main person
    team_ids: [],   // team members
    plant_id:'',
    machine_type_id:'',
    machine_number_id:'',
    task_id:'',
});

onMounted(() => {
    window.addEventListener("click", () => {
        operations.value.forEach(op => op.menu = false);
    });
});
const page = usePage();


const isEditing = ref(false);

const operations = ref([
    {
        date: "2025/12/08",
        plant: "F工場",
        machineType: "W",
        machineNo: "3",
        task: "切替",
        start: "15:44:39",
        show: false,
        sessions: [
            {
                person: "ティンザーアウン",
                start: "15:44:39",
                end: "16:20:39",
                total: "00:36:00"
            },
            {
                person: "トーハン",
                start: "16:20:39",
                end: "17:20:39",
                total: "01:00:00"
            }
        ]
    },
    {
        date: "2025/12/09",
        plant: "9工場",
        machineType: "DT302",
        machineNo: "13",
        task: "糸掛け",
        start: "12:22:10",
        show: false,
        sessions: [
            {
                person: "スー ゼヤ",
                start: "12:22:10",
                end: "13:00:00",
                total: "00:38:00"
            }
        ]
    }
]);

function submitForm() {
    console.log("Selected Employees:", selectedMainPersons.value);
}

function cancelEdit() {
    isEditing.value = false;
}

const machinenumbers = ref([]);
const tasks = ref([]);
const smalltasks = ref([]);
//Filter Task By MachineType
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
        },
         withCredentials: true
      });
      
      machinenumbers.value = numbersResponse.data;
      if (!isEditing.value) form.machine_number_id = "";

      const tasksResponse = await axios.get(`/tasks/by-machine-type`, {
        params: { machine_type_id: newTypeId },
         withCredentials: true
      });
      tasks.value = tasksResponse.data;
      if (!isEditing.value) form.task_id = "";
    } catch (error) {
      console.error("Error fetching machine numbers or tasks:", error);
    }
  }
);
// For member



// main person (login user fixed)
const selectedMainPerson = ref();
const teamMembers = ref([]);

// assign login user to form.employee_id


// Load team members (exclude main person)
function loadTeamMembers(personId) {
    const all = props.employees.data || [];
    teamMembers.value = all.filter(e => e.id !== personId);
    form.team_ids = []; // reset previous selection
}

// Initial load
loadTeamMembers();

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



</script>

<template>

    <Head title="-準備課" />
    <main class="p-4 sm:p-6 mx-auto min-h-screen text-xs sm:text-sm lg:text-base">

        <div class="flex flex-col items-center gap-4">

            <!-- LEFT FORM -->
            <div class="w-full lg:w-[30%] mb-8">
                <Container>
                    <form @submit.prevent="submitForm" class="space-y-4 sm:space-y-6 text-xs sm:text-sm">

                        <!-- Multi Employee Select -->
                        <div>
                            <label class="form-label">担当者</label>
                            <el-select v-model="form.team_ids" multiple placeholder="担当者を選択" class="select-uniform !p-0"
                                popper-class="custom-select-dropdown">
                                <el-option v-for="member in teamMembers" :key="member.id" :label="member.name"
                                    :value="member.id" />
                            </el-select>
                        </div>

                        <!-- 工場 & 機台 -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">工場</label>
                                <select v-model="form.plant_id" class="select-uniform">
                                    <option value="">工場を選択</option>
                                    <option v-for="plant in plants.data" :key="plant.id" :value="plant.id">{{ plant.name }}</option>
                                    
                                </select>
                            </div>

                            <div>
                                <label class="form-label">機台</label>
                                <select v-model="form.machine_type_id" class="select-uniform">
                                    <option value="">機台を選択</option>
                                    <option v-for="machinetype in machinetypes.data" :key="machinetype.id" :value="machinetype.id">
                                        {{ machinetype.name }}
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
                                    <option v-for="machinenumber in machinenumbers" :key="machinenumber.id" :value="machinenumber.id">
                                        {{ machinenumber.number }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="form-label">作業</label>
                                <select v-model="form.task_id" class="select-uniform">
                                    <option value="">作業を選択</option>
                                    <option v-for="task in tasks.data" :key="task.id" :value="task.id">{{ task.name }}</option>
                                </select>
                            </div>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex flex-col sm:flex-row gap-2">
                            <PrimaryBtn class="w-full sm:flex-1 py-2">
                                {{ isEditing ? '更新' : 'スタート' }}
                            </PrimaryBtn>

                            <button v-if="isEditing" type="button" @click="cancelEdit"
                                class="w-full sm:flex-1 py-2 bg-gray-400 text-white rounded hover:bg-gray-500">
                                キャンセル
                            </button>
                        </div>

                    </form>
                </Container>
            </div>

            <!-- TABLE -->
            <div class="p-2 sm:p-4 w-full overflow-x-auto">
                <table class="w-full border-collapse min-w-[700px] text-[11px] sm:text-sm lg:text-base">
                    <thead>
                        <tr class="bg-gray-200 text-center">
                            <th class="border p-2">日付</th>
                            <th class="border p-2">工場</th>
                            <th class="border p-2">機台</th>
                            <th class="border p-2">機号</th>
                            <th class="border p-2">作業</th>
                            <th class="border p-2">開始</th>
                            <th class="border p-2">操作</th>
                        </tr>
                    </thead>

                    <tbody>
                        <template v-for="(op, index) in operations" :key="index">

                            <!-- Main Row -->
                            <tr class="text-center hover:bg-gray-50 cursor-pointer" @click="op.show = !op.show">
                                <td class="border p-2 flex items-center gap-2">
                                    <span class="text-lg">
                                        <span v-if="op.show">▼</span>
                                        <span v-else>►</span>
                                    </span>
                                    {{ op.date }}
                                </td>

                                <td class="border p-2">{{ op.plant }}</td>
                                <td class="border p-2">{{ op.machineType }}</td>
                                <td class="border p-2">{{ op.machineNo }}</td>
                                <td class="border p-2">{{ op.task }}</td>
                                <td class="border p-2">{{ op.start }}</td>

                                <td class="border p-2">
                                    <div class="relative" @click.stop>
                                        <button @click="op.menu = !op.menu" class="px-2 py-1 hover:bg-gray-200 rounded">
                                            ⋯
                                        </button>

                                        <!-- dropdown -->
                                        <div v-if="op.menu"
                                            class="absolute right-0 mt-1 bg-white border rounded shadow-md w-28 text-left z-20">
                                            <button
                                                class="block w-full text-left px-3 py-2 hover:bg-gray-100">完了</button>
                                            <button
                                                class="block w-full text-left px-3 py-2 hover:bg-gray-100">編集</button>
                                            <button
                                                class="block w-full text-left px-3 py-2 hover:bg-gray-100">追加</button>
                                            <button
                                                class="block w-full text-left px-3 py-2 text-pink-600 hover:bg-gray-100">止</button>
                                            <button
                                                class="block w-full text-left px-3 py-2 text-red-600 hover:bg-gray-100">削除</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <!-- Sub Table -->
                            <tr v-if="op.show" class="bg-blue-50">
                                <td colspan="7" class="p-0 border">
                                    <table class="w-full border-collapse text-center text-[10px] sm:text-sm">
                                        <thead>
                                            <tr class="bg-blue-200">
                                                <th class="border p-1">担当者</th>
                                                <th class="border p-1">開始</th>
                                                <th class="border p-1">終了</th>
                                                <th class="border p-1">合計時間</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr v-for="(s, i) in op.sessions" :key="i" class="hover:bg-gray-100">
                                                <td class="border p-1">{{ s.person }}</td>
                                                <td class="border p-1">{{ s.start }}</td>
                                                <td class="border p-1">{{ s.end }}</td>
                                                <td class="border p-1">{{ s.total }}</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                        </template>
                    </tbody>
                </table>
            </div>


        </div>
    </main>

</template>

<style scoped>
.select-uniform {
    @apply mt-1 block w-full border border-gray-300 rounded-md py-1 px-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none bg-white text-gray-500 text-sm sm:text-base;
}

.form-label {
    @apply block text-xs sm:text-sm font-medium text-gray-700;
}
</style>
