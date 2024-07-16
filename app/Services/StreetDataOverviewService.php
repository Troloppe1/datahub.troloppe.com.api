<?php

namespace App\Services;

use App\Models\StreetData;

class StreetDataOverviewService
{
    private $sqlStatementsForVerifiedData = [
        'by_location' => "SELECT T1.name, T2.value from locations T1 JOIN (SELECT location_id, count(*) as value from street_data WHERE is_verified = true AND deleted_at IS NULL  GROUP BY location_id) T2 on T1.id = T2.location_id;",

        'by_staff' => "SELECT T1.name, T2.value FROM users T1 JOIN (SELECT creator_id, count(*) AS value FROM street_data WHERE is_verified = true AND deleted_at IS NULL GROUP BY creator_id) T2 ON T1.id = T2.creator_id WHERE id <> 1;",

        'by_sector' => "SELECT T1.name, T2.value from sectors T1 JOIN (SELECT sector_id, count(*) as value from street_data WHERE is_verified = true AND deleted_at IS NULL GROUP BY sector_id) T2 WHERE T1.id = T2.sector_id;"
    ];

    public function sumForWidgets()
    {
        $totalStreetData = StreetData::count();
        $totalVerifiedStreetData = StreetData::where(['is_verified' => true])->count();
        $userStreetData = auth()->user()->streetData()->count();
        $userVerifiedStreetData = auth()->user()->streetData()->where(['is_verified' => true])->count();

        return [
            "total_street_data" => $totalStreetData,
            "total_verified_street_data" => $totalVerifiedStreetData,
            "user_street_data" => $userStreetData,
            "user_verified_street_data" => $userVerifiedStreetData,
        ];
    }

    public function forVisual()
    {
        $verifiedStreetDataByLocation = \DB::select($this->sqlStatementsForVerifiedData['by_location']);
        $verifiedStreetDataBySector =\DB::select($this->sqlStatementsForVerifiedData['by_sector']);

        return [
            "verified_street_data_by_location" => $verifiedStreetDataByLocation,
            "verified_street_data_by_sector" => $verifiedStreetDataBySector,
        ];
    }
    public function userPerformances()
    {
        $verifiedStreetDataByStaff = \DB::select($this->sqlStatementsForVerifiedData['by_staff']);

        return [
            "verified_street_data_by_staff" => $verifiedStreetDataByStaff,
        ];
    }
}