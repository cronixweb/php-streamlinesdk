<?php

namespace Cronixweb\Streamline\Utils;

use Cronixweb\Streamline\Exceptions\StreamlineApiException;
use Cronixweb\Streamline\Models\PreReservationPrice;
use Cronixweb\Streamline\Traits\ModelClient;
use Illuminate\Http\Client\ConnectionException;

class PreReservationPriceClient extends ModelClient
{
    protected string $modelName = PreReservationPrice::class;
    protected string $primaryKey = 'unit_id';
    protected string $findOneMethod = 'GetPreReservationPrice';
    protected string $findAllMethod = '';

    public function __construct(private readonly StreamlineClient $client)
    {
        parent::__construct($client);
    }

    /**
     * Get pre-reservation pricing for a unit.
     *
     * Required: unit_id, startdate, enddate, occupants
     * Optional parameters are passed as described in the API docs.
     *
     * @return PreReservationPrice
     * @throws StreamlineApiException
     * @throws ConnectionException
     */
    public function getPreReservationPrice(
        int $unitId,
        string $startdate,
        string $enddate,
        int $occupants,
        ?int $occupantsSmall = null,
        ?int $pets = null,
        ?int $applyRewardPoints = null,
        ?string $couponCode = null,
        ?int $madetypeId = null,
        ?int $typeId = null,
        ?string $distributorCode = null,
        ?int $pricingModel = null,
        array $optionalFees = [], // array of fee IDs to activate
        ?bool $showPackageAddons = null,
        ?bool $optionalDefaultEnabled = null,
        ?bool $returnPayments = null,
        ?int $paymentTypeId = null,
        ?bool $includeCouponInformation = null,
        ?bool $separateTaxes = null,
        ?string $guestCountryInsuranceValidation = null,
        ?string $guestStateInsuranceValidation = null,
        ?bool $returnNewPricingModelAndRateType = null,
        ?int $rateTypeId = null,
        ?array $adjustRate = null, // [ [date=>mm/dd/yyyy, rate=>float], ...] or associative
        ?bool $guestDepositsShowAll = null,
        ?bool $showDueToday = null,
    ): PreReservationPrice {
        if ($unitId <= 0) {
            throw new \InvalidArgumentException('unit_id must be a positive integer');
        }
        if (!self::isValidDate($startdate) || !self::isValidDate($enddate)) {
            throw new \InvalidArgumentException('startdate and enddate must be in MM/DD/YYYY format');
        }
        if ($occupants <= 0) {
            throw new \InvalidArgumentException('occupants must be a positive integer');
        }

        $params = [
            'unit_id' => $unitId,
            'startdate' => $startdate,
            'enddate' => $enddate,
            'occupants' => $occupants,
        ];

        if ($occupantsSmall !== null) { $params['occupants_small'] = $occupantsSmall; }
        if ($pets !== null) { $params['pets'] = $pets; }
        if ($applyRewardPoints !== null) { $params['apply_reward_points'] = $applyRewardPoints; }
        if ($couponCode !== null) { $params['coupon_code'] = $couponCode; }
        if ($madetypeId !== null) { $params['madetype_id'] = $madetypeId; }
        if ($typeId !== null) { $params['type_id'] = $typeId; }
        if ($distributorCode !== null) { $params['distributor_code'] = $distributorCode; }
        if ($pricingModel !== null) { $params['pricing_model'] = $pricingModel; }
        if ($paymentTypeId !== null) { $params['payment_type_id'] = $paymentTypeId; }
        if ($rateTypeId !== null) { $params['rate_type_id'] = $rateTypeId; }
        if ($guestCountryInsuranceValidation !== null) { $params['guest_country_insurance_validation'] = $guestCountryInsuranceValidation; }
        if ($guestStateInsuranceValidation !== null) { $params['guest_state_insurance_validation'] = $guestStateInsuranceValidation; }

        // Booleans as 1/0 flags per docs
        $boolFlag = function(?bool $v) { return $v === null ? null : ($v ? 1 : 0); };
        $flags = [
            'show_package_addons' => $boolFlag($showPackageAddons),
            'optional_default_enabled' => $boolFlag($optionalDefaultEnabled),
            'return_payments' => $boolFlag($returnPayments),
            'include_coupon_information' => $boolFlag($includeCouponInformation),
            'separate_taxes' => $boolFlag($separateTaxes),
            'return_new_pricing_model_and_rate_type' => $boolFlag($returnNewPricingModelAndRateType),
            'guest_deposits_show_all' => $boolFlag($guestDepositsShowAll),
            'show_due_today' => $boolFlag($showDueToday),
        ];
        foreach ($flags as $k => $v) {
            if ($v !== null) { $params[$k] = $v; }
        }

        // optional_fee_XXXX flags
        foreach ($optionalFees as $feeId) {
            if (is_int($feeId) && $feeId > 0) {
                $params['optional_fee_' . $feeId] = 1;
            }
        }

        // adjust_rate formatting: expects an object map 0:{date,rate},1:{...}
        if ($adjustRate !== null && is_array($adjustRate) && !empty($adjustRate)) {
            $formatted = [];
            $i = 0;
            foreach ($adjustRate as $item) {
                if (is_array($item) && isset($item['date'], $item['rate']) && self::isValidDate($item['date'])) {
                    $formatted[(string)$i] = [
                        'date' => $item['date'],
                        'rate' => $item['rate'],
                    ];
                    $i++;
                }
            }
            if (!empty($formatted)) {
                $params['adjust_rate'] = $formatted;
            }
        }

        $data = $this->client->request('GetPreReservationPrice', $params);

        // Parse into model. API returns fields directly under data
        return PreReservationPrice::parse($data);
    }

    private static function isValidDate(string $date): bool
    {
        return (bool)preg_match('/^(0[1-9]|1[0-2])\/(0[1-9]|[12][0-9]|3[01])\/\d{4}$/', $date);
    }
}
