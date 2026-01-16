<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MachineType;

class MachineTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        //撚糸課
        $machineTypes = [
            ['id' => 1, 'name' => 'DT308','department_id' => 1 ],
            ['id' => 2, 'name' => 'DT302','department_id' => 1 ],
            ['id' => 3, 'name' => 'DTF・DTH','department_id' => 1 ],
            ['id' => 4, 'name' => 'RW','department_id' => 1 ],
           

        ];


        foreach ($machineTypes as $type) {
            MachineType::create($type);
        }



        //準備課
        $machineTypes = [
            ['id' => 5, 'name' => 'W','department_id' => 2 ],
            ['id' => 6, 'name' => 'K','department_id' => 2 ],

        ];

        foreach ($machineTypes as $type) {
            MachineType::create($type);
        }
    }
}
