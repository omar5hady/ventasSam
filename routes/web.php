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
    Route::get('/equipos/activos','EquipoController@getActivos');
    
    Route::get('/pv','SucursalController@index');
    Route::get('/pv/select','SucursalController@select');
    Route::post('/pv/registrar','SucursalController@store');
    Route::put('/pv/actualizar','SucursalController@update');
    Route::put('/pv/activar','SucursalController@activar');
    Route::put('/pv/desactivar','SucursalController@desactivar');
    Route::put('/pv/updateVentas','SucursalController@updateVentas');
    
    Route::get('/roles','RolController@index');
    
    Route::get('/user','UserController@index');
    Route::get('/selectVendedor','UserController@selectVendedor');
    Route::get('/selectVendedorSucursal','UserController@selectVendedorSucursal');
    Route::post('/user/registrar','UserController@store');
    Route::put('/user/actualizar','UserController@update');
    Route::put('/user/activar','UserController@activar');
    Route::put('/user/desactivar','UserController@desactivar');

    //Avisos
    Route::post('/aviso/store','AvisoController@store');
    Route::get('/aviso/get','AvisoController@getNoVistos');
    Route::put('/aviso/ocultar','AvisoController@ocultar');

    //// ventas
    Route::get('/ventas','VentaController@index');
    Route::get('/ventas/indexDetalle','VentaController@indexDetalle');
    Route::post('/ventas/registrar','VentaController@store');
    Route::put('/ventas/update','VentaController@update');
    Route::delete('/venta/detalleEliminar','VentaController@deleteDetalle');
    Route::post('/ventas/addDetalle','VentaController@addDetalle');

    //// cortes
    Route::get('/cortes','CorteController@index');
    Route::get('/cortes/indexDetalle','CorteController@indexDetalle');
    Route::post('/cortes/registrar','CorteController@store');
    Route::delete('/corte/eliminar','CorteController@delete');

    //// inventarios
    Route::get('/inventarios','InventarioController@index');
    Route::get('/inventarios/indexDetalle','InventarioController@indexDetalle');
    Route::post('/inventarios/registrar','InventarioController@store');

    /// Cuota
    Route::get('/cuota','CuotaController@index');
    Route::post('/cuota/registrar','CuotaController@store');
    Route::put('/cuota/update','CuotaController@update');

    //Share
    Route::get('/share/peso','ShareController@peso');
    Route::get('/share/ticketPromedio','ShareController@ticketPromedio');
    Route::get('/share/forecast','ShareController@forecast');
    Route::get('/share/wosDetallado','ShareController@wosDetallado');

    //ShareAdmin
    Route::get('/share/pesoAdmn','ShareController@pesoAdmn');
    Route::get('/share/ticketPromedioAdmn','ShareController@ticketPromedioAdmn');
    Route::get('/share/forecastAdmn','ShareController@forecastAdmn');
    Route::get('/share/wosDetalladoAdmn','ShareController@wosDetalladoAdmn');

    //Excel
    Route::get('/excel/ventas','VentaController@excelVentas');
    Route::get('/excel/inventario','InventarioController@excelInventario');
    Route::get('/excel/wos','InventarioController@excelWos');

    //Dashboard
    Route::get('/dashboard/alcance','DashboardController@alcance');
    Route::get('/dashboard/ventaDia','DashboardController@ventaDia');

    //Dropbox
    Route::get('/file/index', 'FileController@index');
    Route::post('/files/store', 'FileController@store');  
    Route::delete('/files/delete', 'FileController@destroy'); 
    Route::get('/files/{file}/download', 'FileController@download');
    
});



Route::get('/home', 'HomeController@index')->name('home');
