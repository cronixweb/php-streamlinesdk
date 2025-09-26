# Streamline VRS PHP SDK

A PHP SDK for interacting with Streamline VRS JSON API from your Laravel application. It provides convenient, typed clients to access properties, amenities, reviews, gallery images, booked days, and property rates, plus helper utilities like token renewal.

Status: Actively evolving. Please review the Requirements and Notes carefully before integrating.


## Table of Contents
- Overview
- Features
- Requirements
- Installation
- Configuration
- Quick Start
- Usage
  - Properties
  - Amenities
  - Gallery Images
  - Reviews
  - Booked/Blocked Days
  - Property Rates
  - Token Renewal
- Error Handling
- Date Formats and Common Parameters
- FAQ and Notes
- Contributing
- License
- Resources and Bibliography


## Overview
This SDK is a thin wrapper around the Streamline VRS JSON API (https://web.streamlinevrs.com/api/json). It handles request building, authentication parameters, common parsing patterns, and provides small model classes for consistency.

Namespaces used by the code are under `Cronixweb\Streamline\...` (for example, `Cronixweb\Streamline\Streamline`).


## Features
- Simple entry point to authenticate and obtain service-specific clients.
- Methods for common API endpoints:
  - GetPropertyList, GetPropertyInfo
  - GetPropertyAmenities
  - GetPropertyGalleryImages
  - GetGuestReviews
  - GetBlockedDaysForUnit
  - GetPropertyRates
  - RenewExpiredToken
- Light model parsers to normalize typical response structures.
- Defensive handling of API variations (singular vs list response nodes).


## Requirements
- PHP: 8.2 or higher (aligned with Illuminate 12.x requirements)
- Laravel or an environment where Illuminate HTTP client and Facades are available
  - This SDK uses `Illuminate\Support\Facades\Http` internally
- Composer

Note on namespaces/autoloading:
- The code namespaces are `Cronixweb\Streamline\...`.
- Ensure your composer autoload maps `Cronixweb\\Streamline\\` to the `src/` directory when consuming this package. If you use this package via Packagist/Composer, verify the autoload section points to this namespace.


## Installation
Install via Composer in your Laravel project:

- If this package is published to Packagist as `cronixweb/streamline-sdk`:
  composer require cronixweb/streamline-sdk

- For local development (path repository), add to your project's composer.json repositories and require it accordingly. Ensure autoload maps `Cronixweb\\Streamline\\` to `vendor/cronixweb/streamline-sdk/src`.

After installation, run:
  composer dump-autoload


## Configuration
This SDK requires Streamline API credentials: `token_key` and `token_secret`.

Recommended: store them in environment variables and your Laravel config, e.g.:
- .env:
  STREAMLINE_TOKEN_KEY=your_token_key
  STREAMLINE_TOKEN_SECRET=your_token_secret

- config/services.php:
  'streamline' => [
      'token_key' => env('STREAMLINE_TOKEN_KEY'),
      'token_secret' => env('STREAMLINE_TOKEN_SECRET'),
  ],


## Quick Start
Example in a Laravel controller or service class:

use Cronixweb\Streamline\Streamline;

$apiKey = config('services.streamline.token_key');
$apiSecret = config('services.streamline.token_secret');

$streamline = Streamline::api($apiKey, $apiSecret);

// Fetch first page of properties
$properties = $streamline->properties()->all();

// Get one property by unit_id
$property = $streamline->properties()->find(12345);


## Usage
Below are examples for each available client. All examples assume you already instantiated `Streamline` as shown in Quick Start.

Important: Unless otherwise noted, date inputs use the format MM/DD/YYYY.


### Properties
- List properties:
  $properties = $streamline->properties()->all();

- Get one property by unit_id:
  $property = $streamline->properties()->find(12345);

- Traverse to related clients for a specific property (scoped helpers):
  $propClient = $streamline->properties();
  $amenitiesClient = $propClient->amenities(12345);      // AmenitiesClient
  $galleryClient   = $propClient->galleryImages(12345);  // GalleryImagesClient
  $reviewsClient   = $propClient->reviews(12345);        // ReviewsClient
  $ratesClient     = $propClient->propertyRates(12345);  // PropertyRatesClient


### Amenities
- Get amenities for a unit:
  $amenities = $streamline->properties()->amenities(12345)->all();
  // or using the direct method
  // $amenities = $streamline->amenities()->getPropertyAmenities(12345);


### Gallery Images
- Get gallery images for a unit:
  $images = $streamline->properties()->galleryImages(12345)->all();
  // or
  // $images = $streamline->galleryImages()->getPropertyGalleryImages(12345);

Each item is parsed into a GalleryImage model-like array/object, depending on the specific parser; the fields reflect the API response.


### Reviews
- Get guest reviews (optionally filter by housekeeper_id, unit_id, return_all):
  use Cronixweb\Streamline\Utils\ReviewsClient; // if you need types

  // Scoped to a unit via properties client
  $reviews = $streamline->properties()->reviews(12345)->all();

  // Or use method with filters
  $reviews = $streamline->reviews()->getGuestReviews(
      housekeeperId: null, // e.g. 789
      unitId: 12345,
      returnAll: true
  );


### Booked/Blocked Days
- Fetch blocked days for one unit:
  $blocked = $streamline->bookedDates()->getBlockedDaysForUnit(
      unitId: 12345,
      startdate: '01/01/2025', // optional
      enddate: '01/31/2025',   // optional
      displayB2BBlocks: true,  // optional
      allowInvalid: false,     // optional
      owningId: null           // optional, only for single unit
  );

- Fetch blocked days for multiple units (enddate required when multiple ids):
  $blocked = $streamline->bookedDates()->getBlockedDaysForUnit(
      unitId: [12345, 67890],
      startdate: '01/01/2025',
      enddate: '01/31/2025',
      displayB2BBlocks: true,
      allowInvalid: false
  );

Returns an array of BookedDate items parsed from the API.


### Property Rates
There are two ways to fetch rates:

1) Using the high-level all() which validates inputs and maps options:
  $rates = $streamline->propertyRates()->all([
      'unit_id' => 12345,
      'startdate' => '01/01/2025',
      'enddate' => '01/31/2025',
      // Optional flags:
      'use_room_type_logic' => true,
      'dailyChangeOver' => false,
      'use_homeaway_max_days_notice' => false,
      // Provide either of the following:
      // 'rate_type_ids' => [1, 2, 3],
      // or nested form
      // 'rate_types' => ['id' => [1, 2, 3]],
      'show_los_if_enabled' => true,
      'max_los_stay' => 14, // requires show_los_if_enabled = true
      'use_adv_logic_if_defined' => false,
  ]);

