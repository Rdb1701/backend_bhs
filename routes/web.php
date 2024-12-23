<?php

use App\Http\Controllers\LocationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect('/admin'); 
});


Route::middleware('auth')->post('/save-location', [LocationController::class, 'saveLocation'])->name('save-location');
Route::middleware('auth')->get('/map-selection', [LocationController::class, 'showMapPage'])->name('map-selection');


Route::middleware('auth')->get('/locations_view', [LocationController::class, 'showLocationPage'])->name('location-pages');


