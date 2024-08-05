<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'abbr', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function name(): Attribute
    {
        return Attribute::make(get: function ($value) {
            return str($value)->title()->value();
        });
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function streetData(): HasMany
    {
        return $this->hasMany(StreetData::class);
    }

    public static function getActiveLocation()
    {
        try {
            return static::where(['is_active' => true])->firstOrFail();
        } catch (\Exception $e) {
            throw new ModelNotFoundException('No Active Location.');
        }

    }
}
