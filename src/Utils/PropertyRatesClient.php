<?php

namespace Cronixweb\Streamline\Utils;

use Cronixweb\Streamline\Exceptions\StreamlineApiException;
use Cronixweb\Streamline\Models\PropertyRate;
use Cronixweb\Streamline\Traits\ModelClient;
use Illuminate\Http\Client\ConnectionException;
use InvalidArgumentException;

class PropertyRatesClient extends ModelClient
{
    protected string $modelName = PropertyRate::class;
    protected string $primaryKey = 'unit_id';
    protected string $findOneMethod = '';
    protected string $findAllMethod = 'GetPropertyRates';

    public function __construct(private readonly StreamlineClient $client, private readonly int $unitId = 0)
    {
        parent::__construct($client);
    }

    public static function for(PropertiesClient $client, int $unitId): self
    {
        return new self($client->client(), $unitId);
    }

    public function all($body = []): array
    {
        // unit_id can come from the scoped PropertiesClient->propertyRates($unitId)
        // or be provided directly in the body when using Streamline::propertyRates()
        $unitId = $this->unitId > 0 ? $this->unitId : ($body['unit_id'] ?? 0);
        if ($unitId <= 0) {
            throw new InvalidArgumentException('unit_id is required and must be a positive integer');
        }

        $startDate = $body['startDate'] ?? null;
        $endDate = $body['endDate'] ?? null;
        if (!$startDate || !$endDate) {
            throw new InvalidArgumentException('startDate and endDate are required (MM/DD/YYYY)');
        }

        // Map optional flags and inputs (all optional)
        $useRoomTypeLogic = $body['use_room_type_logic'] ?? null;
        $dailyChangeOver = $body['dailyChangeOver'] ?? null;
        $useHomeawayMaxDaysNotice = $body['use_homeaway_max_days_notice'] ?? null;
        // Accept either rate_types => [id => [..]] or a flat rate_type_ids => [..]
        $rateTypeIds = $body['rate_types']['id'] ?? ($body['rate_type_ids'] ?? null);
        $showLosIfEnabled = $body['show_los_if_enabled'] ?? null;
        $maxLosStay = $body['max_los_stay'] ?? null;
        $useAdvLogicIfDefined = $body['use_adv_logic_if_defined'] ?? null;

        return $this->getPropertyRates(
            $unitId,
            $startDate,
            $endDate,
            $useRoomTypeLogic,
            $dailyChangeOver,
            $useHomeawayMaxDaysNotice,
            $rateTypeIds,
            $showLosIfEnabled,
            $maxLosStay,
            $useAdvLogicIfDefined,
        );
    }

    /**
     * Fetch property rates for a given unit and date range.
     *
     * @param int $unitId
     * @param string $startDate MM/DD/YYYY
     * @param string $endDate MM/DD/YYYY
     * @param bool|null $useRoomTypeLogic
     * @param bool|null $dailyChangeOver
     * @param bool|null $useHomeawayMaxDaysNotice
     * @param int[]|null $rateTypeIds
     * @param bool|null $showLosIfEnabled
     * @param int|null $maxLosStay 1-180, requires show_los_if_enabled=true
     * @param bool|null $useAdvLogicIfDefined
     * @return PropertyRate[]
     * @throws StreamlineApiException
     * @throws ConnectionException
     */
    public function getPropertyRates(
        int    $unitId,
        string $startDate,
        string $endDate,
        ?bool  $useRoomTypeLogic = null,
        ?bool  $dailyChangeOver = null,
        ?bool  $useHomeawayMaxDaysNotice = null,
        ?array $rateTypeIds = null,
        ?bool  $showLosIfEnabled = null,
        ?int   $maxLosStay = null,
        ?bool  $useAdvLogicIfDefined = null,
    ): array
    {
        if ($unitId <= 0) {
            throw new InvalidArgumentException('unit_id must be a positive integer');
        }
        if (!self::isValidDate($startDate) || !self::isValidDate($endDate)) {
            throw new InvalidArgumentException('startdate and enddate must be in MM/DD/YYYY format');
        }
        if ($dailyChangeOver && $useHomeawayMaxDaysNotice) {
            throw new InvalidArgumentException('dailyChangeOver and use_homeaway_max_days_notice cannot be used together');
        }
        if ($maxLosStay !== null) {
            if ($maxLosStay < 1 || $maxLosStay > 180) {
                throw new InvalidArgumentException('max_los_stay must be between 1 and 180');
            }
            if (!$showLosIfEnabled) {
                throw new InvalidArgumentException('max_los_stay requires show_los_if_enabled=true');
            }
        }

        $params = [
            'unit_id' => $unitId,
            'startdate' => $startDate,
            'enddate' => $endDate,
        ];

        $boolFlag = fn(?bool $v) => $v === null ? null : ($v ? 1 : 0);
        $flags = [
            'use_room_type_logic' => $boolFlag($useRoomTypeLogic),
            'dailyChangeOver' => $boolFlag($dailyChangeOver),
            'use_homeaway_max_days_notice' => $boolFlag($useHomeawayMaxDaysNotice),
            'show_los_if_enabled' => $boolFlag($showLosIfEnabled),
            'use_adv_logic_if_defined' => $boolFlag($useAdvLogicIfDefined),
        ];
        foreach ($flags as $k => $v) {
            if ($v !== null) {
                $params[$k] = $v;
            }
        }

        if ($maxLosStay !== null) {
            $params['max_los_stay'] = $maxLosStay;
        }

        if ($rateTypeIds !== null && is_array($rateTypeIds) && !empty($rateTypeIds)) {
            // Best-effort structure based on docs; API often accepts { rate_types: { id: [..] } }
            $ids = [];
            foreach ($rateTypeIds as $id) {
                if (is_int($id) && $id > 0) {
                    $ids[] = $id;
                }
            }
            if (!empty($ids)) {
                $params['rate_types'] = ['id' => $ids];
            }
        }

        $data = $this->client->request('GetPropertyRates', $params);

        // Response can be multiple <data> nodes; $data may be a list or single assoc
        $items = [];
        if (is_array($data)) {
            if ($this->isAssoc($data)) {
                // Single row
                $items = [$data];
            } else {
                // List of rows
                $items = $data;
            }
        }

        $result = [];
        foreach ($items as $item) {
            // Some implementations wrap fields inside a 'data' key; handle defensively
            if (isset($item['season']) || isset($item['date'])) {
                $result[] = PropertyRate::parse($item);
            } elseif (isset($item['data']) && is_array($item['data'])) {
                $result[] = PropertyRate::parse($item['data']);
            }
        }
        return $result;
    }

    private static function isValidDate(string $date): bool
    {
        return (bool)preg_match('/^(0[1-9]|1[0-2])\/(0[1-9]|[12][0-9]|3[01])\/\d{4}$/', $date);
    }

    private function isAssoc(array $arr): bool
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
}
