<?php

use App\Http\Controllers\Admin\UmkmController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/map', function () {
    return view('map');
});

Route::get('/kuliner', function () {
    return view('kuliner');
});

Route::get('/tentang', function () {
    return view('tentang');
});

Route::get('/kuliner/views', function () {
    return view('kuliner/views');
});

Route::middleware(['auth', 'verified'])->group(function () {

    Route::prefix('admin')
    ->middleware(['auth'])
    ->name('admin.')
    ->group(function () {

        Route::get('/dashboard', function () {
            return view('admin/dashboard');
        })->name('dashboard');

        Route::resource('umkm', UmkmController::class);

        Route::post('/umkm/import', [UmkmController::class, 'import'])->name('umkm.import');

        Route::post('/umkm/clustering', [UmkmController::class, 'runClustering'])->name('umkm.clustering');
    });

});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';

