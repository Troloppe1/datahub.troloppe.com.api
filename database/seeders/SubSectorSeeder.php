<?php

namespace Database\Seeders;

use App\Models\SubSector;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubSectorSeeder extends Seeder
{
    private array $subSectors = [
        // Residential
        ["sector_id" => 1, "name" => "Apartments"],
        ["sector_id" => 1, "name" => "Detached"],
        ["sector_id" => 1, "name" => "Semi Detached"],
        ["sector_id" => 1, "name" => "Terrace"],
        ["sector_id" => 1, "name" => "Storey Building"],
        ["sector_id" => 1, "name" => "Shortlet"],
       
        // Commercial
        ["sector_id" => 2, "name" => "Office"],
        ["sector_id" => 2, "name" => "Retail"],
        ["sector_id" => 2, "name" => "Event"],
        ["sector_id" => 2, "name" => "Hotel"],
        
        // Industrial
        ["sector_id" => 3, "name" => "Factory"],
        ["sector_id" => 3, "name" => "Warehouse"],
        
        // Land
        ["sector_id" => 4, "name" => "Bareland"],
        
        // Health Care
        ["sector_id" => 5, "name" => "Hospitals"],
        ["sector_id" => 5, "name" => "Medical Centres"],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->subSectors as $subSector) {
            SubSector::create($subSector);
        }
    }
}
