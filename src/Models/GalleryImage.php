<?php

namespace Cronixweb\Streamline\Models;

use Cronixweb\Streamline\Traits\StreamlineModel;

class GalleryImage extends StreamlineModel
{
    public function __construct(
        public readonly ?int $id = 0,
        public readonly ?string $description = '',
        public readonly ?string $original_path = '',
        public readonly ?string $image_path = '',
        public readonly ?string $thumbnail_path = '',
    ) {}

    public static function parse(array $data): GalleryImage
    {
        return new GalleryImage(
            id: isset($data['id']) ? (int) $data['id'] : '',
            description: $data['description'] ?? '',
            original_path: $data['original_path'] ?? '',
            image_path: $data['image_path'] ?? '',
            thumbnail_path: $data['thumbnail_path'] ?? '',
        );
    }
}
