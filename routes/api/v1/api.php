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


Route::middleware('header.auth')->group(function(){
    // v1.0 - Auth prefix Group
    Route::prefix('/auth')->group(function(){
        Route::post('/users/login','App\Http\Controllers\ctrl_User@login');
        Route::post('/users/logout','App\Http\Controllers\ctrl_User@logout')->middleware('auth:api');
        Route::post('/users/signup','App\Http\Controllers\ctrl_User_Info@store');
    });

    Route::middleware('auth:api')->group(function(){

        Route::prefix('/admin')->group(function(){

            Route::middleware(['admin.auth', 'scope:validate-admin'])->group(function(){
                // v1.0 - Product group
                Route::resource('/products','App\Http\Controllers\ctrl_Product');
                Route::resource('/sellers','App\Http\Controllers\ctrl_Seller');
                Route::resource('/orders','App\Http\Controllers\ctrl_Order');
                Route::resource('/orders/{orderId}/attachments','App\Http\Controllers\ctrl_Order_Attachment');
                Route::resource('/users','App\Http\Controllers\ctrl_User');
                Route::resource('/status','App\Http\Controllers\ctrl_Order_Status');
                Route::resource('/settings','App\Http\Controllers\ctrl_Setting');
            });

        });

        Route::prefix('/proxy')->group(function(){

        });

    });

});


Route::get('unautherization',function(){
    return response()->json(array('status'=>'failed','code'=>'401','message'=>'Unauthorized user'));
});


Route::any('{any}', function(){
    return response()->json([
        'status'    => 'failed',
        'code'      => 404,
        'message'   => 'You have an invalid URL or METHOD set in the URL Path property of a method from a REST API',
    ], 404);
})->where('any', '.*');
