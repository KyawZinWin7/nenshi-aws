<?php

namespace Database\Seeders;
use App\Models\Employee;
use App\Models\Department;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();
         $this->call([
        DepartmentSeeder::class,
        ]);

        Employee::create([
            'name' => 'Admin',
            'employee_code' => '56789',
            'password' => Hash::make('56789'),
            'department_id' => 1, // ★ MUST HAVE
            'role' => 'superadmin',
        ]);

    }
}
