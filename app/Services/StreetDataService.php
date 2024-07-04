<?php

namespace App\Services;

use App\Models\StreetData;

class StreetDataService
{
    private $uniqueCodeQueryStmt = "SELECT T1.id, T2.value, T1.location_id from street_data T1, (SELECT unique_code as value, MAX(id) as latestId from street_data GROUP BY unique_code) T2 where id = T2.latestId";


    /**
     * Returns all distinct unique street data codes
     *
     * @return array
     */
    public function getAllUniqueStreetDataCodes(): array
    {
        return \DB::select($this->uniqueCodeQueryStmt);
    }
}