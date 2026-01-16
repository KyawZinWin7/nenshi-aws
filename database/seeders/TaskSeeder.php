<?php

namespace Database\Seeders;

use App\Models\Task;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //撚糸課
        $tasks = [
            ['id' => 1, 'name' => 'パーン指し', 'machine_type_id' => 2, 'department_id' => 1],
            ['id' => 2, 'name' => '卸し～パーン指し', 'machine_type_id' => 2, 'department_id' => 1],
            ['id' => 3, 'name' => '機台掃除', 'machine_type_id' => 2, 'department_id' => 1],
            ['id' => 4, 'name' => '糸仕掛け', 'machine_type_id' => 2, 'department_id' => 1],
            ['id' => 5, 'name' => '糸卸し', 'machine_type_id' => 2, 'department_id' => 1],
            ['id' => 6, 'name' => '運転', 'machine_type_id' => 2, 'department_id' => 1, 'task_type' => 'drive'],
            ['id' => 7, 'name' => '修理', 'machine_type_id' => 2, 'department_id' => 1, 'task_type' => 'repair'],

            ['id' => 8, 'name' => 'パーン指し', 'machine_type_id' => 1, 'department_id' => 1],
            ['id' => 9, 'name' => '卸し～パーン指し', 'machine_type_id' => 1, 'department_id' => 1],
            ['id' => 10, 'name' => '糸仕掛け', 'machine_type_id' => 1, 'department_id' => 1],
            ['id' => 11, 'name' => '糸卸し', 'machine_type_id' => 1, 'department_id' => 1],
            ['id' => 12, 'name' => '運転', 'machine_type_id' => 1, 'department_id' => 1, 'task_type' => 'drive'],
            ['id' => 13, 'name' => '修理', 'machine_type_id' => 1, 'department_id' => 1, 'task_type' => 'repair'],

            ['id' => 14, 'name' => '運転', 'machine_type_id' => 3, 'department_id' => 1, 'task_type' => 'drive'],
            ['id' => 15, 'name' => '修理', 'machine_type_id' => 3, 'department_id' => 1, 'task_type' => 'repair'],

            ['id' => 16, 'name' => '卸し～運転', 'machine_type_id' => 4, 'department_id' => 1],
            ['id' => 17, 'name' => '原糸入れ', 'machine_type_id' => 4, 'department_id' => 1],
            ['id' => 18, 'name' => '糸仕掛け', 'machine_type_id' => 4, 'department_id' => 1],
            ['id' => 19, 'name' => '糸入れ～運転', 'machine_type_id' => 4, 'department_id' => 1],
            ['id' => 20, 'name' => '糸卸し', 'machine_type_id' => 4, 'department_id' => 1],
            ['id' => 21, 'name' => '運転', 'machine_type_id' => 4, 'department_id' => 1, 'task_type' => 'drive'],
            ['id' => 22, 'name' => '修理', 'machine_type_id' => 4, 'department_id' => 1, 'task_type' => 'repair'],
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }

        //準備課

        $tasks = [
            ['id' => 23, 'name' => '運転', 'machine_type_id' => 5, 'department_id' => 2, 'task_type' => 'drive'],
            ['id' => 24, 'name' => '修理', 'machine_type_id' => 5, 'department_id' => 2, 'task_type' => 'repair'],

            ['id' => 25, 'name' => '運転', 'machine_type_id' => 6, 'department_id' => 2, 'task_type' => 'drive'],
            ['id' => 26, 'name' => '修理', 'machine_type_id' => 6, 'department_id' => 2, 'task_type' => 'repair'],
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }
    }
}
