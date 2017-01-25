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
    Route::resource('/usuarios', 'UserController');
    Route::get('/pagos/', 'PaymentController@index');  // Todos los pagos del usuario logueado
    Route::get('/pagos/todos', 'PaymentController@VerTodos'); // Todos los pagos de todos los proveedores
    Route::get('/pagos/detalle/{payment_id}', 'PaymentController@show'); // detalles de un pago (si es propio o tiene permisos)
    Route::get('/pagos/{prov_id}', 'PaymentController@PorProveedor');  // Todos los pagos de un proveedor por id

    Route::get('/api/usuarios', 'UserController@anyData');
    Route::get('/api/pagos/{user?}', 'PaymentController@anyData');

    // ********** Generar Archivos para transferencias bancarias
    Route::get('/banco/galicia', 'BancoGaliciaController@index');
    Route::get('/banco/galicia/{archivo}', 'BancoGaliciaController@descargarArchivo')
        ->where('archivo', '[A-Za-z0-9\-\_\.]+');
    Route::post('/banco/galicia', 'BancoGaliciaController@generarArchivoTransferencias');


});