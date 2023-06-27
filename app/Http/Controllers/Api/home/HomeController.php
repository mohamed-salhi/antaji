<?php

namespace App\Http\Controllers\Api\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\artists;
use App\Http\Resources\BusinessVideoResource;
use App\Http\Resources\LocationResource;
use App\Http\Resources\ProductHomeResource;
use App\Models\BusinessVideo;
use App\Models\Category;
use App\Models\City;
use App\Models\DeliveryAddresses;
use App\Models\Location;
use App\Models\Product;
use App\Models\Service;
use App\Models\Setting;
use App\Models\SupCategory;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class HomeController extends Controller
{
    public function home()
    {
        $services = Service::select('name')->get();
        $categories = Category::query()->take(6)->get();
        $productLeasing = ProductHomeResource::collection(Product::query()->where('type', 'leasing')->take(6)->get());
        $productNewLeasing = ProductHomeResource::collection(Product::query()->where('type', 'leasing')->orderByDesc('created_at')->take(6)->get());
        $productSale = ProductHomeResource::collection(Product::query()->where('type', 'sale')->take(6)->get());
        $productNewSale = ProductHomeResource::collection(Product::query()->where('type', 'sale')->orderByDesc('created_at')->take(6)->get());
        $artists = artists::collection(User::query()->where('type', 'artist')->take(6)->get());
        $locations = LocationResource::collection(Location::query()->orderByDesc('created_at')->take(6)->get());
        $businessVideo = BusinessVideoResource::collection(BusinessVideo::query()->orderByDesc('created_at')->take(6)->get());
        return mainResponse(true, "done", compact('services', 'categories', 'productLeasing', 'productNewLeasing', 'productSale', 'productNewSale', 'artists', 'locations', 'businessVideo'), [], 200);
    }

    public function termsConditions()
    {
        $setting = Setting::query()->select('terms_conditions')->get();
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
            $artists->where('city_uuid', $city);
        }
        $artists = $artists->get();

        return mainResponse(true, "done", artists::collection($artists), [], 200);
    }

    public function getCityFromCounty($uuid)
    {
        return mainResponse(true, "done", City::query()->where('country_uuid', $uuid)->get(), [], 200);
    }

    public function getSupFromCategory($uuid)
    {
        return mainResponse(true, "done", SupCategory::query()->where('category_uuid', $uuid)->get(), [], 200);
    }

    public function getProductFromCategory(Request $request, $uuid)
    {
        $orderCreate = ($request->created_at == 'old') ? 'orderByDesc' : 'orderBy';
        $orderName = ($request->name == 'Desc') ? 'orderByDesc' : 'orderBy';
        $products = Product::query()->where('sup_category_uuid', $uuid);
        if ($request->has('created_at')) {
            $products->$orderCreate('created_at');
        }
        if ($request->has('name')) {
            $products->$orderName('name');
        }
        $products = $products->get();
        return mainResponse(true, "done", ProductHomeResource::collection($products), [], 200);
    }

    public function getDetailsProduct($uuid)
    {
        $product = Product::query()->where('uuid', $uuid)->with('specifications')->get();
        if ($product) {
            return mainResponse(true, "done", $product, [], 200);

        } else {
            return mainResponse(false, "product not found", [], ['product not found'], 200);
        }
    }

    public function businessVideo($uuid)
    {
        $businessVide = BusinessVideo::query()->find($uuid);
        $update = $businessVide->increment('view');
        return mainResponse(true, "done", $businessVide, [], 200);
    }

    public function addDeliveryAddresses(Request $request)
    {
//        return $request;
        $rules = [
            'address' => 'required|string',
            'lng' => 'required',
            'lat' => 'required',
            'country_uuid' => 'required|exists:countries,uuid',
            'city_uuid' => ['required',
                Rule::exists(City::class, 'uuid')->where(function ($query) use ($request) {
                    $query->where('country_uuid', $request->country_uuid);
                }),
            ],
        'default' => 'nullable|boolean',

        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
        $request->merge([
            'user_uuid' => $user->uuid
        ]);
        if ($request->has('default')){
            DeliveryAddresses::query()->where('user_uuid',$request->user_uuid)->update([
               'default'=>false
            ]);
        }
       $delivery_addresses= DeliveryAddresses::query()->create($request->only('address','lng','lat','country_uuid','city_uuid','user_uuid','default'));

        return mainResponse(true, "done", $delivery_addresses, [], 200);

    }

    public function getDeliveryAddresses(){
        $user = Auth::guard('sanctum')->user();

        $delivery_addresses= DeliveryAddresses::query()->where('user_uuid',$user->uuid)->get();

        if ($delivery_addresses){
            return mainResponse(true, "done", $delivery_addresses, [], 200);

        }
        return mainResponse(false, "err",[], ['حصل خطا ما'], 500);

    }
    public function deleteDeliveryAddresses($uuid){
        DeliveryAddresses::destroy($uuid);
        return mainResponse(true, "done",[], [], 200);
    }
    public function updateDeliveryAddresses(Request $request)
    {
//        return $request;
        $rules = [
            'address' => 'required|string',
            'lng' => 'required',
            'lat' => 'required',
            'country_uuid' => 'required|exists:countries,uuid',
            'city_uuid' => ['required',
                Rule::exists(City::class, 'uuid')->where(function ($query) use ($request) {
                    $query->where('country_uuid', $request->country_uuid);
                }),
            ],
            'default' => 'nullable|boolean',

        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
        $request->merge([
            'user_uuid' => $user->uuid
        ]);
        $delivery_addresses= DeliveryAddresses::query()->find($request->uuid);
        if ($delivery_addresses){
            $user = Auth::guard('sanctum')->user();

            if ($request->has('default')){
                DeliveryAddresses::query()->where('user_uuid',$request->user_uuid)->update([
                    'default'=>false
                ]);
            }
            $delivery_addresses->update($request->only('address','lng','lat','country_uuid','city_uuid','user_uuid','default'));
            return mainResponse(true, "done", $delivery_addresses, [], 200);

        }else{
            return mainResponse(false, 'delivery_addresses not found', [], ['delivery_addresses not found'], 101);

        }


    }


}
