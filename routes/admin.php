<?php

use Illuminate\Routing\Router;

Route::group([
    'namespace' => 'Admin',
], function (Router $router) {
    $router->post('login', 'PublicController@login');

    $router->post('a_user/list', 'AUserController@getList');
    $router->post('a_user/save', 'AUserController@save');
    $router->post('a_user/info', 'AUserController@getInfo');
    $router->post('a_user/logout', 'AUserController@logout');

    $router->post('c_user/list', 'CUserController@getList');
    $router->post('c_user/update', 'CUserController@update');

    $router->get('jiaxin/list', 'JiaxinController@getList');
    $router->get('jiaxin/update', 'JiaxinController@update');

});
