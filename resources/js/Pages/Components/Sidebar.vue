<script setup>
// Props are declared here to receive data from the parent component.
import { defineProps } from 'vue';
import { route } from 'ziggy-js';

defineProps({
  isVisible: {
    type: Boolean,
    required: true,
  },
  user: {
    type: Object,
    required: false,
  },
});
</script>

<template>
  <aside
    class="fixed top-0 left-0 z-40 w-48 h-screen pt-14 transition-transform bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700 "
    :class="{ '-translate-x-full': !isVisible }" aria-label="Sidenav">


    <div class="overflow-y-auto py-5 px-3 h-full bg-white dark:bg-gray-800">
      <ul class="space-y-2">



        <!--Start Mainoperation Complete-->
        <li>
          <a :href="route('mainoperations.admincompletelist')" :class="[
            'flex items-center p-2 text-base font-medium rounded-lg group',
            route().current('mainoperations.admincompletelist')
              ? 'bg-[#FF9500] text-white'
              : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'
          ]">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
              stroke="currentColor" width="24" height="24">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 12l2 2 4-4M7 7h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>



            <span class="ml-3">完了(撚糸)</span>
          </a>
        </li>
        <!--End Mainoperation Complete-->
        <!--Start SizingOperationComplete-->
        <li>
          <a :href="route('sizingoperations.admincompletelist')" :class="[
            'flex items-center p-2 text-base font-medium rounded-lg group',
            route().current('sizingoperations.admincompletelist')
              ? 'bg-[#FF9500] text-white'
              : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'
          ]">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
              stroke="currentColor" width="24" height="24">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 12l2 2 4-4M7 7h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>



            <span class="ml-3">完了(準備)</span>
          </a>
        </li>
        <!--End SizingOperation Complete-->
        <!--Start Plants-->
        <li>
          <a :href="route('plants.index')" :class="[
            'flex items-center p-2 text-base font-medium rounded-lg group',
            route().current('plants.index')
              ? 'bg-[#FF9500] text-white'
              : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'
          ]">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
              stroke="currentColor" width="24" height="24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M3 21v-8l4 4 4-4v8h10V10l-4-4-4 4V3H3v18z" />
            </svg>




            <span class="ml-3">工場</span>
          </a>
        </li>
        <!--End Plants-->


        <!--Start Department-->
        <li>
          <a :href="route('departments.index')" :class="[
            'flex items-center p-2 text-base font-medium rounded-lg group',
            route().current('departments.index')
              ? 'bg-[#FF9500] text-white'
              : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'
          ]">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor"
              stroke-width="1.8" 　width="24" height="24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M4 20h16M6 20V10h5v10m0 0V6h7v14" />
            </svg>





            <span class="ml-3">部門</span>
          </a>
        </li>
        <!--End Department-->

        <!--Start Employee-->
        <li>
          <a :href="route('employees.index')" :class="[
            'flex items-center p-2 text-base font-medium rounded-lg group',
            ['employees.index', 'employees.create', 'employees.edit'].includes(route().current())
              ? 'bg-[#FF9500] text-white'
              : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'
          ]">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
            </svg>



            <span class="ml-3"> 従業員</span>
          </a>
        </li>
        <!--End Employee-->



        <!--Start　MachineType -->
        <li>
          <a :href="route('machinetypes.index')" :class="[
            'flex items-center p-2 text-base font-medium rounded-lg group',
            ['machinetypes.index', 'machinetypes.create', 'machinetypes.edit'].includes(route().current())
              ? 'bg-[#FF9500] text-white'
              : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'
          ]">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
              stroke="currentColor" width="24" height="24">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M11.983 1.688a1 1 0 011.034 0l1.518.877a1 1 0 00.531.152l1.808.106a1 1 0 01.826.582l.95 1.647a1 1 0 00.386.386l1.647.95a1 1 0 01.582.826l.106 1.808a1 1 0 00.152.531l.877 1.518a1 1 0 010 1.034l-.877 1.518a1 1 0 00-.152.531l-.106 1.808a1 1 0 01-.582.826l-1.647.95a1 1 0 00-.386.386l-.95 1.647a1 1 0 01-.826.582l-1.808.106a1 1 0 00-.531.152l-1.518.877a1 1 0 01-1.034 0l-1.518-.877a1 1 0 00-.531-.152l-1.808-.106a1 1 0 01-.826-.582l-.95-1.647a1 1 0 00-.386-.386l-1.647-.95a1 1 0 01-.582-.826l-.106-1.808a1 1 0 00-.152-.531l-.877-1.518a1 1 0 010-1.034l.877-1.518a1 1 0 00.152-.531l.106-1.808a1 1 0 01.582-.826l1.647-.95a1 1 0 00.386-.386l.95-1.647a1 1 0 01.826-.582l1.808-.106a1 1 0 00.531-.152l1.518-.877z" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>



            <span class="ml-3"> 機台</span>
          </a>
        </li>
        <!--End MachineType-->


        <!--Start　Task -->
        <li>
          <a :href="route('tasks.index')" :class="[
            'flex items-center p-2 text-base font-medium rounded-lg group',
            ['tasks.index', 'tasks.create', 'tasks.edit'].includes(route().current())
              ? 'bg-[#FF9500] text-white'
              : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'
          ]">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
              stroke="currentColor" width="24" height="24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14 3l7 7-1 1-7-7 1-1zM4 21l7-7-1-1-7 7 1 1z" />
            </svg>


            <span class="ml-3"> 作業</span>
          </a>
        </li>
        <!--End Task-->


        <!--Start Small Task -->
        <li>
          <a :href="route('smalltasks.index')" :class="[
            'flex items-center p-2 text-base font-medium rounded-lg group',
            ['smalltasks.index', 'smalltasks.create', 'smalltasks.edit'].includes(route().current())
              ? 'bg-[#FF9500] text-white'
              : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'
          ]">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
              stroke="currentColor" width="24" height="24">
              <path stroke-linecap="round" stroke-linejoin="round"
                d="M9 12l2 2 4-4M12 22c5.523 0 10-4.477 10-10S17.523 2 12 2 2 6.477 2 12s4.477 10 10 10z" />
            </svg>



            <span class="ml-3"> 小作業</span>
          </a>
        </li>
        <!--End Task-->



        <!--Start　Machine Number -->
        <li>
          <a :href="route('machinenumbers.index')" :class="[
            'flex items-center p-2 text-base font-medium rounded-lg group',
            ['machinenumbers.index', 'machinenumbers.create', 'machinenumbers.edit'].includes(route().current())
              ? 'bg-[#FF9500] text-white'
              : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'
          ]">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
              stroke="currentColor" width="24" height="24">
              <path stroke-linecap="round" stroke-linejoin="round" d="M7 4h10M7 20h10M9 4v16M15 4v16" />
            </svg>



            <span class="ml-3"> 機械番号</span>
          </a>
        </li>
        <!--End Machine Number-->





        <!--Start　Machine Status -->
        <li>
          <a :href="route('machines.status')" :class="[
            'flex items-center p-2 text-base font-medium rounded-lg group',
            ['machines.status'].includes(route().current())
              ? 'bg-[#FF9500] text-white'
              : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'
          ]">

            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

              <circle cx="12" cy="12" r="9" />
              <path d="M3 12h4l2-4 4 8 2-4h4" />
            </svg>








            <span class="ml-3"> 機械状態</span>
          </a>




        </li>
        <!--End Machine Status-->

        <!--Start　Machine operation time summary -->
        <li>
          <a :href="route('machines.status')" :class="[
            'flex items-center p-2 text-base font-medium rounded-lg group',
            [''].includes(route().current())
              ? 'bg-[#FF9500] text-white'
              : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'
          ]">




            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
              stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">

              
              <circle cx="12" cy="12" r="3" />
              <path d="M12 1v2M12 21v2M4.22 4.22l1.42 1.42M18.36 18.36l1.42 1.42
           M1 12h2M21 12h2M4.22 19.78l1.42-1.42M18.36 5.64l1.42-1.42" />

              
              <path d="M12 12l4 2" />
            </svg>
            <span class="ml-3 txt-sm"> 機械稼働時間</span>
          </a>




        </li>
        <!--End Machine operation time summary-->


        <!--Start　Export with excel mainoperation -->
        <li>
          <a :href="route('mainoperations.export')" :class="[
            'flex items-center p-2 text-base font-medium rounded-lg group',
            ['mainoperations.export'].includes(route().current())
              ? 'bg-[#FF9500] text-white'
              : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'
          ]">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
              stroke="currentColor" width="24" height="24">
              <rect x="2" y="2" width="20" height="20" rx="4" stroke="currentColor" stroke-width="2" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8M8 12l4-4 4 4" />
            </svg>




            <span class="ml-3"> エクスポート</span>
          </a>
        </li>
        <!--End Export with excel mainoperation -->


        <!--Start　Export with excel for Sizingoperation -->
        <li>
          <a :href="route('sizingoperations.export')" :class="[
            'flex items-center p-2 text-base font-medium rounded-lg group',
            ['sizingoperations.export'].includes(route().current())
              ? 'bg-[#FF9500] text-white'
              : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'
          ]">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
              stroke="currentColor" width="24" height="24">
              <rect x="2" y="2" width="20" height="20" rx="4" stroke="currentColor" stroke-width="2" />
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v8M8 12l4-4 4 4" />
            </svg>




            <span class="ml-1 text-sm"> エクスポート(準備)</span>
          </a>
        </li>
        <!--End Export with excel for sizingoperation-->




        <li v-if="user">
          <Link :href="route('mainoperations.export')" class="block py-2 px-3 rounded-sm md:p-0 text-sm md:text-base"
            :class="{
              'text-blue-700 bg-gray-100 md:bg-transparent md:text-blue-700 dark:text-blue-500': route().current('mainoperations.export'),
              'text-gray-900 hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent': !route().current('mainoperations.export')
            }" @click="closeMenu">
          エクスポート
          </Link>
        </li>













      </ul>
    </div>
  </aside>
</template>
