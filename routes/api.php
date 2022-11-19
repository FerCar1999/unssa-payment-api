<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\StudentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

Route::group(['prefix' => 'auth'], function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('me', [AuthController::class, 'me']);
});

Route::group(['prefix' => 'students'], function () {
    Route::get('subjects', [StudentController::class, 'mySubjects']);
});

Route::group(['prefix' => 'payments'], function () {
    Route::get('made', [PaymentController::class, 'paymentsMade']);
    Route::get('duty', [PaymentController::class, 'duty']);
    Route::post('', [PaymentController::class, 'store']);
});


//RUTAS PARA EL LADO DE ADMINISTRADOR
Route::group(['prefix' => 'admin'], function () {
    Route::post('login', [AuthController::class, 'loginAdmin']);
});
