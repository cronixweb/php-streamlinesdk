<?php

namespace Cronixweb\Streamline\Models;

use Cronixweb\Streamline\Traits\StreamlineModel;

class Review extends StreamlineModel
{
    public function __construct(
        public readonly ?int $id = null,
        public readonly ?string $title = null,
        public readonly ?string $initial_text = null,
        public readonly ?int $company_id = null,
        public readonly ?string $full_name = null,
        public readonly ?string $email = null,
        public readonly ?int $survey_id = null,
        public readonly ?int $reservation_id = null,
        public readonly ?int $unit_id = null,
        public readonly ?int $housekeeper_id = null,
        public readonly ?string $comments = null,
        public readonly ?string $creation_date = null,
        public readonly ?string $travelagent_id = null,
        public readonly ?int $status_id = null,
        public readonly ?int $show_in_owner_area = null,
        public readonly ?int $show_in_site = null,
        public readonly ?string $first_name = null,
        public readonly ?string $last_name = null,
        public readonly ?string $last_time_status_changed = null,
        public readonly ?string $comments_for_web = null,
        public readonly ?string $comments_for_owner = null,
        public readonly ?int $published_on_twitter = null,
        public readonly ?int $published_on_facebook = null,
        public readonly ?int $madetype_id = null,
        public readonly ?string $reservation_cross_reference_code = null,
        public readonly ?string $submit_date = null,
    ) {}

    public static function parse(array $data): Review
    {
        return new Review(
            id: isset($data['id']) ? (int) $data['id'] : null,
            title: $data['title'] ?? null,
            initial_text: $data['initial_text'] ?? null,
            company_id: isset($data['company_id']) ? (int) $data['company_id'] : null,
            full_name: $data['full_name'] ?? null,
            email: $data['email'] ?? null,
            survey_id: isset($data['survey_id']) ? (int) $data['survey_id'] : null,
            reservation_id: isset($data['reservation_id']) ? (int) $data['reservation_id'] : null,
            unit_id: isset($data['unit_id']) ? (int) $data['unit_id'] : null,
            housekeeper_id: isset($data['housekeeper_id']) ? (int) $data['housekeeper_id'] : null,
            comments: $data['comments'] ?? null,
            creation_date: $data['creation_date'] ?? null,
            travelagent_id: $data['travelagent_id'] ?? null,
            status_id: isset($data['status_id']) ? (int) $data['status_id'] : null,
            show_in_owner_area: isset($data['show_in_owner_area']) ? (int) $data['show_in_owner_area'] : null,
            show_in_site: isset($data['show_in_site']) ? (int) $data['show_in_site'] : null,
            first_name: $data['first_name'] ?? null,
            last_name: $data['last_name'] ?? null,
            last_time_status_changed: $data['last_time_status_changed'] ?? null,
            comments_for_web: $data['comments_for_web'] ?? null,
            comments_for_owner: $data['comments_for_owner'] ?? null,
            published_on_twitter: isset($data['published_on_twitter']) ? (int) $data['published_on_twitter'] : null,
            published_on_facebook: isset($data['published_on_facebook']) ? (int) $data['published_on_facebook'] : null,
            madetype_id: isset($data['madetype_id']) ? (int) $data['madetype_id'] : null,
            reservation_cross_reference_code: $data['reservation_cross_reference_code'] ?? null,
            submit_date: $data['submit_date'] ?? null,
        );
    }
}
