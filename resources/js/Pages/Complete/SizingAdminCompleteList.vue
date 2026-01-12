<script setup>
import { Head, useForm } from '@inertiajs/vue3'
import Container from '../../Components/Container.vue'
import PrimaryBtn from '../../Components/PrimaryBtn.vue'
import { ref, onMounted, watch } from 'vue'
import axios from 'axios'
import Swal from 'sweetalert2'
import { router } from '@inertiajs/vue3'
import debounce from 'lodash/debounce'
import AdminLayout from '../Components/AdminLayout.vue';

/* ================= PROPS ================= */
const props = defineProps({
    employees: Object,
    plants: Object,
    machinetypes: Object,
    tasks: Object,
    machinenumbers: Object,
    sizingoperations: Object,
    filters: Object,
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
        sizingoperations.value = newData.map(item => ({
            ...item.data,
            show: false,
            menu: false,
        }))

        // console.log('FIXED sizingoperations =', sizingoperations.value)
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








/* =================  UNCOMPLETE LOG ================= */

const uncompleteForm = useForm({});
const uncompleteSMO = async (opId) => {
    //Confirm Dialog
    const confirmResult = await Swal.fire({
        title: "この作業を未完了してもよろしいですか？",
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "はい、未完了します。",
        cancelButtonText: "キャンセル",

    });

    if (!confirmResult.isConfirmed) return;

    //Complete sizing operation directly

    uncompleteForm.post(route('sizingoperations.uncomplete', { id: opId }), {
        onSuccess: () => {
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: '作業が未完了しました',
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
/* =================  Pagination ================= */
const goToPage = (url) => {
    if (!url) return

    router.visit(url, {
        preserveScroll: true,
        preserveState: true,
    })
}


//Radio Button For Machinetype select and Search Function and dropdown fileter

const search = ref(props.filters.search ?? '')
const selectedMachineType = ref(props.filters.machine_type_id ?? 'all')
const selectedTasks = ref(props.filters.tasks ?? [])
const showFilter = ref(false)


/* ================= WATCH ================= */
watch(
    [search, selectedMachineType, selectedTasks],
    debounce(() => {
        router.get(
            route('sizingoperations.completelist'),
            {
                search: search.value || null,
                machine_type_id:
                    selectedMachineType.value === 'all'
                        ? null
                        : selectedMachineType.value,
                tasks: selectedTasks.value.length
                    ? selectedTasks.value
                    : null,
            },
            {
                preserveState: true,
                replace: true,
            }
        )
    }, 400)
)


/* ================= CLICK OUTSIDE ================= */
onMounted(() => {
    window.addEventListener('click', () => {
        showFilter.value = false
    })
})
</script>

<template>

    <Head title="-準備課" />
    <AdminLayout>
        <main class="p-4 sm:p-6 mx-auto  min-h-screen text-xs sm:text-sm lg:text-base">

            <div class="flex flex-col items-center gap-4">




                <!-- TABLE -->
                <div class="w-full max-w-full">

                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">

                        <!--  Search -->
                        <input type="text" v-model="search" placeholder="検索（工場・日付・担当者）" class="border rounded px-3 py-2 text-sm w-full sm:w-64
                   focus:outline-none focus:ring-2 focus:ring-blue-400 placeholder-gray-400" />

                        <!--  Machine Type Radio -->
                        <div class="flex flex-wrap items-center gap-2 text-sm font-semibold">
                            <label class="flex mr-4 items-center">
                                <input type="radio" value="all" v-model="selectedMachineType" class="mr-2" />
                                すべて
                            </label>

                            <label v-for="machinetype in machinetypes.data" :key="machinetype.id"
                                class="flex mr-4 items-center">
                                <input type="radio" :value="machinetype.id" v-model="selectedMachineType"
                                    class="mr-2" />
                                {{ machinetype.name }}
                            </label>
                        </div>

                        <!--  Task Filter Dropdown -->
                        <div class="relative">
                            <button @click.stop="showFilter = !showFilter"
                                class="border px-4 py-2 rounded flex items-center gap-2 hover:bg-gray-100">
                                フィルター
                                <span v-if="selectedTasks.length"
                                    class="text-xs bg-blue-500 text-white px-2 rounded-full">
                                    {{ selectedTasks.length }}
                                </span>
                                ▼
                            </button>

                            <div v-if="showFilter"
                                class="absolute right-0 mt-2 w-56 bg-white border rounded shadow-lg z-50 p-3"
                                @click.stop>
                                <p class="text-sm font-semibold mb-2">作業を選択</p>

                                <div v-for="task in tasks.data" :key="task.name" class="flex items-center gap-2 mb-1">
                                    <input type="checkbox" :value="task.name" v-model="selectedTasks" />
                                    <span class="text-sm">{{ task.name }}</span>
                                </div>

                                <button v-if="selectedTasks.length" @click="selectedTasks = []"
                                    class="text-xs text-blue-600 hover:underline mt-2">
                                    クリア
                                </button>
                            </div>
                        </div>
                    </div>




                    <!-- TABLE CARD -->
                    <div class="bg-white rounded-lg shadow overflow-x-auto">

                        <table class="w-full border-collapse min-w-[1000px] text-xs sm:text-sm">
                            <thead>
                                <tr class="bg-gray-100 text-center text-gray-700">
                                    <th class="border p-2">日付</th>
                                    <th class="border p-2">工場</th>
                                    <th class="border p-2">機台</th>
                                    <th class="border p-2">機号</th>
                                    <th class="border p-2">作業</th>
                                    <th class="border p-2">開始</th>
                                    <th class="border p-2">終了</th>
                                    <th class="border p-2">時間停止</th>
                                    <th class="border p-2">合計時間</th>
                                    <th class="border p-2">操作</th>
                                </tr>
                            </thead>

                            <tbody>
                                <template v-for="op in sizingoperations" :key="op.id">

                                    <!-- MAIN ROW -->
                                    <tr class="text-center hover:bg-blue-50 cursor-pointer transition"
                                        @click="op.show = !op.show">
                                        <td class="border p-2 flex items-center gap-2 justify-center">
                                            <span class="text-sm">
                                                <span v-if="op.show">▼</span>
                                                <span v-else>►</span>
                                            </span>
                                            {{ op.created_at }}
                                        </td>

                                        <td class="border p-2">{{ op.plant?.name ?? '-' }}</td>
                                        <td class="border p-2">{{ op.machine_type?.name ?? '-' }}</td>
                                        <td class="border p-2">{{ op.machine_number?.number ?? '-' }}</td>

                                        <td class="border p-2">
                                            <span class="bg-blue-100 text-blue-700 px-2 py-0.5 rounded text-xs">
                                                {{ op.task?.name ?? '-' }}
                                            </span>
                                        </td>

                                        <td class="border p-2">{{ op.start_time }}</td>

                                        <td class="border p-2">
                                            <span v-if="op.end_time">{{ op.end_time }}</span>
                                            <span v-else class="text-red-500 font-medium">進行中</span>
                                        </td>

                                        <td class="border p-2">{{ op.paused_seconds_hour }}</td>
                                        <td class="border p-2 font-semibold">{{ op.worked_time }}</td>

                                        <td class="border p-2">
                                            <button @click.stop="uncompleteSMO(op.id)" v-if="op.status === 'completed'"
                                                class="bg-orange-500 text-white text-xs px-3 py-1 rounded hover:bg-orange-600">
                                                未完了
                                            </button>
                                            <!-- <div class="relative" @click.stop>
                                            <button @click="op.menu = !op.menu"
                                                class="px-2 py-1 rounded hover:bg-gray-200">
                                                ⋯
                                            </button>

                                            <div v-if="op.menu"
                                                class="absolute right-0 mt-1 bg-white border rounded shadow-md w-28 z-20 text-left">
                                                <button @click="uncompleteSMO(op.id)"
                                                    class="block w-full px-3 py-2 hover:bg-gray-100">
                                                    未完了
                                                </button>
                                              
                                                <button @click="deleteSizingOperation(op.id)" class="block w-full px-3 py-2 text-red-600 hover:bg-gray-100">
                                                    削除
                                                </button>
                                            </div>
                                        </div> -->
                                        </td>
                                    </tr>

                                    <!-- SUB TABLE -->
                                    <tr v-if="op.show" class="bg-blue-50">
                                        <td colspan="10" class="p-0 border">

                                            <table class="w-full text-center text-xs">
                                                <thead class="bg-blue-200 text-gray-700">
                                                    <tr>
                                                        <th class="border p-1">担当者</th>
                                                        <th class="border p-1">開始</th>
                                                        <th class="border p-1">終了</th>
                                                        <th class="border p-1">時間停止</th>
                                                        <th class="border p-1">合計時間</th>
                                                        <th class="border p-1">操作</th>
                                                    </tr>
                                                </thead>

                                                <tbody>
                                                    <tr v-for="log in op.sizinglogs" :key="log.id"
                                                        class="hover:bg-blue-100 transition">
                                                        <td class="border p-1 font-medium">
                                                            {{ log.employee.name ?? '-' }}
                                                        </td>

                                                        <td class="border p-1">{{ log.start_time }}</td>
                                                        <td class="border p-1">{{ log.end_time ?? '-' }}</td>
                                                        <td class="border p-1">
                                                            {{ log.paused_duration_per_employee }}
                                                        </td>
                                                        <td class="border p-1 font-semibold">
                                                            {{ log.duration_per_employee }}
                                                        </td>

                                                        <td class="border p-1">
                                                            <button v-if="!log.end_time" @click="completeLog(log)"
                                                                class="bg-green-500 text-white text-xs px-2 py-1 rounded hover:bg-green-600">
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
                        <!-- PAGINATION -->
                        <div v-if="props.sizingoperations.total > 0"
                            class="flex items-center justify-between text-sm text-gray-600 py-3">
                            <!-- 件数表示 (LEFT) -->
                            <div>
                                全 {{ props.sizingoperations.total }} 件中
                                {{ props.sizingoperations.from }} ～ {{ props.sizingoperations.to }} 件を表示
                            </div>

                            <!-- PAGINATION (RIGHT) -->
                            <div v-if="props.sizingoperations.links.length > 3" class=" px-3 flex items-center gap-1">
                                <button v-for="(link, index) in props.sizingoperations.links" :key="index"
                                    @click="link.url && goToPage(link.url)" :disabled="!link.url"
                                    class="px-3 py-1 border text-sm rounded transition" :class="{
                                        'bg-blue-500 text-white': link.active,
                                        'text-gray-400 cursor-not-allowed': !link.url,
                                        'hover:bg-blue-100': link.url && !link.active
                                    }">
                                    <span v-if="link.label.includes('Previous')">前へ</span>
                                    <span v-else-if="link.label.includes('Next')">次へ</span>
                                    <span v-else v-html="link.label"></span>
                                </button>
                            </div>
                        </div>

                        <!-- No Data -->
                        <div v-else class="text-sm text-gray-400 py-3 text-center">
                            表示するデータがありません
                        </div>


                    </div>
                </div>


            </div>
        </main>
    </AdminLayout>
</template>


<style scoped>
.select-uniform {
    @apply mt-1 block w-full border border-gray-300 rounded-md py-1 px-2 focus:ring-2 focus:ring-blue-400 focus:border-blue-400 outline-none bg-white text-gray-500 text-sm sm:text-base;
}

.form-label {
    @apply block text-xs sm:text-sm font-medium text-gray-700;
}
</style>
