<?php

/**
 * 后台路由
 */

/**后台模块**/
Route::group(['namespace' => 'Admin','prefix' => 'admin'], function (){

    Route::get('login','AdminsController@showLoginForm')->name('login');  //后台登陆页面

    Route::post('login-handle','AdminsController@loginHandle')->name('login-handle'); //后台登陆逻辑

    Route::get('logout','AdminsController@logout')->name('admin.logout'); //退出登录

    Route::get('editAvatar/{id}','AdminsController@editAvatar')->name('admin.editAvatar'); //修改头像页面
    Route::post('updateAvatar/{id}','AdminsController@updateAvatar')->name('admin.updateAvatar'); //修改头像
    Route::get('editPassword/{id}','AdminsController@editPassword')->name('admin.editPassword'); //修改密码页面
    Route::post('updatePassword/{id}','AdminsController@updatePassword')->name('admin.updatePassword'); //修改密码

    /**需要登录认证模块**/
    Route::middleware(['auth:admin','rbac'])->group(function (){

        Route::resource('index', 'IndexsController', ['only' => ['index']]);  //首页

        //Speakers
        Route::get('fruser_list', 'FrUserController@list')->name('fruser.list');
        Route::post('fruser_import', 'FrUserController@import')->name('fruser.import');

        //电子报
        Route::get('subscription_list', 'NewsletterController@subscription_list')->name('newsletter.subscription_list');
        Route::get('createsubscription', 'NewsletterController@createsubscription')->name('newsletter.createsubscription');
        Route::post('createrunsubscription', 'NewsletterController@createrunsubscription')->name('newsletter.createrunsubscription');
        Route::post('toggle_status', 'NewsletterController@toggle_status')->name('newsletter.toggle_status');
        Route::get('updatesubscription/{id}', 'NewsletterController@updatesubscription')->name('newsletter.updatesubscription');
        Route::post('updaterunsubscription', 'NewsletterController@updaterunsubscription')->name('newsletter.updaterunsubscription');

        Route::get('newsletter_list', 'NewsletterController@newsletter_list')->name('newsletter.newsletter_list');
        Route::get('createnewsletter', 'NewsletterController@createnewsletter')->name('newsletter.createnewsletter');
        Route::post('createrunnewsletter', 'NewsletterController@createrunnewsletter')->name('newsletter.createrunnewsletter');
        Route::get('updatenewsletter/{id}', 'NewsletterController@updatenewsletter')->name('newsletter.updatenewsletter');
        Route::post('updaterunnewsletter', 'NewsletterController@updaterunnewsletter')->name('newsletter.updaterunnewsletter');
        Route::get('newsletter_info/{id}', 'NewsletterController@newsletter_info')->name('newsletter.newsletter_info');
        Route::post('delnewsletter', 'NewsletterController@delnewsletter')->name('newsletter.delnewsletter');
        Route::post('newsletterlog', 'NewsletterController@newsletterlog')->name('newsletter.newsletterlog');
        Route::get('ajaxsend/{id}', 'NewsletterController@ajaxsend')->name('newsletter.ajaxsend');
        Route::get('newsletterloglist', 'NewsletterController@newsletterloglist')->name('newsletter.newsletterloglist');
        Route::get('newsletterloginfo/{id}', 'NewsletterController@newsletterloginfo')->name('newsletter.newsletterloginfo');
        Route::post('again_sendfind', 'NewsletterController@again_sendfind')->name('newsletter.again_sendfind');

        //邮件模板
        Route::get('mailmagic_list', 'MailmagicboardController@mailmagic_list')->name('mailmagicboard.mailmagic_list');
        Route::get('createmailmagiclist', 'MailmagicboardController@createmailmagiclist')->name('mailmagicboard.createmailmagiclist');
        Route::post('createrunmailmagic', 'MailmagicboardController@createrunmailmagic')->name('mailmagicboard.createrunmailmagic');
        Route::post('delmailmagic', 'MailmagicboardController@delmailmagic')->name('mailmagicboard.delmailmagic');
        Route::get('updatemailmagiclist/{id}', 'MailmagicboardController@updatemailmagiclist')->name('mailmagicboard.updatemailmagiclist');
        Route::post('updaterunmailmagiclist', 'MailmagicboardController@updaterunmailmagiclist')->name('mailmagicboard.updaterunmailmagiclist');
        Route::get('mailmagiclist_info/{id}', 'MailmagicboardController@mailmagiclist_info')->name('mailmagicboard.mailmagiclist_info');
        Route::post('send_email', 'MailmagicboardController@send_email')->name('mailmagicboard.send_email');


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

        Route::get('blogs/blog', 'BlogsController@blog')->name('blogs.blog'); //blog首页
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

        Route::get('changelogs/list', 'ChangeLogsController@list')->name('changelogs.list'); //changelogs首页
        Route::get('changelogs/create', 'ChangeLogsController@create')->name('changelogs.create');
        Route::post('changelogs/store', 'ChangeLogsController@store')->name('changelogs.store');
        Route::get('changelogs/edit/{id}', 'ChangeLogsController@edit')->name('changelogs.edit');
        Route::post('changelogs/update/{id}', 'ChangeLogsController@update')->name('changelogs.update');
        Route::get('changelogs/softDel/{id?}', 'ChangeLogsController@softDel')->name('changelogs.softDel');
        Route::post('changelogs/getsupport', 'ChangeLogsController@getsupport')->name('changelogs.getsupport');


        Route::get('support/list', 'SupportController@list')->name('support.list'); //support首页
        Route::get('support/create', 'SupportController@create')->name('support.create');//support新增页面
        Route::post('support/store', 'SupportController@store')->name('support.store');//support保存功能
        Route::get('support/edit/{id}', 'SupportController@edit')->name('support.edit');//support编辑页面
        Route::post('support/update/{id}', 'SupportController@update')->name('support.update');//support编辑功能
        Route::post('support/changeStatus', 'SupportController@changeStatus')->name('support.changeStatus');//support改变状态
        Route::get('support/softDel/{id?}', 'SupportController@softDel')->name('support.softDel');//support删除

        //用户管理
        Route::get('user/list', 'UserController@list')->name('user.list'); //用户列表
        Route::get('user/create', 'UserController@create')->name('user.create'); //添加用户页面
        Route::post('user/store', 'UserController@store')->name('user.store'); //添加用户接口
        Route::get('user/edit/{id}', 'UserController@edit')->name('user.edit'); //编辑资料页面
        Route::post('user/update/{id}', 'UserController@update')->name('user.update'); //更新资料
        Route::get('user/detail/{id}', 'UserController@detail')->name('user.detail'); //详情
        Route::post('user/export', 'UserController@export')->name('user.export'); //导出
        Route::get('user/resetPassword/{id}', 'UserController@resetPassword')->name('user.resetPassword');//重置密码
        Route::get('user/logout-list', 'UserController@logoutList')->name('user.logoutList'); //注销用户列表

        Route::get('goodsclassification/index', 'GoodsclassificationController@index')->name('goodsclassification.index');
        Route::get('goodsclassification/sdkindex', 'GoodsclassificationController@sdkindex')->name('goodsclassification.sdkindex');

        Route::get('goodsclassification/creategoodsClassification/{pid?}', 'GoodsclassificationController@creategoodsClassification')->name('goodsclassification.creategoodsClassification');
        Route::get('goodsclassification/createsaasgoodsClassification/{pid?}', 'GoodsclassificationController@createsaasgoodsClassification')->name('goodsclassification.createsaasgoodsClassification');

        Route::post('goodsclassification/createRungoodsclassification', 'GoodsclassificationController@createRungoodsclassification')->name('goodsclassification.createRungoodsclassification');
        Route::post('goodsclassification/createRunsaasgoodsclassification', 'GoodsclassificationController@createRunsaasgoodsclassification')->name('goodsclassification.createRunsaasgoodsclassification');

        Route::get('goodsclassification/updategoodsClassification/{pid}', 'GoodsclassificationController@updategoodsClassification')->name('goodsclassification.updategoodsClassification');

        Route::get('goodsclassification/updatesaasgoodsClassification/{pid}', 'GoodsclassificationController@updatesaasgoodsClassification')->name('goodsclassification.updatesaasgoodsClassification');

        Route::post('goodsclassification/updateRungoodsclassification', 'GoodsclassificationController@updateRungoodsclassification')->name('goodsclassification.updateRungoodsclassification');

        Route::post('goodsclassification/updatesaasRungoodsclassification', 'GoodsclassificationController@updatesaasRungoodsclassification')->name('goodsclassification.updatesaasRungoodsclassification');

        Route::post('goodsclassification/delgoodsclassification', 'GoodsclassificationController@delgoodsclassification')->name('goodsclassification.delgoodsclassification');

        Route::post('goodsclassification/delsaasgoodsclassification', 'GoodsclassificationController@delsaasgoodsclassification')->name('goodsclassification.delsaasgoodsclassification');

        Route::get('goods/index', 'GoodsController@index')->name('goods.index');
        Route::get('goods/saasIndex', 'GoodsController@saasIndex')->name('goods.saasIndex');
        Route::get('goods/creategoods', 'GoodsController@creategoods')->name('goods.creategoods');
        Route::get('goods/createsaasgoods', 'GoodsController@createsaasgoods')->name('goods.createsaasgoods');
        Route::post('goods/createrungoods', 'GoodsController@createrungoods')->name('goods.createrungoods');
        Route::post('goods/createrunsaasgoods', 'GoodsController@createrunsaasgoods')->name('goods.createrunsaasgoods');
        Route::get('goods/updategoods/{id}', 'GoodsController@updategoods')->name('goods.updategoods');
        Route::get('goods/updatesaasgoods/{id}', 'GoodsController@updatesaasgoods')->name('goods.updatesaasgoods');

        Route::post('goods/updaterungoods', 'GoodsController@updaterungoods')->name('goods.updaterungoods');
        Route::post('goods/updaterunsaasgoods', 'GoodsController@updaterunsaasgoods')->name('goods.updaterunsaasgoods');

        Route::post('goods/delgoods', 'GoodsController@delgoods')->name('goods.delgoods');
        Route::post('goods/show', 'GoodsController@show')->name('goods.show');
        Route::get('goods/info/{id}', 'GoodsController@info')->name('goods.info');
        Route::get('goods/saasinfo/{id}', 'GoodsController@saasinfo')->name('goods.saasinfo');


        Route::get('order/index', 'OrderController@index')->name('order.index');
        Route::get('order/saasindex', 'OrderController@saasindex')->name('order.saasindex');
        Route::get('order/create', 'OrderController@create')->name('order.create');
        Route::get('order/saascreate', 'OrderController@saascreate')->name('order.saascreate');
        Route::get('order/getinfo/{id}', 'OrderController@getinfo')->name('order.getinfo');
        Route::get('order/getsaasinfo/{id}', 'OrderController@getsaasinfo')->name('order.getsaasinfo');
        Route::post('order/createrun', 'OrderController@createrun')->name('order.createrun');
        Route::post('order/saascreaterun', 'OrderController@saascreaterun')->name('order.saascreaterun');
        Route::post('order/updatestatus', 'OrderController@updatestatus')->name('order.updatestatus');

        Route::get('license/index', 'LicenseController@index')->name('license.index');
        Route::get('license/createLicense','LicenseController@createLicense')->name('license.createLicense');
        Route::post('license/createrunLicense','LicenseController@createrunLicense')->name('license.createrunLicense');
        Route::get('license/updateLicense/{id}', 'LicenseController@updateLicense')->name('license.updateLicense');
        Route::post('license/changeStatus', 'LicenseController@changeStatus')->name('license.changeStatus');
        Route::get('license/info/{id}', 'LicenseController@info')->name('license.info');

        Route::get('emailBlacklist/update', 'EmailBlackListController@store')->name('emailBlacklist.update');//邮箱黑名单
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

/**下载文件**/
Route::group(['namespace'=>'Common', 'middleware' => ['auth:admin','rbac']], function(){
    Route::get('download', 'FileController@download')->name('download'); //下载文件
});

