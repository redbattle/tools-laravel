<?php

use Illuminate\Routing\Router;

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

Route::group([
    'namespace' => 'Api',
], function (Router $router) {
    $router->post('public/register', 'PublicController@register');
    $router->post('public/login', 'PublicController@login');
    $router->post('public/getvcode', 'PublicController@getVCode');
    $router->post('public/reset_password', 'PublicController@resetPassword');

    $router->post('user/logout', 'UserController@logout');
    $router->post('user/info', 'UserController@getInfo');
    $router->post('user/update_info', 'UserController@updateInfo');
    $router->post('user/update_phone', 'UserController@updatePhone');
    $router->post('user/bind_email', 'UserController@bindEmail');
    $router->post('user/update_email', 'UserController@updateEmail');
    $router->post('user/update_password', 'UserController@updatePassword');


});
