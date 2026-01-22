<template>
  <nav
    class="bg-white border-b border-gray-200 px-4 py-2.5 dark:bg-gray-800 dark:border-gray-700 fixed left-0 right-0 top-0 z-50">
    <div class="flex flex-wrap justify-between items-center">
      <!-- Left side: Logo & sidebar toggle -->
      <div class="flex justify-start items-center">
        <button @click="$emit('toggle-sidebar')" aria-controls="drawer-navigation"
          class="p-2 mr-2 text-gray-600 rounded-lg cursor-pointer hover:text-gray-900 hover:bg-gray-100 focus:bg-gray-100 dark:focus:bg-gray-700 focus:ring-2 focus:ring-gray-100 dark:focus:ring-gray-700 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white">
          <svg aria-hidden="true" class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
            <path fill-rule="evenodd"
              d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h6a1 1 0 110 2H4a1 1 0 01-1-1zM3 15a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1z"
              clip-rule="evenodd"></path>
          </svg>
          <span class="sr-only">Toggle sidebar</span>
        </button>

        <a href="#" class="flex items-center justify-between mr-4">
          <svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" width="200" height="50"
            viewBox="0 0 503 70">
            <image
              xlink:href="data:image/png;base64,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"
              x="0" y="-30" width="300" height="150" />
          </svg>
          <!-- <span class="self-center text-2xl font-semibold whitespace-nowrap dark:text-white">Dashboard</span> -->
        </a>
      </div>

      <!-- Right side: Buttons + Username -->
      <div class="flex flex-col md:flex-row md:items-center gap-2 md:gap-4 relative">

        <!-- Buttons -->
        <div class="flex flex-row gap-2">
          <!-- 撚糸課 -->
          <Link :href="route('login')" @click="closeMenu" class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200
             bg-white text-gray-800 border border-gray-300
             hover:bg-blue-50 hover:text-blue-700 shadow-sm">
          撚糸課
          </Link>

          <!-- 準備課 -->
          <Link :href="route('sizingoperation')" @click="closeMenu" class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200
             bg-white text-gray-800 border border-gray-300
             hover:bg-blue-50 hover:text-blue-700 shadow-sm">
          準備課
          </Link>
        </div>

        <!-- Username Dropdown -->
        <div class="relative self-start md:self-auto">
          <button @click="toggleDropdown($event)" class="flex items-center gap-1 text-sm font-medium rounded-md
             px-2 py-1 hover:bg-gray-100">
            <span>{{ $page.props.auth.user.name }}</span>
            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
          </button>

          <div v-if="dropdownOpen" ref="dropdownRef"
            class="absolute right-0 mt-2 w-40 bg-white rounded-md shadow-lg py-1 z-50">
            <a href="#" @click.prevent="logout" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
              ログアウト
            </a>
          </div>
        </div>
      </div>



    </div>
  </nav>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { Inertia } from '@inertiajs/inertia'
import axios from 'axios'

const dropdownOpen = ref(false);
const dropdownRef = ref(null);

const toggleDropdown = (event) => {
  event.stopPropagation(); // ← button click မှာ listener ကို stop
  dropdownOpen.value = !dropdownOpen.value;
}

const logout = () => {
  Inertia.post('/logout')
}

const closeMenu = () => {
  dropdownOpen.value = false;
}

// Outside click handler
const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    closeMenu();
  }
}

onMounted(() => {
  window.addEventListener('click', handleClickOutside);
})

onBeforeUnmount(() => {
  window.removeEventListener('click', handleClickOutside);
})




</script>