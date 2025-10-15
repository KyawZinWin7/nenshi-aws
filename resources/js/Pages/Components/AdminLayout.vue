<template>
  <div class="antialiased bg-gray-50 dark:bg-gray-900">
    <!-- Navbar -->
    <!-- <Navbar @toggle-sidebar="isSidebarVisible = !isSidebarVisible" /> -->
     <Navbar  @toggle-sidebar="isSidebarVisible = !isSidebarVisible" />


    <!-- Sidebar -->
    <Sidebar :isVisible="isSidebarVisible" @close="isSidebarVisible = false" />

    <!-- Main Content -->
    <main
      class="p-4 h-auto pt-20 transition-all duration-300"
      :class="{ 'md:ml-64': isSidebarVisible }"
    >
      <slot></slot>
    </main>
  </div>
</template>

<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';
import Navbar from '../Components/Navbar.vue';
import Sidebar from '../Components/Sidebar.vue';
import { initFlowbite } from 'flowbite';

const isSidebarVisible = ref(false);

// Auto hide/show sidebar based on screen size
const handleResize = () => {
  if (window.innerWidth >= 998) {
    isSidebarVisible.value = true; // desktop → show
  } else {
    isSidebarVisible.value = false; // mobile/tablet → hide
  }
};

onMounted(() => {
  handleResize(); // Initial state
  window.addEventListener('resize', handleResize);
});

onBeforeUnmount(() => {
  window.removeEventListener('resize', handleResize);
});




onMounted(() => {
  initFlowbite();
});

</script>
