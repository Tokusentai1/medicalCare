<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\MedicalHistoryController;
use App\Http\Controllers\MedicineController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('login', [UserController::class, 'login']);

Route::post('register', [UserController::class, 'register']);

Route::apiResource('user', UserController::class)->only('show', 'update', 'destroy');

Route::apiResource('medicine', MedicineController::class)->only('index', 'show');

Route::get('searchMedicine/{name}', [MedicineController::class, 'searchMedicine']);

Route::apiResource('medical-history', MedicalHistoryController::class)->only('store', 'show', 'update', 'destroy');

Route::post('user-medicines', [MedicalHistoryController::class, 'addUserMedicines']);

Route::get('cart/{id}', [CartController::class, 'getCart']);

Route::post('cart/add', [CartController::class, 'addToCart']);

Route::delete('cart/remove', [CartController::class, 'removeMedicine']);

Route::post('buy/{id}', [CartController::class, 'checkout']);

Route::get('getOrders/{id}', [OrderController::class, 'getOrders']);

Route::get('getOrder/{id}', [OrderController::class, 'getOrder']);

Route::get('categories', [CategoryController::class, 'getAllCategories']);

Route::get('category/{id}', [CategoryController::class, 'getCategory']);

Route::get('searchCategory/{name}', [CategoryController::class, 'searchCategory']);
