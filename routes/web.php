<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', function () use ($router) {
    return $router->app->version();
});

$router->group([
        'prefix' => 'api'
    ], function () use ($router) {

        $router->post('register', 'Api\AuthController@register');
        $router->post('login', 'Api\AuthController@login');
        $router->post('logout', 'Api\AuthController@logout');

        $router->post('/password/reset-request', 'Api\RequestPasswordController@sendResetLinkEmail');
        $router->post('/password/reset', [ 'as' => 'password.reset', 'uses' => 'Api\ResetPasswordController@reset' ]);
});
