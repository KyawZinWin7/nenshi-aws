<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
         $departments = [
            // [
            //     'name' => '管理本部　総務部/人事労務部',
            //     'department_code' => '191020',
            // ],
            [
                'id'   => 1,
                'name' => '撚糸課',
                'department_code' => '114010',
            ],
            [
                'id'   => 2,
                'name' => '準備課',
                'department_code' => '113010',
            ],
            
            
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}
