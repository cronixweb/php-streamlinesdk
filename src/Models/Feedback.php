<?php

namespace Cronixweb\Streamline\Models;

use Cronixweb\Streamline\Traits\StreamlineModel;

class Feedback extends StreamlineModel
{
    public function __construct(
        public readonly ?int    $id = 0,
        public readonly ?int    $unit_id = 0,
        public readonly ?int    $status_id = 0,
        public readonly ?int    $rating_id = 0,
        public readonly ?int    $client_id = 0,
        public readonly ?int    $company_id = 0,
        public readonly ?int    $location_id = 0,
        public readonly ?string $creation_date = '',
        public readonly ?string $email = '',
        public readonly ?string $first_name = '',
        public readonly ?string $last_name = '',
        public readonly ?string $title = '',
        public readonly ?string $comments = '',
        public readonly ?string $admin_comments = '',
        public readonly ?int    $reservation_id = 0,
        public readonly ?int    $show_in_owner_area = 0,
        public readonly ?int    $show_in_site = 0,
        public readonly ?int    $madetype_id = 0,
        public readonly ?string $creation_date_system = '',
        public readonly ?string $survey_result_id = null,
        public readonly ?string $original_id = null,
        public readonly ?int    $use_streamshare = 0,
        public readonly ?string $startdate = null,
        public readonly ?string $enddate = null,
        public readonly ?string $name = '',
        public readonly ?string $seo_page_name = '',
        public readonly ?string $location_name = '',
        public readonly ?int    $points = 0,
        public readonly ?string $madetype_name = ''
    )
    {
    }

    public static function parse(array $data): Feedback
    {
        $toInt = fn($v) => isset($v) ? (int)$v : null;
        $toString = fn($v) => isset($v) ? (string)$v : null;

        return new Feedback(
            id: $toInt($data['id'] ?? 0),
            unit_id: $toInt($data['unit_id'] ?? 0),
            status_id: $toInt($data['status_id'] ?? 0),
            rating_id: $toInt($data['rating_id'] ?? 0),
            client_id: $toInt($data['client_id'] ?? 0),
            company_id: $toInt($data['company_id'] ?? 0),
            location_id: $toInt($data['location_id'] ?? 0),
            creation_date: $toString($data['creation_date'] ?? ''),
            email: $toString($data['email'] ?? ''),
            first_name: $toString($data['first_name'] ?? ''),
            last_name: $toString($data['last_name'] ?? ''),
            title: $toString($data['title'] ?? ''),
            comments: $toString($data['comments'] ?? ''),
            admin_comments: $toString($data['admin_comments'] ?? ''),
            reservation_id: $toInt($data['reservation_id'] ?? 0),
            show_in_owner_area: $toInt($data['show_in_owner_area'] ?? 0),
            show_in_site: $toInt($data['show_in_site'] ?? 0),
            madetype_id: $toInt($data['madetype_id'] ?? 0),
            creation_date_system: $toString($data['creation_date_system'] ?? ''),
            survey_result_id: $data['survey_result_id'] ?? null,
            original_id: $data['original_id'] ?? null,
            use_streamshare: $toInt($data['use_streamshare'] ?? 0),
            startdate: $data['startdate'] ?? null,
            enddate: $data['enddate'] ?? null,
            name: $toString($data['name'] ?? ''),
            seo_page_name: $toString($data['seo_page_name'] ?? ''),
            location_name: $toString($data['location_name'] ?? ''),
            points: $toInt($data['points'] ?? 0),
            madetype_name: $toString($data['madetype_name'] ?? '')
        );
    }
}