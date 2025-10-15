<script setup>
// Props are declared here to receive data from the parent component.
import { defineProps } from 'vue';
import { route } from 'ziggy-js';

defineProps({
  isVisible: {
    type: Boolean,
    required: true,
  },
});
</script>

<template>
  <aside
    class="fixed top-0 left-0 z-40 w-64 h-screen pt-14 transition-transform bg-white border-r border-gray-200 dark:bg-gray-800 dark:border-gray-700 "
    :class="{ '-translate-x-full': !isVisible }" aria-label="Sidenav">
    <div class="overflow-y-auto py-5 px-3 h-full bg-white dark:bg-gray-800">
      <ul class="space-y-2">



         <!--Start Mainoperation Complete-->
        <li>
          <a :href="route('mainoperatons.completelist')" :class="[
            'flex items-center p-2 text-base font-medium rounded-lg group',
            route().current('mainoperatons.completelist')
              ? 'bg-[#FF9500] text-white'
              : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'
          ]">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>


            <span class="ml-3">完了</span>
          </a>
        </li>
        <!--End Mainoperation Complete-->
        <!--Start Plants-->
        <li>
          <a :href="route('plants.index')" :class="[
            'flex items-center p-2 text-base font-medium rounded-lg group',
            route().current('plants.index')
              ? 'bg-[#FF9500] text-white'
              : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'
          ]">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>


            <span class="ml-3">工場</span>
          </a>
        </li>
        <!--End Plants-->

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
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
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
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
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
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>


            <span class="ml-3"> 作業</span>
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
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>


            <span class="ml-3"> 機械番号</span>
          </a>
        </li>
        <!--End Machine Number-->

         <!--Start　Export with excel -->
        <li>
          <a :href="route('mainoperations.export')" :class="[
            'flex items-center p-2 text-base font-medium rounded-lg group',
            ['mainoperations.export'].includes(route().current())
              ? 'bg-[#FF9500] text-white'
              : 'text-gray-900 dark:text-white hover:bg-gray-100 dark:hover:bg-gray-700'
          ]">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
              stroke="currentColor" class="size-6">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>


            <span class="ml-3"> エクスポート</span>
          </a>
        </li>
        <!--End Export with excel-->
       




        <li v-if="user">
                        <Link :href="route('mainoperations.export')"
                            class="block py-2 px-3 rounded-sm md:p-0 text-sm md:text-base" :class="{
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
