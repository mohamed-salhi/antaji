<?php

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Location;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use DateTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class OrdersController extends Controller
{
    public function addCart(Request $request){

        $rules = [
            'start' => 'required|date',
            'end' => 'required|date',
            'uuid' => 'required',
            'type' => 'required|in:product,location,',
            ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $startDate = Carbon::parse($request->start);
        $endDate = Carbon::parse($request->end);

        $daysDifference = $endDate->diffInDays($startDate);
        if ($request->type=='product'){
           $contect= Product::query()->find($request->uuid);
           if (!$contect){
               return mainResponse(false, 'uuid not found', [], ['uuid not found'], 101);
           }
        }
        if ($request->type=='location'){
            $contect= Location::query()->find($request->uuid);
            if (!$contect){
                return mainResponse(false, 'uuid not found', [], ['uuid not found'], 101);
            }
        }
        $user = Auth::guard('sanctum')->user();
        $request->merge([
             'multi_day_discounts'=>3*$daysDifference*-1,
             'commission'=>$contect->price*0.02,
             'price_with_day'=>$contect->price*$daysDifference,
             'user_uuid'=>$user->uuid,
             'content_uuid'=>$contect->uuid,
        ]);
        Cart::query()->create($request->only('user_uuid','multi_day_discounts','price_with_day','commission','end','start','type','content_uuid'));
        return mainResponse(true, 'done', [], [], 200);

    }

    public function getCart(){
        $user = Auth::guard('sanctum')->user();
        $cart= Cart::query()->where('user_uuid',$user->uuid)->get();
        $cart=\App\Http\Resources\Cart::collection($cart);
        return mainResponse(true, 'done',compact('cart'), [], 200);

    }
}
