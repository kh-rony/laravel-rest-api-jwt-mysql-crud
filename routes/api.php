<?php

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProductController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::post('register', [UserController::class, 'store']);
Route::post('login', [UserController::class, 'login']);

Route::group(['middleware' => ['jwt.verify', 'jwt.auth']], function()
{
    Route::get('logout', [UserController::class, 'logout']);
    Route::get('user', [UserController::class, 'getAuthUser']);

    Route::get('products', [ProductController::class, 'index']);
    Route::get('products/{id}', [ProductController::class, 'show']);
    Route::post('products', [ProductController::class, 'store']);
    Route::patch('products/{id}',  [ProductController::class, 'update']);
    Route::delete('products/{id}',  [ProductController::class, 'destroy']);
});
