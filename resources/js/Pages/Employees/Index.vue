<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import AdminLayout from '../Components/AdminLayout.vue';


defineProps({
  employees: {
    type: Object,
    required: true,
  },
});

const deleteForm = useForm({});

const deleteEmployee = (employeeId) => {
  Swal.fire({
    title: '本当にこの従業員を削除してもよろしいですか？',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'はい',
    cancelButtonText: 'いいえ',
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      deleteForm.delete(route("employees.destroy", employeeId));
    }
  });
};
</script>

<template>
  <AdminLayout>

    <Head title="-従業員 " />

    <div class="bg-gray-100 py-10 min-h-screen">
      <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
        <div class="sm:flex sm:items-center sm:justify-between">
          <h1 class="text-xl font-semibold text-gray-900 mb-4 sm:mb-0">
            従業員リスト
          </h1>

          <Link :href="route('employees.create')"
            class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
          追加
          </Link>
        </div>

        <div class="mt-8 overflow-x-auto rounded-lg shadow ring-1 ring-black ring-opacity-5 bg-white">
          <table class="min-w-full divide-y divide-gray-300">
            <thead class="bg-gray-50">
              <tr>
                
                <th class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-gray-900 sm:pl-6">名前
                </th>
                <th class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-gray-900 sm:pl-6">
                  社員コード</th>
                <th class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-gray-900 sm:pl-6">部門</th>
                <th class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-gray-900 sm:pl-6">役割</th>
                <th
                  class="relative whitespace-nowrap py-3.5 pl-3 pr-4 text-right text-xs font-semibold text-gray-900 sm:pr-6">
                  操作</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-200 text-sm">
              <tr v-for="employee in employees.data" :key="employee.id">
                
                <td class="whitespace-nowrap py-3 pl-4 pr-3 text-gray-900 sm:pl-6">{{ employee.name }}</td>
                <td class="whitespace-nowrap py-3 pl-4 pr-3 text-gray-900 sm:pl-6">{{ employee.employee_code }}</td>
                <td class="whitespace-nowrap py-3 pl-4 pr-3 text-gray-900 sm:pl-6">{{ employee.department_id.name }}</td>

                <td class="whitespace-nowrap py-3 pl-4 pr-3 text-gray-900 sm:pl-6">
                  {{ employee.role === 'admin' ? '管理者' : '従業員' }}
                </td>
                <td class="relative whitespace-nowrap py-3 pl-3 pr-4 text-right sm:pr-6">
                  <Link :href="route('employees.edit', employee.id)"
                    class="inline-block text-indigo-600 hover:text-indigo-900 text-xs sm:text-sm">
                  編集
                </Link>
                  <button @click="deleteEmployee(employee.id)"
                    class="inline-block ml-2 text-red-600 hover:text-red-800 text-xs sm:text-sm">
                    削除
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
