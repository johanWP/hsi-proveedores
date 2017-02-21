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

Route::group(['middleware' => ['auth']], function () {

    Route::resource('/permisos', 'PermissionController');
    Route::resource('/roles', 'RoleController');
    Route::resource('/usuarios', 'UserController');
    Route::get('/pagos/', 'PaymentController@index');  // Todos los pagos del usuario logueado
    Route::get('/pagos/porProveedor/{prov_id}', 'PaymentController@PorProveedor');  // Todos los pagos de un proveedor por id
    Route::get('/pagos/todos', 'PaymentController@VerTodos');
//    Route::get('/pagos/{id}', 'PaymentController@show');
//        ->middleware('role:admin_proveedores, permission:ver_pagos_todos'); // Todos los pagos de todos los proveedores
    Route::get('/pagos/{numPago}', 'PaymentController@show'); // detalles de un pago (si es propio o tiene permisos)

    Route::get('/api/usuarios', 'UserController@anyData');
    Route::get('/api/pagos/{user?}', 'PaymentController@anyData');

    // ********** Generar Archivos para transferencias bancarias
    Route::get('/banco/galicia', 'BancoGaliciaController@index');
    Route::get('/banco/galicia/{archivo}', 'BancoGaliciaController@descargarArchivo')
        ->where('archivo', '[A-Za-z0-9\-\_\.]+');
    Route::post('/banco/galicia', 'BancoGaliciaController@generarArchivoTransferencias');

    // ********** Ver facturas
    Route::get('/facturas/', 'FacturaController@index');  // Todos los pagos del usuario logueado
    Route::get('/facturas/{cuit}/{numComprobante}', 'FacturaController@show');  // ver detalle de factura
    Route::get('/api/facturas/{cuit?}', 'FacturaController@anyData');

});