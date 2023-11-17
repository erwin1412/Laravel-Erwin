<?php

use App\Exports\ProductExport;
use App\Http\Controllers\API\Admin\AdminProductCategoryController;
use App\Http\Controllers\API\Admin\AdminProductController;
use App\Http\Controllers\API\Admin\AdminProductImageController;
use App\Http\Controllers\API\ProductController;
use App\Http\Controllers\API\UserController;
use App\Http\Controllers\API\ProductCategoryController;
use App\Http\Controllers\API\ProductImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    //auth
    Route::get('user', [UserController::class, 'fetch'])->name('fetch');
    Route::post('logout', [UserController::class, 'logout'])->name('logout');
    //admin
    Route::get('admin/products', [AdminProductController::class, 'findAll']);
    Route::post('admin/products', [AdminProductController::class, 'store']);
    Route::put('admin/products/{id}', [AdminProductController::class, 'update']);
    Route::delete('admin/products/{id}', [AdminProductController::class, 'delete']);

    Route::get('admin/categories', [AdminProductCategoryController::class, 'findAll']);
    Route::post('admin/categories', [AdminProductCategoryController::class, 'store']);
    Route::put('admin/categories/{id}', [AdminProductCategoryController::class, 'update']);
    Route::delete('admin/categories/{id}', [AdminProductCategoryController::class, 'delete']);

    Route::get('admin/images', [AdminProductImageController::class, 'findAll']);
    Route::post('admin/images', [AdminProductImageController::class, 'store']);
    Route::put('admin/images/{id}', [AdminProductImageController::class, 'update']);
    Route::delete('admin/images/{id}', [AdminProductImageController::class, 'delete']);



    //user
    Route::get('products', [ProductController::class, 'findAll']);
    Route::post('products', [ProductController::class, 'store']);
    Route::put('products/{id}', [ProductController::class, 'update']);
    Route::delete('products/{id}', [ProductController::class, 'delete']);

    Route::get('categories', [ProductCategoryController::class, 'findAll']);
    Route::post('categories', [ProductCategoryController::class, 'store']);
    Route::put('categories/{id}', [ProductCategoryController::class, 'update']);
    Route::delete('categories/{id}', [ProductCategoryController::class, 'delete']);

    Route::get('images', [ProductImageController::class, 'findAll']);
    Route::post('images', [ProductImageController::class, 'store']);
    Route::put('images/{id}', [ProductImageController::class, 'update']);
    Route::delete('images/{id}', [ProductImageController::class, 'delete']);
});

Route::get('/export-products', function () {
    return \Maatwebsite\Excel\Facades\Excel::download(new ProductExport, 'products.xlsx');
});
