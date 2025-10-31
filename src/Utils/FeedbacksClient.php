<?php

namespace Cronixweb\Streamline\Utils;

use Cronixweb\Streamline\Exceptions\StreamlineApiException;
use Cronixweb\Streamline\Models\Feedback;
use Cronixweb\Streamline\Traits\ModelClient;
use Illuminate\Http\Client\ConnectionException;
use InvalidArgumentException;

class FeedbacksClient extends ModelClient
{
    protected string $modelName = Feedback::class;
    protected string $primaryKey = 'id';
    protected string $findOneMethod = '';
    protected string $findAllMethod = 'GetAllFeedback';
    protected string $dataKey = 'comments';
    protected string $responseKey = 'comments';

    public function __construct(
        private readonly StreamlineClient $client,
        private readonly int $unitId = 0
    ) {
        parent::__construct($client);
    }

    /**
     * Initialize Feedback client for a specific property (unit)
     */
    public static function for(PropertiesClient $client, int $unitId): self
    {
        return new self($client->client(), $unitId);
    }

    /**
     * Retrieve all feedback records for a given unit (or all units if allowed)
     *
     * @param array $body
     * @return array
     * @throws StreamlineApiException
     * @throws ConnectionException
     */
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
     * Get feedback (reviews) for a specific property (unit)
     *
     * @param int $unitId
     * @param array $options
     * @return array
     * @throws StreamlineApiException
     * @throws ConnectionException
     */
    public function getUnitFeedback(int $unitId, array $options = []): array
    {
        if ($unitId <= 0) {
            throw new InvalidArgumentException('unit_id must be a positive integer');
        }

       $data = $this->client->request('GetAllFeedback', [
            'unit_id' => $unitId,
            ...$options,
        ]);
        
        return $data;

    }
}
