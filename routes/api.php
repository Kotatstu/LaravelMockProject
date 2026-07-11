<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PurchaseOrderController;
use App\Http\Controllers\Api\StockController;
use App\Http\Controllers\Api\StockExportController;
use App\Http\Controllers\Api\SupplierController;
use App\Http\Controllers\Api\WarehouseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');


Route::apiResource('warehouse', WarehouseController::class);

Route::get('/category', [CategoryController::class, 'index']);
Route::post('/category/create', [CategoryController::class, 'store']);
Route::get('/category/{category}', [CategoryController::class, 'show']);
Route::put('/category/update/{category}', [CategoryController::class, 'update']);
Route::delete('/category/delete/{category}', [CategoryController::class, 'destroy']);

Route::get('/supplier', [SupplierController::class, 'index']);
Route::post('/supplier/create', [SupplierController::class, 'store']);
Route::get('/supplier/{supplier}', [SupplierController::class, 'show']);
Route::put('/supplier/update/{supplier}', [SupplierController::class, 'update']);
Route::delete('/supplier/delete/{supplier}', [SupplierController::class, 'destroy']);

Route::get('/product', [ProductController::class, 'index']);
Route::post('/product/create', [ProductController::class, 'store']);
Route::get('/product/{product}', [ProductController::class, 'show']);
Route::put('product/update/{product}', [ProductController::class, 'update']);
Route::delete('/product/delete/{product}', [ProductController::class, 'destroy']);

Route::get('/stock', [StockController::class, 'index']);
Route::post('/stock/create', [StockController::class, 'store']);
Route::get('/stock/{stock}', [StockController::class, 'show']);
Route::put('/stock/update/{stock}', [StockController::class, 'update']);
Route::delete('/stock/delete/{stock}', [StockController::class, 'destroy']);

Route::get('/purchaseOrder', [PurchaseOrderController::class, 'index']);
Route::post('/purchaseOrder/create', [PurchaseOrderController::class, 'store']);
Route::get('/purchaseOrder/{purchaseOrder}', [PurchaseOrderController::class, 'show']);
Route::put('/purchaseOrder/update/{purchaseOrder}', [PurchaseOrderController::class, 'update']);
Route::delete('purchaseOrder/delete/{purchaseOrder}', [PurchaseOrderController::class, 'destroy']);
Route::post('/purchaseOrder/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive']);

Route::get('stockExport', [StockExportController::class, 'index']);
Route::post('stockExport/create', [StockExportController::class, 'store']);
Route::get('stockExport/{stockExport}', [StockExportController::class, 'show']);
Route::put('stockExport/update/{stockExport}', [StockExportController::class, 'update']);
Route::delete('stockExport/delete/{stockExport}', [StockExportController::class, 'destroy']);
Route::post('stockExport/{stockExport}/dispatch', [StockExportController::class, 'dispatch']);


