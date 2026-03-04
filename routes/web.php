<?php

use App\Http\Controllers\Admin\UmkmController;
use App\Http\Controllers\MainController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

Route::get('/', [MainController::class, 'home'])->name('home');

Route::get('/map', [MainController::class, 'map'])->name('map');

Route::get('/kuliner', [MainController::class, 'kuliner'])->name('kuliner');

Route::get('/tentang', [MainController::class, 'tentang'])->name('tentang');

Route::get('/kuliner/{id}', [MainController::class, 'view'])->name('kuliner.view');

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

