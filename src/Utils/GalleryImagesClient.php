<?php

namespace Cronixweb\Streamline\Utils;

use Cronixweb\Streamline\Exceptions\StreamlineApiException;
use Cronixweb\Streamline\Models\GalleryImage;
use Cronixweb\Streamline\Traits\ModelClient;
use Illuminate\Http\Client\ConnectionException;
use InvalidArgumentException;

class GalleryImagesClient extends ModelClient
{
    protected string $modelName = GalleryImage::class;
    protected string $primaryKey = 'unit_id';
    protected string $findOneMethod = 'GetPropertyGalleryImages';
    protected string $findAllMethod = '';

    public function __construct(private readonly StreamlineClient $client)
    {
        parent::__construct($client);
    }

    /**
     * Fetch gallery images for a specific property (unit).
     *
     * @param int $unitId
     * @return GalleryImage[]
     * @throws StreamlineApiException
     * @throws ConnectionException
     */
    public function getPropertyGalleryImages(int $unitId): array
    {
        if ($unitId <= 0) {
            throw new InvalidArgumentException('unit_id must be a positive integer');
        }

        $data = $this->client->request('GetPropertyGalleryImages', [
            'unit_id' => $unitId,
        ]);

        // Normalize the returned structure: data.image can be a list or a single item
        $items = [];
        if (isset($data['image'])) {
            $imageData = $data['image'];
            if (array_keys($imageData) !== range(0, count($imageData) - 1)) {
                // single assoc
                $items = [$imageData];
            } else {
                $items = $imageData;
            }
        }

        $result = [];
        foreach ($items as $item) {
            $result[] = GalleryImage::parse($item);
        }
        return $result;
    }
}
