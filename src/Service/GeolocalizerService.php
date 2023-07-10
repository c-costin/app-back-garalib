<?php

namespace App\Service;

use App\Repository\AddressRepository;
use App\Repository\GarageRepository;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GeolocalizerService
{
    // Module
    private $httpClient;
    private $garageRepository;

    // Properties
    private $address;
    private $coordinate;

    public function __construct(HttpClientInterface $httpClientInterface, GarageRepository  $garageRepository)
    {
        $this->httpClient = $httpClientInterface;
        $this->garageRepository = $garageRepository;
    }

    public function findGarageByAddress(string $address, $radiate)
    {
        // Set Address
        $this->address = $address;

        // Fetch Coordinates
        $this->fetchCoordinate();

        // Find Garage
        return $this->garageRepository->findGarageByAddress($this->coordinate["lat"], $this->coordinate["lon"], $radiate);
    }

    public function fetchCoordinate(): void
    {
        $response = $this->httpClient->request("GET", "https://nominatim.openstreetmap.org/search.php?q={$this->address}&format=jsonv2");

        // $statusCode = $response->getStatusCode();
        // $contentType = $response->getHeaders()['content-type'][0];
        // $content = $response->getContent();
        $content = $response->toArray();

        $this->coordinate = [
            "lat" => $content[0]['lat'],
            "lon" => $content[0]['lon'],
        ];
    }

    public function setAddressInDatabase()
    {
        
    }
}