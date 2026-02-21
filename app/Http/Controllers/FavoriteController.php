<?php

namespace App\Http\Controllers;

use App\Models\Park;
use Illuminate\Http\Request;

use Inertia\Inertia;
use Inertia\Response;


class FavoriteController extends Controller
{
    public function toggle(Park $park)
    {
        $user = auth()->user();
        $user->favoriteParks()->toggle($park->id);
        return back();
    }
    
    public function index(): Response
    {
        $user = auth()->user();

        if (!$user) {
            return Inertia::render('Favorites/Index', [
                'parks' => [],
                'isGuest' => true
            ]);
        }

        $parks = $user->favoriteParks()->orderBy('name')
            ->get([
                'parks.id',
                'parks.name',
                'parks.latitude',
                'parks.longitude',
                'parks.api_id',
            ])
            ->map(function ($park) {
                $park->is_favorited = true;
                return $park;
            });

        return Inertia::render('Favorites/Index', [
            'parks' => $parks,
            'isGuest' => false
        ]);
    }
}

