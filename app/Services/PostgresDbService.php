<?php

namespace App\Services;

use Illuminate\Database\Connection;
use \Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class PostgresDbService
{
    public readonly Connection $dbConn;

    public function __construct()
    {
        $this->dbConn = DB::connection("alt_pgsql");
    }

    /**
     * Initializes a query builder for the "external_listings.listings" table
     * using the PostgreSQL connection.
     * 
     * @param string $table
     *
     * @return Builder Query builder instance.
     */
    public function createQueryBuilder(string $table): Builder
    {
        // Connects to the PostgreSQL database and sets up the query builder for the specified table.
        return $this->dbConn->table($table);
    }
}
