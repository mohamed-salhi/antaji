<?php

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

Route::middleware(['guest:sanctum'])->prefix('auth')->group(function () {
    Route::get('/intros', [\App\Http\Controllers\Api\AuthController::class, 'intros']);
    Route::get('/first', [\App\Http\Controllers\Api\AuthController::class, 'first']);
    Route::post('/login', [\App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('/verify_code', [\App\Http\Controllers\Api\AuthController::class, 'verifyCode']);
    Route::post('/again', [\App\Http\Controllers\Api\AuthController::class, 'again']);
    Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
});
Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('logout/{fcm?}/{token?}', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::get('/home', [\App\Http\Controllers\Api\Home\HomeController::class, 'home']);
    Route::get('terms_conditions', [\App\Http\Controllers\Api\Home\HomeController::class, 'termsConditions']);
    Route::post('update_profile', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'updateProfile']);
    Route::get('profile', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'profile']);

    Route::post('contact', [\App\Http\Controllers\Api\Contact\ContactController::class, 'contact']);
    Route::post('add_business/video', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'addBusinessVideo'])->middleware('artists');
    Route::post('add_business/images', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'addBusinessImages'])->middleware('artists');;
    Route::get('get_business/{type}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'getBusiness'])->middleware('artists');;
    Route::delete('delete_business_image/{uuid}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'deleteBusinessImage'])->middleware('artists');;
    Route::delete('delete_business_video/{uuid}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'deleteBusinessVideo'])->middleware('artists');;
    Route::get('account_settings', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'accountSettingsGet']);
    Route::post('account_settings', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'updateAccountSetting']);
    Route::get('artists', [\App\Http\Controllers\Api\Home\HomeController::class, 'artists']);
    Route::get('getSupFromCategory/{uuid}', [\App\Http\Controllers\Api\Home\HomeController::class, 'getSupFromCategory']);
    Route::get('getCityFromCounty/{uuid}', [\App\Http\Controllers\Api\Home\HomeController::class, 'getCityFromCounty']);
    Route::post('add/course', [\App\Http\Controllers\Api\Content\ContentController::class, 'addCourse']);
    Route::post('add/serving', [\App\Http\Controllers\Api\Content\ContentController::class, 'addServing']);
    Route::post('add/location', [\App\Http\Controllers\Api\Content\ContentController::class, 'addLocation']);
    Route::post('add/product', [\App\Http\Controllers\Api\Content\ContentController::class, 'addProduct']);
    Route::post('update/course', [\App\Http\Controllers\Api\Content\ContentController::class, 'updateCourse']);
    Route::post('update/serving', [\App\Http\Controllers\Api\Content\ContentController::class, 'updateServing']);
    Route::post('update/location', [\App\Http\Controllers\Api\Content\ContentController::class, 'updateLocation']);
    Route::post('update/product', [\App\Http\Controllers\Api\Content\ContentController::class, 'updateProduct']);
    Route::delete('delete/course/{uuid}', [\App\Http\Controllers\Api\Content\ContentController::class, 'deleteCourse']);
    Route::delete('delete/serving/{uuid}', [\App\Http\Controllers\Api\Content\ContentController::class, 'deleteServing']);
    Route::delete('delete/location/{uuid}', [\App\Http\Controllers\Api\Content\ContentController::class, 'deleteLocation']);
    Route::delete('delete/product/{uuid}', [\App\Http\Controllers\Api\Content\ContentController::class, 'deleteProduct']);
    Route::get('get/my/Location', [\App\Http\Controllers\Api\Content\ContentController::class, 'getMyLocation']);
    Route::get('get/my/Serving', [\App\Http\Controllers\Api\Content\ContentController::class, 'getMyServing']);
    Route::get('get/my/Course', [\App\Http\Controllers\Api\Content\ContentController::class, 'getMyCourse']);
    Route::get('get/my/Product/{type}', [\App\Http\Controllers\Api\Content\ContentController::class, 'getMyProduct']);

    Route::get('getProductFromCategory/{uuid}', [\App\Http\Controllers\Api\Home\HomeController::class, 'getProductFromCategory']);
    Route::get('getDetailsProduct/{uuid}', [\App\Http\Controllers\Api\Home\HomeController::class, 'getDetailsProduct']);

    Route::get('services/sell_buy', [\App\Http\Controllers\Api\OurServices\ServicesController::class, 'sellBuy']);
    Route::get('services/leasing', [\App\Http\Controllers\Api\OurServices\ServicesController::class, 'leasing']);
    Route::get('services/locations', [\App\Http\Controllers\Api\OurServices\ServicesController::class, 'locations']);
    Route::get('services/services', [\App\Http\Controllers\Api\OurServices\ServicesController::class, 'services']);
    Route::get('business/video/{uuid}', [\App\Http\Controllers\Api\Home\HomeController::class, 'businessVideo']);

    Route::get('profile/{uuid}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'getProfile']);
    Route::get('profile/{uuid}/products/{type}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'getProductProfile']);
    Route::get('profile/{uuid}/business/{type}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'getBusinessProfile']);

    Route::post('delivery_addresses', [\App\Http\Controllers\Api\Home\HomeController::class, 'addDeliveryAddresses']);
    Route::get('delivery_addresses', [\App\Http\Controllers\Api\Home\HomeController::class, 'getDeliveryAddresses']);
    Route::post('update/delivery_addresses', [\App\Http\Controllers\Api\Home\HomeController::class, 'updateDeliveryAddresses']);
    Route::delete('delete/delivery_addresses/{uuid}', [\App\Http\Controllers\Api\Home\HomeController::class, 'deleteDeliveryAddresses']);
    Route::post('add/cart', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'addCart']);
    Route::get('get/cart', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'getCart']);


});
