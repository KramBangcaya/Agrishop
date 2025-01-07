<?php

use App\Http\Controllers\API\UserController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductsController;
use App\Http\Controllers\MobileController;
use App\Http\Controllers\ReportController;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
Route::middleware('auth')->get('/user', function (Request $request) {
    return response()->json(['userID' => $request->user()->id]);
});

Route::get('/', function () {
    return redirect()->route('login'); // Redirect to login route
});

Route::get('/public/buyer/index.php', function () {
    return view('index');
});

Route::prefix('products')->group(function () {
    Route::get('/all', [ProductsController::class, 'index_all']);
    Route::get('/all_product', [ProductsController::class, 'all_product']);
    Route::get('/category/{id}', [App\Http\Controllers\ProductsController::class, 'category_all']);
    Route::get('/product/{id}', [App\Http\Controllers\ProductsController::class, 'product']);
    Route::get('/product/seller/{id}', [App\Http\Controllers\ProductsController::class, 'seller']);
    Route::get('/price-range', [App\Http\Controllers\ProductsController::class, 'getPrice']);
});

Route::prefix('report')->group(function () {
    Route::get('/all_user', [ReportController::class, 'report_user']);
});

Route::prefix('categories')->group(function () {
    Route::get('/all', [CategoryController::class, 'all']);
});
Route::prefix('login')->group(function () {
    Route::get('/submit', [MobileController::class, 'authenticate']);
});

Route::prefix('login')->group(function () {
    Route::get('/submit', [UserController::class, 'authenticate']);
});

Route::prefix('seller')->group(function () {
    Route::get('/all', [UserController::class, 'all']);
    Route::get('/all_buyer', [UserController::class, 'all_buyer']);
});
Route::prefix('register')->group(function () {
    Route::get('/submit', [MobileController::class, 'Registration']);
});
Route::prefix('registers')->group(function () {
    Route::get('/submit', [MobileController::class, 'Registrations']);
});
Auth::routes();
Route::get('/student', [App\Http\Controllers\HomeController::class, 'index'])->name('dashboard');
// Route::get('/{any}', [App\Http\Controllers\HomeController::class, 'index'])->where('any', '.*');
// Route::view('/{any}','dashboard')->where('any','^(?!js/).*');
Route::get('{path}', [App\Http\Controllers\HomeController::class, 'index'])->where('any', '^(?!js/).*')->where('path', '([A-z\d\-/_.]+)?');
Route::get('/get-server-ip', function() {
    $hostname = gethostname();
    $ip = gethostbyname($hostname); // Get the IP address of the server by hostname

    return response()->json([
        'ip' => $ip
    ]);
});
