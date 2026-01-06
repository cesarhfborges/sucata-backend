<?php

/** @var Router $router */

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

use Laravel\Lumen\Routing\Router;

$router->post('login', 'AuthController@login');

$router->group([
    'middleware' => 'auth:api',
], function () use ($router) {

    $router->group(['prefix' => 'empresas'], function () use ($router) {
        $router->get('', 'EmpresasController@index');
        $router->post('', 'EmpresasController@store');
        $router->get('{id}', 'EmpresasController@show');
        $router->put('{id}', 'EmpresasController@update');
        $router->delete('{id}', 'EmpresasController@destroy');
    });
});
