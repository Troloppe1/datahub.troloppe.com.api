<?php

namespace Database\Seeders;

use App\Models\Sector;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectorSeeder extends Seeder
{
    private array $sectors = [
        ['name' => 'residential'],
        ['name' => 'commercial'],
        ['name' => 'industrial'],
        ['name' => 'land'],
        ['name' => 'health care']
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->sectors as $sector) {
            Sector::create($sector);
        }
    }
}
