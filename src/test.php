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
} catch (\Throwable $e) {
    echo "Property error: " . $e->getMessage() . "\n";
}

// 2) Amenities for the property
try {
    $amenities = $api->properties($unitId)->amenities()->all();
    echo "Amenities:\n";
    print_r($amenities);
} catch (\Throwable $e) {
    echo "Amenities error: " . $e->getMessage() . "\n";
}

// 3) Guest reviews (surveys)
try {
    $reviews = $api->reviews()->getGuestReviews(unitId: $unitId, returnAll: true);
    echo "Reviews:\n";
    print_r($reviews);
} catch (\Throwable $e) {
    echo "Reviews error: " . $e->getMessage() . "\n";
}

// 4) Gallery images
try {
    $images = $api->galleryImages()->getPropertyGalleryImages($unitId);
    echo "Gallery Images:\n";
    print_r($images);
} catch (\Throwable $e) {
    echo "Gallery images error: " . $e->getMessage() . "\n";
}

// 5) Booked/Blocked dates
try {
    $booked = $api->bookedDates()->getBlockedDaysForUnit($unitId, startdate: '12/05/2019', displayB2BBlocks: true);
    echo "Booked/Blocked Dates:\n";
    print_r($booked);
} catch (\Throwable $e) {
    echo "Booked dates error: " . $e->getMessage() . "\n";
}

// 6) Pre-reservation price
try {
    $price = $api->preReservationPrice()->getPreReservationPrice(
        unitId: $unitId,
        startdate: '01/10/2020',
        enddate: '01/17/2020',
        occupants: 2,
        occupantsSmall: 2,
        pets: 1,
        includeCouponInformation: true,
        separateTaxes: true,
        showDueToday: true
    );
    echo "Pre-Reservation Price:\n";
    print_r($price);
} catch (\Throwable $e) {
    echo "Pre-res price error: " . $e->getMessage() . "\n";
}