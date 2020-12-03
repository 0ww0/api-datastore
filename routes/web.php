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
        'prefix' => 'public'
    ], function () use ($router) {

        $router->post('register', 'Auth\RegisterController');
        $router->post('login', 'Auth\LoginController');

        $router->group([
            'prefix' => 'email'
        ], function () use ($router) {

            $router->get('verify', ['as' => 'email.verify', 'uses' => 'Api\EmailController@verify']);

        });

        $router->group([
            'prefix' => 'password'
        ], function () use ($router) {

            $router->post('forgot', ['as' => 'password.forgot', 'uses' => 'Api\PasswordController@forgot']);
            $router->post('recover', ['as' => 'password.recover', 'uses' => 'Api\PasswordController@recover']);

        });
});


$router->group([
        'middleware' => ['auth', 'verified'],
        'prefix' => 'api'
    ], function () use ($router) {

        $router->get('me', 'Auth\MeController');
        $router->post('logout', 'Auth\LogoutController');
        $router->post('refresh', 'Auth\RefreshController');

        $router->group([
            'prefix' => 'user'
        ], function () use ($router) {

            $router->get('/', 'Api\UserController@index');
            $router->get('/{id}', 'Api\UserController@show');
            $router->put('/{id}', 'Api\UserController@update');
            $router->delete('/{id}', 'Api\UserController@destroy');

        });
});
