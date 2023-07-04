<?php

namespace App\Http\Controllers\Api\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\artists;
use App\Http\Resources\BusinessVideoResource;
use App\Http\Resources\Categories;
use App\Http\Resources\CityResource;
use App\Http\Resources\homePage;
use App\Http\Resources\LocationResource;
use App\Http\Resources\ProductHomeResource;
use App\Http\Resources\SubCategoryResource;
use App\Models\Ads;
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
        $home_page = new  homePage(Setting::query()->first());
        $city = City::query()->select('uuid', 'name')->first();
        $city = new CityResource($city);
        $services = Service::select('name', 'uuid', 'status')->get();
        $categories = Categories::collection(Category::query()->take(6)->get());
        $product_leasing = ProductHomeResource::collection(Product::query()->where('type', 'leasing')->take(6)->get());
        $product_new_leasing = ProductHomeResource::collection(Product::query()->where('type', 'leasing')->orderByDesc('created_at')->take(6)->get());
        $ad = Ads::query()->first();
        $product_sale = ProductHomeResource::collection(Product::query()->where('type', 'sale')->take(6)->get());
        $product_new_sale = ProductHomeResource::collection(Product::query()->where('type', 'sale')->orderByDesc('created_at')->take(6)->get());
        $artists = artists::collection(User::query()->where('type', 'artist')->take(6)->get());
        $locations = LocationResource::collection(Location::query()->orderByDesc('created_at')->take(6)->get());
        $business_video = BusinessVideoResource::collection(BusinessVideo::query()->orderByDesc('created_at')->take(6)->get());
        return mainResponse(true, "done", compact('home_page', 'city', 'services', 'categories', 'product_leasing', 'product_new_leasing', 'ad', 'product_sale', 'product_new_sale', 'artists', 'locations', 'business_video'), [], 200);
    }

    public function termsConditions()
    {
        $setting = Setting::query()->select('terms_conditions')->first();
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

    public function categories()
    {
        $categories = Category::query()->paginate();
        $items = $categories->getCollection();
        $items = Categories::collection($items);
        $categories->setCollection(collect($items));
        $items = $categories;

        return mainResponse(true, "done", compact('items'), [], 200);
    }

    public function getSupFromCategory($uuid)
    {
        $sub_categories = SupCategory::query()->where('category_uuid', $uuid)->paginate();
        $items = $sub_categories->getCollection();
        $items = SubCategoryResource::collection($items);
        $sub_categories->setCollection(collect($items));
        $items = $sub_categories;
        return mainResponse(true, "done", compact('items'), [], 200);
    }

    public function getProductFromCategory(Request $request, $uuid, $sub_category_uuid)
    {
        $orderCreate = ($request->created_at == 'old') ? 'orderByDesc' : 'orderBy';
        $orderName = ($request->name == 'Desc') ? 'orderByDesc' : 'orderBy';
        $products = Product::query()
            ->where('category_uuid', $uuid)
            ->where('sup_category_uuid', $sub_category_uuid);
        if ($request->has('created_at')) {
            $products->$orderCreate('created_at');
        }
        if ($request->has('name')) {
            $products->$orderName('name');
        }
        $products = $products->paginate();
        $items = $products->getCollection();
        $items = ProductHomeResource::collection($items);
        $products->setCollection(collect($items));
        $items = $products;

        return mainResponse(true, "done", compact('items'), [], 200);
    }

    public function getDetailsProduct($uuid)
    {
        $product = Product::query()->where('uuid', $uuid)->with('specifications')->with('user:name,uuid')->get();
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
        if ($request->has('default')) {
            DeliveryAddresses::query()->where('user_uuid', $request->user_uuid)->update([
                'default' => false
            ]);
        }
        $delivery_addresses = DeliveryAddresses::query()->create($request->only('address', 'lng', 'lat', 'country_uuid', 'city_uuid', 'user_uuid', 'default'));

        return mainResponse(true, "done", $delivery_addresses, [], 200);

    }

    public function getDeliveryAddresses()
    {
        $user = Auth::guard('sanctum')->user();

        $delivery_addresses = DeliveryAddresses::query()->where('user_uuid', $user->uuid)->get();

        if ($delivery_addresses) {
            return mainResponse(true, "done", $delivery_addresses, [], 200);

        }
        return mainResponse(false, "err", [], ['حصل خطا ما'], 500);

    }

    public function deleteDeliveryAddresses($uuid)
    {
        DeliveryAddresses::destroy($uuid);
        return mainResponse(true, "done", [], [], 200);
    }

    public function updateDeliveryAddresses(Request $request)
    {
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
        $delivery_addresses = DeliveryAddresses::query()->find($request->uuid);
        if ($delivery_addresses) {
            $user = Auth::guard('sanctum')->user();

            if ($request->has('default')) {
                DeliveryAddresses::query()->where('user_uuid', $request->user_uuid)->update([
                    'default' => false
                ]);
            }
            $delivery_addresses->update($request->only('address', 'lng', 'lat', 'country_uuid', 'city_uuid', 'user_uuid', 'default'));
            return mainResponse(true, "done", $delivery_addresses, [], 200);

        } else {
            return mainResponse(false, 'delivery_addresses not found', [], ['delivery_addresses not found'], 101);

        }


    }


}
