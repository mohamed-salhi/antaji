<?php

namespace App\Http\Controllers\Api\OurServices;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\LocationResource;
use App\Http\Resources\ProductHomeResource;
use App\Http\Resources\ServingResource;
use App\Models\CategoryContent;
use App\Models\Location;
use App\Models\Order;
use App\Models\Product;
use App\Models\Serving;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ServicesController extends Controller
{
    public function sellBuy(Request $request)
    {
//        $orderCreate = ($request->created_at == 'old') ? 'orderByDesc' : 'orderBy';
//        $orderName = ($request->name == 'desc') ? 'orderByDesc' : 'orderBy';
        $orderPrice = ($request->price == 'top') ? 'orderByDesc' : 'orderBy';
        $filter_price_from = $request->filter_price_from ?? null;
        $filter_price_to = $request->filter_price_to ?? null;
        $filter_buy = $request->filter_buy ?? false;
        $more_buy = false;
        if ($filter_buy) {
            $more_buy = Order::query()
                ->selectRaw('COUNT(content_uuid) as number,content_uuid')
                ->where('type', Product::SALE)
                ->where('content_type', Order::PRODUCT)
                ->groupBy('content_uuid')
                ->orderByDesc('number')
                ->limit(5)
                ->pluck('content_uuid');
        }

        $products = Product::query()
            ->where('type', 'sale')
            ->when($filter_price_to, function ($q) use ($filter_price_to, $filter_price_from) {
                $q->whereBetween('price', [$filter_price_from, $filter_price_to]);
                $q->orderByDesc('price');

            })->when($more_buy, function ($q) use ($more_buy) {
                $q->whereIn('uuid', $more_buy);
            });
        if ($request->has('search')) {
            $products->where('name', 'like', "%{$request->search}%");
        }
//        if ($request->has('created_at')) {
//            $products->$orderCreate('created_at');
//        }
//        if ($request->has('name')) {
//            $products->$orderName('name');
//        }
        if ($request->has('price')) {
            $products->$orderPrice('price');
        }
        $products = $products->paginate();
        $items = pageResource($products, ProductHomeResource::class);;
        return mainResponse(true, "done", compact('items'), [], 200);

    }

    public function rent(Request $request)
    {
        $orderPrice = ($request->price == 'top') ? 'orderByDesc' : 'orderBy';
        $filter_price_from = $request->filter_price_from ?? null;
        $filter_price_to = $request->filter_price_to ?? null;
        $filter_buy = $request->filter_buy ?? false;
        $more_buy = false;
        if ($filter_buy) {
            $more_buy = Order::query()
                ->selectRaw('COUNT(content_uuid) as number,content_uuid')
                ->whereNull('type')
                ->where('content_type', Order::PRODUCT)
                ->groupBy('content_uuid')
                ->orderByDesc('number')
                ->limit(5)
                ->pluck('content_uuid');
        }
        $products = Product::query()
            ->where('type', 'rent')
            ->when($filter_price_to, function ($q) use ($filter_price_to, $filter_price_from) {
                $q->whereBetween('price', [$filter_price_from, $filter_price_to]);
                $q->orderByDesc('price');

            })->when($more_buy, function ($q) use ($more_buy) {
                $q->whereIn('uuid', $more_buy);
            });
        if ($request->has('search')) {
            $products->where('name', 'like', "%{$request->search}%");
        }

        if ($request->has('price')) {
            $products->$orderPrice('price');
        }
        $products = $products->paginate();
        $items = pageResource($products, ProductHomeResource::class);;
        return mainResponse(true, "done", compact('items'), [], 200);


    }

    public function locations(Request $request)
    {
        $category_uuid = $request->category_uuid;
        $search = $request->search ?? null;
        $orderPrice = ($request->price == 'top') ? 'orderByDesc' : 'orderBy';
        $filter_price_from = $request->filter_price_from ?? null;
        $filter_price_to = $request->filter_price_to ?? null;
        $filter_buy = $request->filter_buy ?? false;
        $more_buy = false;
        if ($filter_buy) {
            $more_buy = Order::query()
                ->selectRaw('COUNT(content_uuid) as number,content_uuid')
                ->whereNull('type',)
                ->where(function (Builder $q) {
                    $q->where('content_type', Order::LOCATION);
                })
                ->groupBy('content_uuid')
                ->orderByDesc('number')
                ->limit(5)
                ->pluck('content_uuid');
        }

        $categories = CategoryContent::query()
            ->where('type', 'location')
            ->get();
        $categories = CategoryResource::collection($categories);
        $locations = Location::query()
            ->when($category_uuid, function (Builder $query) use ($category_uuid) {
                $query->whereHas('categories', function ($q) use ($category_uuid) {
                    $q->where('uuid', $category_uuid);
                });
            })
            ->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
            })->when($filter_price_to, function ($q) use ($filter_price_to, $filter_price_from) {
                $q->whereBetween('price', [$filter_price_from, $filter_price_to]);
                $q->orderByDesc('price');

            })->when($more_buy, function ($q) use ($more_buy) {
                $q->whereIn('uuid', $more_buy);
            });

        $products = $locations->paginate();
        $items = pageResource($products, LocationResource::class);;
        return mainResponse(true, "done", compact('items', 'categories'), [], 200);

    }

    public function services(Request $request)
    {

//        $name = $request->name ?? '';
        $search = $request->search ?? null;
        $city_uuid = $request->city_uuid ?? null;

        $filter_price_from = $request->filter_price_from ?? null;
        $filter_price_to = $request->filter_price_to ?? null;
        $filter_buy = $request->filter_buy ?? false;
//        $more_buy = false;
//        if ($filter_buy) {
//            $more_buy = Order::query()
//                ->selectRaw('COUNT(content_uuid) as number,content_uuid')
//                ->whereNull('type')
//                ->where('content_type', Order::SERVICE)
//                ->groupBy('content_uuid')
//                ->orderByDesc('number')
//                ->limit(5)
//                ->pluck('content_uuid');
//        }
        $serving = Serving::query()
            ->when($search, function (Builder $query, string $search) {
                $query->where('name', 'like', "%{$search}%");
            })->when($filter_price_to, function ($q) use ($filter_price_to, $filter_price_from) {
                $q->whereBetween('price', [$filter_price_from, $filter_price_to]);
                $q->orderByDesc('price');

            })->when($city_uuid, function (Builder $query, string $city_uuid) {
                $query->where('city_uuid', $city_uuid);
            })
//            ->when($more_buy, function ($q) use ($more_buy) {
//                $q->whereIn('uuid', $more_buy);
//            });
        ;

        $serving = $serving->paginate();
        $items = pageResource($serving, ServingResource::class);;
        return mainResponse(true, "done", compact('items'), [], 200);
    }

    public function service($uuid)
    {
        $serving = Serving::query()->findOrFail($uuid);
        $serving = new ServingResource($serving);

        return mainResponse(true, 'done', $serving, [], 200);
    }
}
