<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return 
    
    // ['Laravel' => app()->version()];

    'Hello, Laravel API!';


});

require __DIR__.'/auth.php';
