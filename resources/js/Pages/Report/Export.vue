<script setup>
import Container from '../../Components/Container.vue';
import PrimaryBtn from '../../Components/PrimaryBtn.vue';
import { ref, computed } from 'vue';
import axios from 'axios';
import Swal from 'sweetalert2';
import AdminLayout from '../Components/AdminLayout.vue';


const props = defineProps({
  mainoperations: { type: Object, required: true },
  machinetypes: { type: Object, required: true },
  tasks: { type: Object, required: true },
  employees: { type: Object, required: true },
  plants: { type: Object, required: true },
  departments: { type: Object, required: true },
});

const form = ref({
  machine_type_id: "",
  machine_number: "",
  task_id: "",
  employee_id: "",
  date_from: "",
  date_to: "",
  plant_id: "",
  department_id: "",
});

// Compute unique task names with their IDs
const taskNames = computed(() => {
  const uniqueTasks = [];
  const seenNames = new Set();

  props.tasks.data.forEach(task => {
    if (!seenNames.has(task.name)) {
      seenNames.add(task.name);
      uniqueTasks.push({ id: task.id, name: task.name });
    }
  });

  return uniqueTasks;
});







const exportExcel = async () => {
  const data = {
    date_from: form.value.date_from,
    date_to: form.value.date_to,
    employee_id: form.value.employee_id,
    machine_type_id: form.value.machine_type_id,
    machine_number: form.value.machine_number,
    task_id: form.value.task_id,
    plant_id: form.value.plant_id,
    department_id: form.value.department_id,
  };

  try {
    const response = await axios.post('/exportstore', data, {
      responseType: 'blob',
    });

    const url = window.URL.createObjectURL(new Blob([response.data]));
    const link = document.createElement('a');
    link.href = url;
    link.setAttribute('download', 'mainoperations.xlsx');
    document.body.appendChild(link);
    link.click();

    Swal.fire({
      icon: 'success',
      title: '成功',
      text: 'Excelが正常にダウンロードされました！',
      timer: 2000,
      showConfirmButton: false,
    });

  } catch (error) {
    // ✅ Validation Error (422)
    if (error.response && error.response.status === 422) {
      // blob data ကို text ပြန်ဖတ်
      const reader = new FileReader();
      reader.onload = () => {
        try {
          const errData = JSON.parse(reader.result);
          const errors = errData.errors;
          const firstError = Object.values(errors)[0][0];
          Swal.fire({
            icon: 'warning',
            title: '入力エラー',
            text: firstError,
          });
        } catch (e) {
          Swal.fire({
            icon: 'error',
            title: 'エラー',
            text: '不明なエラーが発生しました。',
          });
        }
      };
      reader.readAsText(error.response.data);
      return;
    }

    // ✅ No data found (404)
    if (error.response && error.response.status === 404) {
      const reader = new FileReader();
      reader.onload = () => {
        const errData = JSON.parse(reader.result);
        Swal.fire({
          icon: 'error',
          title: 'エラー',
          text: errData.message,
        });
      };
      reader.readAsText(error.response.data);
      return;
    }

    // ✅ Other errors
    Swal.fire({
      icon: 'error',
      title: 'エラー',
      text: 'Excelのダウンロードに失敗しました！',
    });
  }

};






</script>

<template>

  <AdminLayout>
    <main class="mx-auto p-4 sm:p-6 max-w-screen-lg min-h-screen bg-gray-100 flex justify-center">
      <div>
        <Container>
          <form @submit.prevent="exportExcel" class="space-y-4 sm:space-y-6 text-xs sm:text-sm">
            <!-- Date Picker -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700">開始日</label>
                <input type="date" v-model="form.date_from"
                  class="mt-1 block w-full border rounded-md py-1.5 sm:py-2 px-2 sm:px-3 text-xs sm:text-sm" />
              </div>
              <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700">終了日</label>
                <input type="date" v-model="form.date_to"
                  class="mt-1 block w-full border rounded-md py-1.5 sm:py-2 px-2 sm:px-3 text-xs sm:text-sm" />
              </div>
            </div>


            <!-- Employee &  Department -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <!-- Employee -->
              <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700">担当者</label>
                <select v-model="form.employee_id"
                  class="mt-1 block w-full border rounded-md py-1.5 sm:py-2 px-2 sm:px-3 text-xs sm:text-sm focus:ring focus:outline-none">
                  <option value="">すべて</option>
                  <option v-for="employee in employees.data" :key="employee.id" :value="employee.id">
                    {{ employee.name }}
                  </option>
                </select>
              </div>

              <!-- Department -->
              <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700">部門</label>
                <select v-model="form.department_id"
                  class="mt-1 block w-full border rounded-md py-1.5 sm:py-2 px-2 sm:px-3 text-xs sm:text-sm">
                  <option value="">すべて</option>
                  <option v-for="department in departments.data" :key="department.id" :value="department.id">{{ department.name }}</option>
                </select>
              </div>
            </div>
            <!-- Plant & Machine Type -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

              <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700">工場</label>
                <select v-model="form.plant_id"
                  class="mt-1 block w-full border rounded-md py-1.5 sm:py-2 px-2 sm:px-3 text-xs sm:text-sm">
                  <option value="">すべて</option>
                  <option v-for="plant in plants.data" :key="plant.id" :value="plant.id">{{ plant.name }}</option>
                </select>
              </div>

              <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700">機台</label>
                <select v-model="form.machine_type_id"
                  class="mt-1 block w-full border rounded-md py-1.5 sm:py-2 px-2 sm:px-3 text-xs sm:text-sm">
                  <option value="">すべて</option>
                  <option v-for="mt in machinetypes.data" :key="mt.id" :value="mt.id">{{ mt.name }}</option>
                </select>
              </div>
            </div>


            <!-- Task  & Mahcine Number  -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">

              <!-- Task -->
              <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700">作業</label>
                <select v-model="form.task_id"
                  class="mt-1 block w-full border rounded-md py-1.5 sm:py-2 px-2 sm:px-3 text-xs sm:text-sm">
                  <option value="">すべて</option>
                  <option v-for="task in taskNames" :key="task.id" :value="task.id">{{ task.name }}</option>
                </select>
              </div>

              <div>
                <label class="block text-xs sm:text-sm font-medium text-gray-700">機台の番号</label>
                <select v-model="form.machine_number"
                  class="mt-1 block w-full border rounded-md py-1.5 sm:py-2 px-2 sm:px-3 text-xs sm:text-sm">
                  <option value="">すべて</option>
                  <option v-for="num in 50" :key="num" :value="String(num)">{{ num }}</option>
                </select>
              </div>
            </div>



            <!-- Submit -->
            <PrimaryBtn type="submit" class="w-full py-2 sm:py-2.5 text-xs sm:text-sm">Excelを出力</PrimaryBtn>
          </form>
        </Container>
      </div>
    </main>
  </AdminLayout>

</template>
