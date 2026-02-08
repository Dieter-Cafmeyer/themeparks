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
     * Get all rides for a park
     */
    public function rides(string $parkSlug): array
    {
        return $this->client()->get("/rides/{$parkSlug}")->throw()->json();
    }

    /**
     * Get a single ride by slug
     */
    public function ride(string $slug): array
    {
        return $this->client()->get("/ride/{$slug}")->throw()->json();
    }

    /**
     * Get all events for a park
     */
    public function events(string $parkSlug): array
    {
        return $this->client()->get("/events/{$parkSlug}")->throw()->json();
    }

    /**
     * Get a single event by slug
     */
    public function event(string $slug): array
    {
        return $this->client()->get("/event/{$slug}")->throw()->json();
    }

    /**
     * Get schedules for a park
     */
    public function schedules(string $parkSlug): array
    {
        return $this->client()->get("/schedules/{$parkSlug}")->throw()->json();
    }

    /**
     * Get schedule for a specific date
     */
    public function schedule(string $parkSlug, string $date): array
    {
        return $this->client()->get("/schedule/{$parkSlug}/{$date}")->throw()->json();
    }
}
