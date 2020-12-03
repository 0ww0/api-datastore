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

        $router->post('register', 'Auth\RegisterController@register');
        $router->post('login', 'Auth\LoginController@login');

        $router->group([
            'prefix' => 'email'
        ], function () use ($router) {

            $router->get('verify', ['as' => 'email.verify', 'uses' => 'Email\VerifyController@verify']);

        });

        $router->group([
            'prefix' => 'password'
        ], function () use ($router) {

            $router->post('forgot', ['as' => 'password.forgot', 'uses' => 'Password\ForgotController@forgot']);
            $router->post('recover', ['as' => 'password.recover', 'uses' => 'Password\RecoverController@recover']);

        });
});


$router->group([
        'middleware' => ['auth', 'verified'],
        'prefix' => 'api'
    ], function () use ($router) {

        $router->get('me', 'Auth\MeController@me');
        $router->post('logout', 'Auth\LogoutController@logout');
        $router->post('refresh', 'Auth\RefreshController@refresh');

        $router->group([
            'prefix' => 'user'
        ], function () use ($router) {

            $router->get('/', 'User\UserController@index');
            $router->get('/{id}', 'User\UserController@show');
            $router->put('/{id}', 'User\UserController@update');
            $router->delete('/{id}', 'User\UserController@destroy');

        });
});
