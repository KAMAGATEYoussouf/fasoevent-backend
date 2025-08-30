<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        City::create([
            'id' => Str::uuid(),
            'name' => 'Ouagadougou',
        ]);

        City::create([
            'id' => Str::uuid(),
            'name' => 'Bobo Dioulasso',
        ]);
    }
}
