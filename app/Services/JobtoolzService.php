<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class JobtoolzService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = config('services.jobtoolz.base_url');
        $this->apiKey = config('services.jobtoolz.key');
    }

    public function getLocations()
    {
        $client = new \GuzzleHttp\Client();
        $url = $this->baseUrl . '/content/v1/locations';
        $response = $client->get(
            $url,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en',
                ],
            ]
        );
        $body = $response->getBody();
        return json_decode((string) $body);
    }

    public function getJobs()
    {
        $client = new \GuzzleHttp\Client();
        $url = $this->baseUrl . '/content/v1/jobs';
        $response = $client->get(
            $url,
            [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Content-Type' => 'application/json',
                    'Accept' => 'application/json',
                    'Accept-Language' => 'en',
                ],
            ]
        );
        $body = $response->getBody();
        return json_decode((string) $body);
    }
}
