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

Route::get('job', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'job']);

Route::get('/intros', [\App\Http\Controllers\Api\AuthController::class, 'intros']);
Route::get('/countries', [\App\Http\Controllers\Api\AuthController::class, 'countries']);
Route::get('/home', [\App\Http\Controllers\Api\Home\HomeController::class, 'home']);
Route::get('/get/categories', [\App\Http\Controllers\Api\Content\ContentController::class, 'categories']);
Route::get('/get/categories_content/{type}', [\App\Http\Controllers\Api\Content\ContentController::class, 'categoriesContent']);

Route::get('categories', [\App\Http\Controllers\Api\Home\HomeController::class, 'categories']);
Route::get('categories/{uuid}', [\App\Http\Controllers\Api\Home\HomeController::class, 'getSupFromCategory']);
Route::get('categories/{uuid}/{sub_category_uuid}', [\App\Http\Controllers\Api\Home\HomeController::class, 'getProductFromCategory']);

Route::get('pages/{id}', [\App\Http\Controllers\Api\Home\HomeController::class, 'page']);
Route::get('see_all', [\App\Http\Controllers\Api\Home\HomeController::class, 'seeAll']);

Route::get('courses/{uuid}', [\App\Http\Controllers\Api\Home\HomeController::class, 'getDetailsCourse']);

Route::get('map', [\App\Http\Controllers\Api\Home\HomeController::class, 'map']);
Route::get('search', [\App\Http\Controllers\Api\Home\HomeController::class, 'search']);
Route::get('search/history', [\App\Http\Controllers\Api\Home\HomeController::class, 'historySearch']);
Route::delete('search/history/delete/{uuid?}', [\App\Http\Controllers\Api\Home\HomeController::class, 'deleteHistorySearch']);

