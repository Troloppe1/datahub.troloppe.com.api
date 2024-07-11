<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Auth\Events\Registered;

class CreateUser extends CreateRecord
{
    protected static string $resource = UserResource::class;

    public function create(bool $another = false): void
    {
        parent::create($another);
    }
}
