<script setup>
import Container from '../../Components/Container.vue';
import PrimaryBtn from '../../Components/PrimaryBtn.vue';
import { useForm,usePage } from '@inertiajs/vue3';
import SearchForm from '../../Components/SearchForm.vue';
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
  date_from: "",
  date_to: "",
});
</script>

<template>
  <main class="mx-auto p-4 sm:p-6 max-w-screen-lg min-h-screen bg-gray-100 flex  justify-center">

      <!-- Left Form -->
      <div class="">
        <Container>
          <form  class="space-y-4 sm:space-y-6 text-xs sm:text-sm">
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
            <PrimaryBtn class="w-full py-2 sm:py-2.5 text-xs sm:text-sm">Excelを出力</PrimaryBtn>
          </form>
        </Container>
      </div>
  </main>
</template>
