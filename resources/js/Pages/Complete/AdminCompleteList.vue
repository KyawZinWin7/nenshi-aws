<script setup>
import DataTable from '../../Components/DataTable.vue';
import { onMounted, ref } from 'vue';
import Footer from '../../Footer.vue';
import AdminLayout from '../Components/AdminLayout.vue';

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


</script>

<template>

    <AdminLayout>

        <Head title=" - 完了" />
        <main class="mx-auto p-2 min-h-screen">
            <div class="p-2 bg-gray-100 ">
                <!-- Pass fetched data to DataTable -->
                <DataTable :mainoperations="mainoperations" :machinetypes="machinetypes" :tasks="tasks" />
            </div>

        </main>
    </AdminLayout>



</template>
