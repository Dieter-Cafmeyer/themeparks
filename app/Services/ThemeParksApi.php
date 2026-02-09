<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class ThemeParksApi
{
    protected string $baseUrl;

    public function __construct()
    {
        // base URL kan eventueel via config
        $this->baseUrl = 'https://api.themeparks.wiki/v1';
    }

    /**
     * Return a preconfigured HTTP client for the API
     */
    protected function client()
    {
        return Http::baseUrl($this->baseUrl)
            ->acceptJson()
            ->timeout(10);
    }

    /**
     * Get all destinations
     */
    public function destinations(): array
    {
        return $this->client()
            ->get('/destinations')->throw()->json();
    }

    /**
     * Get a single destination by slug
     */
    public function destination(string $slug): array
    {
        return $this->client()
            ->get("/destination/{$slug}")->throw()->json();
    }

    /**
     * Get a single park by id
     */
    public function park(string $id): array
    {
        return $this->client()->get("/entity/{$id}")->throw()->json();
    }

    /**
     * Get all attractions for a park
     */
    public function attractions(string $id): array
    {
        return $this->client()->get("/entity/{$id}/live")->throw()->json();
    }
    /**
     * Get all children, like restaurants and shows for a park
     */
    public function children(string $id): array
    {
        return $this->client()->get("/entity/{$id}/children")->throw()->json();
    }

    /**
     * Get a schedule by id
     */
    public function schedule(string $id): array
    {
        return $this->client()->get("/entity/{$id}/schedule")->throw()->json();
    }

    /**
     * Get schedule for a specific date
     */
    public function scheduleByMonth(string $id, string $date): array
    {
        $year = date('Y', strtotime($date));
        $month = date('m', strtotime($date));
        return $this->client()->get("/schedule/{$id}/{$year}/{$month}")->throw()->json();
    }
}
