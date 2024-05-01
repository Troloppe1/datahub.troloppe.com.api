<?php

namespace Database\Seeders;

use App\Models\Location;
use App\Models\Section;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class InititalLocationAndSectionSeeder extends Seeder
{
    public static array $locations = [
        "Ikoyi" => [
            "Abacha Estate",
            "Parkview Estate",
            "Banana Island ",
            "Ikoyi 1 ",
            "Osborne Foreshore 1",
            "Osborne Foreshore 2",
            "Mojisola Onikoyi Estate",
            "South West Ikoyi",
            "Dolphin Estate",
        ],
        "Victoria Island" => [
            "Victoria Island 1",
            "Oniru",
            "Eko Atlantic",
            "Dideolu Estate",
        ],
        "Lekki" => [
            "Lekki Right",
            "VGC",
            "Lekki 1",
        ],
        "Ikeja" => [
            "Ikeja GRA",
            "Maryland Estate",
            "Sonibare Estate",
            "Adeniyi Jones",
        ],
        "Opebi" => [
            "Awuse Estate"
        ]
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (static::$locations as $location => $sections) {
            $location = Location::create(['name' => str($location)->lower()->value()]);
            foreach($sections as $section) {
                $location->sections()->create(['name' => str($section)->lower()->value()]);
            }
        }
    }
}
