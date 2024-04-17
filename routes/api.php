<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\PasswordResetController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


//open Routes

Route::post('/register', [ApiController::class, 'register']);
Route::post('/login' ,[ApiController::class, 'login']);
Route::post('/send_reset_password_email' ,[PasswordResetController::class, 'send_reset_password_email']);
Route::post('/reset-password/{token}',[PasswordResetController::class, 'reset']);

//protected Routes//
Route::group([
    "middleware"=>["auth:sanctum"]
],function(){
        Route::get('/profile',[ApiController::class, 'profile']);
        Route::get('/logged_user',[ApiController::class, 'logged_user']);
        Route::post('/changePassword',[ApiController::class, 'changePassword']);
        Route::get('/logout',[ApiController::class, 'logout']);
});