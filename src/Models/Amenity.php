<?php

namespace Cronixweb\Streamline\Models;

use Cronixweb\Streamline\Traits\StreamlineModel;

class Amenity extends StreamlineModel
{
    public function __construct(
        public readonly ?string $group_name = '',
        public readonly ?array $group_description = [],
        public readonly ?string $amenity_name = '',
        public readonly ?array $amenity_description = [],
        public readonly ?int    $amenity_id = 0,
        public readonly ?string $amenity_show_on_website = '',
    )
    {
    }

    public static function parse(array $data): Amenity
    {
        return new Amenity(
            group_name: $data['group_name'] ?? '',
            group_description: $data['group_description'] ?? '',
            amenity_name: $data['amenity_name'] ?? '',
            amenity_description: $data['amenity_description'] ?? '',
            amenity_id: isset($data['amenity_id']) ? (int)$data['amenity_id'] : '',
            amenity_show_on_website: $data['amenity_show_on_website'] ?? '',
        );
    }
}
