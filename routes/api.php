<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\AuthController;
use \App\Http\Controllers\Api\EmployeeController;
use \App\Http\Controllers\Api\SupplierController;
use \App\Http\Controllers\Api\CategoryController;
use \App\Http\Controllers\Api\ProductController;
use \App\Http\Controllers\Api\ExpenseController;
use \App\Http\Controllers\Api\SalaryController;
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

//Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//    return $request->user();
//});
Route::group([

    'middleware' => 'api',
    'prefix' => 'auth'

], function ($router) {

    Route::post('login', [AuthController::class, 'login']);
    Route::post('signup', [AuthController::class, 'signup']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::post('me', [AuthController::class, 'me']);

});
//Emloyee routes

Route::resource('employee', EmployeeController::class)->except(['index','create']);
Route::post('employee', [EmployeeController::class, 'index']);
Route::post('employee/store', [EmployeeController::class, 'store']);

//Supplier Routes

Route::apiResource('supplier',SupplierController::class)->except(['create','edit','index']);
Route::post('supplier', [SupplierController::class, 'index']);
Route::post('supplier/store', [SupplierController::class, 'store']);
Route::get('suppliers', [SupplierController::class, 'getAllSuppliers']);


//Category Routes

Route::apiResource('category',CategoryController::class)->except(['create','edit','index']);
Route::post('category', [CategoryController::class, 'index']);
Route::post('category/store', [CategoryController::class, 'store']);
Route::get('categories', [CategoryController::class, 'getAllCategories']);


//Product Routes

Route::apiResource('product',ProductController::class)->except(['create','edit','index']);
Route::post('product', [ProductController::class, 'index']);
Route::post('product/store', [ProductController::class, 'store']);

//Expense Route

Route::apiResource('expense',ExpenseController::class)->except(['create','edit','index']);
Route::post('expense', [ExpenseController::class, 'index']);
Route::post('expense/store', [ExpenseController::class, 'store']);

//Expense Salary

Route::post('salary/paysalary', [SalaryController::class, 'paySalary']);
Route::get('salary/allmonths', [SalaryController::class, 'allSalary']);
Route::post('salary/month', [SalaryController::class, 'allSalaryByMonth']);
Route::delete('salary/{id}', [SalaryController::class, 'deletePay']);
