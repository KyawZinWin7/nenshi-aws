<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import AdminLayout from '../Components/AdminLayout.vue';

defineProps({
    plants: {
        type: Object,
        required: true,
    },
});

const deleteForm = useForm({});

const deletePlant = (plantId) => {
    Swal.fire({
        title: '本当にこの工場を削除してもよろしいですか？',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'はい',
        cancelButtonText: 'いいえ',
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            deleteForm.delete(route("plants.destroy", plantId));
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
                        工場一覧
                    </h1>

                    <Link :href="route('plants.create')"
                        class="inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto">
                    追加
                    </Link>
                </div>

                <div class="mt-8 overflow-x-auto rounded-lg shadow ring-1 ring-black ring-opacity-5 bg-white">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                
                                <th
                                    class="whitespace-nowrap py-3.5 pl-4 pr-3 text-left text-xs font-semibold text-gray-900 sm:pl-6">
                                    名前</th>
                               
                                <th
                                    class="relative whitespace-nowrap py-3.5 pl-3 pr-4 text-right text-xs font-semibold text-gray-900 sm:pr-6">
                                    操作</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 text-sm">
                            <tr v-for="plant in plants.data" :key="plant.id">
                                
                                <td class="whitespace-nowrap py-3 pl-4 pr-3 text-gray-900 sm:pl-6">{{ plant.name }}
                                </td>
                               
                                <td class="relative whitespace-nowrap py-3 pl-3 pr-4 text-right sm:pr-6">
                                    <Link :href="route('plants.edit', plant.id)"
                                        class="inline-block text-indigo-600 hover:text-indigo-900 text-xs sm:text-sm">
                                    編集
                                    </Link>
                                    <button @click="deletePlant(plant.id)"
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
