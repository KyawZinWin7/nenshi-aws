<script setup>
// import { route } from '../../../vendor/tightenco/ziggy/src/js';


defineProps({
    routeName: String,
    componentName: String,
});


</script>

<template>
    <Link :href="route(routeName)" class="px-3 py-2 rounded-lg
    hover:bg-slate-700" :class="{ 'bg-slate-700': $page.component === componentName }">
    <slot />
    </Link>


    


</template>