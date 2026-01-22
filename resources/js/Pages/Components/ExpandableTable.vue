<template>
  <div class="p-4">
    <table class="w-full border border-gray-300 rounded-lg">
      <thead class="bg-gray-100">
        <tr>
          <th class="text-left p-2">Task Name</th>
          <th class="text-left p-2 w-32">Status</th>
        </tr>
      </thead>

      <tbody>
        <template v-for="task in tasks" :key="task.id">

          <!-- MAIN TASK ROW -->
          <tr
            class="cursor-pointer bg-white hover:bg-gray-50"
            @click="toggle(task)"
          >
            <td class="p-2 font-medium">
              {{ task.title }}
            </td>
            <td class="p-2">
              {{ task.status }}
            </td>
          </tr>

          <!-- SUB TASK ROWS -->
          <tr
            v-for="sub in task.subtasks"
            :key="sub.id"
            v-show="task.open"
            class="bg-gray-50"
          >
            <td class="p-2 pl-8 text-gray-700">
              — {{ sub.title }}
            </td>
            <td class="p-2 text-gray-600">
              {{ sub.status }}
            </td>
          </tr>

        </template>
      </tbody>
    </table>
  </div>
</template>

<script setup>
import { ref } from "vue";

const tasks = ref([
  {
    id: 1,
    title: "Build UI",
    status: "Pending",
    open: false,
    subtasks: [
      { id: 1, title: "Create layout", status: "Done" },
      { id: 2, title: "Style components", status: "In progress" }
    ]
  },
  {
    id: 2,
    title: "Backend API",
    status: "In progress",
    open: false,
    subtasks: [
      { id: 1, title: "Auth system", status: "Pending" },
      { id: 2, title: "DB schema design", status: "Done" }
    ]
  }
]);

const toggle = (task) => {
  task.open = !task.open;
};
</script>
