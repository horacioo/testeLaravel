<?php

use App\Http\Controllers\payment\pagamentoController;
use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return view('welcome');
});


Route::get('/payment', [pagamentoController::class, 'index'])->name('pagamento');
Route::post('/obrigado', [pagamentoController::class, 'conclusao'])->name('obrigado');