Route::get('artists', [\App\Http\Controllers\Api\Home\HomeController::class, 'artists']);
Route::get('users/{uuid}', [\App\Http\Controllers\Api\Home\HomeController::class, 'artist']);
Route::post('users/{uuid}/business/{type}/{video_uuid}', [\App\Http\Controllers\Api\Home\HomeController::class, 'businessVideo']);
Route::get('users/{uuid}/business/{type}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'getBusinessProfile']);
Route::get('users/{uuid}/products/{type}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'getProductProfile']);
Route::get('users/{uuid}/courses', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'getCourseProfile']);


Route::get('products/{uuid}', [\App\Http\Controllers\Api\Home\HomeController::class, 'getDetailsProduct']);
Route::get('locations/{uuid}', [\App\Http\Controllers\Api\Home\HomeController::class, 'getDetailsLocation']);

//Route::get('profile/{type}/products/{uuid?}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'getProductProfile']);
//Route::get('profile/course/{uuid?}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'getCourseProfile']);
Route::get('/cities', [\App\Http\Controllers\Api\AuthController::class, 'cities']);
Route::get('services/sell_buy', [\App\Http\Controllers\Api\OurServices\ServicesController::class, 'sellBuy']);
Route::get('services/leasing', [\App\Http\Controllers\Api\OurServices\ServicesController::class, 'rent']);
Route::get('services/locations', [\App\Http\Controllers\Api\OurServices\ServicesController::class, 'locations']);
Route::get('services/services', [\App\Http\Controllers\Api\OurServices\ServicesController::class, 'services']);
Route::get('services/services/{uuid}', [\App\Http\Controllers\Api\OurServices\ServicesController::class, 'service']);


Route::middleware(['guest:sanctum'])->prefix('auth')->group(function () {
    Route::post('/send_code', [\App\Http\Controllers\Api\AuthController::class, 'login']);
    Route::post('/verify_code', [\App\Http\Controllers\Api\AuthController::class, 'verifyCode']);
    Route::post('/register', [\App\Http\Controllers\Api\AuthController::class, 'register']);
});
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('settings', [\App\Http\Controllers\Api\Home\HomeController::class, 'settings']);
    Route::post('settings', [\App\Http\Controllers\Api\Home\HomeController::class, 'updateSettings']);

    Route::post('logout', [\App\Http\Controllers\Api\AuthController::class, 'logout']);
    Route::post('profile/update', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'updateProfile']);

    Route::get('contact_us', [\App\Http\Controllers\Api\Contact\ContactController::class, 'contactUs']);
    Route::post('contact_us', [\App\Http\Controllers\Api\Contact\ContactController::class, 'contact']);


    Route::get('business/{type}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'getBusiness'])->middleware('artists');
    Route::post('business/videos/add', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'addBusinessVideo'])->middleware('artists');
    Route::post('business/videos/update/{uuid}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'updateBusinessVideo'])->middleware('artists');
    Route::get('business/videos/edit/{uuid}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'editBusinessVideo'])->middleware('artists');


    Route::delete('business/videos/delete/{uuid}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'deleteBusinessVideo'])->middleware('artists');;
    Route::post('business/images/add', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'addBusinessImages'])->middleware('artists');;
    Route::delete('business/images/delete/{uuid}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'deleteBusinessImage'])->middleware('artists');;

    Route::get('profile', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'getProfile']);


    Route::get('account_settings', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'accountSettingsGet']);
    Route::post('account_settings', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'updateAccountSetting']);


    Route::post('favorites', [\App\Http\Controllers\Api\Home\HomeController::class, 'favoritePost']);
    Route::get('favorites', [\App\Http\Controllers\Api\Home\HomeController::class, 'favoriteGet']);

    Route::get('my_subscriptions/courses', [\App\Http\Controllers\Api\Home\HomeController::class, 'mySubscriptionsCourses']);

    Route::get('contents/products/categories', [\App\Http\Controllers\Api\Content\ContentController::class, 'productCategories']);
    Route::get('contents/products/categories/{uuid}', [\App\Http\Controllers\Api\Content\ContentController::class, 'productSubCategories']);
    Route::get('contents/products/create', [\App\Http\Controllers\Api\Content\ContentController::class, 'createProduct']);

    Route::post('contents/products/add', [\App\Http\Controllers\Api\Content\ContentController::class, 'addProduct']);
    Route::get('contents/products/{type}', [\App\Http\Controllers\Api\Content\ContentController::class, 'getMyProduct']);

    Route::get('contents/products/{uuid}/edit', [\App\Http\Controllers\Api\Content\ContentController::class, 'editProduct']);
    Route::post('contents/products/{uuid}/update', [\App\Http\Controllers\Api\Content\ContentController::class, 'updateProduct']);
    Route::delete('contents/products/{uuid}', [\App\Http\Controllers\Api\Content\ContentController::class, 'deleteProduct']);

    Route::get('contents/locations/categories', [\App\Http\Controllers\Api\Content\ContentController::class, 'locationCategories']);
    Route::get('contents/locations', [\App\Http\Controllers\Api\Content\ContentController::class, 'getMyLocation']);
    Route::post('contents/locations/add', [\App\Http\Controllers\Api\Content\ContentController::class, 'addLocation']);
    Route::get('contents/locations/{uuid}/edit', [\App\Http\Controllers\Api\Content\ContentController::class, 'editLocation']);
    Route::post('contents/locations/{uuid}/update', [\App\Http\Controllers\Api\Content\ContentController::class, 'updateLocation']);
    Route::delete('contents/locations/{uuid}', [\App\Http\Controllers\Api\Content\ContentController::class, 'deleteLocation']);

    Route::get('contents/services/categories', [\App\Http\Controllers\Api\Content\ContentController::class, 'servingCategories']);
    Route::get('contents/services', [\App\Http\Controllers\Api\Content\ContentController::class, 'getMyServing']);
    Route::post('contents/services/add', [\App\Http\Controllers\Api\Content\ContentController::class, 'addServing']);
    Route::get('contents/services/{uuid}/edit', [\App\Http\Controllers\Api\Content\ContentController::class, 'editServing']);
    Route::post('contents/services/{uuid}/update', [\App\Http\Controllers\Api\Content\ContentController::class, 'updateServing']);
    Route::delete('contents/services/{uuid}', [\App\Http\Controllers\Api\Content\ContentController::class, 'deleteServing']);

    Route::get('contents/courses', [\App\Http\Controllers\Api\Content\ContentController::class, 'getMyCourse']);
    Route::post('contents/courses/add', [\App\Http\Controllers\Api\Content\ContentController::class, 'addCourse']);
    Route::get('contents/courses/{uuid}/edit', [\App\Http\Controllers\Api\Content\ContentController::class, 'editCourse']);
    Route::post('contents/courses/{uuid}/update', [\App\Http\Controllers\Api\Content\ContentController::class, 'updateCourse']);
    Route::delete('contents/courses/{uuid}', [\App\Http\Controllers\Api\Content\ContentController::class, 'deleteCourse']);



