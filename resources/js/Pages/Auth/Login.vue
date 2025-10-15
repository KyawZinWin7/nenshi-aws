<script setup>
import { ref, onMounted, onBeforeUnmount } from "vue";
import { Head, useForm } from "@inertiajs/vue3";
import Footer from "../../Footer.vue";

const form = useForm({
  employee_code: "",
  password: "",
  remember: false,
});

const submit = () => {
  form.post(route("login"), {
    onFinish: () => form.reset("password"),
  });
};

// image slider logic
const images = [
  "/img/slide-001-min.png",
  "/img/slide-002-min.png",
  "/img/slide-003-min.png",
];
const currentImage = ref(0);
let interval = null;

onMounted(() => {
  interval = setInterval(() => {
    currentImage.value = (currentImage.value + 1) % images.length;
  }, 4000);
});

onBeforeUnmount(() => {
  clearInterval(interval);
});
</script>

<template>
  <Head title="ログイン" />
  <div class="bg-gray-100 lg:h-screen flex items-center justify-center p-4">
    <div
      class="max-w-6xl bg-white shadow-[0_2px_10px_-3px_rgba(6,81,237,0.3)] p-4 lg:p-5 rounded-md"
    >
      <div class="grid md:grid-cols-2 items-center gap-y-8">
        <!-- ✅ Login Form -->
        <form
          @submit.prevent="submit"
          class="max-w-md mx-auto w-full p-4 md:p-6"
        >
          <div class="mb-8 text-center">
            <h1 class="text-2xl font-semibold text-slate-800">ログイン</h1>
          </div>

          <div class="space-y-6">
            <div>
              <label
                class="text-slate-900 text-sm font-medium mb-2 block"
                for="employee_code"
                >社員コード</label
              >
              <div class="relative flex items-center">
                <input
                  id="employee_code"
                  v-model="form.employee_code"
                  type="text"
                  required
                  class="w-full text-sm text-slate-900 bg-slate-100 focus:bg-transparent pl-4 pr-10 py-3 rounded-md border border-slate-100 focus:border-blue-600 outline-none transition-all"
                  placeholder="社員コードを入力"
                />
              </div>
              <div v-if="form.errors.employee_code" class="text-red-500 text-sm mt-1">
                {{ form.errors.employee_code }}
              </div>
            </div>

            <div>
              <label
                class="text-slate-900 text-sm font-medium mb-2 block"
                for="password"
                >パスワード</label
              >
              <div class="relative flex items-center">
                <input
                  id="password"
                  v-model="form.password"
                  type="password"
                  required
                  class="w-full text-sm text-slate-900 bg-slate-100 focus:bg-transparent pl-4 pr-10 py-3 rounded-md border border-slate-100 focus:border-blue-600 outline-none transition-all"
                  placeholder="パスワードを入力"
                />
              </div>
              <div v-if="form.errors.password" class="text-red-500 text-sm mt-1">
                {{ form.errors.password }}
              </div>
            </div>

            
          </div>

          <div class="mt-10">
            <button
              type="submit"
              :disabled="form.processing"
              class="w-full py-3 text-white bg-blue-600 hover:bg-blue-700 rounded-md font-medium transition-all shadow-lg"
            >
              ログイン
            </button>
          </div>
        </form>

        <!-- ✅ Image Slider Section -->
        <div class="w-full h-full relative">
          <div
            class="aspect-square bg-gray-50 relative rounded-md overflow-hidden w-full h-full"
          >
            <img
              :src="images[currentImage]"
              class="w-full h-full object-cover transition-opacity duration-1000"
              alt="login background"
            />
            <div
              class="absolute inset-0 bg-indigo-600/70 flex items-center justify-center text-center px-6"
            >
              <div>
                <h1 class="text-white text-sm  mb-4">
                  技術開発で織物の未来を紡ぐ、<br/>松文産業グループ
                </h1>
                <p
                  class="text-slate-100 text-xs leading-relaxed"
                >
                  130年を超える歴史。多領域に展開する織物の可能性。
                  <br />加工糸から織物まで手掛ける一貫生産体制が「安定」と「先進」を実現しています。
                </p>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  
</template>
