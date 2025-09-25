<?php

namespace Cronixweb\Streamline\Models;

use Cronixweb\Streamline\Traits\StreamlineModel;

class PreReservationPrice extends StreamlineModel
{
    public function __construct(
        public readonly ?int $unit_id = null,
        public readonly ?float $price = null,
        public readonly ?float $taxes = null,
        public readonly ?float $coupon_discount = null,
        public readonly ?float $total = null,
        public readonly ?float $first_day_price = null,
        public readonly ?string $unit_name = null,
        public readonly ?string $location_name = null,
        public readonly ?int $unit_rewards = null,
        public readonly ?int $company_rewards = null,
        public readonly ?string $reward_points_discount = null,
        public readonly ?string $distribution_channel_currency = null,
        public readonly array $guest_deposits = [],
        public readonly array $required_fees = [],
        public readonly array $optional_fees = [],
        public readonly array $taxes_details = [],
        public readonly array $reservation_days = [],
        public readonly array $security_deposits = [],
        public readonly ?string $security_deposit_text = null,
        public readonly ?int $due_today = null,
    ) {}

    public static function parse(array $data): PreReservationPrice
    {
        // Helper to normalize a node that may be a single item or a list of items
        $normalizeList = function ($node, string $childKey = null): array {
            if (!isset($node)) {
                return [];
            }
            $items = $node;
            if ($childKey !== null && is_array($node) && isset($node[$childKey])) {
                $items = $node[$childKey];
            }
            if (!is_array($items)) {
                return [];
            }
            // If associative, wrap as single
            if (array_keys($items) !== range(0, count($items) - 1)) {
                return [$items];
            }
            return $items;
        };

        // Guest deposits can be returned as single object or multiple under guest_deposits
        $guestDeposits = [];
        if (isset($data['guest_deposits'])) {
            $guestDeposits = $normalizeList($data['guest_deposits']);
        }

        $requiredFees = [];
        if (isset($data['required_fees'])) {
            $requiredFees = $normalizeList($data['required_fees']);
        }

        $optionalFees = [];
        if (isset($data['optional_fees'])) {
            $optionalFees = $normalizeList($data['optional_fees']);
        }

        $taxesDetails = [];
        if (isset($data['taxes_details'])) {
            $taxesDetails = $normalizeList($data['taxes_details']);
        }

        $reservationDays = [];
        if (isset($data['reservation_days'])) {
            $reservationDays = $normalizeList($data['reservation_days']);
        }

        // security_deposits may be an object with multiple security_deposit children
        $securityDeposits = [];
        if (isset($data['security_deposits'])) {
            $securityDeposits = $normalizeList($data['security_deposits'], 'security_deposit');
        }

        return new PreReservationPrice(
            unit_id: isset($data['unit_id']) ? (int) $data['unit_id'] : null,
            price: isset($data['price']) ? (float) $data['price'] : null,
            taxes: isset($data['taxes']) ? (float) $data['taxes'] : null,
            coupon_discount: isset($data['coupon_discount']) ? (float) $data['coupon_discount'] : null,
            total: isset($data['total']) ? (float) $data['total'] : null,
            first_day_price: isset($data['first_day_price']) ? (float) $data['first_day_price'] : null,
            unit_name: $data['unit_name'] ?? null,
            location_name: $data['location_name'] ?? null,
            unit_rewards: isset($data['unit_rewards']) ? (int) $data['unit_rewards'] : null,
            company_rewards: isset($data['company_rewards']) ? (int) $data['company_rewards'] : null,
            reward_points_discount: $data['reward_points_discount'] ?? null,
            distribution_channel_currency: $data['distribution_channel_currency'] ?? null,
            guest_deposits: $guestDeposits,
            required_fees: $requiredFees,
            optional_fees: $optionalFees,
            taxes_details: $taxesDetails,
            reservation_days: $reservationDays,
            security_deposits: $securityDeposits,
            security_deposit_text: $data['security_deposit_text'] ?? null,
            due_today: isset($data['due_today']) ? (int) $data['due_today'] : null,
        );
    }
}
