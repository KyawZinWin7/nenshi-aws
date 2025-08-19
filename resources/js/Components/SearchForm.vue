<script setup>
const emit = defineEmits(['search']);
const search = (e) => {
    emit('search', e.target.value);
}
</script>

<template>
  <form action="" class="py-2 px-3 flex w-full">
    <label class="sr-only" for="search">Search:</label>
    <div class="relative w-full">
      <input
        @input="search"
        type="text"
        id="search"
        name="search"
        placeholder="検索..."
        class="w-full sm:max-w-xs px-3 sm:px-4 py-2 text-sm sm:text-base 
               border border-gray-300 rounded-lg shadow-sm 
               focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 
               bg-white text-gray-900 placeholder-gray-400 transition"
      />
    </div>
  </form>
</template>
