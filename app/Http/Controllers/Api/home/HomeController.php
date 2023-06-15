<?php

namespace App\Http\Controllers\Api\Home;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\Setting;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home(){
        $services=Service::select('name')->get();
        return mainResponse(true, "done", compact('services'), [], 200);
    }
    public function termsConditions(){
        $setting=Setting::all();
        return mainResponse(true, "done", $setting, [], 200);
    }
}
