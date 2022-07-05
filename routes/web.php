<?php

use App\Http\Controllers\auth\LoginController;
use App\Http\Controllers\CalendarController;
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

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_session'),
    'verified'
])->group(function () {
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');
});

// Route::get('login/google', [LoginController::class, 'redirectToProvider'])->name('login-google');
// Route::get('login/google/callback', [LoginController::class, 'handleProviderCallback'])->name('oauth');



// Route::resource('calendar', CalendarController::class);



Route::get('create-events', [CalendarController::class, 'create'])->name('create-events');
Route::post('store-events', [CalendarController::class, 'store'])->name('store-events');


Route::get('index', [CalendarController::class, 'index'])->name('index');
Route::get('login/google/callback', [CalendarController::class, 'oauth'])->name('oauth');

Route::get('show', [CalendarController::class, 'show'])->name('show');
Route::post('logout', [LoginController::class, 'logout'])->name('logout');