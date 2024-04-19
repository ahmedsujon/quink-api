<?php

use App\Http\Controllers\api\app\HomeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\user\auth\AuthenticationController;
use App\Http\Controllers\api\user\auth\UserResetPasswordController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::post('v1/login', [AuthenticationController::class, 'login']);
Route::post('v1/register', [AuthenticationController::class, 'register']);
Route::post('v1/reset-password', [UserResetPasswordController::class, 'sendEmail']);
Route::post('v1/change-password', [UserResetPasswordController::class, 'changePassword']);

Route::get('v1/trending-posts', [HomeController::class, 'trendingPosts']);

//Authenticated user
Route::group(['middleware' => ['jwtUser:user-api', 'jwt.auth'], 'prefix' => 'v1/user'], function () {
    Route::post('logout', [AuthenticationController::class, 'userLogout']);

    //User Profile
    Route::get('profile', [AuthenticationController::class, 'userProfile']);
});

