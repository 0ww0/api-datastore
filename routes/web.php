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

        $router->group([
            'prefix' => 'email'
        ], function () use ($router) {

            $router->get('verify', 'Api\EmailController@verify');

        });

        $router->group([
            'prefix' => 'password'
        ], function () use ($router) {

            $router->post('forgot', 'Api\PasswordController@forgot');
            $router->post('recover', 'Api\PasswordController@recover');

        });
});


$router->group([
        'middleware' => ['auth', 'verified'],
        'prefix' => 'api'
    ], function () use ($router) {

        $router->get('me', 'Api\AuthController@me');
        $router->post('logout', 'Api\AuthController@logout');
        $router->post('refresh', 'Api\AuthController@refresh');

        $router->group([
            'prefix' => 'user'
        ], function () use ($router) {

            $router->get('/', 'Api\UserController@index');
            $router->post('/', 'Api\UserController@create');
            $router->get('/{id}', 'Api\UserController@show');
            $router->put('/{id}', 'Api\UserController@update');
            $router->delete('/{id}', 'Api\UserController@destroy');

        });
});
