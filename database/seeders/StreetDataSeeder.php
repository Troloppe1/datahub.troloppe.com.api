<?php

namespace Database\Seeders;

use App\Models\StreetData;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class StreetDataSeeder extends Seeder
{
    private $streetData = [
        [
            "id" => 1,
            "geolocation" => "https://googlemaps.com",
            "creator_id" => 1,
            "unique_code" => "ST001",
            "street_address" => "123 Main St",
            "description" => "Residential street with single-family homes",
            "sector" => "residential",
            "location_id" => 4,
            "section_id" => 17,
            "number_of_units" => 25,
            "contact_name" => "John Okafor",
            "contact_numbers" => "555-1234",
            "contact_email" => "john.doe@example.com",
            "construction_status" => "completed",
            "is_verified" => true,
            "image_path" => "https://picsum.photos/200/300",
            "created_at" => "2023-01-15 08:00:00"
        ],
        [
            "id" => 2,
            "geolocation" => "https://googlemaps.com",
            "creator_id" => 1,
            "unique_code" => "ST002",
            "street_address" => "456 Elm St",
            "description" => "Mixed-use street with shops and apartments",
            "sector" => "commercial",
            "location_id" => 2,
            "section_id" => 11,
            "number_of_units" => 15,
            "contact_name" => "Jane Smith",
            "contact_numbers" => "555-5678",
            "contact_email" => "jane.smith@example.com",
            "construction_status" => "under_construction",
            "is_verified" => true,
            "image_path" => "https://picsum.photos/200/300",
            "created_at" => "2023-05-20 08:00:00"
        ],
        [
            "id" => 3,
            "geolocation" => "https://googlemaps.com",
            "creator_id" => 2,
            "unique_code" => "ST003",
            "street_address" => "789 Oak St",
            "description" => "Suburban street with townhouses",
            "sector" => "residential",
            "location_id" => 2,
            "section_id" => 10,
            "number_of_units" => 30,
            "contact_name" => "Robert Brown",
            "contact_numbers" => "555-8765",
            "contact_email" => "robert.brown@example.com",
            "construction_status" => "under_construction",
            "is_verified" => false,
            "image_path" => "https://picsum.photos/200/300",
            "created_at" => "2023-09-10 08:00:00"
        ],
        [
            "id" => 4,
            "geolocation" => "https://googlemaps.com",
            "creator_id" => 2,
            "unique_code" => "ST004",
            "street_address" => "101 Pine St",
            "description" => "Industrial street with warehouses",
            "sector" => "industrial",
            "location_id" => 3,
            "section_id" => 14,
            "number_of_units" => 10,
            "contact_name" => "Emily Davis",
            "contact_numbers" => "555-4321",
            "contact_email" => "emily.davis@example.com",
            "construction_status" => "completed",
            "is_verified" => false,
            "image_path" => "https://picsum.photos/200/300",
            "created_at" => "2023-12-01 08:00:00"
        ],
        [
            "id" => 5,
            "geolocation" => "https://googlemaps.com",
            "creator_id" => 1,
            "unique_code" => "ST005",
            "street_address" => "202 Maple St",
            "description" => "Commercial street with office buildings",
            "sector" => "commercial",
            "location_id" => 1,
            "section_id" => 3,
            "number_of_units" => 20,
            "contact_name" => "Michael Johnson",
            "contact_numbers" => "555-6543",
            "contact_email" => "michael.johnson@example.com",
            "construction_status" => "under_construction",
            "is_verified" => true,
            "image_path" => "https://picsum.photos/200/300",
            "created_at" => "2024-02-28 08:00:00"
        ],
        [
            "id" => 6,
            "geolocation" => "https://googlemaps.com",
            "creator_id" => 1,
            "unique_code" => "ST001",
            "street_address" => "123 Main St",
            "description" => "(Latest) Residential street with single-family homes",
            "sector" => "residential",
            "location_id" => 4,
            "section_id" => 17,
            "number_of_units" => 25,
            "contact_name" => "John Okafor",
            "contact_numbers" => "555-1234",
            "contact_email" => "john.doe@example.com",
            "construction_status" => "completed",
            "is_verified" => true,
            "image_path" => "https://picsum.photos/200/300",
            "created_at" => "2023-06-15 10:00:00"
        ],
        [
            "id" => 7,
            "geolocation" => "https://googlemaps.com",
            "creator_id" => 1,
            "unique_code" => "ST002",
            "street_address" => "456 Elm St",
            "description" => "(Latest) Mixed-use street with shops and apartments",
            "sector" => "commercial",
            "location_id" => 2,
            "section_id" => 11,
            "number_of_units" => 15,
            "contact_name" => "Jane Smith",
            "contact_numbers" => "555-5678",
            "contact_email" => "jane.smith@example.com",
            "construction_status" => "under_construction",
            "is_verified" => true,
            "image_path" => "https://picsum.photos/200/300",
            "created_at" => "2023-11-20 08:00:00"
        ],
        [
            "id" => 8,
            "geolocation" => "https://googlemaps.com",
            "creator_id" => 2,
            "unique_code" => "ST003",
            "street_address" => "789 Oak St",
            "description" => "(Latest) Suburban street with townhouses",
            "sector" => "residential",
            "location_id" => 2,
            "section_id" => 10,
            "number_of_units" => 30,
            "contact_name" => "Robert Brown",
            "contact_numbers" => "555-8765",
            "contact_email" => "robert.brown@example.com",
            "construction_status" => "under_construction",
            "is_verified" => false,
            "image_path" => "https://picsum.photos/200/300",
            "created_at" => "2024-03-10 08:00:00"
        ],
        [
            "id" => 9,
            "geolocation" => "https://googlemaps.com",
            "creator_id" => 2,
            "unique_code" => "ST004",
            "street_address" => "101 Pine St",
            "description" => "(Latest) Industrial street with warehouses",
            "sector" => "industrial",
            "location_id" => 3,
            "section_id" => 14,
            "number_of_units" => 10,
            "contact_name" => "Emily Davis",
            "contact_numbers" => "555-4321",
            "contact_email" => "emily.davis@example.com",
            "construction_status" => "completed",
            "is_verified" => false,
            "image_path" => "https://picsum.photos/200/300",
            "created_at" => "2024-5-01 08:00:00"
        ],
        [
            "id" => 10,
            "geolocation" => "https://googlemaps.com",
            "creator_id" => 1,
            "unique_code" => "ST005",
            "street_address" => "202 Maple St",
            "description" => "(Latest) Commercial street with office buildings",
            "sector" => "commercial",
            "location_id" => 1,
            "section_id" => 3,
            "number_of_units" => 20,
            "contact_name" => "Michael Johnson",
            "contact_numbers" => "555-6543",
            "contact_email" => "michael.johnson@example.com",
            "construction_status" => "under_construction",
            "is_verified" => true,
            "image_path" => "https://picsum.photos/200/300",
            "created_at" => "2024-06-28 08:00:00"
        ]
    ];
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach($this->streetData as $streetData){
            StreetData::create($streetData);
        }
    }
}
