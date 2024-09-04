<?php

use App\Http\Controllers\Api\V2\Auth\LoginController;
use App\Http\Controllers\Api\V2\Auth\LogoutController;
use App\Http\Controllers\Api\V2\Auth\RegisterController;
use App\Http\Controllers\Api\V2\MeController;
use App\Http\Controllers\Api\V2\ProvisionBillController;
use Illuminate\Support\Facades\Route;
use LaravelJsonApi\Laravel\Facades\JsonApiRoute;
use LaravelJsonApi\Laravel\Http\Controllers\JsonApiController;
use LaravelJsonApi\Laravel\Routing\ResourceRegistrar;
use App\Http\Controllers\Api\V2\Auth\ForgotPasswordController;
use App\Http\Controllers\Api\V2\Auth\ResetPasswordController;
use App\Http\Controllers\UploadController;

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

Route::prefix('v2')->group(function () {
    Route::get('/login', [ProvisionBillController::class, 'logIn']);
    Route::get('provisionBill', [ProvisionBillController::class, 'getChartData']);
    Route::get('notifications', [ProvisionBillController::class, 'getNotificationData']);
    Route::get('sendContactUs', [ProvisionBillController::class, 'sendContactUs']);
    Route::get('sendEmail', [ProvisionBillController::class, 'sendEmail']);
    Route::get('send_simple_mail', [ProvisionBillController::class, 'send_simple_mail']);
    Route::get('get_setting', [ProvisionBillController::class, 'getSetting']);
    Route::get('set_setting', [ProvisionBillController::class, 'setSetting']);
});
