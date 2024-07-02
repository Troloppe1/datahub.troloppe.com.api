<?php

namespace Database\Seeders;

use App\Models\Section;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SectionSeeder extends Seeder
{
    private array $sections = [
        // Ikoyi
        ["location_id" => 1, "name" => "Abacha Estate"],
        ["location_id" => 1, "name" => "Parkview Estate"],
        ["location_id" => 1, "name" => "Banana Island"],
        ["location_id" => 1, "name" => "Ikoyi 1"],
        ["location_id" => 1, "name" => "Osborne Foreshore 1"],
        ["location_id" => 1, "name" => "Osborne Foreshore 2"],
        ["location_id" => 1, "name" => "Mojisola Onikoyi Estate"],
        ["location_id" => 1, "name" => "South West Ikoyi"],
        ["location_id" => 1, "name" => "Dolphin Estate"],

        // Lekki
        ["location_id" => 2, "name" => "Lekki Right",],
        ["location_id" => 2, "name" => "VGC"],
        ["location_id" => 2, "name" => "Lekki 1"],

        //Victoria Island
        ["location_id" => 3, "name" => "Victoria Island 1"],
        ["location_id" => 3, "name" => "Oniru"],
        ["location_id" => 3, "name" => "Eko Atlantic"],
        ["location_id" => 3, "name" => "Dideolu Estate"],

        // Ikeja
        ["location_id" => 4, "name" => "Ikeja GRA"],
        ["location_id" => 4, "name" => "Maryland Estate"],
        ["location_id" => 4, "name" => "Sonibare Estate"],
        ["location_id" => 4, "name" => "Adeniyi Jones"],

        // Opebi
        ["location_id" => 5, "name" => "Awuse Estate"],
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->sections as $section) {
            Section::create($section);
        }
    }
}
