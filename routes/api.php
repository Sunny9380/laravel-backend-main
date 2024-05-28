<?php
use App\Http\Controllers\v1\admin\VendorController;
use App\Http\Controllers\v1\user\HotelController;
use App\Mail\vendor\VendorWelcome;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('v1')->group(function () {

    // Route::get('/test', [HotelController::class, 'test']);


    require __DIR__ . '/apis/global/apis.php';

    require __DIR__ . '/apis/auth/apis.php';

    require __DIR__ . '/apis/auth.php';

});
