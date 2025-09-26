<?php

namespace Cronixweb\Streamline\Models;

use Cronixweb\Streamline\Traits\StreamlineModel;

class BookedDate extends StreamlineModel
{
    public function __construct(
        public readonly ?string $confirmation_id = '',
        public readonly ?string $startdate = '',
        public readonly ?string $enddate = '',
        public readonly ?string $checkout = '',
        public readonly ?int    $type_id = 0,
        public readonly ?string $type_name = '',
        public readonly ?string $type_description = '',
    )
    {
    }

    public static function parse(array $data): BookedDate
    {
        return new BookedDate(
            confirmation_id: $data['confirmation_id'] ?? '',
            startdate: $data['startdate'] ?? '',
            enddate: $data['enddate'] ?? '',
            checkout: $data['checkout'] ?? '',
            type_id: isset($data['type_id']) ? (int)$data['type_id'] : '',
            type_name: $data['type_name'] ?? '',
            type_description: $data['type_description'] ?? '',
        );
    }
}
