<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobPostController;
use App\Http\Controllers\ParkOverviewController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AttractionController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ParkOverviewController::class, 'index'])->name('parks');
Route::get('/parks/{id}', [ParkOverviewController::class, 'detail'])->name('parks.detail');
Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites');

Route::get('/jobs/{jobPost}', [JobPostController::class, 'detail'])->name('jobs.detail');

Route::get('/parks/{id}/schedule/{year}/{month}', function($id, $year, $month) {
    $response = Http::get("https://api.themeparks.wiki/v1/entity/{$id}/schedule/{$year}/{$month}");
    return $response->json();
});

Route::middleware('guest')->group(function () {
    Route::inertia('/register', 'Auth/Register')->name('register');
    Route::post('/register', [AuthController::class, 'register']);

    Route::inertia('/login', 'Auth/Login')->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [AuthController::class, 'account'])->name('dashboard');
    Route::post('/dashboard', [AuthController::class, 'edit'])->name('account.edit');

    Route::post('/parks/favorite/{park}', [FavoriteController::class, 'toggle'])->name('parks.favorite');
    Route::post('/attractions/{attractionId}/favorite', [AttractionController::class, 'toggleFavorite'])->name('attractions.favorite');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/dashboard/parks', [ParkOverviewController::class, 'edit'])->name('dashboard.parks');
    Route::put('/dashboard/destinations/{destination}', [ParkOverviewController::class, 'updateDestination'])->name('dashboard.destinations.update');
    Route::put('/dashboard/parks/{park}', [ParkOverviewController::class, 'updatePark'])->name('dashboard.parks.update');

    Route::get('/dashboard/users', [UserController::class, 'index'])->name('dashboard.users');
    Route::get('/dashboard/users/{user}', [UserController::class, 'edit'])->name('dashboard.users.edit');
    Route::post('/dashboard/users/{user}', [UserController::class, 'update'])->name('dashboard.users.update');
    Route::delete('/dashboard/users/{user}', [UserController::class, 'delete'])->name('dashboard.users.delete');
});

Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, ['en', 'nl', 'fr'])) {
        session(['locale' => $locale]);
    }

    return redirect()->back();
})->name('lang.switch');