2) Using the explicit method with typed parameters:
  $rates = $streamline->propertyRates()->getPropertyRates(
      unitId: 12345,
      startdate: '01/01/2025',
      enddate: '01/31/2025',
      useRoomTypeLogic: null,
      dailyChangeOver: null,
      useHomeawayMaxDaysNotice: null,
      rateTypeIds: [1, 2],
      showLosIfEnabled: true,
      maxLosStay: 14,
      useAdvLogicIfDefined: null
  );

Returned items are parsed into PropertyRate models.

Validation rules enforced by the client:
- unit_id must be a positive integer
- startdate and enddate must be in MM/DD/YYYY format
- dailyChangeOver and use_homeaway_max_days_notice cannot be used together
- If max_los_stay is set, it must be 1–180 and requires show_los_if_enabled=true


### Token Renewal
- You can refresh/rotate your token pair via:
  $new = $streamline->refreshToken();
  // returns: ['apikey' => '...', 'apiSecret' => '...']
  // The Streamline instance automatically updates its internal client.

Persist the new credentials in your app (e.g., save to database or update env variables) if needed.


## Error Handling
Most client methods may throw:
- Cronixweb\Streamline\Exceptions\StreamlineApiException when the API returns an error or the response is malformed.
- Illuminate\Http\Client\ConnectionException for transport-level issues.
- InvalidArgumentException when you pass invalid inputs (e.g., bad dates, missing unit_id, etc.).

Recommended handling pattern:

try {
    $rates = $streamline->propertyRates()->getPropertyRates(12345, '01/01/2025', '01/31/2025');
} catch (\Cronixweb\Streamline\Exceptions\StreamlineApiException $e) {
    // Handle API-level error
} catch (\Illuminate\Http\Client\ConnectionException $e) {
    // Handle network/transport error
} catch (\InvalidArgumentException $e) {
    // Handle input validation error
}


## Date Formats and Common Parameters
- Dates are expected as strings in MM/DD/YYYY; the SDK performs basic validation where relevant.
- Several boolean flags are sent to the API as 1/0 per Streamline conventions; the SDK converts true/false accordingly.
- Some endpoints accept multiple unit_ids; the SDK handles array to comma-separated string conversion as required.


## FAQ and Notes
- Is this Laravel-only?
  - The SDK uses `Illuminate\Support\Facades\Http`. In practice, this means it’s best used inside a Laravel application where the HTTP client and facades are bootstrapped. Using it in a plain PHP project would require additional bootstrapping of the container and facades.

- What about pagination?
  - If Streamline endpoints support pagination, you can pass the appropriate params through the `all($body)` methods. This SDK does not currently abstract pagination.

- Why do some responses flatten or wrap data differently?
  - The Streamline API sometimes returns either a single object or an array depending on results. The SDK normalizes common cases to consistent lists for convenience.

- Namespace note
  - Code uses `Cronixweb\Streamline\...` namespaces. Ensure your autoloader maps that namespace to the `src/` directory when consuming this package.


## Contributing
- Issues and PRs are welcome. Please describe the problem clearly and include reproducible examples when possible.


## License
- MIT (or the license specified for this repository).


## Resources and Bibliography
- Streamline VRS API JSON Docs: https://partner.streamlinevrs.com/apidocs/api-group
- Streamline VRS general developer portal or documentation as provided by Streamline.
- Laravel HTTP Client (Illuminate 12.x): https://laravel.com/docs/12.x/http-client
- PSR-4 Autoloading (Composer): https://getcomposer.org/doc/04-schema.md#psr-4
- PHP Manual: https://www.php.net/manual/en/

Last updated: 2025-09-26
