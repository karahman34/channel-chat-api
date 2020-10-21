<?php

/** @var \Laravel\Lumen\Routing\Router $router */

use App\Http\Controllers\AuthController;

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

$router->post('broadcasting/auth', [
    'uses' => 'BroadcastController@authenticate',
    'middleware' => 'auth'
]);

// Auth Route
$router->group(['prefix' => 'auth', 'as' => 'auth'], function ($router) {
    $router->post('/login', [
        'uses' => 'AuthController@login',
        'as' => 'login',
        'middleware' => 'guest',
    ]);

    $router->group(['middleware' => ['auth']], function ($router) {
        $router->get('me', 'AuthController@me');
        
        $router->post('refresh', 'AuthController@refresh');
        $router->post('logout', 'AuthController@logout');
    });
});

// Users Route
$router->group(['prefix' => 'users', 'as' => 'user'], function () use ($router) {
    $router->post('/register', [
        'uses' => 'UserController@register',
        'as' => 'register',
        'middleware' => 'guest'
    ]);

    $router->group(['middleware' => 'auth'], function ($router) {
        $router->patch('/{id:\d+}', 'UserController@update');
        $router->patch('/{id:\d+}/password', 'UserController@changePassword');
    });
});

// Channels Route
$router->group(['prefix' => 'channels', 'as' => 'channel', 'middleware' => 'auth'], function () use ($router) {
    $router->post('/{roomId:\d+}', [
        'uses' => 'ChannelController@newMessage'
    ]);
});
