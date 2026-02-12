<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Park;
use App\Services\ThemeParksApi;
use Inertia\Inertia;

class ParkOverviewController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        $destinations = Destination::query()
            ->where('is_active', true)
            ->with(['parks' => function ($query) use ($userId) {
                $query->where('is_active', true)->orderBy('name')
                    ->when($userId, function ($q) use ($userId) {
                        $q->withExists([
                            'usersWhoFavorited as is_favorited' => function ($sub) use ($userId) {
                                $sub->where('user_id', $userId);
                            }
                        ]);
                    })
                    ->when(!$userId, function ($q) {
                        $q->addSelect([
                            'is_favorited' => \DB::raw('false')
                        ]);
                    });
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
            'is_active' => ! $destination->is_active,
        ]);

        return back()->with('success', __('messages.destination_updated'));
    }

    public function updatePark(Park $park)
    {
        $park->update([
            'is_active' => ! $park->is_active,
        ]);

        return back()->with('success', __('messages.park_updated'));
    }

    public function detail(string $id, ThemeParksApi $api)
    {
        $park = $api->park($id);
        $map = $api->children($id);
        $live = $api->attractions($id);

        // Shows
        $shows = array_filter($live['liveData'], function ($child) {
            return str_contains(strtolower($child['entityType'] ?? ''), 'show');
        });

        usort($shows, function ($a, $b) {
            return strcmp($a['name'] ?? '', $b['name'] ?? '');
        });

        $user = auth()->user();
        $isFavorited = false;

        if ($user) {
            $localPark = Park::where('api_id', $id)->first();

            if ($localPark) {
                $isFavorited = $user->favoriteParks()->where('park_id', $localPark->id)->exists();
            }
        }

        // Attractions
        $attractions = array_filter($live['liveData'], function ($child) {
            return str_contains(strtolower($child['entityType'] ?? ''), 'attraction');
        });

        return Inertia::render('Parks/Detail', [
            'park' => array_merge($park, ['is_favorited' => $isFavorited], ['internal_id' => $localPark ? $localPark->id : null]),
            'shows' => $shows,
            'map' => $map,
            'attractions' => $attractions,
            'title' => $park['name'],
            'attractions_title' => __('messages.attractions_title'),
            'shows_title' => __('messages.shows_title'),
            'map_title' => __('messages.map_title'),
            'last_updated' => __('messages.last_updated'),
            'seconds_ago' => __('messages.seconds_ago'),
            'minute' => __('messages.minute'),
            'minutes' => __('messages.minutes'),
            'ago' => __('messages.ago'),
        ]);
    }
}
