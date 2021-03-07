<?php

use \Illuminate\Support\Facades\Broadcast;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Broadcast::routes(['middleware' => ['auth:sanctum']]);

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::prefix("v1")
    ->group(function () {

        Route::prefix("auth")->group(function () {

            Route::post("login", [\App\Http\Controllers\Api\AuthController::class, "login"]);
            Route::middleware('auth:sanctum')->post("logout", [\App\Http\Controllers\Api\AuthController::class, "logout"]);
            Route::middleware('auth:sanctum')->get("user", [\App\Http\Controllers\Api\AuthController::class, "user"]);

        });

        Route::middleware('auth:sanctum')->group(function () {

            Route::get('contacts', [\App\Http\Controllers\Api\ContactsController::class, "all"]);
            Route::get('dialog/{user}', [\App\Http\Controllers\Api\DialogController::class, "index"]);
            Route::post('dialog/{user}', [\App\Http\Controllers\Api\DialogController::class, "send"]);

        });

        Route::get("/", [\App\Http\Controllers\Api\IndexController::class, "index"]);
        Route::post("/test", [\App\Http\Controllers\Api\IndexController::class, "test"]);

    });
