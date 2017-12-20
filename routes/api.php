<?php

use Illuminate\Http\Request;

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

// 调用接口
Route::group(['prefix'=>'user'], function() {
    Route::post('login', 'Api\UserController@login');
    Route::post('doregister', 'Web\WebController@doRegister');
    Route::get('receivetype', 'Api\UserController@receivetype');
    Route::post('addgroup', 'Api\UserController@addgroup');
    Route::post('getgid', 'Api\UserController@getgid');
});

Route::group(['prefix'=>'address'], function() {
    Route::post('addresslist', 'Api\AddressController@addresslist');
    Route::post('address', 'Api\AddressController@address');
    Route::get('getareas', 'Api\AddressController@getareas');
    Route::post('checkarea', 'Api\AddressController@checkarea');
    Route::post('test', 'Api\AddressController@test');
});

Route::group(['prefix'=>'test'], function(){
    Route::post('gettoken', 'Api\TestController@gettoken');
    Route::any('test', 'Api\TestController@test');
    Route::any('tests', 'Api\TestController@tests');
});


