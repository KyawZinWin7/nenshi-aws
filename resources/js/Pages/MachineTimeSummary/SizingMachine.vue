<script setup>
import { router, Head } from '@inertiajs/vue3'
import { ref } from 'vue'
import AdminLayout from '../Components/AdminLayout.vue'

const props = defineProps({
    machinenumbers: {
        type: Array,
        required: true,
    },
    month: {
        type: String,
        required: true,
    },
    plants: {
        type: Array,
        required: true,
    },
    selectedPlant: {
        type: String,
      
    },
})

/**
 *  month (already searched)
 */
const [initYear, initMonth] = props.month.split('-')
const year = ref(initYear)
const monthVal = ref(initMonth)

/**
 *  plant filter (backend)
 */
const selectedPlant = ref(props.selectedPlant ?? '')

/**
 *  year list
 */
const now = new Date()
const years = Array.from({ length: 6 }, (_, i) => now.getFullYear() - 3 + i)

/**
 *  search (date + plant)
 */
// const search = () => {
//     router.get(
//         route('sizing.machines.operation-hours'),
//         {
//             month: `${year.value}-${monthVal.value}`,
//             plant_id: selectedPlant.value,
//         },
//         {
//             preserveState: true,
//             preserveScroll: true,
//         }
//     )
// }


/**
 *  date search (manual)
 */
const searchByDate = () => {
    router.get(
        route('sizing.machines.operation-hours'),
        {
            month: `${year.value}-${monthVal.value}`,
            plant_id: selectedPlant.value, // keep current plant
        },
        {
            preserveState: true,
            preserveScroll: true,
        }
    )
}

/**
 *  plant filter (auto)
 */
const searchByPlant = () => {
    router.get(
        route('sizing.machines.operation-hours'),
        {
            month: `${year.value}-${monthVal.value}`, // keep date
            plant_id: selectedPlant.value,
        },
        {
            preserveState: true,
            preserveScroll: true,
            replace: true, // history မပေါင်း
        }
    )
}

/**
 * ⏱ seconds → H:mm:ss
 */
const formatTime = (seconds) => {
    seconds = Number(seconds) || 0
    const h = Math.floor(seconds / 3600)
    const m = Math.floor((seconds % 3600) / 60)
    const s = seconds % 60
    return `${h}:${String(m).padStart(2, '0')}:${String(s).padStart(2, '0')}`
}
</script>

<template>
    <AdminLayout>

        <Head title="機械別 作業時間一覧" />

        <div class="bg-gray-100 py-8 min-h-screen">
            <div class="mx-auto max-w-7xl px-4">

                <h1 class="text-xl font-semibold mb-6">
                    機械別 作業時間一覧
                </h1>

                <!--  Filters -->
                <div class="bg-white p-4 rounded shadow mb-6 space-y-4">

                    <!-- Date -->
                    <div class="flex gap-3">
                        <select v-model="year" class="border rounded px-3 py-2">
                            <option v-for="y in years" :key="y" :value="y">
                                {{ y }}年
                            </option>
                        </select>

                        <select v-model="monthVal" class="border rounded px-3 py-2">
                            <option v-for="m in 12" :key="m" :value="String(m).padStart(2, '0')">
                                {{ m }}月
                            </option>
                        </select>
                          <button @click="searchByDate" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        検索
                    </button>
                    </div>
                   
                    <!-- Plant radio -->
                    <div>
                        <p class="font-semibold mb-2">工場</p>
                        <div class="flex gap-4 flex-wrap">
                            <label class="flex items-center gap-1">
                                <input type="radio" v-model="selectedPlant" value="" @change="searchByPlant">
                                全部
                            </label>

                            <label v-for="p in plants" :key="p.id" class="flex items-center gap-1">
                                <input type="radio" v-model="selectedPlant" :value="String(p.id)"
                                    @change="searchByPlant">
                                {{ p.name }}
                            </label>


                        </div>
                    </div>

                   

                </div>

                <!-- 📊 Table -->
                <div class="bg-white shadow rounded overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-sm font-semibold">工場</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">機台</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">機械番号</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">運転時間</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">準備時間</th>
                                <th class="px-4 py-3 text-left text-sm font-semibold">修理時間</th>
                            </tr>
                        </thead>

                        <tbody class="divide-y divide-gray-200">
                            <tr v-for="m in machinenumbers" :key="`${m.plant_name}-${m.machine_type_name}-${m.number}`"
                                class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm">{{ m.plant_name }}</td>
                                <td class="px-4 py-2 text-sm">{{ m.machine_type_name }}</td>
                                <td class="px-4 py-2 text-sm">{{ m.number }}</td>
                                <td class="px-4 py-2 text-sm">{{ formatTime(m.running_seconds) }}</td>
                                <td class="px-4 py-2 text-sm">{{ formatTime(m.setup_seconds) }}</td>
                                <td class="px-4 py-2 text-sm">{{ formatTime(m.repair_seconds) }}</td>
                            </tr>

                            <tr v-if="machinenumbers.length === 0">
                                <td colspan="6" class="px-4 py-6 text-center text-gray-500">
                                    データがありません
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </AdminLayout>
</template>
