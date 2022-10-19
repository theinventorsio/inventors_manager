<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\FormsManagerController;
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

//Route::get('/airtables/show', [AirtablesController::class, 'show']);

Route::post('/auth/login', [AuthController::class, 'loginUser']);
Route::post('/auth/register', [AuthController::class, 'createUser'])->middleware('local');

Route::get('/database/getDBtoCSV', [DatabaseController::class, 'getDBtoCSV'])->middleware('auth:sanctum');

Route::get('/form/responses', [FormsManagerController::class, 'getResponses'])->middleware('auth:sanctum');
Route::post('/form/updateStudents', [FormsManagerController::class, 'updateStudents'])->middleware('auth:sanctum');
Route::post('/form/createForm', [FormsManagerController::class, 'createForm'])->middleware('auth:sanctum');

//Route::post('/updateStudents', [FormsManagerController::class, 'updateStudents'])
//Route::post('/createForm', [FormsManagerController::class, 'createForm']);


