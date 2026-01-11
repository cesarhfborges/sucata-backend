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

    $router->get('perfil', 'PerfilController@perfil');
    $router->put('perfil', 'PerfilController@updatePerfil');

    $router->post('check-email', 'UsuariosController@checkEmailAvailability');

    $router->group(['prefix' => 'empresas'], function () use ($router) {
        $router->get('', 'EmpresasController@index');
        $router->post('', 'EmpresasController@store');
        $router->get('{id}', 'EmpresasController@show');
        $router->put('{id}', 'EmpresasController@update');
        $router->delete('{id}', 'EmpresasController@destroy');
    });

    $router->group(['prefix' => 'usuarios'], function () use ($router) {
        $router->get('', 'UsuariosController@index');
        $router->post('', 'UsuariosController@store');
        $router->get('{id}', 'UsuariosController@show');
        $router->put('{id}', 'UsuariosController@update');
        $router->delete('{id}', 'UsuariosController@destroy');
    });

    $router->group(['prefix' => 'materiais'], function () use ($router) {
        $router->get('', 'MateriaisController@index');
        $router->post('', 'MateriaisController@store');
        $router->get('{codigo}', 'MateriaisController@show');
        $router->put('{codigo}', 'MateriaisController@update');
        $router->delete('{codigo}', 'MateriaisController@destroy');
    });

    $router->group(['prefix' => 'clientes'], function () use ($router) {
        $router->get('', 'ClientesController@index');
//        $router->post('', 'UsuariosController@store');
//        $router->get('{id}', 'UsuariosController@show');
//        $router->put('{id}', 'UsuariosController@update');
        $router->delete('{id}', 'ClientesController@destroy');
    });

    $router->group(['prefix' => 'notas-fiscais'], function () use ($router) {
        $router->get('', 'NotasFiscaisController@index');
        $router->post('', 'NotasFiscaisController@store');
        $router->get('{id}', 'NotasFiscaisController@show');
        $router->put('{id}', 'NotasFiscaisController@update');
        $router->delete('{id}', 'NotasFiscaisController@destroy');

        $router->group(['prefix' => '{id}/itens'], function () use ($router) {
            $router->get('', 'NotaFiscalItensController@index');
            $router->post('', 'NotaFiscalItensController@store');
            $router->get('{itemId}', 'NotaFiscalItensController@show');
            $router->put('{itemId}', 'NotaFiscalItensController@update');
            $router->delete('{itemId}', 'NotaFiscalItensController@destroy');

            $router->post('{itemId}/movimentar', 'NotaFiscalItensController@movimentar');
        });
    });
});
