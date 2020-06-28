<?php

/**
 * api 顶部
 */
Route::group(['namespace'=>'Api'],function (){

//    /**
//     *  utils api 分组
//     */
//    Route::group(["prefix" => "utils",'namespace'=>'Utils'],function () {
//        Route::get("getCsrf","CSRFController@getCsrf")->name('utils.getcsrf');
//    });

    /**
     *  order api 分组
     */
    Route::group(["prefix" => "order",'namespace'=>'Order'],function () {
        /**
         * orders
         */
        Route::get('orders/findByUid','OrdersController@findByUid')->name('orders.findByUid');
        Route::put('orders/{id}/changeOrderStatus/{value}','OrdersController@changeOrderStatus')->name('orders.changeOrderStatus');
        Route::post('orders/findOrderByOrderNoAndPhone','OrdersController@findOrderByOrderNoAndPhone')->name('orders.findOrderByOrderNoAndPhone');
        Route::post('orders/search','OrdersController@search')->name('orders.search');
        Route::post('orders/search/{page}/{size}','OrdersController@searchPage')->name('orders.searchPage');
        Route::resource("orders","OrdersController",[
            'only'=>['index','show','store','destroy']
        ]);
    });


    /**
     *  property api 分组
     */
    Route::group(["prefix" => "property",'namespace'=>'Property'],function () {

        /**
         * keys
         */
        Route::resource("keys","KeysController",[
            'only'=>['index','show','store','update','destroy']
        ]);
        Route::put('keys/{id}/putOn','KeysController@putOn')->name('keys.putOn');
        Route::get('keys/{id}/findByCateId','KeysController@findByCateId')->name('keys.findByCateId');

    });


    /**
     *  product api 分组
     */
    Route::group(["prefix" => "product",'namespace'=>'Product'],function () {


        /**
         * cates
         */
        Route::resource("cates","CatesController",[
            'only'=>['index','show','store','update','destroy']
        ]);
        Route::put('cates/{id}/putOn','CatesController@putOn')->name('cates.putOn');
        Route::get('cates/{id}/getSpus','CatesController@getSpus')->name('cates.getSpus');
        Route::get('cates/{id}/getKeys','CatesController@getKeys')->name('cates.getKeys');
        Route::post('cates/search','CatesController@search')->name('cates.search');
        Route::post('cates/search/{page}/{size}','CatesController@searchPage')->name('cates.searchPage');


        /**
         * spus
         */
        Route::resource("spus","SpusController",[
            'only'=>['index','show','store','update','destroy']
        ]);
        Route::put('spus/{id}/putOn','SpusController@putOn')->name('spus.putOn');
        Route::get('spus/{id}/getSkus','SpusController@getSkus')->name('spus.getSkus');
        Route::post('spus/search','SpusController@search')->name('spus.search');
        Route::post('spus/search/{page}/{size}','SpusController@searchPage')->name('spus.searchPage');

        /**
         * skus
         */
        Route::resource("skus","SkusController",[
            'only'=>['index','show','store','update','destroy']
        ]);
        Route::put('skus/{id}/updateStock/{value}','SkusController@updateStock')->name('skus.updateStock');
        Route::post('skus/search','SkusController@search')->name('skus.search');
        Route::post('skus/search/{page}/{size}','SkusController@searchPage')->name('skus.searchPage');

    });

    /**
     * user api 分组
     */
    Route::group(["prefix" => "user",'namespace'=>'User'],function () {
        // more

        /**
         * users
         */
        Route::group(["prefix"=>"users"],function (){
            Route::post("login","UserAuthController@login")->name('users.login');
            Route::get("logout","UserAuthController@logout")->name('users.logout');
            Route::get("getInfo","UserAuthController@getInfo")->name('users.getInfo');
            Route::put("changePassword","UserAuthController@changePassword")->name('users.changePassword');
            Route::put("userChangePassword","UserAuthController@userChangePassword")->name('users.userChangePassword');
            Route::put("editUserInfo","UserAuthController@editUserInfo")->name('users.editUserInfo');
            Route::post('register','UserAuthController@register')->name('users.register');
            Route::get('active','UserAuthController@active')->name('users.active');
            Route::post('handleForget','UserAuthController@handleForget')->name('users.handleForget');
        });
        /* 权限分开*/
        Route::resource("users","UsersController",[
            'only'=>['index','show','update','destroy']
        ]);
        Route::put('users/{id}/unseal','UsersController@unseal')->name('users.unseal');
        Route::put('users/{id}/forbid','UsersController@forbid')->name('users.forbid');
        Route::post('users/search','UsersController@search')->name('users.search');
        Route::post('users/search/{page}/{size}','UsersController@searchPage')->name('users.searchPage');


        /**
         * preInfos
         */
        Route::get('preInfos/findByUid','PreInfosController@findByUid')->name("preInfos.findByUid");
        Route::resource("preInfos","PreInfosController",[
            'only'=>['index','show','store','update','destroy']
        ]);
        Route::post('preInfos/search','PreInfosController@search')->name('preInfos.search');
        Route::post('preInfos/search/{page}/{size}','PreInfosController@searchPage')->name('preInfos.searchPage');

        /**
         * admins
         */
        Route::group(["prefix"=>"admins"],function (){
            Route::post("login","AdminController@login")->name('admins.login');
            Route::get("getInfo","AdminController@getInfo")->name('admins.getInfo');
            Route::get("logout","AdminController@logout")->name('admins.logout');
          /*  Route::put("changePassword","AdminController@changePassword")->name('admins.changePassword'); */
        });
        Route::resource("admins","AdminController",[
            'only'=>['index','show','store','update','destroy']
        ]);
        Route::put('admins/{id}/unseal','AdminController@unseal')->name('admins.unseal');
        Route::post('admins/search','AdminController@search')->name('admins.search');
        Route::post('admins/search/{page}/{size}','AdminController@searchPage')->name('admins.searchPage');



    });
});
