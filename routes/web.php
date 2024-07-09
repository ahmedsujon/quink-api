<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\auth\SocialiteController;

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

Route::get('/auth/google', [SocialiteController::class, 'redirectToGoogle']);
Route::get('/auth/google/callback', [SocialiteController::class, 'getGoogleTokenFromWeb']);

Route::get('/', function () {
    return redirect()->route('admin.login');
});

//Call Route Files
require __DIR__ . '/admin.php';
