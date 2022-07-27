<?php

/**
 * 后台路由
 */

/**后台模块**/
Route::group(['namespace' => 'Admin','prefix' => 'admin'], function (){

    Route::get('login','AdminsController@showLoginForm')->name('login');  //后台登陆页面

    Route::post('login-handle','AdminsController@loginHandle')->name('login-handle'); //后台登陆逻辑

    Route::get('logout','AdminsController@logout')->name('admin.logout'); //退出登录

    /**需要登录认证模块**/
    Route::middleware(['auth:admin','rbac'])->group(function (){

        Route::resource('index', 'IndexsController', ['only' => ['index']]);  //首页

       // Route::resource('documentation','DocumentationController',['only'=>['index','create','store','update','edit','destroy','platformVersion'] ]);

        Route::get('platformVersion', 'DocumentationController@platformVersion')->name('documentation.platformVersion');
        Route::get('sdkClassification', 'DocumentationController@sdkClassification')->name('documentation.sdkClassification');
        Route::get('sdkDocumentation', 'DocumentationController@sdkDocumentation')->name('documentation.sdkDocumentation');



        Route::get('updateSdkClassification/{id}', 'DocumentationController@updateSdkClassification')->name('documentation.updateSdkClassification');
        Route::get('updateRunPlatformVersion/{id}', 'DocumentationController@updatePlatformVersion')->name('documentation.updatePlatformVersion');
        Route::get('updatesdkDocumentation/{id}', 'DocumentationController@updatesdkDocumentation')->name('documentation.updatesdkDocumentation');
        Route::post('updateRunSdkclassification', 'DocumentationController@updateRunSdkclassification')->name('documentation.updateRunSdkclassification');
        Route::post('updateRunPlatformVersion', 'DocumentationController@updateRunPlatformVersion')->name('documentation.updateRunPlatformVersion');
        Route::post('updateRunsdkDocumentation', 'DocumentationController@updateRunsdkDocumentation')->name('documentation.updateRunsdkDocumentation');


        Route::get('platformVersioncreate/{pid?}', 'DocumentationController@createPlatformVersion')->name('documentation.createPlatformVersion');
        Route::get('createsdkDocumentation/{classification_ids?}', 'DocumentationController@createsdkDocumentation')->name('documentation.createsdkDocumentation');
        Route::get('createSdkClassification/{pid?}/{platformid?}/{version?}', 'DocumentationController@createSdkClassification')->name('documentation.createSdkClassification');
        Route::post('createRunSdkclassification', 'DocumentationController@createRunSdkclassification')->name('documentation.createRunSdkclassification');
        Route::post('createRunPlatformVersion', 'DocumentationController@createRunPlatformVersion')->name('documentation.createRunPlatformVersion');
        Route::post('createRunsdkDocumentation', 'DocumentationController@createRunsdkDocumentation')->name('documentation.createRunsdkDocumentation');


        Route::post('showHideclassification', 'DocumentationController@showHideclassification')->name('documentation.showHideclassification');

        Route::post('delPlatformVersion', 'DocumentationController@delPlatformVersion')->name('documentation.delPlatformVersion');
        Route::post('delsdkDocumentation', 'DocumentationController@delsdkDocumentation')->name('documentation.delsdkDocumentation');
        Route::post('delSdkclassification', 'DocumentationController@delSdkclassification')->name('documentation.delSdkclassification');
        Route::post('update_leve', 'DocumentationController@update_leve')->name('documentation.update_leve');


        Route::get('index/main', 'IndexsController@main')->name('index.main'); //首页数据分析

        Route::get('blogs/blog/{slug?}', 'BlogsController@blog')->name('blogs.blog'); //blog首页
        Route::get('blogs/blogCreate', 'BlogsController@blogCreate')->name('blogs.blogCreate');
        Route::post('blogs/blogStore', 'BlogsController@blogStore')->name('blogs.blogStore');
        Route::get('blogs/blogEdit/{id}', 'BlogsController@blogEdit')->name('blogs.blogEdit');
        Route::post('blogs/blogUpdate/{id}', 'BlogsController@blogUpdate')->name('blogs.blogUpdate');
        Route::post('blogs/editorUpload', 'BlogsController@editorUpload')->name('editorUpload');
//        Route::resource('blogs','BlogsController',['only'=>['blog','create','store','update','edit','destroy'] ]);
//        Route::resource('blogs','BlogsController',['only'=>['tags','tagCreate','tagStore','update','tagEdit','destroy'] ]);
        Route::get('blogs/tags', 'BlogsController@tags')->name('blogs.tags'); //tags首页
        Route::get('blogs/tagCreate', 'BlogsController@tagCreate')->name('blogs.tagCreate');
        Route::post('blogs/tagStore', 'BlogsController@tagStore')->name('blogs.tagStore');
        Route::get('blogs/tagEdit/{id}', 'BlogsController@tagEdit')->name('blogs.tagEdit');
        Route::post('blogs/tagUpdate/{id}', 'BlogsController@tagUpdate')->name('blogs.tagUpdate');
        Route::get('blogs/softDel/{table?}/{id?}', 'BlogsController@softDel')->name('blogs.softDel');
//        Route::get('blogs/tagEdit/{id}', function ($id) {
//            return $id;
//        })->name('blogs.tagEdit');
        Route::get('blogs/types', 'BlogsController@types')->name('blogs.types'); //types首页
        Route::get('blogs/typeCreate', 'BlogsController@typeCreate')->name('blogs.typeCreate');
        Route::post('blogs/typeStore', 'BlogsController@typeStore')->name('blogs.typeStore');
        Route::get('blogs/typeEdit/{id}', 'BlogsController@typeEdit')->name('blogs.typeEdit');
        Route::post('blogs/typeUpdate/{id}', 'BlogsController@typeUpdate')->name('blogs.typeUpdate');

        Route::get('admins/status/{statis}/{admin}','AdminsController@status')->name('admins.status');

//        Route::get('admins/delete/{admin}','AdminsController@delete')->name('admins.delete');
        Route::get('admins/delete','AdminsController@del')->name('admins.delete');

        Route::resource('admins','AdminsController',['only' => ['index', 'create', 'store', 'update', 'edit']]); //管理员

        Route::get('roles/access/{role}','RolesController@access')->name('roles.access');

        Route::post('roles/group-access/{role}','RolesController@groupAccess')->name('roles.group-access');

        Route::resource('roles','RolesController',['only'=>['index','create','store','update','edit','destroy'] ]);  //角色

        Route::get('roles/delete','RolesController@del')->name('roles.delete');

        Route::get('rules/status/{status}/{rules}','RulesController@status')->name('rules.status');

        Route::resource('rules','RulesController',['only'=> ['index','create','store','update','edit','destroy'] ]);  //权限

        Route::resource('actions','ActionLogsController',['only'=> ['index'] ]);  //日志
        
        
    });
    
    Route::group( ['namespace' => "Count", 'middleware' => ['auth:admin','rbac']],function (){
        Route::resource('dayCount','DayCountController',['only'=> ['index'] ]);  //用户总览
        Route::resource('keepCount','KeepCountController',['only'=> ['index'] ]);  //留存 
        Route::resource('liveUserCount','LiveUserCountController',['only'=> ['index'] ]);  //活跃统计 
        Route::get('gameUser','GameUserController@search')->name('gameUser.search');  //用户查询 
        
        Route::resource('appCount','AppCountController',['only'=> ['index'] ]);  //应用总览 
        Route::resource('usidCount','UsidCountController',['only'=> ['index'] ]);  //厂商总览 
        
        Route::get('gameApp/info','GameAppController@info' )->name('gameApp.info');  //应用详情  应用排行  应用审核  应用配置
        Route::get('gameApp/rank','GameAppController@rank')->name('gameApp.rank');  //   应用排行  应用审核   
        Route::get('gameApp/check','GameAppController@check')->name('gameApp.check');  //    应用审核   
        Route::get('gameApp/config','GameAppController@config')->name('gameApp.config');  //    应用配置   
        Route::get('gameApp/typeConfig','GameAppController@typeConfig')->name('gameApp.typeConfig');  //     类型配置
        
        Route::resource('pay','PayController',['only'=> ['index'] ]);  //充值  
        Route::get('pay/info','PayController@info')->name('pay.info');  //     充值详情
        Route::resource('realPay','RealPayController',['only'=> ['index'] ]);  //提现详情
        Route::resource('payCount','PayCountController',['only'=> ['index'] ]);  //流水统计
        
        Route::get('config/pfid','ConfigController@pfid')->name('config.pfid');//关系配置
        Route::get('admin/management','AdminController@management')->name('admin.management');//账户管理  角色管理
        Route::get('admin/pfidIndex/{level}','AdminController@pfidIndex')->name('admin.pfidIndex');//账户管理  角色管理
        //
        Route::get('admin/createPfid/{level}','AdminController@createPfid')->name('admin.createPfid');//账户管理  角色管理
        //Route::get('admin/usidIndex','AdminController@usidIndex')->name('admin.usidIndex');//账户管理  角色管理
        
        Route::get('admin/role1','AdminController@role1')->name('admin.role1');//账户管理  角色管理
        
        Route::get('config/index','ConfigController@index')->name('config.index');//配置
        Route::get('config/create/{key}','ConfigController@create')->name('config.create');//配置
        Route::get('config/delete/{id}','ConfigController@delete')->name('config.delete');//配置
        Route::post('config/opeary/{id}','ConfigController@opeary')->name('config.opeary');//配置
    });
});

