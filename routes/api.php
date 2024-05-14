<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CajaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\CreditoController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\PagoAlquilerController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\SaldoAlquilerController;
use App\Http\Controllers\ServicioController;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class, 'login'])->name('login'); 
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);
    Route::post('register', [AuthController::class, 'register']);
});

//Cliente
Route::get('clientes/{tipodocumento}/{numerodocumento}', [ClienteController::class, 'getByTipoDocumento']);

//Empresa
Route::get('empresas', [EmpresaController::class, 'index']);
Route::get('empresas/{id}', [EmpresaController::class, 'show']);
Route::post('empresas', [EmpresaController::class, 'store']);
Route::patch('empresas/{id}', [EmpresaController::class, 'update']);
Route::delete('empresas/{id}', [EmpresaController::class, 'destroy']);

//Caja
Route::get('cajas', [CajaController::class, 'index']);
Route::get('cajas/{id}', [CajaController::class, 'getCerrarCaja']);
Route::get('cajas/{fecha1?}/{fecha2}', [CajaController::class, 'indexFilter']);
Route::post('cajas', [CajaController::class, 'store']);
Route::patch('cajas/{id}', [CajaController::class, 'update']);
Route::post('cajas/cierre', [CajaController::class, 'cerrarCaja']);
Route::delete('cajas/{id}', [CajaController::class, 'destroy']);

//Credito
Route::get('creditos/cliente/{nro_doc}', [CreditoController::class, 'show']);
Route::get('creditos/comprobante/{id}', [CreditoController::class, 'getUltimoNroComprobante']);
Route::get('creditos/{responID?}/{fecha1?}/{fecha2?}/{nrodoc?}', [CreditoController::class, 'index']);
Route::post('creditos', [CreditoController::class, 'store']);
Route::patch('creditos/{id}', [CreditoController::class, 'update']);
Route::delete('creditos/{id}', [CreditoController::class, 'destroy']);

//Servicio
Route::get('servicios', [ServicioController::class, 'index']);
Route::get('servicios/{id}', [ServicioController::class, 'show']);
Route::post('servicios', [ServicioController::class, 'store']);
Route::patch('servicios/{id}', [ServicioController::class, 'update']);
Route::delete('servicios/{id}', [ServicioController::class, 'destroy']);

//Pagos
Route::get('pagos', [PagoController::class, 'index']);
Route::get('pagos/{id}', [PagoController::class, 'show']);
Route::post('pagos', [PagoController::class, 'store']);
Route::patch('pagos/{id}', [PagoController::class, 'update']);
Route::delete('pagos/{id}', [PagoController::class, 'destroy']);

//PagoAlquiler
Route::get('pagoalquileres', [PagoAlquilerController::class, 'index']);
Route::get('pagoalquileres/{fechaInicio?}/{fechaFinal?}', [PagoAlquilerController::class, 'indexFiltro']);
Route::get('pagoalquileres/{id}', [PagoAlquilerController::class, 'show']);
Route::post('pagoalquileres', [PagoAlquilerController::class, 'store']);
Route::patch('pagoalquileres/{id}', [PagoAlquilerController::class, 'update']);
Route::delete('pagoalquileres/{id}', [PagoAlquilerController::class, 'destroy']);

//SaldoAlquiler
Route::post('saldoalquileres', [SaldoAlquilerController::class, 'store']);

//Usuarios
Route::get('usuarios', [AuthController::class, 'index']);
Route::get('usuarios/empresa', [AuthController::class, 'getUsersByEmpresa']);
Route::get('usuarios/{tipodoc}/{nrodoc}', [AuthController::class, 'getDatosUsersByDoc']);
Route::post('usuarios', [AuthController::class, 'register']);
Route::patch('usuarios/{id}', [AuthController::class, 'update']);
Route::delete('usuarios/{id}', [AuthController::class, 'destroy']);
