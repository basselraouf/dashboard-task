<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
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
/////////////////////////////////////////////////////  Admin  /////////////////////////////////////////////////////
Route::group(['prefix' => 'admin'], function (){
    Route::post('/register',[AdminController::class,'register']);
    Route::get('/login',[AdminController::class,'login']);
    Route::get('/logout',[AdminController::class,'logout']);
    Route::get('/refresh',[AdminController::class,'refresh']);
});

/////////////////////////////////////////////////////  User  //////////////////////////////////////////////////////
Route::group(['prefix' => 'user'], function(){
    Route::post('/register', [UserController::class, 'register']);
    Route::get('/login', [UserController::class, 'login']);
    Route::get('/logout', [UserController::class, 'logout'])->middleware('auth:sanctum');
});

///////////////////////////////////////////////////  Products  ////////////////////////////////////////////////////

// Route::resource('products', ProductController::class);


Route::prefix('products')->group(function () {
    Route::get('/', [ProductController::class, 'index']);
    Route::post('/', [ProductController::class, 'store']);
    Route::put('/{id}', [ProductController::class, 'update']);
    Route::delete('/{id}', [ProductController::class, 'destroy']);
    Route::post('/search', [ProductController::class, 'search']);
});
