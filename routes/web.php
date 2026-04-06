<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ParkOverviewController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AttractionController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Http;
use App\Models\Park;

Route::get('/', [ParkOverviewController::class, 'index'])->name('parks');
Route::get('/parks/{slug}', [ParkOverviewController::class, 'detail'])->name('parks.detail');
Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites');

Route::get('/parks/{id}/schedule/{year}/{month}', function($id, $year, $month) {
    $park = Park::find($id);
    
    if (!$park || !$park->api_id) {
        return response()->json(['error' => 'Park not found'], 404);
    }
    
    $response = Http::get("https://api.themeparks.wiki/v1/entity/{$park->api_id}/schedule/{$year}/{$month}");
    return $response->json();
});

Route::get('/parks/{id}/weather/{year}/{month}', function($id, $year, $month) {
    $park = Park::find($id);
    
    if (!$park || !$park->latitude || !$park->longitude) {
        return response()->json(['error' => 'Park not found or coordinates missing'], 404);
    }
    
    // Calculate date range for the month
    $requestedStart = "{$year}-{$month}-01";
    $lastDay = date('t', strtotime($requestedStart));
    $requestedEnd = "{$year}-{$month}-{$lastDay}";
    
    // Open-Meteo free API only supports ~16 days forecast
    $today = date('Y-m-d');
    $maxForecastDate = date('Y-m-d', strtotime($today . ' +14 days'));
    
    // Adjust dates to stay within allowed range
    $startDate = max($today, $requestedStart);
    $endDate = min($maxForecastDate, $requestedEnd);
    
    // If start date is after end date, return empty data
    if ($startDate > $endDate) {
        return response()->json([
            'daily' => [
                'time' => [],
                'temperature_2m_max' => [],
                'temperature_2m_min' => [],
                'weathercode' => [],
                'precipitation_sum' => [],
                'windspeed_10m_max' => [],
            ]
        ]);
    }
    
    // Open-Meteo API call (free, no API key required)
    $response = Http::get('https://api.open-meteo.com/v1/forecast', [
        'latitude' => $park->latitude,
        'longitude' => $park->longitude,
        'daily' => 'temperature_2m_max,temperature_2m_min,weathercode,precipitation_sum,windspeed_10m_max',
        'timezone' => 'auto',
        'start_date' => $startDate,
        'end_date' => $endDate,
    ]);
    
    return $response->json();
});

// Guest routes
Route::middleware('guest')->group(function () {
    Route::inertia('/register', 'Auth/Register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::inertia('/login', 'Auth/Login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});


// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [AuthController::class, 'account'])->name('dashboard');
    Route::post('/dashboard', [AuthController::class, 'edit'])->name('account.edit');

    Route::post('/parks/favorite/{park}', [FavoriteController::class, 'toggle'])->name('parks.favorite');
    Route::post('/attractions/{attractionId}/favorite', [AttractionController::class, 'toggleFavorite'])->name('attractions.favorite');
});


// Admin routes
Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard/parks', [ParkOverviewController::class, 'edit'])->name('dashboard.parks');
    Route::put('/dashboard/parks/{park}', [ParkOverviewController::class, 'updatePark'])->name('dashboard.parks.update');
    Route::put('/dashboard/destinations/{destination}', [ParkOverviewController::class, 'updateDestination'])->name('dashboard.destinations.update');

    Route::get('/dashboard/users', [UserController::class, 'index'])->name('dashboard.users');
    Route::get('/dashboard/users/{user}', [UserController::class, 'edit'])->name('dashboard.users.edit');
    Route::post('/dashboard/users/{user}', [UserController::class, 'update'])->name('dashboard.users.update');
    Route::delete('/dashboard/users/{user}', [UserController::class, 'delete'])->name('dashboard.users.delete');
});


// Language switcher route
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'nl', 'fr'])) {
        session(['locale' => $locale]);
    }
    return redirect()->back();
})->name('lang.switch');
