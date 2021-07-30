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
   
//    company

Route::group([
    "prefix"=>"company",
    "namespace"=>"\App\Http\Controllers",
    "middleware"=>"api"
   ],function($router){
       Route::post("login","AuthCompanyController@login");
       Route::post("register","AuthCompanyController@register");
       Route::get("profil","AuthCompanyController@profile");
       Route::post("signout","AuthCompanyController@signout");
       Route::post("refresh","AuthCompanyController@refresh");
       Route::group(
        [
            "prefix"=>"dashbaord",
        ],
        function ($router) {
            Route::get("orders","DashboardController@orders");
            Route::get("lastorders","DashboardController@lastorders");
            Route::get("chartdata","DashboardController@chartdata");
        }
    );
    // end of dashboard
    // begin of users 
    Route::group([
        "prefix"=>"users"
    ],function($router){
        Route::get("/","UsersController@index");
        Route::get("/{id}","UsersController@show");
        Route::post("/","UsersController@store");
        Route::put("/{id}","UsersController@update");
        Route::delete("/{id}","UsersController@destroy");
        Route::post("/{id}/refresh","UsersController@refresh");
    });
    // end of users
    // begin of orders 
    Route::group([
        "prefix"=>"orders"
    ],function($router){
        Route::get("/","OrdersController@index");
        Route::get("/{id}","OrdersController@show");
        // Route::post("/","OrdersController@store");
        // Route::put("/{id}","OrdersController@update");
        // Route::delete("/{id}","OrdersController@destroy");
        // Route::post("/{id}/refresh","OrdersController@refresh");
    });
    // end of orders
   });
   
