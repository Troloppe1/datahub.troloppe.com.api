<?php

namespace App\Services;

use App\Models\Location;
use App\Models\Section;
use App\Models\Sector;
use Illuminate\Support\Facades\DB;

class StreetDataService
{
    private $uniqueCodeQueryStmt = "SELECT T2.latest_id as id, T2.value, T1.location_id from street_data T1 JOIN (SELECT unique_code as value, MAX(id) as latest_id from street_data where deleted_at IS NULL GROUP BY unique_code ) T2 ON T1.id = T2.latest_id WHERE T2.value IS NOT NULL;";

    private $searchedStreetDataStmt = "SELECT T2.latest_id as id, T1.street_address as street_address, T1.development_name as development_name, T2.value as unique_code, T1.image_path as image_path from street_data T1 JOIN (SELECT unique_code as value, MAX(id) as latest_id from street_data where deleted_at IS NULL GROUP BY unique_code ) T2 ON T1.id = T2.latest_id WHERE T2.value IS NOT NULL AND (street_address LIKE :street_address_q OR development_name LIKE :development_name_q OR unique_code LIKE :unique_code_q);";

    /**
     * Returns all distinct unique street data codes
     *
     * @return array
     */
    public function getAllUniqueStreetDataCodes(): array
    {
        return DB::select($this->uniqueCodeQueryStmt);
    }

    /**
     * Returns all searched street data options by query
     *
     * @return array
     */
    public function getSearchedStreetDataOptions(string $searchTerm = ""): array
    {
        $searchTerm = "%{$searchTerm}%";
        return DB::select($this->searchedStreetDataStmt, [
            'street_address_q' => $searchTerm,
            'development_name_q' => $searchTerm,
            'unique_code_q' => $searchTerm
        ]);
    }

    public function imagePrefixGenerator(int $sectionId, int $sectorId): string
    {
        $section = Section::find($sectionId);
        $sector = Sector::find($sectorId);
        return str("{$section->name}-{$sector->name}")->replace(" ", "-",)->lower()->value();
    }
}
