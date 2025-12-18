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

/* ================= CREATE ================= */
const createSizingOperation = () => {
    form.post(route('sizingoperations.store'), {
        onSuccess: () => {
            Swal.fire({
                position: 'top-end',
                icon: 'success',
                title: '準備課スタートしました',
                showConfirmButton: false,
                timer: 1500,
            })
            form.reset()
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

/* ================= MACHINE FILTER ================= */
const machinenumbers = ref([])
const machinetypes = ref([])
const tasks = ref([])

watch(
    () => form.machine_type_id,
    async (newTypeId) => {
        if (!newTypeId) {
            machinenumbers.value = []
            tasks.value = []
            form.machine_number_id = ''
            form.task_id = ''
            return
        }

        const numbersResponse = await axios.get('/machines/by-type', {
            params: {
                plant_id: form.plant_id,
                machine_type_id: newTypeId,
            },
        })
        machinenumbers.value = numbersResponse.data

        const tasksResponse = await axios.get('/tasks/by-machine-type', {
            params: { machine_type_id: newTypeId },
        })
        tasks.value = tasksResponse.data
    }
)

watch(
    () => form.plant_id,
    async (newPlantId) => {
        if (!newPlantId) {
            machinetypes.value = []
            machinenumbers.value = []
            form.machine_type_id = ''
            form.machine_number_id = ''
            return
        }

        const { data } = await axios.get(`/machines/by-plant/${newPlantId}`)
        machinetypes.value = data.machineTypes
        machinenumbers.value = data.machineNumbers
    }
)

/* ================= TEAM ================= */
const teamMembers = ref(props.employees.data || [])
</script>
<template>

    <Head title="-準備課" />

    <main class="p-4 sm:p-6 mx-auto min-h-screen text-xs sm:text-sm lg:text-base">

        <div class="flex flex-col items-center gap-4">

            <!-- LEFT FORM -->
            <div class="w-full lg:w-[30%] mb-8">
                <Container>
                    <form @submit.prevent="createSizingOperation" class="space-y-4">

                        <div>
                            <label class="form-label">担当者</label>
                            <el-select v-model="form.team_ids" multiple placeholder="担当者を選択" class="select-uniform !p-0">
                                <el-option v-for="member in teamMembers" :key="member.id" :label="member.name"
                                    :value="member.id" />
                            </el-select>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">工場</label>
                                <select v-model="form.plant_id" class="select-uniform">
                                    <option value="">工場を選択</option>
                                    <option v-for="p in plants.data" :key="p.id" :value="p.id">
                                        {{ p.name }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="form-label">機台</label>
                                <select v-model="form.machine_type_id" class="select-uniform">
                                    <option value="">機台を選択</option>
                                    <option v-for="m in machinetypes" :key="m.id" :value="m.id">
                                        {{ m.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="form-label">機号</label>
                                <select v-model="form.machine_number_id" class="select-uniform">
                                    <option value="">機号</option>
                                    <option v-for="n in machinenumbers" :key="n.id" :value="n.id">
                                        {{ n.number }}
                                    </option>
                                </select>
                            </div>

                            <div>
                                <label class="form-label">作業</label>
                                <select v-model="form.task_id" class="select-uniform">
                                    <option value="">作業を選択</option>
                                    <option v-for="t in tasks" :key="t.id" :value="t.id">
                                        {{ t.name }}
                                    </option>
                                </select>
                            </div>
                        </div>

                        <PrimaryBtn class="w-full">
                            スタート
                        </PrimaryBtn>

                    </form>
                </Container>
            </div>

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
                                            <button class="block w-full px-3 py-2 hover:bg-gray-100">完了</button>
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
                                            </tr>
                                        </thead>

                                        <tbody>
                                           
                                            <tr v-for="log in op.sizinglogs" :key="log.id">
                                                <td class="border text-sm p-1">{{ log.employee.name?? '-' }}</td>
                                                <td class="border text-sm p-1">{{ log.start_time }}</td>
                                                <td class="border text-sm p-1">{{ log.end_time ?? '-' }}</td>
                                                <td class="border text-sm p-1">{{ log.duration }}</td>
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
