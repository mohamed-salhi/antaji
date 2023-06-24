<?php

namespace App\Http\Controllers\Api\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\artists;
use App\Http\Resources\LocationResource;
use App\Http\Resources\ProductHomeResource;
use App\Models\BusinessVideo;
use App\Models\Category;
use App\Models\City;
use App\Models\Location;
use App\Models\Product;
use App\Models\Service;
use App\Models\Setting;
use App\Models\SupCategory;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function home()
    {
        $services = Service::select('name')->get();
        $categories=Category::query()->take(10)->get();
        $productLeasing=ProductHomeResource::collection(Product::query()->where('type','leasing')->take(10)->get());
        $productNewLeasing=ProductHomeResource::collection(Product::query()->where('type','leasing')->orderByDesc('created_at')->take(10)->get());
        $productSale=ProductHomeResource::collection(Product::query()->where('type','sale')->take(10)->get());
        $productNewSale=ProductHomeResource::collection(Product::query()->where('type','sale')->orderByDesc('created_at')->take(10)->get());
        $artists = artists::collection(User::query()->where('type', 'artist')->take(10)->get()) ;
        $locations=LocationResource::collection(Location::query()->orderByDesc('created_at')->take(10)->get());
        $businessVideo=BusinessVideo::query()->orderByDesc('created_at')->take(10)->get();
        return mainResponse(true, "done", compact('services','categories','productLeasing','productNewLeasing','productSale','productNewSale','artists','locations','businessVideo'), [], 200);
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
    public function getSupFromCategory($uuid){
        return mainResponse(true, "done", SupCategory::query()->where('category_uuid',$uuid)->get(), [], 200);
    }
    public function getProductFromCategory(Request $request, $uuid){
        $orderCreate = ($request->created_at == 'old') ? 'orderByDesc' : 'orderBy';
        $orderName = ($request->name == 'Desc') ? 'orderByDesc' : 'orderBy';
        $products=Product::query()->where('sup_category_uuid',$uuid);
        if ($request->has('created_at')) {
            $products->$orderCreate('created_at');
        }
        if ($request->has('name')) {
            $products->$orderName('name');
        }
        $products = $products->get();
        return mainResponse(true, "done", ProductHomeResource::collection($products), [], 200);
    }
    public function getDetailsProduct($uuid){
        $product= Product::query()->where('uuid',$uuid)->with('specifications')->get();
        if ($product){
            return mainResponse(true, "done", $product, [], 200);

        }else{
            return mainResponse(false, "product not found", [], ['product not found'], 200);
        }
    }

    public function businessVideo($uuid){
       $businessVide= BusinessVideo::query()->find($uuid);
       $update=$businessVide->increment('view');
        return mainResponse(true, "done", $businessVide, [], 200);
    }





}
