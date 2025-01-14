<?php

use Illuminate\Support\Facades\Route;

Route::group([], function () {
    Route::get('/property-data/initial', function () {
        $regions = [
            [
                "id" => 1,
                "name" => "Island"
            ],
            [
                "id" => 2,
                "name" => "Mainland"
            ]
        ];
        $sectors =  [
            [
                "id" => 1,
                "name" => "Residential"
            ],
            [
                "id" => 2,
                "name" => "Commercial"
            ],
            [
                "id" => 3,
                "name" => "Land"
            ],
            [
                "id" => 4,
                "name" => "HealthCare"
            ],
            [
                "id" => 5,
                "name" => "Recreational"
            ],
            [
                "id" => 6,
                "name" => "Hospitality"
            ],
        ];
        $offers = array(
            array(
                "id" => 1,
                "name" => "Sale",
                "created_at" => "2024-10-26T15:10:36.413Z",
                "updated_at" => "2024-10-26T15:10:36.413Z"
            ),
            array(
                "id" => 2,
                "name" => "Lease",
                "created_at" => "2024-10-26T15:10:36.413Z",
                "updated_at" => "2024-10-26T15:10:36.413Z"
            ),
            array(
                "id" => 3,
                "name" => "Sale/Lease",
                "created_at" => "2024-10-26T15:10:36.413Z",
                "updated_at" => "2024-10-26T15:10:36.413Z"
            ),
            array(
                "id" => 4,
                "name" => "Short-Let",
                "created_at" => "2024-10-26T15:10:36.413Z",
                "updated_at" => "2024-10-26T15:10:36.413Z"
            )
        );
        return response()->json([
            "regions" => $regions,
            "sectors" => $sectors,
            "offers" => $offers
        ]);
    });

    Route::get('/property-data/locations', function () {

        $regionId = request()->query('region_id');
        $locations = [
            [
                "id" => 1,
                "region_id" => 1,
                "name" => "Ikoyi"
            ],
            [
                "id" => 2,
                "region_id" => 1,
                "name" => "Victoria Island"
            ],
            [
                "id" => 3,
                "region_id" => 2,
                "name" => "Ikeja"
            ],
            [
                "id" => 4,
                "region_id" => 2,
                "name" => "Yaba"
            ],
        ];

        $filteredLocations = $regionId ?
            array_values(array_filter($locations, fn($location) => $location['region_id'] == $regionId)) :
            $locations;


        return response()->json(["locations" => $filteredLocations]);
    });

    Route::get('/property-data/sections', function () {
        $locationId = request()->query('location_id');
        $sections = [
            [
                "id" => 1,
                "location_id" => 1,
                "name" => "Ikoyi 1"
            ],
            [
                "id" => 2,
                "location_id" => 1,
                "name" => "Osbourne 1"
            ],
            [
                "id" => 3,
                "location_id" => 2,
                "name" => "Victoria Island 1"
            ],
            [
                "id" => 4,
                "location_id" => 2,
                "name" => "Oniru Estate"
            ],
            [
                "id" => 5,
                "location_id" => 3,
                "name" => "Ikeja GRA"
            ],
            [
                "id" => 6,
                "location_id" => 3,
                "name" => "Opebi"
            ],
            [
                "id" => 7,
                "location_id" => 4,
                "name" => "Akoka"
            ],
            [
                "id" => 8,
                "location_id" => 4,
                "name" => "Yaba"
            ],
        ];


        $filteredSections = $locationId ?
            array_values(array_filter($sections, fn($section) => $section['location_id'] == $locationId)) :
            $sections;


        return response()->json(["sections" => $filteredSections]);
    });

    Route::get('/property-data/lgas', function () {
        $sectionId = request()->query('section_id');
        $lgas = [
            [
                "id" => 1,
                "section_id" => 1,
                "name" => "Eti-Osa"
            ],
            [
                "id" => 2,
                "section_id" => 2,
                "name" => "Eti-Osa"
            ],
            [
                "id" => 3,
                "section_id" => 3,
                "name" => "Eti-Osa"
            ],
            [
                "id" => 4,
                "section_id" => 4,
                "name" => "Eti-Osa"
            ],
            [
                "id" => 5,
                "section_id" => 5,
                "name" => "Ikeja LGA"
            ],
            [
                "id" => 6,
                "section_id" => 6,
                "name" => "Ikeja LGA"
            ],
            [
                "id" => 7,
                "section_id" => 7,
                "name" => "Yaba LGA"
            ],
            [
                "id" => 8,
                "section_id" => 8,
                "name" => "Yaba LGA"
            ],
        ];


        $filteredLgas = $sectionId ?
            array_values(array_filter($lgas, fn($lga) => $lga['section_id'] == $sectionId)) :
            $lgas;


        return response()->json(["lgas" => $filteredLgas]);
    });

    Route::get('/property-data/sub-sectors', function () {
        $sectorId = request()->query('sector_id');
        $sub_sectors =  [
            [
                "id" => 1,
                "sector_id" => 1,
                "name" => "Apartments"
            ],
            [
                "id" => 2,
                "sector_id" => 1,
                "name" => "Detached"
            ],
            [
                "id" => 3,
                "sector_id" => 2,
                "name" => "Office"
            ],
            [
                "id" => 4,
                "sector_id" => 2,
                "name" => "Retail"
            ],
            [
                "id" => 5,
                "sector_id" => 3,
                "name" => "Vacant Land"
            ],
            [
                "id" => 6,
                "sector_id" => 3,
                "name" => "Existing Structure"
            ],
            [
                "id" => 7,
                "sector_id" => 4,
                "name" => "Hospital"
            ],
            [
                "id" => 8,
                "sector_id" => 5,
                "name" => "Lounge"
            ],
            [
                "id" => 9,
                "sector_id" => 6,
                "name" => "Hotel"
            ],
        ];

        $filteredSubSectors = $sectorId ?
            array_values(array_filter($sub_sectors, fn($lga) => $lga['sector_id'] == $sectorId)) :
            $sub_sectors;

        return response()->json(["sub_sectors" => $filteredSubSectors]);
    });
});
