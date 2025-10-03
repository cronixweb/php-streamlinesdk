<?php

namespace Cronixweb\Streamline\Utils;

use Cronixweb\Streamline\Exceptions\StreamlineApiException;
use Cronixweb\Streamline\Models\Review;
use Cronixweb\Streamline\Traits\ModelClient;
use Illuminate\Http\Client\ConnectionException;
use InvalidArgumentException;

class ReviewsClient extends ModelClient
{
    protected string $modelName = Review::class;
    protected string $primaryKey = 'unit_id';
    protected string $findOneMethod = '';
    protected string $findAllMethod = 'GetGuestReviews';

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
     * Fetch guest reviews (surveys) filtered by optional parameters.
     *
     * @param int|null $housekeeperId
     * @param int|null $unitId
     * @param bool|null $returnAll
     * @return array An array of Review models
     * @throws StreamlineApiException
     * @throws ConnectionException
     */
    public function getGuestReviews(?int $housekeeperId = null, ?int $unitId = null, ?bool $returnAll = null): array
    {
        $params = [];

        if ($housekeeperId !== null) {
            if ($housekeeperId <= 0) {
                throw new InvalidArgumentException('housekeeper_id must be a positive integer when provided');
            }
            $params['housekeeper_id'] = $housekeeperId;
        }

        if ($unitId !== null) {
            if ($unitId <= 0) {
                throw new InvalidArgumentException('unit_id must be a positive integer when provided');
            }
            $params['unit_id'] = $unitId;
        }

        if ($returnAll !== null) {
            // API expects 1/0 according to docs
            $params['return_all'] = $returnAll ? 1 : 0;
        }

        $data = $this->client->request('GetGuestReviews', $params);

        // Normalize to a flat list of review arrays
        $reviewItems = [];
        if (isset($data['reviews'])) {
            $reviews = $data['reviews'];
            if (isset($reviews['review'])) {
                $items = $reviews['review'];
                // It can be a single associative array or a list
                if (array_keys($items) !== range(0, count($items) - 1)) {
                    // single item
                    $reviewItems = [$items];
                } else {
                    $reviewItems = $items;
                }
            }
        }

        // Map to Review models
        $result = [];
        foreach ($reviewItems as $item) {
            $result[] = Review::parse($item);
        }
        return $result;
    }
}
