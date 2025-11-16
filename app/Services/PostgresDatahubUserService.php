<?php

namespace App\Services;

use App\Models\User;
use App\QueryBuilders\PostgresDatahubDbBuilder;
use Illuminate\Database\Query\Builder;

class PostgresDatahubUserService
{
    public function __construct(
        private readonly PostgresDatahubDbBuilder $postgresDatahubDbBuilder,
    ) {}


    public function createUser(User $user): void
    {
        $data = $this->processUserData($user);
        $this->getQueryBuilder()->insert($data);
    }

    public function updateUser(int $userId, User $user)
    {
        $data = $this->processUserData($user);
        $this->getQueryBuilder()->where('id', $userId)->update($data);
    }

    public function deleteUser(int $userId): void
    {
        $this->getQueryBuilder()->delete(['id' => $userId]);
    }

    public function softDeleteUser(int $userId): void
    {
        $this->getQueryBuilder()->where('id', $userId)->update(['deleted_at' => now()]);
    }

    private function processUserData(User $user){
        return [
            'id' => $user->id,
            'email' => $user->email,
            'name' => $user->name,
            'password' => $user->password,
            'created_at' => $user->created_at,
            'updated_at' => $user->updated_at,
        ];
    }
    /**
     * Initializes a query builder for the "external_listings.listings" table
     * using the PostgreSQL connection.
     * 
     * @param string $table
     *
     * @return Builder Query builder instance.
     */
    private function getQueryBuilder(string $table = "public.users"): Builder
    {
        return $this->postgresDatahubDbBuilder->createQueryBuilder($table);
    }
}
