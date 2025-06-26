<?php

use App\Core\Route;
use App\Core\Template;

// routes
Route::get('/', function () {
    return view('welcome')->with('title', 'Welcome Page');
}, 'welcome');

