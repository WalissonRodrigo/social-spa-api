<?php

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
Route::post('cadastro', 'UsuarioController@store');
Route::post('login', 'UsuarioController@login');
Route::get('usuarios', 'UsuarioController@usuarios');
//Group Routes Middleware Authenticate and using API Token
Route::middleware('auth:api')->group(function () {
    Route::get('user', 'UsuarioController@show');
    Route::put('perfil', 'UsuarioController@update');
    Route::post('conteudo/adicionar', 'ConteudoController@store');
    Route::get('conteudo', 'ConteudoController@index');
    Route::put('conteudo/curtir/{id}', 'ConteudoController@update');
});

Route::get('/testes', 'UsuarioController@teste');
