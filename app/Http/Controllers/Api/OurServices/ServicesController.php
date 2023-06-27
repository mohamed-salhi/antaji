<?php

namespace App\Http\Controllers\Api\OurServices;

use App\Http\Controllers\Controller;
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
    public function sellBuy(Request $request){
        $orderCreate = ($request->created_at == 'old') ? 'orderByDesc' : 'orderBy';
        $orderName = ($request->name == 'Desc') ? 'orderByDesc' : 'orderBy';
        $orderPrice = ($request->price == 'bottom') ? 'orderByDesc' : 'orderBy';

        $products=Product::query()
            ->where('type','sale');
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
        return mainResponse(true, "done", ProductHomeResource::collection($products), [], 200);

    }

    public function leasing(Request $request){
        $orderCreate = ($request->created_at == 'old') ? 'orderByDesc' : 'orderBy';
        $orderName = ($request->name == 'Desc') ? 'orderByDesc' : 'orderBy';
        $orderPrice = ($request->price == 'bottom') ? 'orderByDesc' : 'orderBy';

        $products=Product::query()
            ->where('type','leasing');
        if ($request->has('created_at')) {
            $products->$orderCreate('created_at');
        }
        if ($request->has('name')) {
            $products->$orderName('name');
        }
        if ($request->has('price')) {
            $products->$orderPrice('price');
        }
        $products = $products->get();
        return mainResponse(true, "done", ProductHomeResource::collection($products), [], 200);

    }
    public function locations(Request $request){
        $category=$request->category;
        $name=$request->name;
        $categories=CategoryContent::query()->where('type','location')->select('name','uuid')->get();
        $locations=Location::query()
            ->when($category,function (Builder $query) use ($category){
            $query->whereHas('categories',function ($q) use ($category){
                $q->where('uuid',$category);
            });
        })->when($name,function (Builder $query)use ($name){
            $query->where('name',$name);
        })->get();

        $locations = LocationResource::collection($locations);

        return mainResponse(true, "done",compact('categories','locations'), [], 200);

    }
    public function services(Request $request){
        $name=$request->name??'';
        $serving=Serving::query()
            ->when($name, function (Builder $query, string $name) {
                $query->where('name', $name);
            })->get();
        return mainResponse(true, 'done', ServingResource::collection($serving), [], 200);
    }
}
