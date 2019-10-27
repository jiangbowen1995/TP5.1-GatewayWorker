<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

//Route::get('think', function () {
//    return 'hello,ThinkPHP5!';
//});
//
//Route::get('hello/:name', 'index/hello');
//
//return [
//
//];
//use think\Route;
Route::rule('/','api/v1.home.index/index','GET');
Route::rule('/users/add','api/v1.home.index/add','GET');
Route::rule('/users/edit/:id','api/v1.home.index/edit','GET');
Route::rule('/users/insert','api/v1.home.index/insert','POST');
Route::rule('/users/update','api/v1.home.index/update','POST');
Route::rule('/users/delete/:id','api/v1.home.index/delete','GET');

Route::rule('/users/getUserList','api/v1.home.user/getUserList','POST');

Route::rule('/login/login_view','api/v1.home.login/login_view','GET');
Route::rule('/login/reg_view','api/v1.home.login/reg_view','GET');
Route::rule('/login/reg','api/v1.home.login/insert','POST');
Route::rule('/login/login','api/v1.home.login/login','POST');
Route::rule('/login/logout','api/v1.home.login/logout','GET');

Route::rule('/user/add_friend','api/v1.home.user/add_friend_view','GET');

Route::rule('/user/saveFriend','api/v1.home.user/saveFriend','POST');
Route::rule('/user/saveGroup','api/v1.home.user/saveGroup','POST');
Route::rule('/user/add_group','api/v1.home.user/add_group_view','GET');
Route::rule('/user/messge/:id','api/v1.home.user/index','GET');
Route::rule('/user/group/:id','api/v1.home.user/g_index','GET');
Route::rule('load','api/v1.home.user/load','POST');
Route::rule('/user/bind','api/v1.home.user/bind','POST');
Route::rule('/user/save_message','api/v1.home.user/save_message','POST');

Route::rule('/user/get_name','api/v1.home.user/get_name','POST');
Route::rule('/user/get_head','api/v1.home.user/get_head','POST');
Route::rule('/user/changeNoRead','api/v1.home.user/changeNoRead','POST');

Route::rule('type/:id','index/index/index','GET');
Route::rule('type','index/index/sb','GET');
Route::rule('connect','index/index/connect','GET');
Route::rule('testcomposer','index/index/testcomposer','GET');
Route::rule('generQrcode','index/index/generQrcode','GET');
Route::rule('testcomposer1','index/index/testcomposer1','GET');
Route::rule('testrabbitmq','index/index/testrabbitmq','GET');
Route::rule('testpay','index/index/testpay','GET');
Route::rule('redis','redis/index/index','GET');
Route::rule('rabbitmqsend','rabbitmq/index/index','GET');
Route::rule('rabbitmqrece','rabbitmq/index/receiver','GET');
Route::rule('sb/[:id]','index/index/sb','GET');
Route::rule('sb','index/index/sb','GET');
Route::rule('getId','index/index/getId','GET');
Route::rule('sendMessage','api/v1.home.producter/sendMessage','GET');
Route::rule('recMessage','api/v1.home.consumer/receMessage','GET');
