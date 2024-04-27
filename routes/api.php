<?php

use App\Http\Controllers\Api\ApiController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Api'], function () {
    Route::get('/get-all-images', [ApiController::class, 'getAllImages']);
    Route::get('/get-image/{id}', [ApiController::class, 'getImage']);
});
