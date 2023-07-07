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

Route::get('/test', function () {
//    return fcmNotification(['cEpiRwyTTGW94wVNbOmDM5:APA91bGUs6TykV_5Y6d-R6TNz6-ySC9lRwEGu6Z6hEZtE0UMUbGS8kX0ZftLtjetREpQqfeZeFzF-5H6DAdzzQuo_YERZ4LzhjUVBYpTbu-x5cjZ2x_pBua7bBih-8_xUbg4qbnAFyVh'], 'new test', 'new test', 'new test', 'general', 'android');

    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    \Illuminate\Support\Facades\Artisan::call('view:clear');
    \Illuminate\Support\Facades\Artisan::call('route:clear');
//    dump(1);
//    \Illuminate\Support\Facades\Artisan::call('migrate');
//    \Illuminate\Support\Facades\Artisan::call('db:seed');

        \Illuminate\Support\Facades\Artisan::call('storage:link');
    dd(3232);
});
