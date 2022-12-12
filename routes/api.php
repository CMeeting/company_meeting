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

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
Route::group(['namespace'=>'Api'], function(){
        Route::get('/sdkIndex','DocumentationController@sdkIndex')->name('documentation.sdkIndex');
        Route::get('/sdkInfo','DocumentationController@sdkInfo')->name('documentation.sdkInfo');
        Route::get('/blogs/bloglist','BlogsController@blogList');
        Route::get('/blogs/blogdetail','BlogsController@blogDetail');
        Route::get('/blogs/getBlogForTags','BlogsController@getBlogForTags');
        Route::post('/subscription','SubscriptionController@subscription_status');
        Route::any('/support','SupportController@getsupport');
        Route::any('/thefeedback','SupportController@thefeedback');
        Route::get('/changelogs','ChangelogsController@changelogs');
        Route::get('/getgoods','GoodsController@getGoods');
        Route::post('/getgoodsprice','OrderController@getgoodsprice');
        Route::get('/notify','OrderController@notify');
        Route::get('/wechatNotify','OrderController@wechatNotify');
        Route::get('/emailtest','EmailtestContr@emailtest');
        Route::post('/paddlecallback','OrderController@paddlecallback');
});

Route::group(['middleware'=>'jwt.auth', 'namespace'=>'Api'], function(){
    Route::post('/cart','OrdercartController@cart');
    Route::get('/getcart','OrdercartController@getcart');
    Route::post('/getorderinfo','OrderController@getorderinfo');
    Route::get('/getorderlist','OrderController@getorderlist');
    Route::post('/get_license','OrderController@getlicense');
    Route::post('/getordertryoutlist','OrderController@getordertryoutlist');
    Route::post('/createorder','OrderController@createorder');
    Route::post('/createcatorder','OrdercartController@createcatorder');
    Route::post('/noorderpay','OrderController@noorderpay');
    Route::post('/repurchase','OrderController@repurchase');
});

//用户管理
Route::group(['prefix'=>'user', 'namespace'=>'Api'], function (){
    //注册
    Route::post('register', 'UserController@register');
    //登录
    Route::post('login', 'UserController@login');
    //忘记密码
    Route::post('forget-password', 'UserController@forgetPassword');
    //修改密码 - 通过邮箱修改
    Route::post('change-password-by-email', 'UserController@changePasswordByEmail');

    Route::group(['middleware'=>'jwt.auth'], function (){
        //修改密码 - 用户中心修改
        Route::post('change-password', 'UserController@changePassword');
        //修改邮箱
        Route::post('change-email', 'UserController@changeEmail');
        //获取用户基本信息
        Route::get('get-user-info', 'UserController@getUserInfo');
        //修改Full Name
        Route::post('change-fullName', 'UserController@changeFullName');
        //获取账单信息
        Route::get('get-billing-info', 'UserController@getBillingInfo');
        //修改账单信息
        Route::post('edit-billing-info', 'UserController@editBillingInfo');
        //注销账号
        Route::post('logout', 'UserController@logout');
        //退出登录
        Route::get('sign-out', 'UserController@signOut');
    });
});


Route::post('generate-license-code', 'Admin\LicenseController@generateLicenseCode');
