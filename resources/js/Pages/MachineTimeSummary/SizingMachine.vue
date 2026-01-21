<script setup>
import { router, Head } from '@inertiajs/vue3'
import { ref, watch } from 'vue'
import AdminLayout from '../Components/AdminLayout.vue'

const props = defineProps({
    machinenumbers: {
        type: Array,
        required: true,
    },
    month: {
        type: String,
        default: null,
    },
})



const now = new Date()

const year = ref(now.getFullYear())
const month = ref(String(now.getMonth() + 1).padStart(2, '0'))

const years = Array.from({ length: 6 }, (_, i) => now.getFullYear() - 3 + i)

const search = () => {
    router.get(
        route('sizing.machines.operation-hours'),
        { month: `${year.value}-${month.value}` },
        { preserveState: true }
    )
}

/**
 * format seconds → H:mm:ss
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
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">

                <!-- Header -->
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-6">
                    <h1 class="text-xl font-semibold text-gray-900 mb-4 sm:mb-0">
                        機械別 作業時間一覧
                    </h1>

                    <div class="flex items-center gap-3">
                        <!-- Year -->
                        <select v-model="year" class="border rounded px-3 py-2 text-sm">
                            <option v-for="y in years" :key="y" :value="y">
                                {{ y }}年
                            </option>
                        </select>

                        <!-- Month -->
                        <select v-model="month" class="border rounded px-3 py-2 text-sm">
                            <option v-for="m in 12" :key="m" :value="String(m).padStart(2, '0')">
                                {{ m }}月
                            </option>
                        </select>

                        <button @click="search"
                            class="bg-blue-600 text-white px-4 py-2 rounded text-sm hover:bg-blue-700">
                            検索
                        </button>
                    </div>

                </div>

                <!-- Table -->
                <div class="overflow-x-auto bg-white shadow rounded">
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
                            <tr v-for="m in machinenumbers" :key="m.id" class="hover:bg-gray-50">
                                <td class="px-4 py-2 text-sm">{{ m.plant_name }}</td>
                                <td class="px-4 py-2 text-sm">{{ m.machine_type_name }}</td>
                                <td class="px-4 py-2 text-sm">{{ m.number }}</td>
                                <td class="px-4 py-2 text-sm">{{ formatTime(m.running_seconds) }}</td>
                                <td class="px-4 py-2 text-sm">{{ formatTime(m.setup_seconds) }}</td>
                                <td class="px-4 py-2 text-sm">{{ formatTime(m.repair_seconds) }}</td>
                            </tr>

                            <tr v-if="machinenumbers.length === 0">
                                <td colspan="7" class="px-4 py-6 text-center text-gray-500">
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


<style scoped>
.th {
    @apply py-3.5 px-4 text-left text-sm font-semibold text-gray-900;
}

.td {
    @apply whitespace-nowrap py-4 px-4 text-sm text-gray-900;
}
</style>
