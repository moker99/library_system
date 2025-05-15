<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\BookController;
use App\Http\Controllers\ShelfController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// 書櫃 CRUD
Route::get('/shelves', [ShelfController::class, 'index']);
Route::post('/shelves', [ShelfController::class, 'store']);
Route::get('/shelves/{id}', [ShelfController::class, 'show']);
Route::post('/shelves/reorder', [ShelfController::class, 'reorder']);

// 書櫃的樓層管理
Route::post('/shelves/{shelf}/layers', [ShelfController::class, 'addLayer']);

// 新增書本
Route::post('/shelves/{shelf}/books', [BookController::class, 'storeToShelfAuto']);
Route::post('/shelves/{shelf}/layers/{layer}/books', [BookController::class, 'storeToSpecificLayer']);
Route::post('/books/auto', [BookController::class, 'storeAutoGlobal']);

// 書本搜尋／批次查詢
Route::get('/books/search', [BookController::class, 'search']);
Route::post('/books/batch-search', [BookController::class, 'batchSearch']);
