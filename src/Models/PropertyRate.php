<?php

namespace Cronixweb\Streamline\Models;

use Cronixweb\Streamline\Traits\StreamlineModel;

class PropertyRate extends StreamlineModel
{
    public function __construct(
        public readonly ?string $season = null,
        public readonly ?string $date = null, // YYYY-MM-DD per example
        public readonly ?float $rate = null,
        public readonly ?int $minStay = null,
        public readonly ?int $booked = null,
        public readonly ?string $changeOver = null,
    ) {}

    public static function parse(array $data): PropertyRate
    {
        $toFloat = function ($v) {
            if ($v === '' || $v === null) { return null; }
            return is_numeric($v) ? (float)$v : null;
        };
        $toInt = function ($v) {
            if ($v === '' || $v === null) { return null; }
            return is_numeric($v) ? (int)$v : null;
        };

        return new PropertyRate(
            season: $data['season'] ?? null,
            date: $data['date'] ?? null,
            rate: $toFloat($data['rate'] ?? null),
            minStay: $toInt($data['minStay'] ?? null),
            booked: $toInt($data['booked'] ?? null),
            changeOver: $data['changeOver'] ?? null,
        );
    }
}
