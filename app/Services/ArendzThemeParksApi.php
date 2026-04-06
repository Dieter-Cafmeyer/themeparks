<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ArendzThemeParksApi
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.arendz.base_url', 'https://tp.arendz.nl'), '/');
    }

    protected function client()
    {
        return Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->timeout(15);
    }

    public function parks(): array
    {
        return $this->client()->get('/parks')->throw()->json();
    }

    public function park(string $id): array
    {
        return $this->client()->get("/parks/{$id}")->throw()->json();
    }

    public function rides(string $parkId): array
    {
        return $this->client()->get("/parks/{$parkId}/rides")->throw()->json();
    }
}
