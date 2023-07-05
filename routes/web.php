<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RestaurantMap; 

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('search');
});

Route::get('/restaurants/{keyword}', [RestaurantMap::class, 'getRestaurants']);


#Auth::routes();

#Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');