<?php

use Cronixweb\Streamline\Streamline;

// Replace with your actual API credentials
$apiKey = getenv('STREAMLINE_TOKEN_KEY') ?: '';
$apiSecret = getenv('STREAMLINE_TOKEN_SECRET') ?: '';

$api = Streamline::api($apiKey, $apiSecret);

// Sample unit id for testing
$unitId = 28254; // replace with a real unit id

// 1) Property info
try {
    $property = $api->properties()->find($unitId);
    echo "Property info:\n";
    print_r($property);
} catch (Throwable $e) {
    echo "Property error: " . $e->getMessage() . "\n";
}

// 2) Amenities for the property
try {
    $amenities = $api->properties($unitId)->amenities(23432)->all();
    echo "Amenities:\n";
    print_r($amenities);
} catch (Throwable $e) {
    echo "Amenities error: " . $e->getMessage() . "\n";
}

// 3) Guest reviews (surveys)
try {
    $reviews = $api->properties()->reviews(23432)->all();
    echo "Reviews:\n";
    print_r($reviews);
} catch (Throwable $e) {
    echo "Reviews error: " . $e->getMessage() . "\n";
}

// 4) Gallery images
try {
    $images = $api->properties($unitId)->galleryImages(23432)->all();
    echo "Gallery Images:\n";
    print_r($images);
} catch (Throwable $e) {
    echo "Gallery images error: " . $e->getMessage() . "\n";
}

// 5) Booked/Blocked dates
try {
    $booked = $api->properties($unitId)->propertyRates(23432)->all(['startDate' => '12/05/2019', 'endDate' => '12/05/2020']);
    echo "Booked/Blocked Dates:\n";
    print_r($booked);
} catch (Throwable $e) {
    echo "Booked dates error: " . $e->getMessage() . "\n";
}
