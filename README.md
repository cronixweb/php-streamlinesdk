<p align="center">
  <a href="https://cronixweb.com" target="_blank" rel="noreferrer">
    <img src="https://cronixweb.com/wp-content/uploads/2024/04/Logo-1.svg" alt="Cronix LLC" height="72">
  </a>
</p>

<p align="center">
  <a href="https://www.streamlinevrs.com" target="_blank" rel="noreferrer">
    <strong>Streamline VRS</strong><br/>
    <sub>Vacation Rental Software · All-in-one property management platform for professional managers</sub>
  </a>
</p>

<h1 align="center">Streamline VRS PHP SDK</h1>

<p align="center">
  Laravel-ready, typed PHP SDK for the <a href="https://www.streamlinevrs.com" target="_blank" rel="noreferrer">Streamline Vacation Rental Software (VRS)</a> JSON API,<br>
  engineered by <a href="https://cronixweb.com" target="_blank" rel="noreferrer">Cronix LLC</a>, a full-service eCommerce &amp; digital agency.
</p>

<p align="center">
  <a href="https://packagist.org/packages/cronixweb/streamline-sdk">
    <img src="https://img.shields.io/badge/Packagist-cronixweb%2Fstreamline--sdk-informational" alt="Packagist">
  </a>
  <img src="https://img.shields.io/badge/PHP-8.2%2B-777bb4" alt="PHP 8.2+">
  <img src="https://img.shields.io/badge/Laravel-HTTP%20Client%20Ready-orange" alt="Laravel HTTP">
  <img src="https://img.shields.io/badge/Status-Actively%20Evolving-success" alt="Status: Actively evolving">
  <img src="https://img.shields.io/badge/License-MIT-lightgrey" alt="MIT License">
</p>

---

> Need help integrating <a href="https://www.streamlinevrs.com" target="_blank" rel="noreferrer">Streamline VRS</a> into your Laravel or eCommerce stack?<br>
> Cronix LLC can architect, build, and maintain your integration from end-to-end.<br>
> 👉 <strong><a href="https://cronixweb.com/contact-us" target="_blank" rel="noreferrer">Talk to an expert</a></strong> or email <strong>sales@cronixweb.com</strong>.

