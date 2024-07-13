<?php

namespace App\Services;

class StreetDataService
{
    private $uniqueCodeQueryStmt = "SELECT T2.latest_id as id, T2.value, T1.location_id from street_data T1 JOIN (SELECT unique_code as value, MAX(id) as latest_id from street_data where deleted_at IS NULL GROUP BY unique_code ) T2 ON T1.id = T2.latest_id WHERE T2.value IS NOT NULL;";
    
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