<script setup>
import { defineProps } from 'vue';

const props = defineProps({
    machinetypes: {
        type: Object,
        required: true,
    },
    tasks: {
        type: Object,
        required: true,
    },
});

const emit = defineEmits(['filter']);
const filter = (e) => {
    
    emit('filter', e.target.value);
};
</script>

<template>
    <label class="flex mr-4 items-center flex-nowrap"
  >
    <input type="radio" name="show" value="all" checked class="mr-2" @change="filter"/>
    <span class="whitespace-nowrap ml-1">すべて</span>
  </label>

  <label
    v-for="machinetype in machinetypes?.data ?? []"
    :key="machinetype.id"
    class="flex mr-4 items-center flex-nowrap"
  >
    <input type="radio" name="show" :value="machinetype.id" class="mr-2" @change="filter"/>
    <span class="whitespace-nowrap ml-1">{{ machinetype.name }}</span>
  </label>
</template>
