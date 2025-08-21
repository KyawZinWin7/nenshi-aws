
<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';


defineProps({
    machinetypes:{
        type: Object,
        required: true,
    },
});

const deleteForm = useForm({});



//For Delte MachineType
const deleteMachineType = (machinetypeId) => {
  Swal.fire({
    title: "この機台を削除してもよろしいですか？",
    icon: "warning",
    showCancelButton: true,
    confirmButtonText: "はい",
    cancelButtonText: "いいえ",
    reverseButtons: true,
  }).then((result) => {
    if (result.isConfirmed) {
      deleteForm.delete(route("machinetypes.destroy", machinetypeId));
    }
  });
};


//End


</script>



<template>
  <Head title=" - 機台" />
  <div class="bg-gray-100 py-10 min-h-screen">
    <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8"> <!-- ここにpx追加で横余白確保 -->

      <div class="sm:flex sm:items-center sm:justify-between">
        <h1 class="text-xl font-semibold text-gray-900">
          機台リスト
        </h1>
        <Link
          :href="route('machinetypes.create')"
          class="mt-4 sm:mt-0 inline-flex items-center justify-center rounded-md border border-transparent bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow-sm hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 sm:w-auto"
        >
          追加
        </Link>
      </div>

      <div class="mt-8 flex flex-col">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
          <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">

            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 md:rounded-lg relative">
              <table class="min-w-full divide-y divide-gray-300 table-auto md:table-fixed">
                <thead class="bg-gray-50">
                  <tr>
                    <th
                      scope="col"
                      class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6"
                    >
                      コード
                    </th>
                    <th
                      scope="col"
                      class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6"
                    >
                      名前
                    </th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                      操作
                    </th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                  <tr v-for="machinetype in machinetypes.data" :key="machinetype.id">
                    <td
                      class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6"
                    >
                      {{ machinetype.id }}
                    </td>
                    <td
                      class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6"
                    >
                      {{ machinetype.name }}
                    </td>
                    <td
                      class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6 space-x-2"
                    >
                      <Link
                        :href="route('machinetypes.edit', machinetype.id)"
                        class="text-indigo-600 hover:text-indigo-900"
                      >
                        編集
                      </Link>
                      <button
                        @click="deleteMachineType(machinetype.id)"
                        class="text-red-600 hover:text-red-900"
                      >
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
</template>
