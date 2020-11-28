<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BarberController;
use App\Http\Controllers\UserController;

Route::prefix("auth")->group(function(){
    Route::post("/login", [AuthController::class, "login"]);
    Route::post("/logout", [AuthController::class, "logout"]);
    Route::get("/refresh", [AuthController::class, "refresh"]);
    Route::get("/account", [AuthController::class, "me"]);
    Route::post("/create", [AuthController::class, "create"]);
});


Route::group(['middleware' => ['auth:api']], function () {
    Route::prefix("users")->group(function(){
        Route::put("account", [UserController::class, "update"]);
    });
});

Route::group(['middleware' => ['auth:api']], function () {
    Route::prefix("barbers")->group(function(){
        Route::get("/", [BarberController::class, "index"]);
    });
});
