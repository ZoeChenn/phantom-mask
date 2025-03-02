<?php

use App\Http\Controllers\API\PharmacyController;
use App\Http\Controllers\API\MaskController;
use App\Http\Controllers\API\CustomerController;
use App\Http\Controllers\API\StatisticsController;
use App\Http\Controllers\API\SearchController;
use App\Http\Controllers\API\PurchaseController;
use Illuminate\Support\Facades\Route;

// 藥局相關路由
Route::get('/pharmacies', [PharmacyController::class, 'getPharmacyByOpenHour']);
Route::get('/pharmacies/{id}/masks', [PharmacyController::class, 'getPharmacyMasks']);
Route::get('/pharmacies/filter', [PharmacyController::class, 'getPharmaciesByMaskFilter']);

// 客戶相關路由
Route::get('/customers/top/{count}', [CustomerController::class, 'getTopCustomers']);

// 統計相關路由
Route::get('/statistics', [StatisticsController::class, 'getMaskSales']);

// 搜索相關路由
Route::get('/search', [SearchController::class, 'getPharmacyAndMaskbyName']);

// 購買相關路由
Route::post('/purchases', [PurchaseController::class, 'buyMask']); 