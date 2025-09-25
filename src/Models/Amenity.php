<?php

namespace Cronixweb\Streamline\Models;

use Cronixweb\Streamline\Traits\StreamlineModel;

class Amenity extends StreamlineModel
{
    public function __construct(
        public readonly ?string $group_name = null,
        public readonly ?string $group_description = null,
        public readonly ?string $amenity_name = null,
        public readonly ?string $amenity_description = null,
        public readonly ?int $amenity_id = null,
        public readonly ?string $amenity_show_on_website = null,
    ) {}

    public static function parse(array $data): Amenity
    {
        return new Amenity(
            group_name: $data['group_name'] ?? null,
            group_description: $data['group_description'] ?? null,
            amenity_name: $data['amenity_name'] ?? null,
            amenity_description: $data['amenity_description'] ?? null,
            amenity_id: isset($data['amenity_id']) ? (int) $data['amenity_id'] : null,
            amenity_show_on_website: $data['amenity_show_on_website'] ?? null,
        );
    }
}