---

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [Requirements](#requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Quick Start](#quick-start)
- [Usage](#usage)
  - [Properties](#properties)
  - [Amenities](#amenities)
  - [Gallery Images](#gallery-images)
  - [Reviews](#reviews)
  - [Booked / Blocked Days](#booked--blocked-days)
  - [Property Rates](#property-rates)
  - [Token Renewal](#token-renewal)
- [Error Handling](#error-handling)
- [Date Formats &amp; Common Parameters](#date-formats--common-parameters)
- [FAQ &amp; Notes](#faq--notes)
- [Cronix LLC · Services &amp; Support](#cronix-llc--services--support)
- [Contributing](#contributing)
- [License](#license)
- [Resources &amp; Bibliography](#resources--bibliography)

---

## Overview

**Streamline VRS PHP SDK** is a thin, Laravel-friendly wrapper around the  
[Streamline VRS JSON API](https://web.streamlinevrs.com/api/json).

It focuses on:

- Handling authentication and token renewal
- Building and sending requests via Laravel’s HTTP client
- Parsing common response structures into small, predictable models
- Providing ergonomic, typed clients for the Streamline endpoints you use most

Namespaces follow:

```php
Cronixweb\Streamline\...
```

For example:

```php
use Cronixweb\Streamline\Streamline;
```

> **Note:** This package is not officially affiliated with Streamline VRS.  
> It is maintained by **[Cronix LLC](https://cronixweb.com)**, an eCommerce and digital agency that frequently works with travel, hospitality, and property platforms.

---

## Features

- 🔐 **Simple Streamline authentication entry point**  
  Single `Streamline::api($key, $secret)` to bootstrap all clients against the Streamline VRS API.

- 🔗 **Convenient, typed clients for common resources**
  - Properties: `GetPropertyList`, `GetPropertyInfo`
  - Amenities: `GetPropertyAmenities`
  - Media: `GetPropertyGalleryImages`
  - Reviews: `GetGuestReviews`
  - Availability: `GetBlockedDaysForUnit`
  - Rates: `GetPropertyRates`
  - Auth: `RenewExpiredToken`

- 🧩 **Lightweight model parsing**
  - Normalizes typical response structures into model-like arrays/objects.
  - Handles “single item vs list” response quirks defensively.

- 🧰 **Laravel-native tooling**
  - Uses `Illuminate\Support\Facades\Http` internally.
  - Fits naturally into Laravel services, controllers, jobs, and console commands.

- 🚧 **Actively evolving**
  - Designed to be extended with more endpoints and helpers over time.

---

## Requirements

- **PHP**: `8.2` or higher (aligned with Laravel / Illuminate 12.x)
- **Laravel** (recommended)
  - Or any environment where the Laravel HTTP client & facades are available.
- **Composer**

Namespace & autoload notes:

- SDK namespaces: `Cronixweb\Streamline\...`
- Ensure your Composer autoload maps:

```json
{
  "autoload": {
    "psr-4": {
      "Cronixweb\\Streamline\\": "src/"
    }
  }
}
```

If you install via Packagist, this will typically be preconfigured.

---

## Installation

Install via Composer in your Laravel project:

```bash
composer require cronixweb/streamline-sdk
```

> If you’re still developing locally or using this via a path repository, point your project’s `repositories` section to your checkout and map the namespace to `src/`.

Example (path repository) in your app’s `composer.json`:

```json
{
  "repositories": [
    {
      "type": "path",
      "url": "packages/cronixweb/streamline-sdk"
    }
  ],
  "require": {
    "cronixweb/streamline-sdk": "*"
  }
}
```

Then regenerate the autoloader:

```bash
composer dump-autoload
```

---

## Configuration

The SDK requires your Streamline API credentials: `token_key` and `token_secret`.

**1. Environment variables**

```bash
# .env
STREAMLINE_TOKEN_KEY=your_token_key
STREAMLINE_TOKEN_SECRET=your_token_secret
```

**2. Service configuration**

```php
// config/services.php
return [
    // ...
    'streamline' => [
        'token_key'    => env('STREAMLINE_TOKEN_KEY'),
        'token_secret' => env('STREAMLINE_TOKEN_SECRET'),
    ],
];
```

You can override this with your own config file or secrets manager if needed.

---

## Quick Start

In a Laravel controller, job, or service class:

```php
use Cronixweb\Streamline\Streamline;

$apiKey    = config('services.streamline.token_key');
$apiSecret = config('services.streamline.token_secret');

$streamline = Streamline::api($apiKey, $apiSecret);

// Fetch first page of properties
$properties = $streamline->properties()->all();

// Fetch a single property by unit_id
$property = $streamline->properties()->find(12345);
```

From here you can traverse into scoped clients (amenities, gallery images, etc.) or call dedicated top-level clients.

---

## Usage

> Unless otherwise noted, **dates are in `MM/DD/YYYY` format** as expected by Streamline.

All examples below assume you’ve already instantiated `Streamline` as shown in [Quick Start](#quick-start).

### Properties

List properties:

```php
$properties = $streamline->properties()->all();
```

Get a single property by `unit_id`:

```php
$property = $streamline->properties()->find(12345);
```

Traverse to related, property-scoped clients:

```php
$propClient    = $streamline->properties();
$amenities     = $propClient->amenities(12345)->all();      // AmenitiesClient
$galleryImages = $propClient->galleryImages(12345)->all();  // GalleryImagesClient
$reviews       = $propClient->reviews(12345)->all();        // ReviewsClient
$rates         = $propClient->propertyRates(12345)->all();  // PropertyRatesClient
```

---

### Amenities

Get amenities for a unit:

```php
$amenities = $streamline
    ->properties()
    ->amenities(12345)
    ->all();

// Or via a dedicated client:
// $amenities = $streamline->amenities()->getPropertyAmenities(12345);
```

---

### Gallery Images

Get gallery images for a unit:

```php
$images = $streamline
    ->properties()
    ->galleryImages(12345)
    ->all();

// Or via a dedicated client:
// $images = $streamline->galleryImages()->getPropertyGalleryImages(12345);
```

Each item is parsed into a gallery-image model (array/object), mirroring the underlying API fields.

---

### Reviews

Get guest reviews, optionally scoped and filtered:

```php
use Cronixweb\Streamline\Utils\ReviewsClient; // for type hints, if desired

// Scoped to a unit via properties client
$reviews = $streamline->properties()->reviews(12345)->all();

// Or using the method with filters
$reviews = $streamline->reviews()->getGuestReviews(
    housekeeperId: null,   // e.g. 789
    unitId:       12345,
    returnAll:    true
);
```

---

### Booked / Blocked Days

Fetch blocked days for **one** unit:

```php
$blocked = $streamline->bookedDates()->getBlockedDaysForUnit(
    unitId:           12345,
    startdate:        '01/01/2025', // optional
    enddate:          '01/31/2025', // optional
    displayB2BBlocks: true,         // optional
    allowInvalid:     false,        // optional
    owningId:         null          // optional, single unit only
);
```

Fetch blocked days for **multiple** units (requires `enddate`):

```php
$blocked = $streamline->bookedDates()->getBlockedDaysForUnit(
    unitId:           [12345, 67890],
    startdate:        '01/01/2025',
    enddate:          '01/31/2025',
    displayB2BBlocks: true,
    allowInvalid:     false
);
```

Returns an array of booked/blocked date items parsed from the API.

---

### Property Rates

There are two primary ways to fetch rates.

#### 1. High-level `all()` helper

The `all()` method accepts an options array and handles validation + mapping:

```php
$rates = $streamline->propertyRates()->all([
    'unit_id'                      => 12345,
    'startdate'                    => '01/01/2025',
    'enddate'                      => '01/31/2025',

    // Optional flags:
    'use_room_type_logic'          => true,
    'dailyChangeOver'              => false,
    'use_homeaway_max_days_notice' => false,

    // Provide either of the following (not both):
    // 'rate_type_ids' => [1, 2, 3],
    // or nested:
    // 'rate_types'    => ['id' => [1, 2, 3]],

    'show_los_if_enabled'          => true,
    'max_los_stay'                 => 14,   // requires show_los_if_enabled = true
    'use_adv_logic_if_defined'     => false,
]);
```

#### 2. Explicit typed method

```php
$rates = $streamline->propertyRates()->getPropertyRates(
    unitId:                   12345,
    startdate:                '01/01/2025',
    enddate:                  '01/31/2025',
    useRoomTypeLogic:         null,
    dailyChangeOver:          null,
    useHomeawayMaxDaysNotice: null,
    rateTypeIds:              [1, 2],
    showLosIfEnabled:         true,
    maxLosStay:               14,
    useAdvLogicIfDefined:     null
);
```

**Validation rules enforced by the client:**

- `unit_id` must be a positive integer.
- `startdate` and `enddate` must be in `MM/DD/YYYY` format.
- `dailyChangeOver` and `use_homeaway_max_days_notice` cannot be used together.
- If `max_los_stay` is set:
  - Must be between `1` and `180`.
  - Requires `show_los_if_enabled = true`.

Returned items are parsed into `PropertyRate` model structures.

---

### Token Renewal

Refresh/rotate your token pair:

```php
$new = $streamline->refreshToken();

// Example response:
// [
//     'apikey'    => '...',
//     'apiSecret' => '...',
// ]
```

The `Streamline` instance automatically updates its internal client with the new credentials.  
If you persist credentials externally (DB, secrets store, etc.), make sure to store the new values.

---

## Error Handling

Most client methods may throw:

- `Cronixweb\Streamline\Exceptions\StreamlineApiException`
  - When the API returns an error or an unexpected/malformed payload.
- `Illuminate\Http\Client\ConnectionException`
  - For network/transport-level issues.
- `InvalidArgumentException`
  - When you pass invalid inputs (e.g., bad dates, missing `unit_id`, invalid flags).

Recommended pattern:

```php
use Cronixweb\Streamline\Exceptions\StreamlineApiException;
use Illuminate\Http\Client\ConnectionException;

try {
    $rates = $streamline
        ->propertyRates()
        ->getPropertyRates(12345, '01/01/2025', '01/31/2025');
} catch (StreamlineApiException $e) {
    // Handle API-level error (log, alert, transform to domain exception, etc.)
} catch (ConnectionException $e) {
    // Handle network/transport errors (timeouts, DNS issues, etc.)
} catch (\InvalidArgumentException $e) {
    // Handle invalid input before hitting the API
}
```

---

## Date Formats &amp; Common Parameters

- **Dates**
  - Always `MM/DD/YYYY` strings.
  - The SDK does basic validation where appropriate.
- **Boolean flags**
  - Sent as `1`/`0` integers per Streamline conventions.
- **Multiple IDs**
  - Some endpoints accept multiple `unit_id`s.
  - Arrays are automatically translated to comma-separated strings.

---

## FAQ &amp; Notes

**Is this Laravel-only?**

- The SDK uses `Illuminate\Support\Facades\Http`.  
  It’s designed with Laravel in mind, but any framework/bootstrap that provides the necessary Illuminate components can work.

**What about pagination?**

- If a Streamline endpoint supports pagination, pass the pagination parameters through the relevant `all($body)` or method calls.
- This SDK doesn’t currently abstract pagination; you control page size and offsets directly.

**Why does the SDK sometimes flatten or wrap data differently from the raw API?**

- The Streamline API sometimes returns:
  - A single object for one result, or
  - An array for multiple.
- The SDK normalizes common cases to consistent lists/collections to simplify your application code.

**Namespace reminder**

- All code is under `Cronixweb\Streamline\...`.  
  Ensure your autoloader maps that namespace to `src/` (or equivalent) when consuming the package.

---

## Cronix LLC · Services &amp; Support

This SDK is built and maintained by **[Cronix LLC](https://cronixweb.com)** — a full-service digital marketing and eCommerce development agency specializing in **custom eCommerce websites, apps, and integrations**.

If you’re using **[Streamline Vacation Rental Software](https://www.streamlinevrs.com)** for:

- Vacation rentals
- Travel &amp; hospitality
- Property management platforms
- Custom B2B booking portals

…Cronix can help you:

- Architect Streamline-backed Laravel or headless storefronts
- Build performance-optimized APIs and dashboards
- Design beautiful, conversion-oriented frontends
- Maintain and evolve your integration over time

📞 **Let’s talk about your project**

- Website: https://cronixweb.com  
- Contact: https://cronixweb.com/contact-us  
- Request a quote: https://cronixweb.com/request-a-quote  
- Email: `sales@cronixweb.com`

> Ignite your ideas. Let’s chat and turn them into reality.

---

## Contributing

Contributions are welcome!

1. Fork the repository
2. Create your feature branch: `git checkout -b feature/my-awesome-feature`
3. Commit your changes: `git commit -m "Add my awesome feature"`
4. Push the branch: `git push origin feature/my-awesome-feature`
5. Open a Pull Request describing:
   - The problem
   - Your changes
   - Any breaking impacts or migration notes

Please include reproducible examples or failing tests whenever possible.

---

## License

This SDK is released under the **MIT License**.  
See the `LICENSE` file for full details.

---

## Resources &amp; Bibliography

- Streamline VRS API JSON Docs:  
  https://partner.streamlinevrs.com/apidocs/api-group
- Streamline VRS JSON API endpoint:  
  https://web.streamlinevrs.com/api/json
- Streamline VRS website:  
  https://www.streamlinevrs.com
- Laravel HTTP Client (Illuminate 12.x):  
  https://laravel.com/docs/12.x/http-client
- PSR-4 Autoloading (Composer):  
  https://getcomposer.org/doc/04-schema.md#psr-4
- PHP Manual:  
  https://www.php.net/manual/en/

---

<sub>Last updated: 2025-11-14</sub>
