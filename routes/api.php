<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\FormsManagerController;
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

Route::post('/auth/login', [AuthController::class, 'loginUser']);
Route::post('/auth/register', [AuthController::class, 'createUser']);


Route::post('/updateStudents', [FormsManagerController::class, 'updateStudents'])->middleware('auth:sanctum');
Route::post('/createForm', [FormsManagerController::class, 'createForm'])->middleware('auth:sanctum');


