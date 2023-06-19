<?php

use App\Http\Controllers\Admin\places\CityController;
use App\Http\Controllers\Admin\places\countryController;
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


        Route::middleware('auth')->group(function () {
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
            });
            Route::controller(CityController::class)->prefix('cities')->name('cities.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
                Route::post('/update', 'update')->name('update');
                Route::delete('/{uuid}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');
            });
            Route::controller(\App\Http\Controllers\Admin\Service\ServiceController::class)->name('services.')->prefix('services')->group(function () {
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
                Route::delete('/{id}', 'destroy')->name('delete');
                Route::get('/indexTable', 'indexTable')->name('indexTable');
                Route::put('/updateStatus/{status}/{uuid}', 'updateStatus')->name('updateStatus');

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
            Route::controller(\App\Http\Controllers\Admin\Product\ProductLeasingController::class)->name('products.leasing.')->prefix('products/leasing')->group(function () {
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
            Route::controller(\App\Http\Controllers\Admin\setting\SettingController::class)->prefix('settings')->name('settings.')->group(function () {
                Route::get('/', 'index')->name('index');
                Route::post('/store', 'store')->name('store');
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
                Route::get('/sup/{uuid}', 'supIndex')->name('sup');
                Route::post('/sup/{uuid}/store', 'supStore')->name('sup.store');
                Route::post('/sup/{uuid}/update', 'supUpdate')->name('sup.update');
                Route::get('/sup/{uuid}/indexTable', 'supIndexTable')->name('sup.indexTable');
                Route::put('/sup/{category}/updateStatus/{status}/{sup}', 'supUpdateStatus')->name('sup.updateStatus');
                Route::delete('/sup/{uuid}/{delete}', 'supDestroy')->name('sup.delete');
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
        });

    });
