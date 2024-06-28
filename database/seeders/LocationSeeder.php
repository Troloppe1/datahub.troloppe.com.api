<?php

namespace Database\Seeders;

use App\Models\Location;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class LocationSeeder extends Seeder
{
    private array $locations = [
        ['name' => 'ikoyi', 'is_active' => false],
        ['name' => 'lekki', 'is_active' => false],
        ['name' => 'victoria island', 'is_active' => false],
        ['name' => 'ikeja', 'is_active' => false],
        ['name' => 'opebi', 'is_active' => false]
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        foreach ($this->locations as $location) {
            Location::create($location);
        }

    }
}
