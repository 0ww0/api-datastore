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