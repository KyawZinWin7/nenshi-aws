<template>
  <div class="min-h-screen flex flex-col items-center justify-center bg-gradient-to-b from-gray-100 to-gray-200">
    <h1 class="text-8xl font-bold text-gray-700 animate-bounce">404</h1>
    <p class="text-xl text-gray-600 mt-4">お探しのページは見つかりません。</p>
    <Link
      :href="route('nenshioperations')"
      class="mt-6 px-6 py-2 bg-blue-500 text-white rounded-full hover:bg-blue-600 transition"
    >
      ホームに戻る
    </Link>
  </div>
</template>

<script setup>
import { Link } from '@inertiajs/vue3'
</script>
