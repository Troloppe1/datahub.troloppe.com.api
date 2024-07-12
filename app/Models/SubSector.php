<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SubSector extends Model
{
    use HasFactory;

    public function sector(): BelongsTo
    {
        return $this->belongsTo(Sector::class);
    }

    public function streetData(): HasMany
    {
        return $this->hasMany(StreetData::class);
    }
}
