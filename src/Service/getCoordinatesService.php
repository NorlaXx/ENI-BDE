<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class getCoordinatesService
{
    public function __construct(private HttpClientInterface $client)
    {
    }

    public function getCoordinates(string $address): ?array
    {

        try {
            $response = $this->client->request('GET', 'https://api.jawg.io/places/v1/search', [
                'query' => [
                    'text' => $address,
                    'access-token' => 'oLvGs7mD6LIAq8xV5ctpo14ylPnYF5Hvwz2qzsa36wVVSqfZSYEfxsMPhZqpumfv'
                ]
            ]);

            $data = $response->toArray();
            if (!empty($data['features'][0]['geometry']['coordinates'])) {
                return array_reverse($data['features'][0]['geometry']['coordinates']);
            }
        } catch (\Exception $e) {
            dd($e->getMessage());
            // Optionally handle the error, retry, log, etc.
            return null;
        }

        return null;
    }

}