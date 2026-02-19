<?php

namespace App\Http\Controllers;

use App\Models\Destination;
use App\Models\Park;
use App\Models\UserFavoriteAttraction;
use App\Services\ThemeParksApi;
use Inertia\Inertia;

class ParkOverviewController extends Controller
{
    public function index()
    {
        $userId = auth()->id();

        if ($userId) {
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
        } else {
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
        }

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
            'destinations' => $destinations
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
        $localPark = Park::where('api_id', $id)->first();

        $favorites = [];
        if ($user) {
            // Park favoriet status
            if ($localPark) {
                $isFavorited = $user->favoriteParks()
                    ->where('park_id', $localPark->id)
                    ->exists();
            }

            // Attraction favorieten ophalen
            $favorites = $user->favoriteAttractions()
                ->pluck('attraction_id')
                ->toArray();
        }

        $attractions = array_filter($live['liveData'], function ($child) {
            return str_contains(strtolower($child['entityType'] ?? ''), 'attraction');
        });

        $attractions = array_map(function ($attraction) use ($favorites) {
            $attraction['is_favorited'] = in_array($attraction['id'], $favorites);
            return $attraction;
        }, $attractions);

        return Inertia::render('Parks/Detail', [
            'park' => array_merge(
                $park,
                [
                    'is_favorited' => $isFavorited,
                    'internal_id' => $localPark ? $localPark->id : null
                ]
            ),
            'shows' => $shows,
            'map' => $map,
            'attractions' => $attractions,
            'title' => $park['name']
        ]);
    }
}
