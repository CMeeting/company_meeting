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
Route::namespace('Api')
    ->group(function(){
        Route::get('/sdkIndex','DocumentationController@sdkIndex')->name('documentation.sdkIndex');
        Route::get('/sdkInfo','DocumentationController@sdkInfo')->name('documentation.sdkInfo');
        Route::get('/blogs/bloglist','BlogsController@blogList');
        Route::get('/blogs/blogdetail','BlogsController@blogDetail');
        Route::get('/blogs/getBlogForTags','BlogsController@getBlogForTags');
        Route::post('/subscription','SubscriptionController@subscription_status');
        Route::any('/support','SupportController@getsupport');
    });
