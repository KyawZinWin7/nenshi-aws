<script setup>
import { switchTheme } from '../theme';
import NavLink from '../Components/NavLink.vue';
import { usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, onUnmounted, watch } from 'vue';
import { Link } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import Footer from '../Footer.vue';

const page = usePage();
const employee = computed(() => page.props.auth.user);
// console.log(employee.value);

const show = ref(false);
const mobileMenu = ref(false);
const dropdown = ref(null); // dropdown wrapper reference

function toggleMenu() {
    mobileMenu.value = !mobileMenu.value;
}

function closeMenu() {
    mobileMenu.value = false;
}

function toggleDropdown() {
    show.value = !show.value;
}

function handleClickOutside(event) {
    if (dropdown.value && !dropdown.value.contains(event.target)) {
        show.value = false;
    }
}

onMounted(() => {
    document.addEventListener('click', handleClickOutside);
});

onUnmounted(() => {
    document.removeEventListener('click', handleClickOutside);
});

// Route change မှာ dropdown auto hide
watch(() => page.url, () => {
    show.value = false;
});


</script>

<template>

    <Head>
        <link rel="icon" type="image/png" href="/img/favicon.ico" />
    </Head>

    <!-- overlay for mobile -->
    <div v-show="mobileMenu" @click="closeMenu" class="fixed inset-0 bg-black bg-opacity-50 md:hidden z-40">
    </div>

    <nav class="bg-white border-gray-200 dark:bg-gray-900">
        <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4 relative z-50">
            <a href="/" class="flex items-center space-x-3 rtl:space-x-reverse">
                <img :src="`/img/matsubun-logo.png`" class="h-8" alt="Matsubun" />

            </a>
            <!-- Hamburger Button -->
            <button @click="toggleMenu" type="button" class="inline-flex items-center p-2 w-10 h-10 justify-center text-sm 
                       text-gray-500 rounded-lg md:hidden hover:bg-gray-100 focus:outline-none 
                       focus:ring-2 focus:ring-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 
                       dark:focus:ring-gray-600">
                <span class="sr-only">Open main menu</span>
                <svg class="w-5 h-5" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none"
                    viewBox="0 0 17 14">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M1 1h15M1 7h15M1 13h15" />
                </svg>
            </button>

            <!-- Menu Items -->
            <div class="absolute md:static top-16 left-0 w-full md:w-auto 
                       transition-all duration-300 md:block"
                :class="mobileMenu ? 'block bg-white dark:bg-gray-900 z-50' : 'hidden'">

                <ul class="font-medium flex flex-col md:flex-row md:space-x-8 
                           p-4 md:p-0 border md:border-0 border-gray-100 
                           rounded-lg bg-gray-50 md:bg-transparent 
                           dark:bg-gray-800 md:dark:bg-gray-900 
                           dark:border-gray-700">

                    <li>
                        <Link :href="route('login')" class="block py-2 px-3 rounded-sm md:p-0 text-sm md:text-base"
                            :class="{
                                'text-white bg-blue-700 md:bg-transparent md:text-blue-700 dark:text-white md:dark:text-blue-500': route().current('home'),
                                'text-gray-900 rounded-sm hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent': !route().current('home')
                            }" @click="closeMenu">
                        ホーム
                        </Link>
                    </li>

                    

                    <!-- <li v-if="user">
                        <Link :href="route('machinetypes.index')"
                            class="block py-2 px-3 rounded-sm md:p-0 text-sm md:text-base" :class="{
                                'text-blue-700 bg-gray-100 md:bg-transparent md:text-blue-700 dark:text-blue-500': route().current('machinetypes.index'),
                                'text-gray-900 hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent': !route().current('machinetypes.index')
                            }" @click="closeMenu">
                        機台
                        </Link>
                    </li> -->

                    <!-- <li v-if="user">
                        <Link :href="route('tasks.index')"
                            class="block py-2 px-3 rounded-sm md:p-0 text-sm md:text-base" :class="{
                                'text-blue-700 bg-gray-100 md:bg-transparent md:text-blue-700 dark:text-blue-500': route().current('tasks.index'),
                                'text-gray-900 hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent': !route().current('tasks.index')
                            }" @click="closeMenu">
                        作業
                        </Link>
                    </li> -->

                    <li v-if="employee">
                        <Link :href="route('mainoperatons.completelist')"
                            class="block py-2 px-3 rounded-sm md:p-0 text-sm md:text-base" :class="{
                                'text-blue-700 bg-gray-100 md:bg-transparent md:text-blue-700 dark:text-blue-500': route().current('mainoperatons.completelist'),
                                'text-gray-900 hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent': !route().current('mainoperatons.completelist')
                            }" @click="closeMenu">
                        完了
                        </Link>
                    </li>

                    <li v-if="employee">
                        <div ref="dropdown" class="relative">
                            <div @click="toggleDropdown"
                                class="flex items-center gap-2 px-3 rounded-lg hover:bg-gray-100 cursor-pointer"
                                :class="{ 'bg-gray-100': show }">
                                <p class="text-black text-sm md:text-base">{{ employee.name }}</p>
                                <i class="fa-solid fa-angle-down text-black"></i>
                            </div>
                            <div v-show="show" class="absolute z-50 top-14 right-0 bg-white text-black 
                                        rounded-lg border border-gray-300 overflow-hidden w-40 shadow-lg">
                                <Link :href="route('logout')" method="post" as="button"
                                    class="block w-full px-6 py-3 hover:bg-gray-100 text-left text-sm md:text-base">
                                ログアウト
                                </Link>
                            </div>
                        </div>
                    </li>

                    <!-- Login for guest -->
                    <li v-else>
                        <div class="flex items-center space-x-6">
                            <Link :href="route('login')" 
                                class="block py-2 px-3 rounded-sm md:p-0 text-sm md:text-base"
                                @click="closeMenu">
                                ログイン
                            </Link>
                             <!-- <Link :href="route('register')" 
                                class="block py-2 px-3 rounded-sm md:p-0 text-sm md:text-base"
                                @click="closeMenu">
                                レジスター
                            </Link> -->
                        </div>
                        
                    </li>
                    <li v-if="employee && employee.role === 'admin'">
                        <Link :href="route('mainoperations.admincompletelist')"
                            class="block py-2 px-3 rounded-sm md:p-0 text-sm md:text-base" :class="{
                                'text-blue-700 bg-gray-100 md:bg-transparent md:text-blue-700 dark:text-blue-500': route().current('mainoperations.admincompletelist'),
                                'text-gray-900 hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent': !route().current('mainoperations.admincompletelist')
                            }" @click="closeMenu">
                        ダッシュボード
                        </Link>
                    </li>

                </ul>
            </div>
        </div>
    </nav>

    <slot />
    <Footer />
</template>
