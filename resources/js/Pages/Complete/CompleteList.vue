<script setup>
import DataTable from '../../Components/DataTable.vue';
import { onMounted, ref } from 'vue';
import Footer from '../../Footer.vue';

const props = defineProps({
  mainoperations: {
    type: Object,
    required: true,
  },
  machinetypes: {
    type: Object,
    required: true,
  },
  tasks: {
    type: Object,
    required: true,
  },
});

console.log(props.mainoperations);
console.log(props.machinetypes);
console.log(props.tasks);

</script>

<template>
  <Head title=" - 完了" />
    <main class="mx-auto  min-h-screen">
  <div class="p-3 bg-gray-100 ">
    <!-- Pass fetched data to DataTable -->
    <DataTable :mainoperations="mainoperations" :machinetypes="machinetypes" :tasks="tasks" />
  </div>
  
  </main>

</template>
