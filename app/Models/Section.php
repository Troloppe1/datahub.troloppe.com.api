<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Section extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'location_id'];

    public function location(): BelongsTo{
        return $this->belongsTo(Location::class);
    }

    public function name():Attribute{
        return Attribute::make(get: function($value){
            return str($value)->title()->value();
        });
    }
}
