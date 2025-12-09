<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import AdminLayout from '../Components/AdminLayout.vue';


const props = defineProps({
  machinetypes: Object,
  
})

const form = useForm({
  name: "",
  machine_type_id: "",
})


//For Create SmallTask
const createSmallTask = () => {
  form.post(route("smalltasks.store"), {
    onSuccess: () => {
      form.reset();
      Swal.fire({
        position: "top-end",
        icon: "success",
        title: "小作業が正常に作成されました。",
        showConfirmButton: false,
        timer: 1500,
      });
    },
    onError: () => {
      Swal.fire({
        icon: "error",
        title: "エラーが発生しました",
        text: "もう一度お試しください。",
      });
    },
  });
};

//End
</script>




<template>

  <AdminLayout>

    <Head title=" - 作業" />
    <main class="mx-auto p-6 max-w-screen-sm min-h-screen">
      <div class="max-w-full px-6 py-6 bg-gray-100">
        <form class="w-full" @submit.prevent="createSmallTask">
          <div class="shadow sm:rounded-md sm:overflow-hidden bg-white">
            <div class="py-6 px-4 space-y-6 sm:p-6">
              <div>
                <h3 class="text-lg leading-6 font-medium text-gray-900">小作業</h3>
              </div>
              <!--Start Name-->
              <div class="grid grid-cols-6 gap-6">
                <div class="col-span-6 sm:col-span-6">
                  <label for="name" class="block text-sm font-medium text-gray-700">名前</label>
                  <input v-model="form.name" type="text" id="name"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" />
                  <div v-if="form.errors.name" class="text-red-500 mt-1">
                    {{ form.errors.name }}
                  </div>
                </div>
              </div>
              <!--End Name-->

              <!--Start Task-->
              <div class="grid grid-cols-6 gap-6">
                <div class="col-span-6 sm:col-span-6">
                  <label class="block text-sm font-medium text-gray-700">機台</label>
                  <select v-model="form.machine_type_id"
                    class="mt-1 block w-full border border-gray-300 rounded-md shadow-sm py-2 px-3 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                    <option value="">機台を選択</option>
                    <option v-for="machinetype in machinetypes.data" :key="machinetype.id" :value="machinetype.id">
                      {{ machinetype.name }}
                    </option>
                  </select>
                  <div v-if="form.errors.machine_type_id" class="text-red-500 text-sm mt-1">{{ form.errors.machine_type_id
                  }}</div>
                </div>
              </div>

              <!--End Task-->


             


            </div>
            <div class="px-4 py-3 bg-gray-50 text-right sm:px-6">
              <Link :href="route('tasks.index')"
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-indigo-700 bg-indigo-100 hover:bg-indigo-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 mr-4">
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
