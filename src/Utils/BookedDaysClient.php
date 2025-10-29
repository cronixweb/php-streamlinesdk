<?php

namespace Cronixweb\Streamline\Utils;

use Cronixweb\Streamline\Exceptions\StreamlineApiException;
use Cronixweb\Streamline\Models\BookedDate;
use Cronixweb\Streamline\Traits\ModelClient;
use Illuminate\Http\Client\ConnectionException;
use InvalidArgumentException;

class BookedDaysClient extends ModelClient
{
    protected string $modelName = BookedDate::class;
    protected string $primaryKey = 'unit_id';
    protected string $findAllMethod = 'GetBlockedDaysForUnit';
    protected string $findOneMethod = '';

    /**
     * @param StreamlineClient $client
     * @param int $unitId The unit_id to scope this client to (0 if unscoped)
     */
    public function __construct(private readonly StreamlineClient $client, private readonly int $unitId = 0)
    {
        parent::__construct($client);
    }

    /**
     * Create a new client instance scoped to a specific unit.
     *
     * @param PropertiesClient $client The parent client
     * @param int $unitId The unit_id to scope to
     * @return self
     */
    public static function for(PropertiesClient $client, int $unitId): self
    {
        return new self($client->client(), $unitId);
    }

        /**
     * Fetch blocked days using a parameter array.
     * Implements the 'all()' method expected by ModelClient for findAllMethod.
     *
     * @param array $body Parameters:
     * - 'startdate' (string|null) OPTIONAL: MM/DD/YYYY
     * - 'enddate' (string|null) OPTIONAL: MM/DD/YYYY (required for multiple unit_ids)
     * - 'display_b2b_blocks' (bool|null) OPTIONAL: If true, include no back-to-back booking blocks
     * - 'allow_invalid' (bool|null) OPTIONAL: Include inactive units (for multi-unit searches)
     * - 'owning_id' (int|null) OPTIONAL: Filter for a specific owning record (omit for multi-unit)
     * - 'unit_id' (int|array) OPTIONAL: Overrides client's unit_id. For multi-unit requests.
     * @return BookedDate[]
     * @throws StreamlineApiException
     * @throws ConnectionException
     * @throws InvalidArgumentException
     */
    public function all($body = []): array
    {
        $unitId = $this->unitId > 0 ? $this->unitId : ($body['unit_id'] ?? 0);
        if ($unitId <= 0) {
            throw new InvalidArgumentException('unit_id is required and must be a positive integer');
        }

        return $this->getBlockedDaysForUnit(
            $unitId,
            $body['startdate'] ?? null,
            $body['enddate'] ?? null,
            $body['display_b2b_blocks'] ?? null,
            $body['allow_invalid'] ?? null,
            $body['owning_id'] ?? null
        );
    }

    /**
     * Fetch blocked days for one or multiple units.
     *
     * @param int|array $unitId A single unit_id or an array of unit_ids
     * @param string|null $startdate MM/DD/YYYY
     * @param string|null $enddate MM/DD/YYYY (required when multiple unit_ids are provided)
     * @param bool|null $displayB2BBlocks If true, include no back-to-back booking blocks
     * @param bool|null $allowInvalid Include inactive units (for multi-unit searches)
     * @param int|null $owningId Filter for a specific owning record (omit for multi-unit)
     * @return BookedDate[]
     * @throws StreamlineApiException
     * @throws ConnectionException
     */
    public function getBlockedDaysForUnit(int|array $unitId, ?string $startdate = null, ?string $enddate = null, ?bool $displayB2BBlocks = null, ?bool $allowInvalid = null, ?int $owningId = null): array
    {
        // Normalize unit ids
        $unitIds = is_array($unitId) ? $unitId : [$unitId];
        if (empty($unitIds)) {
            throw new InvalidArgumentException('unit_id must be provided');
        }
        foreach ($unitIds as $id) {
            if (!is_int($id) || $id <= 0) {
                throw new InvalidArgumentException('unit_id values must be positive integers');
            }
        }

        // Validate dates (using static helper)
        if ($startdate !== null && !self::isValidDate($startdate)) {
            throw new InvalidArgumentException('startdate must be in MM/DD/YYYY format');
        }
        if ($enddate !== null && !self::isValidDate($enddate)) {
            throw new InvalidArgumentException('enddate must be in MM/DD/YYYY format');
        }

        // If multiple units, enddate is mandatory per docs
        if (count($unitIds) > 1 && $enddate === null) {
            throw new InvalidArgumentException('enddate is required when requesting multiple unit_ids');
        }

        $params = [];
        $params['unit_id'] = count($unitIds) > 1 ? implode(',', $unitIds) : $unitIds[0];
        if ($startdate !== null) {
            $params['startdate'] = $startdate;
        }
        if ($enddate !== null) {
            $params['enddate'] = $enddate;
        }
        if ($displayB2BBlocks !== null) {
            $params['display_b2b_blocks'] = $displayB2BBlocks ? 1 : 0;
        }
        if ($allowInvalid !== null) {
            $params['allow_invalid'] = $allowInvalid ? 1 : 0;
        }
        if ($owningId !== null && count($unitIds) === 1) {
            $params['owning_id'] = $owningId;
        }

        $data = $this->client->request('GetBlockedDaysForUnit', $params);

        // Normalize response: expected data.blocked_days.blocked to be list or single item
        $items = [];
        if (isset($data['blocked_days'])) {
            $bd = $data['blocked_days'];
            if (isset($bd['blocked'])) {
                $blocked = $bd['blocked'];
                if (is_array($blocked)) {
                    if ($this->isAssoc($blocked)) {
                        $items = [$blocked];
                    } else {
                        $items = $blocked;
                    }
                }
            }
        } else {
            // Fallback: if API returns array directly
            if (isset($data['blocked'])) {
                $blocked = $data['blocked'];
                if ($this->isAssoc($blocked)) {
                    $items = [$blocked];
                } else {
                    $items = $blocked;
                }
            }
        }

        $result = [];
        foreach ($items as $item) {
            $result[] = BookedDate::parse($item);
        }
        return $result;
    }

    /**
     * Helper function to validate date format.
     */
    private static function isValidDate(string $date): bool
    {
        return (bool)preg_match('/^(0[1-9]|1[0-2])\/(0[1-9]|[12][0-9]|3[01])\/\d{4}$/', $date);
    }

    private function isAssoc(array $arr): bool
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
