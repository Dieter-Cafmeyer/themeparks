<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserFavoriteAttraction;

class AttractionController extends Controller
{
    public function toggleFavorite(string $attractionId)
    {
        $user = auth()->user();

        $favorite = UserFavoriteAttraction::where('user_id', $user->id)->where('attraction_id', $attractionId)->first();

        if ($favorite) {
            $favorite->delete();
            $isFavorited = false;
        } else {
            UserFavoriteAttraction::create([
                'user_id' => $user->id,
                'attraction_id' => $attractionId,
            ]);
            $isFavorited = true;
        }

        return response()->json([
            'is_favorited' => $isFavorited
        ]);
    }
}
