<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import AdminLayout from '../Components/AdminLayout.vue';



defineProps({
    tasks: {
        type: Object,
        required: true,
    },
});


//For Delete Task
const deleteForm = useForm({});

const deleteTask = (taskId) => {
    Swal.fire({
        title: "この作業を削除してもよろしいですか？",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "はい",
        cancelButtonText: "いいえ",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            deleteForm.delete(route("tasks.destroy", taskId));
        }
    });
};


//End




</script>



<template>
    <AdminLayout>

        <Head title=" - 作業" />
        <div class="bg-gray-100 py-10 min-h-screen">
            <div class="mx-auto max-w-7xl">
                <div class="px-4 sm:px-6 lg:px-8">
                    <div class="sm:flex sm:items-center">
                        <div class="sm:flex-auto">
                            <h1 class="text-xl font-semibold text-gray-900">
                                作業リスト
                            </h1>
                            <!-- <p class="mt-2 text-sm text-gray-700">
                            A list of all .
                        </p> -->
                        </div>

                        <div class="mt-4 sm:mt-0 sm:ml-16 sm:flex-none">
                            <Link :href="route('tasks.create')"
                                class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                            追加
                            </Link>
                        </div>
                    </div>



                    <div class="mt-8 flex flex-col">
                        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
                            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                                <div
                                    class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg relative">
                                    <table class="min-w-full divide-y divide-gray-300">
                                        <thead class="bg-gray-50">
                                            <tr>

                                                 <th scope="col"
                                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                                    機台
                                                </th>
                                                
                                                <th scope="col"
                                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                                    名前
                                                </th>

                                                <th scope="col"
                                                    class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">
                                                    部門
                                                </th>

                                               


                                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6" />
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-gray-200 bg-white">
                                            <tr v-for="task in tasks.data" :key="task.id">
                                                

                                                <td
                                                    class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                                    {{ task.machine_type_id.name }}
                                                </td>
                                                <td
                                                    class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                                    {{ task.name }}
                                                </td>


                                                <td
                                                    class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                                    {{ task.department_id.name }}
                                                </td>
                                                

                                                <td
                                                    class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                                    <Link :href="route('tasks.edit', task.id)"
                                                        class="text-indigo-600 hover:text-indigo-900">
                                                    編集
                                                    </Link>
                                                    <button @click="deleteTask(task.id)"
                                                        class="ml-2 text-red-600 hover:text-red-900">
                                                        削除
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>