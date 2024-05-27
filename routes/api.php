<?php

use App\Http\Controllers\RatesController;
use Illuminate\Support\Facades\Route;

Route::get('rates', [RatesController::class, 'getRates']);
