<?php

use App\Core\Route;

// API endpoint example
Route::get('/hello', function () {
    return ['message' => 'Hello from API'];
});
