<?php

namespace App\Http\Controllers;

use Inertia\Inertia;
use App\Models\Destination;
use App\Models\Park;
use App\Services\ThemeParksApi;

class ParkOverviewController extends Controller
{
    public function index()
    {
        $destinations = Destination::query()
            ->where('is_active', true)
            ->with(['parks' => function ($query) {
                $query->where('is_active', true)
                      ->orderBy('name');
            }])
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'slug',
            ]);

        return Inertia::render('Parks/Index', [
            'destinations' => $destinations,
            'title' => __('messages.destinations'),
            'search_title' => __('messages.search_resort'),
        ]);
    }

    public function detail(string $id, ThemeParksApi $api)
    {
        $park = $api->park($id);
        return Inertia::render('Parks/Detail', [
            'park' => $park,
            'title' => $park['name'],
        ]);
    }

    public function edit()
    {
        $destinations = Destination::query()
            ->with(['parks' => function ($query) {
                $query->orderBy('name');
            }])
            ->orderBy('name')
            ->get([
                'id',
                'name',
                'slug',
                'is_active',
            ]);

        return Inertia::render('Dashboard/Parks/Edit', [
            'destinations' => $destinations,
            'title' => __('messages.manage_parks'),
            'search_title' => __('messages.search_resort'),
        ]);
    }

    public function updateDestination(Destination $destination)
    {
        $destination->update([
            'is_active' => !$destination->is_active,
        ]);

        return back()->with('success', __('messages.destination_updated'));
    }

    public function updatePark(Park $park)
    {
        $park->update([
            'is_active' => !$park->is_active,
        ]);

        return back()->with('success', __('messages.park_updated'));
    }
}