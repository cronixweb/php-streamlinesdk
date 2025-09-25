<?php

namespace Cronixweb\Streamline;

use Cronixweb\Streamline\Utils\AmenitiesClient;
use Cronixweb\Streamline\Utils\BlockedDaysClient;
use Cronixweb\Streamline\Utils\BookedDaysClient;
use Cronixweb\Streamline\Utils\GalleryImagesClient;
use Cronixweb\Streamline\Utils\PreReservationPriceClient;
use Cronixweb\Streamline\Utils\PropertiesClient;
use Cronixweb\Streamline\Utils\PropertyRatesClient;
use Cronixweb\Streamline\Utils\ReviewsClient;
use Cronixweb\Streamline\Utils\StreamlineClient;

class Streamline
{

    private StreamlineClient $client;

    private function __construct(string $apikey,string $apiSecret)
    {
        $this->client = new StreamlineClient($apikey, $apiSecret);
    }

    public static function api(string $apikey, string $apiSecret): Streamline
    {
        return new self($apikey, $apiSecret);
    }

    /**
     * Refresh the token
     */
    public function refreshToken(): array
    {
        $response = $this->client->request('RenewExpiredToken');

        $apiKey = $response['token_key'];
        $apiSecret = $response['token_secret'];

        // Update the client with the new token
        $this->client = new StreamlineClient($apiKey, $apiSecret);

        return [
            'apikey' => $apiKey,
            'apiSecret' => $apiSecret,
        ];
    }

    public function properties(): PropertiesClient
    {
        return new PropertiesClient($this->client);
    }

    public function amenities(): AmenitiesClient
    {
        return new AmenitiesClient($this->client);
    }

    public function reviews(): ReviewsClient
    {
        return new ReviewsClient($this->client);
    }

    public function galleryImages(): GalleryImagesClient
    {
        return new GalleryImagesClient($this->client);
    }

    public function bookedDates(): BookedDaysClient
    {
        return new BookedDaysClient($this->client);
    }

    public function preReservationPrice(): PreReservationPriceClient
    {
        return new PreReservationPriceClient($this->client);
    }

    public function propertyRates(): PropertyRatesClient
    {
        return new PropertyRatesClient($this->client);
    }
}