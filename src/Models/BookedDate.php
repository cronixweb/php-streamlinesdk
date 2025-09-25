<?php

namespace Cronixweb\Streamline\Models;

use Cronixweb\Streamline\Traits\StreamlineModel;

class BookedDate extends StreamlineModel
{
    public function __construct(
        public readonly ?string $confirmation_id = null,
        public readonly ?string $startdate = null,
        public readonly ?string $enddate = null,
        public readonly ?string $checkout = null,
        public readonly ?int $type_id = null,
        public readonly ?string $type_name = null,
        public readonly ?string $type_description = null,
    ) {}

    public static function parse(array $data): BookedDate
    {
        return new BookedDate(
            confirmation_id: $data['confirmation_id'] ?? null,
            startdate: $data['startdate'] ?? null,
            enddate: $data['enddate'] ?? null,
            checkout: $data['checkout'] ?? null,
            type_id: isset($data['type_id']) ? (int) $data['type_id'] : null,
            type_name: $data['type_name'] ?? null,
            type_description: $data['type_description'] ?? null,
        );
    }
}
