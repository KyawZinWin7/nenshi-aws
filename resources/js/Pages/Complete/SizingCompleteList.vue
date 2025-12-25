<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Container from '../../Components/Container.vue'
import PrimaryBtn from '../../Components/PrimaryBtn.vue'
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'

/* ================= PROPS ================= */
const props = defineProps({
    employees: Object,
    plants: Object,
    machinetypes: Object,
    tasks: Object,
    machinenumbers: Object,
    sizingoperations: Object,
})

/* ================= FORM ================= */
const form = useForm({
    team_ids: [],
    plant_id: '',
    machine_type_id: '',
    machine_number_id: '',
    task_id: '',
})

/* ================= UI STATE ================= */
const isEditing = ref(false)

/* ================= LOCAL COPY (IMPORTANT) ================= */
/* ❌ props ကို မထိ
   ✅ local ref ထုတ်ပြီး show/menu ထည့် */
const sizingoperations = ref([])

watch(
    () => props.sizingoperations.data,
    (newData) => {
        sizingoperations.value = newData.map(op => ({
            ...op,
            show: false,
            menu: false,
        }))
    },
    { immediate: true }
)

/* ================= GLOBAL CLICK (MENU CLOSE) ================= */
onMounted(() => {
    window.addEventListener('click', () => {
        sizingoperations.value.forEach(op => {
            op.menu = false
        })
    })
})







/* ================= COMPLETE LOG ================= */

const completeForm = useForm({});
const completeSMO = async (opId) => {
    //Confirm Dialog
    const confirmResult = await Swal.fire({
        title: "この作業を完了してもよろしいですか？",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "はい、完了します。",
        cancelButtonText: "キャンセル",
        
    });

    if(!confirmResult.isConfirmed) return;

    //Complete sizing operation directly

    completeForm.post(route('sizingoperations.complete', { id: opId }), {
        onSuccess: () => {
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: '作業が完了しました',
                showConfirmButton: false,
                timer: 1500,
            })
        },
        onError: () => {
            Swal.fire({
                icon: 'error',
                title: 'エラーが発生しました',
                text: 'もう一度試してください。',
            })
        },
    })

}
</script>

<template>

    <Head title="-準備課" />

    <main class="p-4 sm:p-6 mx-auto min-h-screen text-xs sm:text-sm lg:text-base">

        <div class="flex flex-col items-center gap-4">

           

            <!-- TABLE -->
            <div class="p-2 sm:p-4 w-full overflow-x-auto">
                <table class="w-full border-collapse min-w-[700px]">
                    <thead>
                        <tr class="bg-gray-200 text-center">
                            <th class="border p-2">日付</th>
                            <th class="border p-2">工場</th>
                            <th class="border p-2">機台</th>
                            <th class="border p-2">機号</th>
                            <th class="border p-2">作業</th>
                            <th class="border p-2">操作</th>
                        </tr>
                    </thead>

                    <tbody>
                        <template v-for="op in sizingoperations" :key="op.id">

                            <!-- MAIN ROW -->
                            <tr class="text-center hover:bg-gray-50 cursor-pointer" @click="op.show = !op.show">
                                <td class="border p-2 flex items-center gap-2">
                                    <span class="text-lg">
                                        <span v-if="op.show">▼</span>
                                        <span v-else>►</span>
                                    </span>
                                    {{ op.created_at }}
                                </td>

                                <td class="border p-2">{{ op.plant.name }}</td>
                                <td class="border p-2">{{ op.machine_type.name }}</td>
                                <td class="border p-2">{{ op.machine_number.name }}</td>
                                <td class="border p-2">{{ op.task.name }}</td>
                                {{ op.sizingLogs }}

                                <td class="border p-2">
                                    <div class="relative" @click.stop>
                                        <button @click="op.menu = !op.menu" class="px-2 py-1">
                                            ⋯
                                        </button>

                                        <div v-if="op.menu"
                                            class="absolute right-0 mt-1 bg-white border rounded shadow-md w-28 z-20">
                                            <button @click="completeSMO(op.id)"
                                                class="block w-full px-3 py-2 hover:bg-gray-100">完了</button>
                                            <button class="block w-full px-3 py-2 hover:bg-gray-100">編集</button>
                                            <button class="block w-full px-3 py-2 hover:bg-gray-100">追加</button>
                                            <button
                                                class="block w-full px-3 py-2 text-pink-600 hover:bg-gray-100">止</button>
                                            <button
                                                class="block w-full px-3 py-2 text-red-600 hover:bg-gray-100">削除</button>
                                        </div>
                                    </div>
                                </td>
                            </tr>

                            <!-- SUB TABLE -->
                            <tr v-if="op.show" class="bg-blue-50">
                                <td colspan="6" class="p-0 border">
                                    <table class="w-full text-center">
                                        <thead class="bg-blue-200">
                                            <tr>
                                                <th class="border text-sm p-1">担当者</th>
                                                <th class="border text-sm p-1">開始</th>
                                                <th class="border text-sm p-1">終了</th>
                                                <th class="border text-sm p-1">合計時間</th>
                                                <th class="border text-sm p-1">操作</th>
                                            </tr>
                                        </thead>

                                        <tbody>
                                            <tr v-for="log in op.sizinglogs" :key="log.id">
                                                <td class="border text-sm p-1">{{ log.employee.name ?? '-' }}</td>
                                                <td class="border text-sm p-1">{{ log.start_time }}</td>
                                                <td class="border text-sm p-1">{{ log.end_time ?? '-' }}</td>
                                                <td class="border text-sm p-1">{{ log.duration }}</td>

                                                <td class="border text-sm p-1">
                                                    <button
                                                        class="bg-green-500 text-white text-xs px-3 py-1 rounded hover:bg-green-600"
                                                        @click="completeLog(log)" v-if="!log.end_time">
                                                        完了
                                                    </button>
                                                </td>
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
