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
Route::group(['namespace' => 'Api'], function () {
    Route::get('/sdkIndex', 'DocumentationController@sdkIndex')->name('documentation.sdkIndex');
    Route::get('/sdkInfo', 'DocumentationController@sdkInfo')->name('documentation.sdkInfo');
    Route::get('/blogs/bloglist', 'BlogsController@blogList');
    Route::get('/blogs/blogdetail', 'BlogsController@blogDetail');
    Route::get('/blogs/getBlogForTags', 'BlogsController@getBlogForTags');
    Route::post('/subscription', 'SubscriptionController@subscription_status');
    Route::any('/support', 'SupportController@getsupport');
    Route::any('/thefeedback', 'SupportController@thefeedback');
    Route::any('/testemail', 'OrderController@testemail');
    Route::get('/changelogs', 'ChangelogsController@changelogs');
    Route::get('/getgoods', 'GoodsController@getGoods');
    Route::post('/getgoodsprice', 'OrderController@getgoodsprice');
    Route::get('/notify', 'OrderController@notify');
    Route::get('/wechatNotify', 'OrderController@wechatNotify');
    Route::get('/emailtest', 'EmailtestContr@emailtest');
    Route::post('/paddlecallback', 'OrderController@paddlecallback');
    Route::get('/invoicemice', 'OrderController@invoicemice');
    Route::post('/paypal-notify', 'OrderController@payPalNotify');
    //webviewer 生成序列码
    Route::post('/license/generate', 'WebViewerLicenseController@generate');
    //webviewer 校验序列码
    Route::post('/license/verify', 'WebViewerLicenseController@verify');
    Route::get('/paypal-callback', 'OrderController@payPalCallBack');

    //support
    Route::post('support', 'ContactEmailController@support');
    Route::post('upload-attachments', 'ContactEmailController@uploadAttachments');

    Route::get('get-saas-goods', 'GoodsController@getSaaSGoods');
    Route::post('/webhook', 'SaaSOrderController@webHook');

    Route::get('activate-test', 'UserController@activateTest');
});

Route::group(['middleware' => ['jwt.auth', 'cors'], 'namespace' => 'Api'], function () {
    Route::post('/cart', 'OrdercartController@cart');
    Route::get('/getcart', 'OrdercartController@getcart');
    Route::post('/getorderinfo', 'OrderController@getorderinfo');
    Route::get('/getorderlist', 'OrderController@getorderlist');
    Route::post('/get_license', 'OrderController@getlicense');
    Route::post('/getordertryoutlist', 'OrderController@getordertryoutlist');
    Route::post('/createorder', 'OrderController@createorder');
    Route::post('/createcatorder', 'OrdercartController@createcatorder');
    Route::post('/noorderpay', 'OrderController@noorderpay');
    Route::post('/repurchase', 'OrderController@repurchase');
    Route::post('/newOrder', 'OrderController@newOrder');
    Route::post('/rewinfo', 'OrderController@rewinfo');
    Route::post('/send-payment-failed-email', 'OrderController@sendPaymentFailedEmail');

    //SaaS
    Route::post('/create-saas-order', 'SaaSOrderController@createOrder');
    Route::get('/get-order-status', 'SaaSOrderController@getOrderStatus');
});

//用户管理
Route::group(['prefix' => 'user', 'namespace' => 'Api'], function () {
    //注册
    Route::post('register', 'UserController@register');
    //登录
    Route::post('login', 'UserController@login');
    //忘记密码
    Route::post('forget-password', 'UserController@forgetPassword');
    //修改密码 - 通过邮箱修改
    Route::post('change-password-by-email', 'UserController@changePasswordByEmail');
    //验证激活
    Route::post('register-verify', 'UserController@verifyRegister');
    //发送验证邮箱邮件
    Route::post('send-verify-email', 'UserController@sendVerifyEmail');

    Route::group(['middleware' => ['jwt.auth', 'cors']], function () {
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
        //修改账单信息-试用
        Route::post('edit-billing-info-trial', 'UserController@editBillingInfoFromTrial');
        //注销账号
        Route::post('logout', 'UserController@logout');
        //退出登录
        Route::get('sign-out', 'UserController@signOut');
    });
});

Route::post('upload', 'Common\FileController@upload')->name('upload'); //下载文件

//测试生成序列码
Route::post('generate-license-code', 'Admin\LicenseController@generateLicenseCode');
//测试验证序列码
Route::post('verify', 'Admin\LicenseController@verifyLicenseCode');

Route::group(['namespace'=>'Common'], function(){
    Route::post('get-invoice', 'FileController@getInvoice')->name('get-invoice'); //下载文件
});

