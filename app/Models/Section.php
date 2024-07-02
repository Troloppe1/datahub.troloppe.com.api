<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Section extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location_id'];

    public function location(): BelongsTo{
        return $this->belongsTo(Location::class);
    }

    public function streetData(): HasMany
    {
        return $this->hasMany(StreetData::class);
    }
}
