<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sector extends Model
{
    use HasFactory;

    protected $fillable = ["name"];
    
    public function subSectors(): HasMany
    {
        return $this->hasMany(SubSector::class);
    }
    
    public function streetData(): HasMany
    {
        return $this->hasMany(StreetData::class);
    }
}
