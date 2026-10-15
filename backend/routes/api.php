<?php

use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\GalleryController;
use App\Http\Controllers\Api\V1\HomeController;
use App\Http\Controllers\Api\V1\SettingController;
use App\Http\Controllers\Api\V1\VentureController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function () {
    Route::get('home', HomeController::class);
    Route::get('settings', SettingController::class);
    Route::get('ventures', VentureController::class);
    Route::get('gallery', GalleryController::class);
    Route::post('contact', ContactController::class)->middleware('throttle:contact');
});
