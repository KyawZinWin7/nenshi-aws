<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Models\Employee;
use App\Models\Task;
use App\Models\MachineType;

class MainOperationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    // public function run(): void
    // {
        
    // }


    public function run()
    {
        $machineTypes = DB::table('machine_types')->pluck('id')->toArray();
        $tasks        = DB::table('tasks')->pluck('id')->toArray();
        $employees    = DB::table('employees')->pluck('id')->toArray();

        // မရှိသေးရင် Error မဖြစ်အောင် အရင် Populate လုပ်
        if (empty($machineTypes)) {
            $machineTypes[] = DB::table('machine_types')->insertGetId(['name' => 'DT-302']);
            $machineTypes[] = DB::table('machine_types')->insertGetId(['name' => 'DT-308']);
        }
        if (empty($tasks)) {
            $tasks[] = DB::table('tasks')->insertGetId(['name' => '糸おろし']);
            $tasks[] = DB::table('tasks')->insertGetId(['name' => 'パーン変える']);
        }
        if (empty($employees)) {
            for ($i=1; $i<=20; $i++) {
                $employees[] = DB::table('employees')->insertGetId([
                    'name' => '担当者-'.$i,
                    'employee_code' => 'EMP'.str_pad($i,3,'0',STR_PAD_LEFT),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        // main_operations 1000 row
        for ($i=1; $i<=1000; $i++) {
            $start = Carbon::now()->subDays(rand(0,5))->setTime(rand(0,23), rand(0,59), rand(0,59));
            $end   = (clone $start)->addMinutes(rand(30, 200));
            $diff  = $start->diff($end)->format('%H:%I:%S');

            DB::table('main_operations')->insert([
                'machine_type_id' => $machineTypes[array_rand($machineTypes)],
                'machine_number'  => rand(1,20),
                'task_id'         => $tasks[array_rand($tasks)],
                'start_time'      => $start,
                'end_time'        => $end,
                'total_time'      => $diff,
                'employee_id'     => $employees[array_rand($employees)],
                'status'          => 1, // 0=未完了, 1=完了
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }


}
