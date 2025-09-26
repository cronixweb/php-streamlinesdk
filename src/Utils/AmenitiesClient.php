<?php

namespace Cronixweb\Streamline\Utils;

use Cronixweb\Streamline\Exceptions\StreamlineApiException;
use Cronixweb\Streamline\Models\Amenity;
use Cronixweb\Streamline\Traits\ModelClient;
use Illuminate\Http\Client\ConnectionException;
use InvalidArgumentException;

class AmenitiesClient extends ModelClient
{
    protected string $modelName = Amenity::class;
    protected string $primaryKey = 'amenity_id';
    protected string $findOneMethod = 'GetPropertyAmenities';
    protected string $findAllMethod = 'GetAmenities';

    public function __construct(private readonly StreamlineClient $client, private readonly int $unitId = 0)
    {
        parent::__construct($client);
    }

    public static function for(PropertiesClient $client, int $unitId)
    {
        return new self($client->client(), $unitId);
    }

    public function all($body = []): array
    {
        if ($this->unitId >= 0) {
            return parent::all([
                'unit_id' => $this->unitId,
                ...$body,
            ]);
        }

        return parent::all($body);
    }

    /**
     * Get amenities for a specific property (unit) by unit_id.
     *
     * @param int $unitId
     * @return array
     * @throws StreamlineApiException
     * @throws ConnectionException
     */
    public function getPropertyAmenities(int $unitId): array
    {
        if ($unitId <= 0) {
            throw new InvalidArgumentException('unit_id must be a positive integer');
        }

        $data = $this->client->request('GetPropertyAmenities', [
            'unit_id' => $unitId,
        ]);

        // The API returns an array of amenity items under data
        return $data;
    }
}
