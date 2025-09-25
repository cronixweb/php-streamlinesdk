<?php

namespace Cronixweb\Streamline\Models;

use Cronixweb\Streamline\Traits\StreamlineModel;

class Review extends StreamlineModel
{
    public function __construct(
        public readonly ?int $id = 0,
        public readonly ?string $title = '',
        public readonly ?string $initial_text = '',
        public readonly ?int $company_id = 0,
        public readonly ?string $full_name = '',
        public readonly ?string $email = '',
        public readonly ?int $survey_id = 0,
        public readonly ?int $reservation_id = 0,
        public readonly ?int $unit_id = 0,
        public readonly ?int $housekeeper_id = 0,
        public readonly ?string $comments = '',
        public readonly ?string $creation_date = '',
        public readonly ?string $travelagent_id = '',
        public readonly ?int $status_id = 0,
        public readonly ?int $show_in_owner_area = 0,
        public readonly ?int $show_in_site = 0,
        public readonly ?string $first_name = '',
        public readonly ?string $last_name = '',
        public readonly ?string $last_time_status_changed = '',
        public readonly ?string $comments_for_web = '',
        public readonly ?string $comments_for_owner = '',
        public readonly ?int $published_on_twitter = 0,
        public readonly ?int $published_on_facebook = 0,
        public readonly ?int $madetype_id = 0,
        public readonly ?string $reservation_cross_reference_code = '',
        public readonly ?string $submit_date = '',
    ) {}

    public static function parse(array $data): Review
    {
        return new Review(
            id: isset($data['id']) ? (int) $data['id'] : '',
            title: $data['title'] ?? '',
            initial_text: $data['initial_text'] ?? '',
            company_id: isset($data['company_id']) ? (int) $data['company_id'] : '',
            full_name: $data['full_name'] ?? '',
            email: $data['email'] ?? '',
            survey_id: isset($data['survey_id']) ? (int) $data['survey_id'] : '',
            reservation_id: isset($data['reservation_id']) ? (int) $data['reservation_id'] : '',
            unit_id: isset($data['unit_id']) ? (int) $data['unit_id'] : '',
            housekeeper_id: isset($data['housekeeper_id']) ? (int) $data['housekeeper_id'] : '',
            comments: $data['comments'] ?? '',
            creation_date: $data['creation_date'] ?? '',
            travelagent_id: $data['travelagent_id'] ?? '',
            status_id: isset($data['status_id']) ? (int) $data['status_id'] : '',
            show_in_owner_area: isset($data['show_in_owner_area']) ? (int) $data['show_in_owner_area'] : '',
            show_in_site: isset($data['show_in_site']) ? (int) $data['show_in_site'] : '',
            first_name: $data['first_name'] ?? '',
            last_name: $data['last_name'] ?? '',
            last_time_status_changed: $data['last_time_status_changed'] ?? '',
            comments_for_web: $data['comments_for_web'] ?? '',
            comments_for_owner: $data['comments_for_owner'] ?? '',
            published_on_twitter: isset($data['published_on_twitter']) ? (int) $data['published_on_twitter'] : '',
            published_on_facebook: isset($data['published_on_facebook']) ? (int) $data['published_on_facebook'] : '',
            madetype_id: isset($data['madetype_id']) ? (int) $data['madetype_id'] : '',
            reservation_cross_reference_code: $data['reservation_cross_reference_code'] ?? '',
            submit_date: $data['submit_date'] ?? '',
        );
    }
}
