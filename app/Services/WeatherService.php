<?php

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Support\Facades\Log;

class WeatherService
{
    private $client;
    private $apiKey;
    private $baseUrl;

    public function __construct()
    {
        $this->client = new Client();
        $this->apiKey = config('services.openweather.api_key');
        $this->baseUrl = 'https://api.openweathermap.org/data/2.5';
    }

    public function getWeatherByCity($city = 'Madrid')
    {
        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => 'API key de OpenWeatherMap no configurada'
            ];
        }

        try {
            $response = $this->client->get("{$this->baseUrl}/weather", [
                'query' => [
                    'q' => $city,
                    'appid' => $this->apiKey,
                    'units' => 'metric',
                    'lang' => 'es'
                ]
            ]);

            $data = json_decode($response->getBody(), true);

            return [
                'success' => true,
                'data' => [
                    'city' => $data['name'],
                    'country' => $data['sys']['country'],
                    'temperature' => round($data['main']['temp']),
                    'feels_like' => round($data['main']['feels_like']),
                    'humidity' => $data['main']['humidity'],
                    'description' => ucfirst($data['weather'][0]['description']),
                    'icon' => $data['weather'][0]['icon'],
                    'wind_speed' => $data['wind']['speed'] ?? 0,
                ]
            ];

        } catch (RequestException $e) {
            Log::error('Error al consultar OpenWeatherMap API: ' . $e->getMessage());
            
            return [
                'success' => false,
                'message' => 'Error al obtener información del clima'
            ];
        }
    }
}