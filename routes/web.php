<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// rutas usuarios invitados
Route::group(['middleware' => ['guest']],function(){

    Route::get('/','Auth\LoginController@showLoginForm');
    Route::get('/login','Auth\LoginController@showLoginForm');
    Route::post('/login','Auth\LoginController@login')->name('login');
});

Route::group(['middleware' => ['auth']],function(){

    Route::post('/logout', 'Auth\LoginController@logout')->name('logout');

    Route::get('/main', function () {
        return view('contenido/contenido');
    })->name('main');
    
    //
    Route::get('/equipos','EquipoController@index');
    Route::post('/equipos/registrar','EquipoController@store');
    Route::put('/equipos/actualizar','EquipoController@update');
    Route::put('/equipos/activar','EquipoController@activar');
    Route::put('/equipos/desactivar','EquipoController@desactivar');
    
    Route::get('/pv','SucursalController@index');
    Route::get('/pv/select','SucursalController@select');
    Route::post('/pv/registrar','SucursalController@store');
    Route::put('/pv/actualizar','SucursalController@update');
    Route::put('/pv/activar','SucursalController@activar');
    Route::put('/pv/desactivar','SucursalController@desactivar');
    
    Route::get('/roles','RolController@index');
    
    Route::get('/user','UserController@index');
    Route::post('/user/registrar','UserController@store');
    Route::put('/user/actualizar','UserController@update');
    Route::put('/user/activar','UserController@activar');
    Route::put('/user/desactivar','UserController@desactivar');
});



Route::get('/home', 'HomeController@index')->name('home');
