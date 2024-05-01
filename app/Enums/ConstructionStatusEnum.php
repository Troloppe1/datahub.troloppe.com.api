<?php

namespace App\Enums;

enum ConstructionStatusEnum: int {
    case ON_HOLD = 1;
    case COMPLETED = 2;
    case UNDER_CONSTRUCTION = 3;

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

