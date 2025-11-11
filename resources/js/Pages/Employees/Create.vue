<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import AdminLayout from '../Components/AdminLayout.vue';


const props = defineProps({
  errors: Object,
  auth: Object,
  departments: Object,
})

const form = useForm({
  name: "",
  employee_code: "",
  department_id: "",
  password: "",
  role: 'user',
})

const createEmployee = () => {
  form.post(route("employees.store"), {
    onSuccess: () => {
      Swal.fire({
        position: "top-end",
        icon: "success",
        title: "登録が成功しました！",
        showConfirmButton: false,
        timer: 1500
      });
    },
    onError: () => {
      Swal.fire({
        icon: "error",
        title: "エラーが発生しました",
        text: "もう一度お試しください。",
      });
    }
  });
};
</script>

<template>

  <AdminLayout>

    <Head title="Create MT" />
    <main class="mx-auto p-6 max-w-screen-sm min-h-screen">
      <div class="w-full px-4 py-6 bg-gray-100 rounded-md">
        <form class="w-full" @submit.prevent="createEmployee">
          <div class="shadow sm:rounded-md sm:overflow-hidden bg-white">
            <div class="py-6 px-4 space-y-6 sm:p-6">
              <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">従業員</h3>
              </div>

              <div class="">
                <div class="col-span-6">
                  <label for="name" class="block text-sm font-medium text-gray-700">名前</label>
                  <input v-model="form.name" type="text" id="name"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                  <div v-if="form.errors.name" class="text-red-500 mt-1">
                    {{ form.errors.name }}
                  </div>
                </div>

                <!--社員コード & 部門 -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                  <!-- 社員コード -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700">社員コード</label>
                    <input v-model="form.employee_code" type="text"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                    <div v-if="form.errors.employee_code" class="text-red-500 text-sm mt-1">{{ form.errors.employee_code
                      }}</div>
                  </div>


                  <!-- 部門 -->
                  <div>
                    <label class="block text-sm font-medium text-gray-700">部門</label>
                    <select v-model="form.department_id"
                      class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                      <option value="">部門を選択</option>
                      <option v-for="department in departments.data" :key="department.id" :value="department.id">
                        {{ department.name }}
                      </option>
                    </select>
                    <div v-if="form.errors.department_id" class="text-red-500 text-sm mt-1">{{ form.errors.department_id
                      }}</div>
                  </div>
                </div>


                <div class="col-span-6">
                  <label for="password" class="block text-sm font-medium text-gray-700">パスワード</label>
                  <input v-model="form.password" type="password" id="password"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                  <div v-if="form.errors.password" class="text-red-500 mt-1">
                    {{ form.errors.password }}
                  </div>
                </div>


                <div class="col-span-6">
                  <label for="role" class="block text-sm font-medium text-gray-700">役割</label>
                  <select v-model="form.role" id="role" class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3
                    focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm bg-white">
                    <option value="user">ユーザー</option>
                    <option value="admin">管理者</option>
                  </select>

                  <div v-if="form.errors.role" class="text-red-500 mt-1">
                    {{ form.errors.role }}
                  </div>
                </div>


              </div>
            </div>

            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6 flex flex-col sm:flex-row sm:justify-end gap-2">
              <Link :href="route('employees.index')"
                class="inline-flex items-center justify-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
              キャンセル
              </Link>
              <button type="submit"
                class="bg-indigo-600 border border-transparent rounded-md shadow-sm py-2 px-4 inline-flex justify-center text-sm font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                保存
              </button>
            </div>
          </div>
        </form>
      </div>
    </main>
  </AdminLayout>
</template>
