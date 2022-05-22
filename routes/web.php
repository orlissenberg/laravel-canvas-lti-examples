<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LtiController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/home', function () {
    if (auth()->check()) {
        return 'Welcome ' . auth()->user()->email;
    }

    return redirect('/');
})->name('home');

Route::controller(LtiController::class)
    ->prefix('/lti')
    ->group(function () {
        Route::post('register', 'register');
        Route::post('handle-assignment', 'handleAssignment')->name('lti-handle-assignment');
    });

Route::controller(AuthController::class)
    ->prefix('/auth')
    ->group(function () {
        Route::get('login', 'login');
        Route::get('callback', 'callback');
    });
