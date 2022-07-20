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
        Route::get('/blogList','BlogsController@blogList')->name('blogs.blogList');
        Route::get('/blogDetail','BlogsController@blogDetail')->name('blogs.blogDetail');
        Route::get('/getBlogForTags','BlogsController@getBlogForTags')->name('blogs.getBlogForTags');
    });
