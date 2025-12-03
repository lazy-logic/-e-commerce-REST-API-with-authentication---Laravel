<?php

/**
 * Web routes for the e-commerce application.
 *
 * These routes are loaded by the RouteServiceProvider and all of them
 * receive the web middleware group.
 */

use Illuminate\Support\Facades\Route;

use Illuminate\Http\Request;


Route::get('/', function () {
    return view('welcome');
});




Route::get('/login', function (Request $request) {
	return response()->json(['message' => 'Unauthenticated.'], 401);
})->name('login');
