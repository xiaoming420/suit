<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    //echo "Welcom McDonald's.";

     return view('index');
});

// 后台
Route::group(['prefix'=>'adm'], function(){

    Route::get('login', 'Adm\Users\LoginController@login');
    Route::post('dologin', 'Adm\Users\LoginController@dologin');
    Route::get('signout', 'Adm\Users\LoginController@signout');
    Route::get('error', 'Adm\Video\VideoController@error');


    Route::group(['prefix'=>'user'], function(){

    });
});




Route::group(['prefix'=>'suit'], function(){
    Route::get('register', 'Web\WebController@register');
    Route::get('reserve', 'Web\WebController@reserve');
});


// 预约服务
Route::group(['prefix'=>'subscribe'], function(){
    Route::get('infopage', 'Web\SubscribeController@infopage'); // 预约界面
    Route::get('getcity', 'Web\SubscribeController@getcity');
});




Route::group(['prefix'=>'adm', 'middleware'=>['adm.token']], function(){

    Route::get('index', 'Adm\Users\UserController@index');

    Route::post('edituserpass', 'Adm\Users\UserController@edituserpass');

    // 后台管理员模块
    Route::group(['prefix'=>'user'], function(){
        Route::get('userlist', 'Adm\Users\UserController@userlist');
        Route::get('adduser', 'Adm\Users\UserController@adduser');
        Route::post('doadduser', 'Adm\Users\UserController@doadduser');
        Route::get('deluser', 'Adm\Users\UserController@deluser');
        Route::get('edituser', 'Adm\Users\UserController@editUserView');
        Route::post('doedituser', 'Adm\Users\UserController@doedituser');
        Route::any('group', 'Adm\Users\UserController@group');
    });

    Route::group(['prefix'=>'reserve'], function(){
        Route::get('reserveList', 'Adm\Users\ReserveController@reserveList');
        Route::any('addgoods', 'Adm\Goods\GoodsController@addgoods');
        Route::any('editsign', 'Adm\Goods\GoodsController@doEditSign');
        Route::any('delgoods', 'Adm\Goods\GoodsController@delgoods');

    });

    Route::group(['prefix'=>'discount'], function(){
        Route::get('discountdetail', 'Adm\Users\DiscountController@discountDetail');
        Route::post('doeditdiscount', 'Adm\Users\DiscountController@doEditDiscount');
    });

});
