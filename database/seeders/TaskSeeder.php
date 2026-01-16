<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Task;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        //撚糸課
        $tasks = [
            ['id' => 1, 'name' => 'パーン指し','machine_type_id' => 2 ,'department_id' => 1 ],
            ['id' => 2, 'name' => '卸し～パーン指し','machine_type_id' => 2 ,'department_id' => 1 ],
            ['id' => 3, 'name' => '機台掃除','machine_type_id' => 2 ,'department_id' => 1 ],
            ['id' => 4, 'name' => '糸仕掛け','machine_type_id' => 2 ,'department_id' => 1 ],
            ['id' => 5, 'name' => '糸卸し','machine_type_id' => 2 ,'department_id' => 1 ],
            ['id' => 6, 'name' => '運転','machine_type_id' => 2 ,'department_id' => 1, 'is_drive_task' => true ],



            ['id' => 7, 'name' => 'パーン指し','machine_type_id' => 1 ,'department_id' => 1 ],
            ['id' => 8, 'name' => '卸し～パーン指し','machine_type_id' => 1 ,'department_id' => 1 ],
            ['id' => 9, 'name' => '糸仕掛け','machine_type_id' => 1 ,'department_id' => 1 ],
            ['id' => 10, 'name' => '糸卸し','machine_type_id' => 1 ,'department_id' => 1 ],
            ['id' => 11, 'name' => '運転','machine_type_id' => 1 ,'department_id' => 1, 'is_drive_task' => true ],


            ['id' => 12, 'name' => '卸し～運転','machine_type_id' => 4 ,'department_id' => 1 ],
            ['id' => 13, 'name' => '原糸入れ','machine_type_id' => 4 ,'department_id' => 1 ],
            ['id' => 14, 'name' => '糸仕掛け','machine_type_id' => 4 ,'department_id' => 1 ],
            ['id' => 14, 'name' => '糸入れ～運転','machine_type_id' => 4 ,'department_id' => 1 ],
            ['id' => 15, 'name' => '糸卸し','machine_type_id' => 4 ,'department_id' => 1 ],
            ['id' => 16, 'name' => '運転','machine_type_id' => 4 ,'department_id' => 1, 'is_drive_task' => true ],
        ];

        foreach ($tasks as $task) {
            Task::create($task);
        }
    }
}
