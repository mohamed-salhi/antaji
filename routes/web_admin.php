<?php

use App\Http\Controllers\Admin\PaymentGateway\ProcessPaymentController;
use App\Http\Controllers\Admin\Places\CityController;
use App\Http\Controllers\Admin\Places\CountryController;
use Illuminate\Support\Facades\Route;
use Mcamara\LaravelLocalization\Facades\LaravelLocalization;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::group(
    [
        'prefix' => LaravelLocalization::setLocale(),
        'middleware' => ['localeSessionRedirect', 'localizationRedirect', 'localeViewPath']
    ],
    function () {
        Route::get('admin/login', function () {
            return view('admin.auth.login');
        });


        Route::middleware('auth')->prefix('admin')->group(function () {

            Route::get('/', function () {
                return redirect(route('countries.index'));
            })->name('admin.index');

            Route::controller(CountryController::class)->prefix('countries')->name('countries.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{uuid}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');
            });
            Route::controller(\App\Http\Controllers\Admin\Intro\IntroController::class)->prefix('intros')->name('intros.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{uuid}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');

            });
            Route::controller(CityController::class)->prefix('cities')->name('cities.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{uuid}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');
            });
//            Route::controller(\App\Http\Controllers\Admin\MainController::class)->prefix('main')->name('main.')->group(function () {
                Route::get('/main', [\App\Http\Controllers\Admin\MainController::class,'index'])->name('main.index');

