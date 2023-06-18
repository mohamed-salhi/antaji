<?php

namespace App\Http\Controllers\Api\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\artists;
use App\Models\City;
use App\Models\Service;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        $services = Service::select('name')->get();
        return mainResponse(true, "done", compact('services'), [], 200);
    }

    public function termsConditions()
    {
        $setting = Setting::all();
        return mainResponse(true, "done", $setting, [], 200);
    }

    public function artists(Request $request)
    {
        $city = $request->city;
        $orderCreate = ($request->created_at == 'old') ? 'orderByDesc' : 'orderBy';
        $orderName = ($request->name == 'Desc') ? 'orderByDesc' : 'orderBy';
        $artists = User::query()->where('type', 'artist');
        if ($request->has('created_at')) {
            $artists->$orderCreate('created_at');
        }
        if ($request->has('name')) {
            $artists->$orderName('name');
        }
        if ($request->has('city')) {
            $artists->where('city_uuid',$city);
        }
        $artists = $artists->get();

        return mainResponse(true, "done", artists::collection($artists), [], 200);
    }

    public function getCityFromCounty($uuid){
        return mainResponse(true, "done", City::query()->where('country_uuid',$uuid)->get(), [], 200);
    }

}
