<?php

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::group([
//     "prefix"=>"auth",
//     "namespace"=>"\App\Http\Controllers",
//     "middleware"=>"api"
//    ],function($router){
//        Route::post("login","AuthController@login");
//        Route::post("register","AuthController@register");
//        Route::get("profil","AuthController@profile");
//        Route::post("signout","AuthController@signout");
//        Route::post("refresh","AuthController@refresh");

//    });
Route::group([
    "prefix" => "company",
    "namespace" => "\App\Http\Controllers",
    "middleware" => "api"
], function ($router) {
    Route::post("login", "AuthCompanyController@login");
    Route::post("register", "AuthCompanyController@register");
    Route::get("profil", "AuthCompanyController@profile");
    Route::post("signout", "AuthCompanyController@signout");
    Route::post("refresh", "AuthCompanyController@refresh");
    Route::put("detail", "AuthCompanyController@updateCompanyDetail");
    Route::put("user", "AuthCompanyController@updateUserDetail");
    Route::put("pwd", "AuthCompanyController@pasword");
    Route::group(
        [
            "prefix" => "dashbaord",
        ],
        function ($router) {
            Route::get("orders", "DashboardController@orders");
            Route::get("lastorders", "DashboardController@lastorders");
            Route::get("chartdata", "DashboardController@chartdata");
        }
    );
    // end of dashboard
    // begin of users 
    Route::group([
        "prefix" => "users"
    ], function ($router) {
        Route::get("/", "UsersController@index");
        Route::get("/{id}", "UsersController@show");
        Route::post("/", "UsersController@store");
        Route::put("/{id}", "UsersController@update");
        Route::delete("/{id}", "UsersController@destroy");
        Route::post("/{id}/refresh", "UsersController@refresh");
    });
    // end of users
    // begin of orders 
    Route::group([
        "prefix" => "orders"
    ], function ($router) {
        Route::get("/", "OrdersController@index");
        Route::get("/{id}", "OrdersController@show");
        Route::put("/{id}", "OrdersController@update");
        Route::put("/{id}/address", "OrdersController@updateAddress");
        Route::put("/{id}/status", "OrdersController@updateStatus");
    });
    // end of orders
    // begin of status
    Route::group([
        "prefix" => "status"
    ], function ($router) {
        Route::get("/", "OrderStatusController@index");
    });
    // end of status
    // begin of status
    Route::group([
        "prefix" => "spams"
    ], function ($router) {
        Route::post("/", "SpamController@spam");
    });
    Route::group([
        "prefix" => "company_space"
    ], function ($router) {
        Route::get("/", "MethodsController@index");
    });
    // methods public 
    Route::group([
        "prefix" => "methods"
    ], function ($router) {
        Route::post("add", "ApiMethodsController@add");
    });
    // end of methods public
    // methods delivery 
    Route::group([
        "prefix" => "delivery"
    ], function ($router) {
        Route::group([
            "prefix" => "orders"
        ], function ($router) {
            Route::get("/", "DeliveryController@orders");
            Route::get("{id}", "DeliveryController@show");
            Route::put("{id}/update", "DeliveryController@updateStatus");
        });
        Route::get("status", "DeliveryController@status");
    });
    // end of methods public
    
});
Route::group([
    "middleware"=>"api",
    "namespace"=>"\App\Http\Controllers"
],function($router){
    Route::post("search","UserInterfaceController@search");
});
