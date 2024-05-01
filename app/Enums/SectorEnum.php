<?php

namespace App\Enums;

enum SectorEnum: int {
    case RESIDENTIAL = 1;
    case  COMMERCIAL = 2;
    case  INDUSTRIAL = 3;
    case  LAND = 4;
    case  HEALTH_CARE = 5;
    case  HOSPITALITY = 6;
    case  RETAIL = 7;
    case EVENTS = 8;
    case EDUCATION = 9;
    case  GOVERNMENT = 10;

    public function label(): string {
        return str($this->name)->lower()->replace('_',' ')->title()->value();
    }

    public static function keyLabel(): array {
        return array_reduce(static::cases(), function($carry, $status){
            $carry[$status->value] = $status->label();
            return $carry;
        }, []);
    }
}

