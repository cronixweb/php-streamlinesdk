<?php

declare(strict_types=1);

// Composer autoload (adjust path if you move this file)
require_once __DIR__ . '/vendor/autoload.php';

use Cronixweb\Streamline\Models\Amenity;
use Cronixweb\Streamline\Models\BookedDate;
use Cronixweb\Streamline\Models\GalleryImage;
use Cronixweb\Streamline\Models\Property;
use Cronixweb\Streamline\Models\Review;
use \Cronixweb\Streamline\Streamline;

// Read credentials from environment instead of hardcoding
$apiKey = getenv('STREAMLINE_TOKEN_KEY') ?: '';
$apiSecret = getenv('STREAMLINE_TOKEN_SECRET') ?: '';

if (!$apiKey || !$apiSecret) {
    header('Content-Type: text/plain');
    echo "Missing credentials. Please set STREAMLINE_TOKEN_KEY and STREAMLINE_TOKEN_SECRET environment variables.";
    exit(1);
}

$api = Streamline::api($apiKey, $apiSecret);

// List all properties
///** @var Property[] $properties */
//$properties = $api->properties()->all();
//
//// Fetch Property Details
///** @var Property $property */
//$property  = $api->properties()->find(436196);

// Fetch Property Amenities
///** @var Amenity[] $amenities */
//$amenities = $api->properties()->amenities(436196)->all();


// Fetch Property Reviews
///** @var Review[] $reviews */
//$reviews = $api->properties()->reviews(436196)->all();


// Fetch Property Images
///** @var GalleryImage[] $images */
//$images = $api->properties()->galleryImages(436196)->all();

// Fetch Booked Dates
///** @var BookedDate[] $bookedDates */
//$bookedDates = $api->properties()->bookedDays(518614)->all();


//// Fetch Property Rates
//$rates = $api->properties()->propertyRates(518614)->all([
//    'startDate' => '10/30/2025',
//    'endDate' => '11/01/2025',
//]);