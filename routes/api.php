<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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
//Route::prefix('v1.0')->group(function () {
//    Route::post('login', [\App\Http\Controllers\Api\AuthController::class, 'login'])->name('login');
//
//    Route::middleware(['XssSanitizer', 'throttle:60,1', 'auth:api'])->group(function () {
//            Route::post('logout', [\App\Http\Controllers\Api\AuthController::class, 'logout'])->name('logout');
//    //        Route::group(['middleware' => ['auth:api']], function () {
//    //            Route::post('logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
//    //        });
//    });
//});

Route::group(['middleware' => ['XssSanitizer', 'throttle:60,1']], function () {
    Route::prefix('v1.0')->group(function () {
        Route::post('login', [\App\Http\Controllers\Api\AuthController::class, 'login'])->name('login');
        Route::group(['middleware' => ['auth:api']], function () {
            Route::post('logout', [\App\Http\Controllers\Api\AuthController::class, 'logout'])->name('logout');
            Route::get('quote/{cache?}', [\App\Http\Controllers\Api\QuoteController::class, 'index'])->name('quote');
        });
    });
});

//    Route::middleware('checkauthapi', 'throttle:60,1')->prefix('1.0')->namespace('API\v1')->group(function () {//added a middleware that check auth
//        //Auth Logout
//        Route::get('logout', 'AuthController@logout')->name('logout');
//
//        //Fetch Data from DataExpenseModel
//        Route::apiResource('expense', 'ExpenseController')->only(['index', 'store', 'show', 'update', 'destroy']);//The only shouldn't be needed
//    });

//    Route::prefix('v1.0')->group(function () {
//        Route::apiResource('auth', \App\Http\Controllers\Api\AuthController::class, ['login', 'logout'])->names([
//            'login' => 'auth/login',
//            'logout' => 'auth/logout',
//        ]);
//    });
//    Route::get('v1.0/quote', [\App\Http\Controllers\Api\QuoteController::class, 'index'])->name('quote');
//    Route::fallback(function () {
//        return Response::json(["Bad request."], \Symfony\Component\HttpFoundation\Response::HTTP_BAD_REQUEST);
//    });