//    Route::get('profile/{uuid}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'getProfile']);
//    Route::get('profile/{type}/products/{uuid?}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'getProductProfile']);
//    Route::get('profile/{type}/business/{uuid?}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'getBusinessProfile']);
//    Route::get('profile/course/{uuid?}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'getCourseProfile']);
//    Route::get('profile/{uuid?}', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'profile']);
    Route::get('profile/edit', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'editProfile']);
    Route::get('profile/specializations', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'editProfileSpecializations']);
    Route::get('profile/skills', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'editProfileSkills']);
    Route::get('profile/delete', [\App\Http\Controllers\Api\Profile\ProfileController::class, 'deleteUser']);

    Route::get('delivery_addresses', [\App\Http\Controllers\Api\Home\HomeController::class, 'getDeliveryAddresses']);
    Route::post('delivery_addresses/add', [\App\Http\Controllers\Api\Home\HomeController::class, 'addDeliveryAddresses']);
    Route::get('delivery_addresses/{uuid}/edit', [\App\Http\Controllers\Api\Home\HomeController::class, 'editDeliveryAddresses']);
    Route::post('delivery_addresses/{uuid}/update', [\App\Http\Controllers\Api\Home\HomeController::class, 'updateDeliveryAddresses']);
    Route::delete('delivery_addresses/{uuid}', [\App\Http\Controllers\Api\Home\HomeController::class, 'deleteDeliveryAddresses']);

    Route::get('cart/prepare', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'prepareCart']);
    Route::post('cart/add', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'addCart']);
    Route::delete('cart/delete/{uuid}', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'deteteCart']);
    Route::get('cart/{uuid?}', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'getCart']);
    Route::get('cart/pledge/{uuid}', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'pledge']);

    Route::post('cart/{uuid}', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'updateCart']);
    Route::get('cart/{uuid}/edit', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'editCart']);

    Route::get('payment/rent', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'getPagePayRent']);
    Route::get('payment/sale', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'getPagePaySale']);

    Route::post('messages', [\App\Http\Controllers\Api\Contact\ContactController::class, 'message']);
    Route::get('messages', [\App\Http\Controllers\Api\Contact\ContactController::class, 'messages']);


    Route::post('payment/checkout', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'checkout']);
    Route::get('payment/{uuid}', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'paymentDetails']);
    Route::get('orders/{uuid}/details', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'orderTracking']);


    //    Route::get('get/paymentGateways', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'paymentGateways']);
    //    Route::get('orders/owner/{type}', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'ordersOwner']);
//    Route::get('orders/courses/tracking', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'orderTrackingCourse']);
//    Route::get('orders/service/tracking', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'orderTrackingService']);
//    Route::get('orders/rent/tracking', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'orderTrackingRent']);
    Route::post('orders/accept/{uuid}', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'acceptStatusOrder']);
    Route::post('orders/receive/{uuid}', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'receiveStatusOrder']);
    Route::post('orders/cancel/{uuid}', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'cancelStatusOrder']);

    Route::delete('discount/delete/{uuid}/{user_uuid}', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'deleteDiscount']);


    Route::get('notifications', [\App\Http\Controllers\Api\Home\HomeController::class, 'notifications']);

    Route::get('orders/{uuid}/review', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'getReviews']);
    Route::post('orders/{uuid}/review', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'AddReviews']);

    Route::get('packages', [\App\Http\Controllers\Api\Package\PackageController::class, 'getPackages']);
    Route::get('packages/payment/{uuid}', [\App\Http\Controllers\Api\Package\PackageController::class, 'payment']);
    Route::post('packages/checkout', [\App\Http\Controllers\Api\Package\PackageController::class, 'checkout']);

    Route::get('chat/conversations', [\App\Http\Controllers\Api\Chat\ChatController::class, 'conversations']);
    Route::get('chat/user', [\App\Http\Controllers\Api\Chat\ChatController::class, 'chat']);
    Route::post('chat/add', [\App\Http\Controllers\Api\Chat\ChatController::class, 'sendMsg']);


    Route::get('orders/chat', [\App\Http\Controllers\Api\Orders\OrderServiceController::class, 'chat']);
    Route::post('orders/chat/add', [\App\Http\Controllers\Api\Orders\OrderServiceController::class, 'sendMsg']);
    Route::post('orders/offer/{service_uuid}/add', [\App\Http\Controllers\Api\Orders\OrderServiceController::class, 'addOffer']);
    Route::get('orders/{type}', [\App\Http\Controllers\Api\Orders\OrdersController::class, 'ordersBuyer']);
    Route::post('orders/bill/store', [\App\Http\Controllers\Api\Orders\OrderServiceController::class, 'storeOffer']);


});
