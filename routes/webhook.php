<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Webhook\EvoltionApiController;

/*
|--------------------------------------------------------------------------
| WEBHOOK Routes
|--------------------------------------------------------------------------
|
| Here is where you can register WEBHOOK routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "webhook" middleware group. Make something great!
|
*/

Route::post('/evolution-api', [EvoltionApiController::class, 'handle']);

