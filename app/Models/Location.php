<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'abbr', 'is_active'];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class);
    }

    public function name(): Attribute
    {
        return Attribute::make(get: function ($value) {
            return str($value)->title()->value();
        });
    }
}
