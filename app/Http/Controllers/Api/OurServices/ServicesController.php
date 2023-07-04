<?php

namespace App\Http\Controllers\Api\OurServices;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\LocationResource;
use App\Http\Resources\ProductHomeResource;
use App\Http\Resources\ServingResource;
use App\Models\CategoryContent;
use App\Models\Location;
use App\Models\Product;
use App\Models\Serving;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class ServicesController extends Controller
{
    public function sellBuy(Request $request)
    {
        $orderCreate = ($request->created_at == 'old') ? 'orderByDesc' : 'orderBy';
        $orderName = ($request->name == 'desc') ? 'orderByDesc' : 'orderBy';
        $orderPrice = ($request->price == 'bottom') ? 'orderByDesc' : 'orderBy';

        $products = Product::query()
            ->where('type', 'sale');
        if ($request->has('search')) {
            $products->where('name', 'like', "%{$request->search}%");
        }
        if ($request->has('created_at')) {
            $products->$orderCreate('created_at');
        }
        if ($request->has('name')) {
            $products->$orderName('name');
        }
        if ($request->has('price')) {
            $products->$orderPrice('price');
        }
        $products = $products->paginate();
        $items = $products->getCollection();
        $items = ProductHomeResource::collection($items);
        $products->setCollection(collect($items));
        $items = $products;
        return mainResponse(true, "done", compact('items'), [], 200);

    }

    public function leasing(Request $request)
    {
        $orderCreate = ($request->created_at == 'old') ? 'orderByDesc' : 'orderBy';
        $orderName = ($request->name == 'desc') ? 'orderByDesc' : 'orderBy';
        $orderPrice = ($request->price == 'bottom') ? 'orderByDesc' : 'orderBy';

        $products = Product::query()
            ->where('type', 'leasing');
        if ($request->has('search')) {
            $products->where('name', 'like', "%{$request->search}%");
        }
        if ($request->has('created_at')) {
            $products->$orderCreate('created_at');
        }
        if ($request->has('name')) {
            $products->$orderName('name');
        }
        if ($request->has('price')) {
            $products->$orderPrice('price');
        }
        $products = $products->paginate();
        $items = $products->getCollection();
        $items = ProductHomeResource::collection($items);
        $products->setCollection(collect($items));
        $items = $products;
        return mainResponse(true, "done", compact('items'), [], 200);

    }

    public function locations(Request $request)
    {
        $category_uuid = $request->category_uuid;
        $name = $request->name;
        $search = $request->search;

        $categories = CategoryContent::query()
            ->where('type', 'location')
            ->get();
        $categories = CategoryResource::collection($categories);
        $locations = Location::query()
            ->when($category_uuid, function (Builder $query) use ($category_uuid) {
                $query->whereHas('categories', function ($q) use ($category_uuid) {
                    $q->where('uuid', $category_uuid);
                });
            })->when($name, function (Builder $query) use ($name) {
                $query->where('name', $name);
            })->when($search, function (Builder $query) use ($search) {
                $query->where('name', 'like', "%{$search}%");
                    })
            ->paginate();

        $items = $locations->getCollection();
        $items = LocationResource::collection($items);
        $locations->setCollection(collect($items));
        $items = $locations;

        return mainResponse(true, "done", compact('items', 'categories'), [], 200);

    }

    public function services(Request $request)
    {
        $name = $request->name ?? '';
        $serving = Serving::query()
            ->when($name, function (Builder $query, string $name) {
                $query->where('name', $name);
            })->paginate();
        $items = $serving->getCollection();
        $items = ServingResource::collection($items);
        $serving->setCollection(collect($items));
        $items = $serving;

        return mainResponse(true, 'done', compact('items'), [], 200);
    }
}
