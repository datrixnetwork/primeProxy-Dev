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
        Route::post('/users/forgot','App\Http\Controllers\ctrl_User@forogtPassword');
        Route::post('/users/edit/password/{userId}','App\Http\Controllers\ctrl_User@editPassword');
    });

    Route::middleware('auth:api')->group(function(){

        Route::prefix('/admin')->group(function(){

            Route::middleware(['admin.auth', 'scope:validate-admin'])->group(function(){

                // v1.0 - Product group
                Route::resource('/products','App\Http\Controllers\ctrl_Product');
                Route::resource('/market/place','App\Http\Controllers\ctrl_Market');
                Route::resource('/sellers','App\Http\Controllers\ctrl_Seller');
                Route::resource('/orders','App\Http\Controllers\ctrl_Order');
                Route::get('/orders/status/counts','App\Http\Controllers\ctrl_Order@showOrdersCount');
                Route::resource('/orders/{orderId}/attachments','App\Http\Controllers\ctrl_Order_Attachment');
                Route::resource('/users','App\Http\Controllers\ctrl_User');
                Route::get('/users/act/count','App\Http\Controllers\ctrl_User@showUserActCounts');
                Route::resource('status','App\Http\Controllers\ctrl_Order_Status');
                Route::resource('/attach/statuses','App\Http\Controllers\ctrl_Attachment_Status');
                Route::resource('/settings','App\Http\Controllers\ctrl_Setting');
                Route::resource('/notification','App\Http\Controllers\ctrl_Notification');

            });

        });

        Route::prefix('/proxy')->group(function(){
            Route::resource('/products','App\Http\Controllers\ctrl_Product');
            Route::resource('/orders','App\Http\Controllers\ctrl_Order');
            Route::resource('status','App\Http\Controllers\ctrl_Order_Status');
            Route::resource('/attach/statuses','App\Http\Controllers\ctrl_Attachment_Status');
            Route::resource('/orders/{orderId}/attachments','App\Http\Controllers\ctrl_Order_Attachment');
            Route::get('/market/place','App\Http\Controllers\ctrl_Market@index');
            Route::get('/orders/status/counts','App\Http\Controllers\ctrl_Order@showOrdersCountUser');
            Route::get('proxy/dashboard','App\Http\Controllers\ctrl_Order@showOrderCommission');
        });

    });
    Route::resource('/setting/payment/gateway','App\Http\Controllers\ctrl_Payment_Gateway');

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
