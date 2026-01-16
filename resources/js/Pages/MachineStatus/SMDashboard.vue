<script setup>
import { ref, computed, watch } from 'vue'
import { usePage, Head } from '@inertiajs/vue3'
import AdminLayout from '../Components/AdminLayout.vue'

const page = usePage()

// backend raw props
const rawMachines = page.props.machinenumbers.data
const plants = page.props.plants.data

// ✅ selected plant (real null)
const selectedPlant = ref(null)

// processed machines
const machines = computed(() =>
  rawMachines.map(m => ({
    machine_id: m.id,
    number: m.number,

    plant_id: m.machine_type_plant_id?.plant_id?.id,
    plant_name: m.machine_type_plant_id?.plant_id?.name,

    type: m.machine_type_plant_id?.machine_type_id?.name,

    status: m.drive_status, // later connect
  }))
)

console.log(machines.value)
// ✅ auto select first plant
watch(
  () => plants,
  (newPlants) => {
    if (selectedPlant.value === null && newPlants.length > 0) {
      selectedPlant.value = Number(newPlants[0].id)
    }
  },
  { immediate: true }
)


// grouped by plant and type
const groupedByPlant = computed(() => {
  const result = {}

  machines.value
    .filter(m => m.plant_id === selectedPlant.value)
    .forEach(m => {
      if (!result[m.plant_name]) result[m.plant_name] = {}
      if (!result[m.plant_name][m.type]) result[m.plant_name][m.type] = []

      result[m.plant_name][m.type].push(m)
    })

  return result
})

// status → color
const statusClass = (status) => {
  switch (status) {
    case 'running': return 'bg-green-500'
    case 'prepare': return 'bg-yellow-400 text-black'
    case 'repair':  return 'bg-blue-500'
    case 'stopped':
    default:        return 'bg-red-500'
  }
}
</script>

<template>
  <AdminLayout>
    <Head title="機械状況" />

    <div class="p-4 sm:p-6 space-y-10">

      <!--  Plant Filter (mobile scrollable) -->
      <div
        class="flex gap-3 items-center
               overflow-x-auto pb-2
               sm:overflow-visible sm:flex-wrap"
      >
        <label
          v-for="plant in plants"
          :key="plant.id"
          class="flex items-center gap-2 cursor-pointer
                 whitespace-nowrap
                 px-3 py-1 rounded-full border
                 text-sm
                 sm:text-base sm:border-none"
        >
          <input
            type="radio"
            :value="Number(plant.id)"
            v-model="selectedPlant"
            class="accent-green-600"
          />
          {{ plant.name }}
        </label>
      </div>

      <!-- Plant loop -->
      <div
        v-for="(types, plantName) in groupedByPlant"
        :key="plantName"
        class="space-y-8"
      >
        <!-- Plant title -->
        <h2 class="text-lg sm:text-xl font-bold flex items-center gap-2">
          <span class="w-3 h-3 bg-green-600 rounded-full"></span>
          {{ plantName }}
        </h2>

        <!--  Machine type loop -->
        <div v-for="(list, type) in types" :key="type">
          <h3 class="font-semibold mb-2 sm:mb-3 text-sm sm:text-base">
            {{ type }}
          </h3>

          <!--  Machine Grid -->
          <div
            class="grid gap-3
                   grid-cols-2
                   sm:grid-cols-4
                   md:grid-cols-6
                   lg:grid-cols-10"
          >
            <div
              v-for="machine in list"
              :key="machine.machine_id"
              class="rounded-lg flex flex-col items-center justify-center
                     text-white shadow
                     h-20 sm:h-24"
              :class="statusClass(machine.status)"
            >
              <div class="text-xs sm:text-sm font-semibold">
                {{ machine.type }}
              </div>
              <div class="text-base sm:text-lg font-bold">
                {{ machine.number }}
              </div>
              <div>{{ machine.status }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- 📘 Legend -->
      <div class="flex flex-wrap gap-4 pt-6 border-t text-sm">
        <div class="flex items-center gap-2">
          <span class="w-4 h-4 bg-green-500 rounded-full"></span>
          運転
        </div>
        <div class="flex items-center gap-2">
          <span class="w-4 h-4 bg-red-500 rounded-full"></span>
          停止
        </div>
        <div class="flex items-center gap-2">
          <span class="w-4 h-4 bg-yellow-400 rounded-full"></span>
          準備
        </div>
        <div class="flex items-center gap-2">
          <span class="w-4 h-4 bg-blue-500 rounded-full"></span>
          修理
        </div>

        {{  }}
      </div>

    </div>
  </AdminLayout>
</template>

