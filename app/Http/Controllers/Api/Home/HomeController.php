<?php

namespace App\Http\Controllers\Api\Home;

use App\Http\Controllers\Controller;
use App\Http\Resources\AddressResource;
use App\Http\Resources\artists;
use App\Http\Resources\BusinessVideoResource;
use App\Http\Resources\Categories;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CityResource;
use App\Http\Resources\CourseDetailsResource;
use App\Http\Resources\CourseMyResource;
use App\Http\Resources\homePage;
use App\Http\Resources\LocationResource;
use App\Http\Resources\MapResource;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\ProductHomeResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\profileArtistResource;
use App\Http\Resources\profileUserResource;
use App\Http\Resources\SubCategoryResource;
use App\Models\Ads;
use App\Models\BusinessVideo;
use App\Models\Category;
use App\Models\CategoryContent;
use App\Models\CategoryLocation;
use App\Models\City;
use App\Models\Country;
use App\Models\Course;
use App\Models\DeliveryAddresses;
use App\Models\Favorite;
use App\Models\FavoriteUser;
use App\Models\FcmToken;
use App\Models\Location;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\Order;
use App\Models\Page;
use App\Models\Product;
use App\Models\Search;
use App\Models\Service;
use App\Models\Setting;
use App\Models\Social;
use App\Models\SubCategory;
use App\Models\User;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
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
        $data = [];

        $categories = Categories::collection(Category::query()->take(6)->get());
        $data[] = [
            'title' => __('Browse categories'),
            'url' => null,
            'data_type' => 'category',
            'type' => 'array',
            'data' => $categories,
        ];

        $product_leasing = ProductHomeResource::collection(Product::query()->where('type', 'rent')->take(6)->get());
        $data[] = [
            'title' => __('Popular products for rent'),
            'url' => 'popular_products_for_rent',
            'data_type' => 'product',
            'type' => 'array',
            'data' => $product_leasing,
        ];

        $product_new_leasing = ProductHomeResource::collection(Product::query()->where('type', 'rent')->orderByDesc('created_at')->take(6)->get());
        $data[] = [
            'title' => __('Newly listed for rent'),
            'url' => 'newly_listed_for_rent',
            'data_type' => 'product',
            'type' => 'array',
            'data' => $product_new_leasing,
        ];

        $ad = Ads::query()->first();
        $data[] = [
            'title' => __('Ad'),
            'url' => null,
            'data_type' => 'ad',
            'type' => 'object',
            'data' => $ad,
        ];

        $product_sale = ProductHomeResource::collection(Product::query()->where('type', 'sale')->take(6)->get());
        $data[] = [
            'title' => __('Popular products for sale'),
            'url' => 'popular_products_for_sale',
            'data_type' => 'product',
            'type' => 'array',
            'data' => $product_sale,
        ];

        $product_new_sale = ProductHomeResource::collection(Product::query()->where('type', 'sale')->orderByDesc('created_at')->take(6)->get());
        $data[] = [
            'title' => __('Newly listed for sale'),
            'url' => 'newly_listed_for_sale',
            'data_type' => 'product',
            'type' => 'array',
            'data' => $product_new_sale,
        ];

        $artists = artists::collection(User::query()->where('type', 'artist')->take(6)->get());
        $data[] = [
            'title' => __('The most prominent artists'),
            'url' => 'the_most_prominent_artists',
            'data_type' => 'artist',
            'type' => 'array',
            'data' => $artists,
        ];

        $locations = LocationResource::collection(Location::query()->orderByDesc('created_at')->take(6)->get());
        $data[] = [
            'title' => __('Latest filming locations'),
            'url' => 'latest_filming_locations',
            'data_type' => 'location',
            'type' => 'array',
            'data' => $locations,
        ];

        $business_video = BusinessVideoResource::collection(BusinessVideo::query()->orderByDesc('created_at')->take(6)->get());
        $data[] = [
            'title' => __('Product of professionals'),
            'url' => 'product_of_professionals',
            'data_type' => 'business_video',
            'type' => 'array',
            'data' => $business_video,
        ];

        return mainResponse(true, "done", compact('home_page', 'city', 'services', 'data'), [], 200);
    }

    public function page($id)
    {
        $setting = Page::query()->where('id', $id)->first();
        if ($id == Page::about_application) {
            $setting->setAttribute('social_medias', Social::query()->select('uuid', 'link')->get()
            );
        }
        return mainResponse(true, "done", $setting, []);
    }

    public function artists(Request $request)
    {

        $city_uuid = $request->city_uuid;
        $orderCreate = ($request->order_created_at == 'desc') ? 'orderByDesc' : 'orderBy';
        $orderName = ($request->order_a_z == 'desc') ? 'orderByDesc' : 'orderBy';
        $artists = User::query()->where('type', 'artist');
        if ($request->has('order_created_at')) {
            $artists->$orderCreate('created_at');
        }

        if ($request->has('filter_most_viewed')) {
            $artists->orderByDesc('view')->take(10);
        }
        if ($request->has('filter_specializations_uuid')) {
            $artists->whereIn('specialization_uuid',$request->filter_specializations_uuid);
        }
        if ($request->has('order_a_z')) {
            $artists->$orderName('name');
        }
        if ($request->has('city_uuid')) {
            $artists->where('city_uuid', $city_uuid);
        }
        if ($request->has('name')) {
            $artists->where('name', 'like',"%{$request->name}%");
        }
        $artists = $artists->paginate();
        $items = pageResource($artists, artists::class);
        $city = City::query()->select('uuid', 'name')->first();
        $city = new CityResource($city);
        return mainResponse(true, "done", compact('items', 'city'), [], 200);
    }

    public function artist(Request $request, $uuid)
    {

        $user = User::query()->findOrFail($uuid);
        if ($user->type == 'artist') {
            $user = new profileArtistResource($user);
        } else {
            $user = new profileUserResource($user);
        }
        return mainResponse(true, "done", $user, [], 200);
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
        $sub_categories = SubCategory::query()->where('category_uuid', $uuid)->paginate();
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
            ->where('sub_category_uuid', $sub_category_uuid);
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

        $product = Product::query()->findOrFail($uuid);
        return mainResponse(true, "done", new ProductResource($product), [], 200);

    }

    public function getDetailsLocation($uuid)
    {
        $location = Location::query()->findOrFail($uuid);
        return mainResponse(true, "done", new LocationResource($location), [], 200);

    }

    public function mySubscriptionsCourses(Request $request)
    {
        $search = $request->search;
        $user = Auth::guard('sanctum')->user();
        $courses = Course::query()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })
            ->whereHas('orders', function ($q) use ($user) {
                $q->where('user_uuid', $user->uuid);
            })->paginate();
        $items = pageResource($courses, CourseMyResource::class);
        return mainResponse(true, "done", compact('items'), [], 200);
    }

    public function getDetailsCourse($uuid)
    {
        $course = Course::query()->findOrFail($uuid);
        return mainResponse(true, "done", new CourseDetailsResource($course), [], 200);

    }

    public function favoritePost(Request $request)
    {
        $rules = [
            'type' => 'required|in:' . Favorite::USER . ',' . Favorite::ARTIST . ',' . Favorite::LOCATION . ',' . Favorite::PRODUCT,
        ];
        if ($request->type == Favorite::USER) {
            $rules['uuid'] = ['required', Rule::exists(User::class, 'uuid')->where('type', User::USER)];
        } elseif ($request->type == Favorite::ARTIST) {
            $rules['uuid'] = ['required', Rule::exists(User::class, 'uuid')->where('type', User::ARTIST)];
        } elseif ($request->type == Favorite::PRODUCT) {
            $rules['uuid'] = ['required', Rule::exists(Product::class, 'uuid')];
        } elseif ($request->type == Favorite::LOCATION) {
            $rules['uuid'] = ['required', Rule::exists(Location::class, 'uuid')];
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }

        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $check = Favorite::query()
                ->where('user_uuid', $user->uuid)
                ->where('content_uuid', $request->uuid)
                ->where('content_type', $request->type)
                ->first();
            if (!$check) {
                Favorite::create([
                    'user_uuid' => $user->uuid,
                    'content_uuid' => $request->uuid,
                    'content_type' => $request->type
                ]);
                return mainResponse(true, 'ok', [], []);
            } else {
                $check->delete();
                return mainResponse(true, 'done delete', [], []);
            }
        } else {
            return mainResponse(false, 'users is not register', [], []);
        }
    }

    public function favoriteGet(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {

            if ($request->type == Favorite::PRODUCT) {
                $items = Product::query()->whereHas('favorite', function ($query) use ($user) {
                    $query->where('user_uuid', $user->uuid);
                })->paginate();
            } elseif ($request->type == Favorite::LOCATION) {
                $items = Location::query()->whereHas('favorite', function ($query) use ($user) {
                    $query->where('user_uuid', $user->uuid);
                })->paginate();
            } elseif ($request->type == Favorite::USER) {
                $items = User::query()->where('type', User::USER)->whereHas('favorite', function ($query) use ($user) {
                    $query->where('user_uuid', $user->uuid);
                    $query->where('type', User::USER);
                })->paginate();
                $items = pageResource($items, artists::class);
                return mainResponse(true, 'ok', compact('items'), []);
            } elseif ($request->type == Favorite::ARTIST) {
                $items = User::query()
                    ->where('type', User::ARTIST)
                    ->whereHas('favorite', function ($query) use ($user) {
                        $query->where('user_uuid', $user->uuid);
                        $query->where('type', User::ARTIST);
                    })->paginate();
                $items = pageResource($items, artists::class);
                return mainResponse(true, 'ok', compact('items'), []);
            }
            $items = pageResource($items, ProductResource::class);
            return mainResponse(true, 'ok', compact('items'), []);
        } else {
            return mainResponse(false, 'type is invalid', [], []);
        }
    }

    public function seeAll(Request $request)
    {
        $type = $request->type;
        $rules = [
            'type' => 'required|in:popular_products_for_sale,newly_listed_for_sale,newly_listed_for_rent,popular_products_for_rent,latest_filming_locations,the_most_prominent_artists,product_of_professionals',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $orderCreated = ($request->order_created_at == 'desc') ? 'orderByDesc' : 'orderBy';

        $orderPrice = ($request->order_price == 'desc') ? 'orderByDesc' : 'orderBy';
        $filter_price_from = $request->filter_price_from ?? null;
        $filter_price_to = $request->filter_price_to ?? null;
        $filter_best_seller = $request->filter_best_seller ?? false;
        $more_buy = false;
        if ($filter_best_seller) {
            $more_buy = Order::query()
                ->selectRaw('COUNT(content_uuid) as number,content_uuid')
                ->groupBy('content_uuid')
//              ->where('content_type',Order::LOCATION)
                ->orderByDesc('number')
//                ->limit(5)
                ->pluck('content_uuid');
        }
        if ($type == 'popular_products_for_sale') {
            $content = Product::query()
                ->where('type', 'sale')
                ->when($filter_price_to, function ($q) use ($filter_price_to, $filter_price_from) {
                    $q->whereBetween('price', [$filter_price_from, $filter_price_to]);
                    $q->orderByDesc('price');

                })->when($more_buy, function ($q) use ($more_buy) {
                    $q->whereIn('uuid', $more_buy);
                });
        } elseif ($type == 'newly_listed_for_sale') {
            $startDate = Carbon::now()->subDays(10)->toDateString();
            $content = Product::query()
                ->where('type', 'sale')
                ->orderByDesc('created_at')
                ->when($filter_price_to, function ($q) use ($filter_price_to, $filter_price_from) {
                    $q->whereBetween('price', [$filter_price_from, $filter_price_to]);
                    $q->orderByDesc('price');

                })->when($more_buy, function ($q) use ($more_buy) {
                    $q->whereIn('uuid', $more_buy);
                })
                ->take(10);
//                ->whereDate('created_at', '>=', $startDate);
        } elseif ($type == 'newly_listed_for_rent') {
            $startDate = Carbon::now()->subDays(10)->toDateString();
            $content = Product::query()
                ->where('type', 'rent')
                ->orderByDesc('created_at')
//                ->whereDate('created_at', '>=', $startDate)
                ->when($filter_price_to, function ($q) use ($filter_price_to, $filter_price_from) {
                    $q->whereBetween('price', [$filter_price_from, $filter_price_to]);
                    $q->orderByDesc('price');

                })->when($more_buy, function ($q) use ($more_buy) {
                    $q->whereIn('uuid', $more_buy);
                });;
        } elseif ($type == 'popular_products_for_rent') {
            $content = Product::query()
                ->where('type', 'rent')->when($filter_price_to, function ($q) use ($filter_price_to, $filter_price_from) {
                    $q->whereBetween('price', [$filter_price_from, $filter_price_to]);
                    $q->orderByDesc('price');

                })->when($more_buy, function ($q) use ($more_buy) {
                    $q->whereIn('uuid', $more_buy);
                });
        } elseif ($type == 'latest_filming_locations') {
            $startDate = Carbon::now()->subDays(10)->toDateString();
            $content = Location::query()
                ->orderByDesc('created_at')
//                ->whereDate('created_at', '>=', $startDate)
                ->when($filter_price_to, function ($q) use ($filter_price_to, $filter_price_from) {
                    $q->whereBetween('price', [$filter_price_from, $filter_price_to]);
                    $q->orderByDesc('price');

                })->when($more_buy, function ($q) use ($more_buy) {
                    $q->whereIn('uuid', $more_buy);
                });
        } elseif ($type == 'the_most_prominent_artists') {
            $artists = User::query()->where('type', 'artist')->paginate();
            $items = $artists->getCollection();
            $items = artists::collection($items);
            $artists->setCollection(collect($items));
            $items = $artists;
            return mainResponse(true, "done", compact('items'), [], 200);
        } elseif ($type == 'product_of_professionals') {
            $business_video = BusinessVideo::query()->orderByDesc('created_at')->paginate();
            $items = $business_video->getCollection();
            $items = BusinessVideoResource::collection($items);
            $business_video->setCollection(collect($items));
            $items = $business_video;
            return mainResponse(true, "done", compact('items'), [], 200);
        }


        if ($request->has('search')) {
            $content->where('name', 'like', "%{$request->search}%");
        }
        if ($request->has('order_created_at')) {
            $content->$orderCreated('created_at');
        }
        if ($request->has('order_price')) {
            $content->$orderPrice('price');
        }
        if ($request->has('filter_latest_releases')) {
            $content->orderByDesc('created_at')->take(10);
        }
        $content = $content->paginate();
        $items = pageResource($content, ProductHomeResource::class);
        return mainResponse(true, "done", compact('items'), [], 200);
    }

    public function map(Request $request)
    {
        $search = $request->search;
        $category_uuid = $request->category_uuid;
        $rules = [
            'search' => 'nullable|string',
            'category_uuid' => 'nullable|string',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }

        $category = false;
        $categoryContent = false;
        if ($category_uuid) {
            $category = Category::query()->find($category_uuid);
            if (!$category) {
                $categoryContent = CategoryContent::query()->find($category_uuid);
                if ($categoryContent) {
                    $categoryContent = true;
                }
            } else {
                $category = true;
            }
            if (!$category && !$categoryContent) {
                return mainResponse(false, __('Category uuid is invalid'), [], [], 101);
            }
        }
        $items = new Collection();
        $items = $items->merge(Category::query()->get());
        $items = $items->merge(CategoryContent::query()->get());
        $items = CategoryResource::collection($items);
        $categories = new Collection();
        $categories = $categories->merge([['uuid' => null, 'name_translate' => __('all')]]);
        $categories = $categories->merge($items);
        $row = DB::raw(
            "(((acos(sin((" . ($request->lat ?? 0) . "*pi()/180)) * sin((`lat`*pi()/180)) + cos((" . ($request->lat ?? 0) . "*pi()/180)) * cos((`lat`*pi()/180)) *
             cos(((" . ($request->lng ?? 0) . "- `lng`) * pi()/180)) )) * 180/pi()) * 60 * 1.1515 * 1.609344)
        as distance");
        $radius = $request->radius ?? 500000000000;
        $products = Product::query()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })
            ->when($category, function ($q) use ($category_uuid) {
                $q->where('category_uuid', $category_uuid);
            })
            ->select('products.*', $row)
            ->having("distance", "<", $radius)
            ->orderBy('distance')
            ->take(10)
            ->get();
        $locations = Location::query()
            ->when($search, function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%');
            })
            ->when($categoryContent, function ($q) use ($category_uuid) {
                $q->whereHas('categories', function ($q) use ($category_uuid) {
                    $q->where('category_contents_uuid', $category_uuid);
                });
            })
            ->select('locations.*', $row)
            ->having("distance", "<", $radius)
            ->orderBy('distance')
            ->take(10)
            ->get();

        $items = new Collection();
        $items = $items->merge(MapResource::collection($products));
        $items = $items->merge(MapResource::collection($locations));

        return mainResponse(true, "done", compact('categories', 'items'), [], 200);
    }

    public function search(Request $request)
    {
        $rules = [
            'search' => 'required|string',
            'city_uuid' => 'nullable|exists:cities,uuid',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $fcm = null;
        $user_uuid = null;
        $user = Auth::guard('sanctum')->user();
        if (!$user) {
            if ($request->has('fcm_token')) {
                $fcm = $request->fcm_token;
            } else {
                return mainResponse(false, 'fcm token is required', [], $validator->errors()->messages(), 101);
            }
        } else {
            $user_uuid = $user->uuid;
        }
        Search::query()->updateOrCreate([
            'title' => $request->search,
            'user_uuid' => $user_uuid,
            'fcm_token' => $fcm
        ], [
                'searched_at' => Carbon::now()
            ]
        );
        $search = $request->search;
        $city_uuid = $request->city_uuid;
        $products = Product::query()
            ->where('name', 'like', '%' . $search . '%')
            ->when($city_uuid, function ($q) use ($city_uuid) {
                $q->whereHas('user', function ($q) use ($city_uuid) {
                    $q->where("city_uuid", $city_uuid);
                });
            })->get();
        $locations = Location::query()
            ->where('name', 'like', '%' . $search . '%')
            ->when($city_uuid, function ($q) use ($city_uuid) {
                $q->whereHas('user', function ($q) use ($city_uuid) {
                    $q->where("city_uuid", $city_uuid);
                });
            })->get();
        $items = new Collection();
        $items = $items->merge($products);
        $items = $items->merge($locations);
        $items = paginate($items);
        $items = pageResource($items, ProductResource::class);
        $city = City::query()->select('uuid', 'name')->first();
        $city = new CityResource($city);
        return mainResponse(true, "done", compact('items', 'city'), [], 200);

    }

    public function historySearch(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        if ($user) {
            $items = Search::query()->where('user_uuid', $user->uuid)->select('title', 'uuid')->orderByDesc('searched_at')->paginate();
        } else {
            if ($request->has('fcm_token')) {
                $items = Search::query()->where('fcm_token', $request->fcm_token)->select('title', 'uuid')->orderByDesc('searched_at')->paginate();
            } else {
                return mainResponse(false, 'fcm token is required', [], [], 101);
            }
        }
        return mainResponse(true, "done", compact('items'), [], 200);
    }

    public function deleteHistorySearch(Request $request, $uuid = null)
    {
        $user_uuid = auth('sanctum')->id();
        if ($uuid) {
            Search::query()->where('fcm_token', $request->fcm_token)->when($user_uuid, function ($q) use ($user_uuid) {
                $q->orWhere('user_uuid', $user_uuid);
            })->findOrFail($uuid)->delete();
        } else {
            Search::query()->where('fcm_token', $request->fcm_token)->when($user_uuid, function ($q) use ($user_uuid) {
                $q->orWhere('user_uuid', $user_uuid);
            })->delete();
        }
        return mainResponse(true, "done", [], [], 200);
    }

    public function businessVideo($uuid, $type, $video_uuid)
    {
        $businessVide = BusinessVideo::query()->findOrFail($video_uuid);
        $update = $businessVide->increment('view');
        return mainResponse(true, "done", [], [], 200);
    }


    public function addDeliveryAddresses(Request $request)
    {
        $rules = [
            'title' => 'required|string',
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
        if ($request->default) {
            DeliveryAddresses::query()->where('user_uuid', $request->user_uuid)->update([
                'default' => false
            ]);
        }
        $delivery_addresses = DeliveryAddresses::query()->create($request->only('title', 'address', 'lng', 'lat', 'country_uuid', 'city_uuid', 'user_uuid', 'default'));
        return mainResponse(true, "done", [], [], 200);
    }

    public function getDeliveryAddresses()
    {
        $user = Auth::guard('sanctum')->user();
        $delivery_addresses = DeliveryAddresses::query()->where('user_uuid', $user->uuid)->orderByDesc('default')->paginate();
        if ($delivery_addresses) {
            $items = pageResource($delivery_addresses, AddressResource::class);
            return mainResponse(true, "done", compact('items'), [], 200);
        }
        return mainResponse(false, "err", [], ['حصل خطا ما'], 500);
    }

    public function deleteDeliveryAddresses($uuid)
    {
        DeliveryAddresses::destroy($uuid);
        if (DeliveryAddresses::query()
            ->where('user_uuid', auth('sanctum')->id())
            ->where('default', 1)
            ->doesntExist()) {

            $deliveryAddress = DeliveryAddresses::query()
                ->where('user_uuid', auth('sanctum')->id())
                ->where('user_uuid', auth('sanctum')->id())
                ->orderByDesc('created_at')->first();
            $deliveryAddress?->update(['default' => 1]);
        }
        return mainResponse(true, "done", [], [], 200);
    }

    public function updateDeliveryAddresses(Request $request, $uuid)
    {
        $rules = [
            'address' => 'required|string',
            'title' => 'required|string',
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
        $delivery_addresses = DeliveryAddresses::query()->find($uuid);
        if ($delivery_addresses) {
            if ($request->default == 1) {
                DeliveryAddresses::query()->where('user_uuid', $request->user_uuid)->update([
                    'default' => false
                ]);
            } else {
                if (DeliveryAddresses::query()
                    ->where('user_uuid', auth('sanctum')->id())
                    ->where('default', 1)
                    ->doesntExist()) {
                    $deliveryAddress = DeliveryAddresses::query()
                        ->where('user_uuid', auth('sanctum')->id())
                        ->where('user_uuid', auth('sanctum')->id())
                        ->orderByDesc('created_at')->first();
                    $deliveryAddress?->update(['default' => 1]);
                }
            }
            $delivery_addresses->update($request->only('title', 'address', 'lng', 'lat', 'country_uuid', 'city_uuid', 'user_uuid', 'default'));
            return mainResponse(true, "done", [], [], 200);
        } else {
            return mainResponse(false, 'delivery_addresses not found', [], ['delivery_addresses not found'], 101);
        }
    }

    public function editDeliveryAddresses($uuid)
    {
        $item = DeliveryAddresses::query()->findOrFail($uuid);
        $item = new AddressResource($item);
        $countries = Country::query()->select('name', 'uuid')
            ->with('cities')
            ->get()
            ->makeHidden(['image']);

        return mainResponse(true, "done", compact('item', 'countries'), [], 200);
    }

    public function notifications()
    {
        $notifications = Notification::query()->whereHas('receiver', function ($q) {
            $q->where('receiver_uuid', auth('sanctum')->id());
        })->orderByDesc('created_at')
            ->paginate();
        $items = pageResource($notifications, NotificationResource::class);

        return mainResponse(true, "done", compact('items'), [], 200);
    }

    public function settings(Request $request)
    {
        $rules = [
            'fcm_token' => 'required|string|max:255',
            'fcm_device' => 'required|in:android,ios',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $request->merge([
            'user_uuid' => auth('sanctum')->id()
        ]);

        $fcm = FcmToken::query()
            ->updateOrCreate($request->only('fcm_token', 'fcm_device', 'user_uuid'));

        $marketing = $fcm->marketing;
        return mainResponse(true, "done", compact('marketing'), [], 200);

    }

    public function updateSettings(Request $request)
    {
        $rules = [
            'fcm_token' => 'required|string|max:255',
            'fcm_device' => 'required|in:android,ios',
            'marketing' => 'required|in:0,1',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $request->merge([
            'user_uuid' => auth('sanctum')->id()
        ]);
        $fcm = FcmToken::query()
            ->updateOrCreate($request->only('fcm_token', 'fcm_device', 'user_uuid'), $request->only('marketing'));

        $marketing = $fcm->marketing;
        return mainResponse(true, "ok", compact('marketing'), [], 200);
    }


}
