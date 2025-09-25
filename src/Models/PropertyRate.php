<?php

namespace Cronixweb\Streamline\Models;

use Cronixweb\Streamline\Traits\StreamlineModel;

class PropertyRate extends StreamlineModel
{
    public function __construct(
        public readonly ?string $season = '',
        public readonly ?string $date = '', // YYYY-MM-DD per example
        public readonly ?float $rate = 0,
        public readonly ?int $minStay = 0,
        public readonly ?int $booked = 0,
        public readonly ?string $changeOver = '',
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
            season: $data['season'] ?? '',
            date: $data['date'] ?? '',
            rate: $toFloat($data['rate'] ?? ''),
            minStay: $toInt($data['minStay'] ?? ''),
            booked: $toInt($data['booked'] ?? ''),
            changeOver: $data['changeOver'] ?? '',
        );
    }
}
