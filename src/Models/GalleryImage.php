<?php

namespace Cronixweb\Streamline\Models;

use Cronixweb\Streamline\Traits\StreamlineModel;

class GalleryImage extends StreamlineModel
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $description = null,
        public readonly ?string $original_path = null,
        public readonly ?string $image_path = null,
        public readonly ?string $thumbnail_path = null,
    ) {}

    public static function parse(array $data): GalleryImage
    {
        return new GalleryImage(
            id: isset($data['id']) ? (int) $data['id'] : null,
            description: $data['description'] ?? null,
            original_path: $data['original_path'] ?? null,
            image_path: $data['image_path'] ?? null,
            thumbnail_path: $data['thumbnail_path'] ?? null,
        );
    }
}
