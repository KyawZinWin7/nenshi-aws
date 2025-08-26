<script setup>
import { ref, computed } from 'vue'
import dayjs from 'dayjs'


const isPopupOpen = ref(false)
const selectedRange = ref({
    start: dayjs().startOf('day').format('YYYY-MM-DD'),
    end: dayjs().add(6, 'day').format('YYYY-MM-DD')
})

const dateInput = computed(() =>
    `${dayjs(selectedRange.value.start).format('YYYY/MM/DD')} - ${dayjs(selectedRange.value.end).format('YYYY/MM/DD')}`
)

function togglePopup() {
    isPopupOpen.value = !isPopupOpen.value
}
const emit = defineEmits(['date-range-selected']);
function applyDateRange() {
    isPopupOpen.value = false
    // Emit the selected date range to the parent component
    emit('date-range-selected', {
        start: selectedRange.value.start,
        end: selectedRange.value.end
    })
    // console.log('Selected Date Range:', selectedRange.value.start, 'to', selectedRange.value.end)
    // You can also handle the search logic here if needed  
}

function cancelPopup() {
    isPopupOpen.value = false
}



</script>

<template>
    <div class="relative">
        <!-- Label and Input -->
        <div class="items-center flex">
            <div class="w-full px-3 py-2 border rounded-lg bg-white text-gray-800 shadow-sm cursor-pointer hover:ring-1 hover:ring-blue-400 transition"
                @click="togglePopup">
                {{ dateInput }}
            </div>
        </div>

        <!-- Popup -->
        <transition name="fade">
            <div v-if="isPopupOpen"
                class="absolute top-full mt-2 w-full bg-white border border-gray-200 rounded-xl shadow-xl p-4 z-30 space-y-4">
                <!-- Start Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                    <input type="date" v-model="selectedRange.start"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>

                <!-- End Date -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                    <input  type="date" v-model="selectedRange.end"
                        class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-blue-500 focus:outline-none" />
                </div>

                <!-- Buttons -->
                <div class="flex justify-end space-x-3 pt-2">
                    <button @click="cancelPopup"
                        class="px-4 py-2 text-sm rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-700 transition">
                        Cancel
                    </button>
                    <button @click="applyDateRange"
                        class="px-4 py-2 text-sm rounded-lg bg-blue-600 hover:bg-blue-700 text-white transition">
                        Apply
                    </button>
                </div>
            </div>
        </transition>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
