<?php

namespace App\Services;

use \Illuminate\Database\Query\Builder;
use Illuminate\Support\Facades\DB;

class PostgresDbService
{
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
        $postgresDb = DB::connection("alt_pgsql");
        return $postgresDb->table($table);
    }
}
