<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\JobPostController;
use App\Http\Controllers\ParkOverviewController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', [ParkOverviewController::class, 'index'])->name('parks');
Route::get('/parks/{id}', [ParkOverviewController::class, 'detail'])->name('parks.detail');

Route::get('/jobs/{jobPost}', [JobPostController::class, 'detail'])->name('jobs.detail');

Route::get('/users', [UserController::class, 'overview'])->name('users');

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

    Route::get('/dashboard/test', [JobPostController::class, 'getDestinations'])->name('dashboard.test');
    Route::get('/dashboard/jobs', [JobPostController::class, 'index'])->name('dashboard.jobs');
    Route::get('/dashboard/jobs/create', [JobPostController::class, 'create'])->name('dashboard.jobs.create');
    Route::post('/dashboard/jobs/create', [JobPostController::class, 'store'])->name('dashboard.jobs.store');
    Route::get('/dashboard/jobs/{jobPost}', [JobPostController::class, 'edit'])->name('dashboard.jobs.edit');
    Route::post('/dashboard/jobs/{jobPost}', [JobPostController::class, 'update'])->name('dashboard.jobs.update');
    Route::delete('/dashboard/jobs/{jobPost}', [JobPostController::class, 'delete'])->name('dashboard.jobs.delete');
});

Route::middleware(['auth', 'admin'])->group(function () {
    Route::get('/admin/parks', [ParkOverviewController::class, 'edit'])->name('admin.parks');
    Route::put('/admin/destinations/{destination}', [ParkOverviewController::class, 'updateDestination'])->name('admin.destinations.update');
    Route::put('/admin/parks/{park}', [ParkOverviewController::class, 'updatePark'])->name('admin.parks.update');

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
