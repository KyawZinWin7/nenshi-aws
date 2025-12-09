<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Plant;

class PlantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plants = [
            ['id'=> 1, 'name' => '8工場' ],
            ['id'=> 2, 'name' => '9工場' ],
            ['id'=> 3, 'name' => '10工場' ],
            ['id'=> 4, 'name' => '11工場' ],
            ['id'=> 5, 'name' => '12工場' ],
            ['id'=> 6, 'name' => 'N1工場' ],
            ['id'=> 7, 'name' => 'I工場' ],
            ['id'=> 8, 'name' => 'A工場' ],
            ['id'=> 9, 'name' => 'F工場' ],

        ];

        foreach ($plants as $plant) {
            Plant::create($plant);
        }
    }
}