//            });
            Route::controller(\App\Http\Controllers\Admin\Service\ServiceController::class)->name('services.')->prefix('services')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');

            });
            Route::controller(\App\Http\Controllers\Admin\Notifications\NotificationController::class)->name('notifications.')->prefix('notifications')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::delete('/{id}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');

            });
            Route::controller(\App\Http\Controllers\Admin\Ads\AdsController::class)->name('ads.')->prefix('ads')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');

            });
            Route::controller(\App\Http\Controllers\Admin\Skill\SkillController::class)->name('skills.')->prefix('skills')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');

            });

            Route::controller(\App\Http\Controllers\Admin\Specialization\SpecializationController::class)->name('specializations.')->prefix('specializations')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');

            });

            Route::controller(\App\Http\Controllers\Admin\Course\CourseController::class)->name('courses.')->prefix('courses')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');
                Route::get('/videos/{uuid}', 'videoIndex')->name('videos.index');
                Route::post('/videos/store', 'videoStore')->name('videos.store');
                Route::post('/videos/update', 'videoUpdate')->name('videos.update');
                Route::delete('/videos/{uuid}/{image}', 'videoDestroy')->name('videos.delete');
                Route::get('/videos/indexTable/{uuid}', 'videoIndexTable')->name('videos.imageIndexTable');
                Route::delete('/{uuid}', 'destroy')->name('delete');
            });
            Route::controller(\App\Http\Controllers\Admin\Location\LocationController::class)->name('locations.')->prefix('locations')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');
                Route::get('/images/{uuid}', 'imageIndex')->name('images.index');
                Route::post('/images/store', 'imageStore')->name('images.store');
                Route::post('/images/update', 'imageUpdate')->name('images.update');
                Route::delete('/images/{uuid}/{image}', 'imageDestroy')->name('images.delete');
                Route::get('/images/indexTable/{uuid}', 'imageIndexTable')->name('images.imageIndexTable');
                Route::delete('/{uuid}', 'destroy')->name('delete');
                Route::controller(\App\Http\Controllers\Admin\Location\CategoryController::class)->name('categories.')->prefix('/categories')->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::post('/store', 'store')->name('store');
                    Route::post('/update', 'update')->name('update');
                    Route::delete('/{uuid}', 'destroy')->name('delete');
                    Route::get('/indexTable', 'indexTable')->name('indexTable');
                    Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');

                });
            });
            Route::controller(\App\Http\Controllers\Admin\Product\ProductRentController::class)->name('products.rent.')->prefix('products/rent')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');
                Route::get('/images/{uuid}', 'imageIndex')->name('images.index');
                Route::post('/images/store', 'imageStore')->name('images.store');
                Route::post('/images/update', 'imageUpdate')->name('images.update');
                Route::delete('/images/{uuid}/{image}', 'imageDestroy')->name('images.delete');
                Route::get('/images/indexTable/{uuid}', 'imageIndexTable')->name('images.imageIndexTable');
                Route::delete('/{uuid}', 'destroy')->name('delete');
                Route::get('/category/{uuid}', 'category')->name('category');

            });
            Route::controller(\App\Http\Controllers\Admin\Product\ProductSaleController::class)->name('products.sales.')->prefix('products/sales')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');
                Route::get('/images/{uuid}', 'imageIndex')->name('images.index');
                Route::post('/images/store', 'imageStore')->name('images.store');
                Route::post('/images/update', 'imageUpdate')->name('images.update');
                Route::delete('/images/{uuid}/{image}', 'imageDestroy')->name('images.delete');
                Route::get('/images/indexTable/{uuid}', 'imageIndexTable')->name('images.imageIndexTable');
                Route::delete('/{uuid}', 'destroy')->name('delete');
                Route::get('/category/{uuid}', 'category')->name('category');

            });
            Route::controller(\App\Http\Controllers\Admin\Setting\SettingController::class)->prefix('settings')->name('settings.')->group(function () {
                Route::get('/policies_privacy', [\App\Http\Controllers\Admin\Setting\SettingController::class, 'policies_privacy'])->name('policies_privacy');
                Route::post('/policies_privacy', [\App\Http\Controllers\Admin\Setting\SettingController::class, 'policies_privacy_post'])->name('policies_privacy');
                Route::get('/about_application', [\App\Http\Controllers\Admin\Setting\SettingController::class, 'about_application'])->name('about_application');
                Route::post('/about_application', [\App\Http\Controllers\Admin\Setting\SettingController::class, 'about_application_post'])->name('about_application');
                Route::get('/terms_conditions', [\App\Http\Controllers\Admin\Setting\SettingController::class, 'terms_conditions'])->name('terms_conditions');
                Route::post('/terms_conditions', [\App\Http\Controllers\Admin\Setting\SettingController::class, 'terms_conditions_post'])->name('terms_conditions');
                Route::get('/delete_my_account', [\App\Http\Controllers\Admin\Setting\SettingController::class, 'delete_my_account'])->name('delete_my_account');
                Route::post('/delete_my_account', [\App\Http\Controllers\Admin\Setting\SettingController::class, 'delete_my_account_post'])->name('delete_my_account');
                Route::post('/', [\App\Http\Controllers\Admin\Setting\SettingController::class, 'post'])->name('index');
                Route::get('/', [\App\Http\Controllers\Admin\Setting\SettingController::class, 'index'])->name('index');

            });
            Route::controller(\App\Http\Controllers\Admin\Discount\DiscountController::class)->name('discount.')->prefix('discount')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');
                Route::get('/indexTable', 'indexTable')->name('indexTable');

            });
            Route::controller(\App\Http\Controllers\Admin\Package\PackageController::class)->name('packages.')->prefix('packages')->group(function () {
                Route::get('/', 'index')->name('index');
//                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
            });
            Route::controller(\App\Http\Controllers\Admin\Discount\MultiDayDiscountController::class)->name('multidaydiscount.')->prefix('multidaydiscount')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::delete('/{id}', 'destroy')->name('delete');

                Route::post('/update', 'update')->name('update');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');
                Route::get('/indexTable', 'indexTable')->name('indexTable');

            });
            Route::controller(\App\Http\Controllers\Admin\Order\OrderController::class)->name('orders.')->prefix('orders')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/indexTable', 'indexTable')->name('indexTable');

            });
            Route::controller(\App\Http\Controllers\Admin\Conversation\ConversationController::class)->name('conversations.')->prefix('conversations')->group(function () {
                Route::get('/{uuid}', 'index')->name('index');
                Route::get('/chat/{uuid?}', 'chat')->name('chat');
                Route::get('/details/{uuid}', 'details')->name('details');
            });

            Route::controller(\App\Http\Controllers\Admin\Role\RolesController::class)->name('roles.')->prefix('roles')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{uuid}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');

            });

            Route::controller(\App\Http\Controllers\Admin\AdminController::class)->name('managers.')->prefix('managers')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{id}', 'updateStatus')->name('updateStatus');
                Route::get('/edit/{id}', 'edit')->name('edit');
            });
            Route::controller(\App\Http\Controllers\Admin\Social\SocialController::class)->name('social.')->prefix('social')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{uuid}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');

            });
            Route::get('/support/index/{uuid?}', [\App\Http\Controllers\Admin\Support\SupportController::class,'index'])->name('index');
            Route::post('/support/message/send', [\App\Http\Controllers\Admin\Support\SupportController::class,'message'])->name('send_msg');
            Route::get('/support/readMore/{uuid}', [\App\Http\Controllers\Admin\Support\SupportController::class,'readMore'])->name('admin.support.read_more');

            Route::controller(\App\Http\Controllers\Admin\Discount\DeliveryController::class)->name('delivery.')->prefix('delivery')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/update', 'update')->name('update');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');
                Route::get('/indexTable', 'indexTable')->name('indexTable');

            });
            Route::controller(\App\Http\Controllers\Admin\Documentation\DocumentationController::class)->name('documentations.')->prefix('documentations')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');

            });
            Route::controller(\App\Http\Controllers\Admin\Contact\ContactController::class)->prefix('contacts')->name('contacts.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::delete('/{uuid}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::post('/view/{uuid}', 'view')->name('view');
                Route::post('/importance/{uuid}/{importance}', 'importance')->name('importance');
            });
            Route::controller(\App\Http\Controllers\Admin\Category\CategoryController::class)->prefix('categories')->name('categories.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{id}', 'updateStatus')->name('updateStatus');
                Route::get('/sub/{uuid}', 'subIndex')->name('sub');
                Route::post('/sub/{uuid}/store', 'subStore')->name('sub.store');
                Route::post('/sub/{uuid}/update', 'subUpdate')->name('sub.update');
                Route::get('/sub/{uuid}/indexTable', 'subIndexTable')->name('sub.indexTable');
                Route::put('/sub/{category}/updateStatus/{status}/{sub}', 'subUpdateStatus')->name('sub.updateStatus');
                Route::delete('/sub/{uuid}/{delete}', 'subDestroy')->name('sub.delete');
                Route::delete('/{id}', 'destroy')->name('delete');
            });
            Route::controller(\App\Http\Controllers\Admin\User\UserController::class)->prefix('users')->name('users.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{id}', 'updateStatus')->name('updateStatus');
                Route::get('/country/{uuid}', 'country')->name('country');

            });
            Route::controller(\App\Http\Controllers\Admin\Artist\ArtistController::class)->prefix('artists')->name('artists.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{id}', 'updateStatus')->name('updateStatus');
                Route::get('/country/{uuid}', 'country')->name('country');
            });
            Route::controller(\App\Http\Controllers\Admin\Business\BusinessController::class)->prefix('business')->name('business.')->group(function () {
                Route::get('/video', 'indexVideo')->name('video.index');
                Route::post('/video/store', 'storeVideo')->name('video.store');
                Route::post('/video/update', 'updateVideo')->name('video.update');
                Route::delete('/video/{id}', 'destroyVideo')->name('video.delete');
                Route::get('/video/{uuid}/indexTable', 'indexTableVideo')->name('video.indexTable');
                Route::put('/video/updateStatus/{status}/{id}', 'updateStatusVideo')->name('video.updateStatus');
                Route::get('/images', 'indexImages')->name('images.index');
                Route::post('/images/store', 'storeImages')->name('images.store');
                Route::post('/images/update', 'updateImages')->name('images.update');
                Route::delete('/images/{id}', 'destroyImages')->name('images.delete');
                Route::get('/images/{uuid}//indexTable', 'indexTableImages')->name('images.indexTable');
                Route::put('/images/updateStatus/{status}/{id}', 'updateStatusImages')->name('images.updateStatus');
            });
            Route::controller(\App\Http\Controllers\Admin\Serving\ServingController::class)->name('servings.')->prefix('servings')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{id}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');

                Route::controller(\App\Http\Controllers\Admin\Serving\CategoryController::class)->name('categories.')->prefix('/categories')->group(function () {
                    Route::get('/', 'index')->name('index');
                    Route::post('/store', 'store')->name('store');
                    Route::post('/update', 'update')->name('update');
                    Route::delete('/{uuid}', 'destroy')->name('delete');
                    Route::get('/indexTable', 'indexTable')->name('indexTable');
                    Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');

                });
            });
            Route::controller(\App\Http\Controllers\Admin\PaymentGateway\PaymentGatewayController::class)->prefix('paymentGateways')->name('paymentGateways.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/getData', 'getData')->name('getData');
                Route::post('/update', 'update')->name('update');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');
                Route::get('content/checkout/{uuid}', 'checkout')->name('checkout')->withoutMiddleware(['auth']);;
                Route::get('content/pay/{uuid}', 'pay')->name('pay')->withoutMiddleware(['auth']);;
                Route::get('payment/{status}', 'status')->name('status')->withoutMiddleware(['auth']);;
            });
            Route::controller(ProcessPaymentController::class)->prefix('payments')->name('payments.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::get('/getData', 'getData')->name('getData');
            });


        });

    });
