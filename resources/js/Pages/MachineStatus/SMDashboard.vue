<script setup>
import { ref, watch, onMounted } from 'vue'
import { Head, usePage, router } from '@inertiajs/vue3'
import debounce from 'lodash/debounce'
import AdminLayout from '../Components/AdminLayout.vue'

// =====================
// Props
// =====================
const props = defineProps({
  machinenumbers: Object,
  plants: Object,
  filters: Object,
})

// =====================
// Page props
// =====================
const page = usePage()
const plantList = page.props.plants.data ?? []

// =====================
// Selected plant
// priority: URL filter → first plant
// =====================
const selectedPlant = ref(null)

// =====================
// Initial sync (IMPORTANT )
// =====================
onMounted(() => {
  if (props.filters?.plant_id) {
    selectedPlant.value = Number(props.filters.plant_id)
  } else if (plantList.length > 0) {
    selectedPlant.value = plantList[0].id

    // URL & backend sync
    router.get(
      route('machines.status'),
      { plant_id: selectedPlant.value },
      {
        replace: true,
        preserveState: true,
      }
    )
  }
})

// =====================
// Backend filter (on change)
// =====================
watch(
  selectedPlant,
  debounce((value) => {
    if (!value) return

    router.get(
      route('machines.status'),
      { plant_id: value },
      {
        preserveState: true,
        preserveScroll: true,
        replace: true,
      }
    )
  }, 300)
)


const isRefreshing = ref(false)

const refresh = () => {
  if (!selectedPlant.value) return

  isRefreshing.value = true

  router.get(
    route('machines.status'),
    { plant_id: selectedPlant.value },
    {
      preserveState: true,
      preserveScroll: true,
      replace: true,
      onFinish: () => {
        isRefreshing.value = false
      },
    }
  )
}


// =====================
// Status color
// =====================
const statusClass = (status) => {
  switch (status) {
    case 'running':
      return 'bg-green-500'
    case 'prepare':
      return 'bg-yellow-400 text-black'
    case 'repair':
      return 'bg-blue-500'
    default:
      return 'bg-red-500'
  }
}
</script>

<template>
  <AdminLayout>

    <Head title="機械状況" />

    <div class="p-4 sm:p-6 space-y-8">

      <!-- =====================
           Plant Filter (Radio + Pill UI)
           ===================== -->
      <div class="flex items-center justify-between gap-3 flex-wrap">

        <!-- Left: Plant radios -->
        <div class="flex gap-2 flex-wrap">
          <label v-for="plant in plantList" :key="plant.id" class="cursor-pointer">
            <input type="radio" :value="plant.id" v-model="selectedPlant" class="hidden" />
            <span class="px-4 py-1.5 rounded-full text-sm font-medium transition" :class="selectedPlant === plant.id
              ? 'bg-green-600 text-white shadow'
              : 'bg-gray-100 text-gray-700 hover:bg-gray-200'">
              {{ plant.name }}
            </span>
          </label>
        </div>

        <!-- Right: Refresh button -->
        <button @click="refresh" :disabled="isRefreshing" class="px-4 py-1.5 rounded-full text-sm font-medium
         flex items-center gap-2
         bg-blue-600 text-white shadow
         hover:bg-blue-700 transition
         disabled:opacity-60">
          <svg v-if="isRefreshing" class="animate-spin h-4 w-4" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none" />
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z" />
          </svg>

          <span>
            {{ isRefreshing ? '更新中…' : '更新' }}
          </span>
        </button>


      </div>


      <!-- =====================
           Machine Dashboard
           ===================== -->
      <div v-for="(plant, plantName) in machinenumbers" :key="plantName" class="space-y-6">
        <h2 class="text-lg sm:text-xl font-bold flex items-center gap-2">
          <span class="w-3 h-3 bg-green-600 rounded-full"></span>
          {{ plantName }}
        </h2>

        <div v-for="(list, type) in plant" :key="type">
          <h3 class="font-semibold mb-3 text-sm sm:text-base">
            {{ type }}
          </h3>

          <div class="grid gap-3
                   grid-cols-2
                   sm:grid-cols-4
                   md:grid-cols-6
                   lg:grid-cols-10">
            <div v-for="machine in list" :key="machine.machine_id" class="rounded-lg h-20 sm:h-24
                     flex flex-col items-center justify-center
                     text-white shadow" :class="statusClass(machine.status)">
              <div class="text-xs sm:text-sm font-semibold">
                {{ machine.type }}
              </div>
              <div class="text-base sm:text-lg font-bold">
                {{ machine.number }}
              </div>
              <div class="text-xs">
                {{
                  machine.status === 'running'
                    ? '運転中'
                    : machine.status === 'prepare'
                      ? '準備中'
                      : machine.status === 'repair'
                        ? '修理中'
                        : '停止'
                }}
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- =====================
           Legend
           ===================== -->
      <div class="flex flex-wrap gap-4 pt-6 border-t text-sm">
        <div class="flex items-center gap-2">
          <span class="w-4 h-4 bg-green-500 rounded-full"></span> 運転
        </div>
        <div class="flex items-center gap-2">
          <span class="w-4 h-4 bg-red-500 rounded-full"></span> 停止
        </div>
        <div class="flex items-center gap-2">
          <span class="w-4 h-4 bg-yellow-400 rounded-full"></span> 準備
        </div>
        <div class="flex items-center gap-2">
          <span class="w-4 h-4 bg-blue-500 rounded-full"></span> 修理
        </div>
      </div>

    </div>
  </AdminLayout>
</template>
