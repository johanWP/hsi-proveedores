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

Route::get('/', 'HomeController@index');
Route::get('/ayuda', function(){
    return view('ayuda');
});

Route::group(['middleware' => ['auth']], function () {

    Route::group(['middleware' => ['role:dar_permisos']], function () {
        Route::resource('/permisos', 'PermissionController');
        Route::resource('/roles', 'RoleController');
    });
    Route::group(['middleware' => ['role:ver_otros_usuarios']], function () {
        Route::resource('/usuarios', 'UserController');
        Route::get('/api/usuarios', 'UserController@anyData');
    });
    Route::group(['middleware' => ['role:generar_archivo_de_pagos']], function () {
        // ********** Generar Archivos para transferencias bancarias
        Route::get('/banco/galicia', 'BancoGaliciaController@index');
        Route::get('/banco/galicia/{archivo}', 'BancoGaliciaController@descargarArchivo')
            ->where('archivo', '[A-Za-z0-9\-\_\.]+');
        Route::post('/banco/galicia', 'BancoGaliciaController@generarArchivoTransferencias');
    });

    Route::get('/pagos/', 'PaymentController@index');  // Todos los pagos del usuario logueado
    Route::get('/pagos/porProveedor/{prov_id}', 'PaymentController@PorProveedor');  // Todos los pagos de un proveedor por id
    Route::get('/pagos/todos', 'PaymentController@VerTodos');
    Route::get('/pagos/{numPago}', 'PaymentController@show'); // detalles de un pago (si es propio o tiene permisos)
    Route::get('/api/pagos/{user?}', 'PaymentController@anyData');




    // ********** Ver facturas
    Route::get('/facturas/', 'FacturaController@index');  // Todos los pagos del usuario logueado
    Route::get('/facturas/{cuit}/{numComprobante}', 'FacturaController@show');  // ver detalle de factura
    
});