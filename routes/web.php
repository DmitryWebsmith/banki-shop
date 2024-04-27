<?php

use App\Http\Controllers\Images\ImagesController;
use Illuminate\Support\Facades\Route;

Route::group(['namespace' => 'Images'], function () {
    Route::get('/', [ImagesController::class, 'showUploadImagesPage'])->name('home');
    Route::post('/', [ImagesController::class, 'storeImages'])->name('store.images');

    Route::get('/images', [ImagesController::class, 'showImagesPage'])->name('show.images.page');
    Route::get('/api-info', [ImagesController::class, 'showApiInfoPage'])->name('show.api.info.page');
    Route::get('/download/zip/{id}', [ImagesController::class, 'downloadImageArchive'])->name('download.image.archive');

    Route::post('/search-images-by-name', [ImagesController::class, 'searchImagesByFileName'])->name('search.images.by.name');
    Route::post('/search-images-by-date-time', [ImagesController::class, 'searchImagesByDateTime'])->name('search.images.by.date.time');

    Route::delete('/image/{id}', [ImagesController::class, 'destroy'])->name('destroy.image');
});
