<?php

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Course;
use App\Models\DeliveryAddresses;
use App\Models\Location;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\Product;
use App\Models\Serving;
use App\Models\Setting;
use App\Models\User;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use DateTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OrdersController extends Controller
{
    public function addCart(Request $request)
    {

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
        if ($request->type == 'product') {
            $contect = Product::query()->where('uuid', $request->uuid)->where('type', 'leasing')->first();
            if (!$contect) {
                return mainResponse(false, 'uuid not found', [], ['uuid not found'], 101);
            }
        }
        if ($request->type == 'location') {
            $contect = Location::query()->find($request->uuid);
            if (!$contect) {
                return mainResponse(false, 'uuid not found', [], ['uuid not found'], 101);
            }
        }
        $user = Auth::guard('sanctum')->user();
        $settings = Setting::query()->first();

        $request->merge([
            'multi_day_discounts' => 3 * $daysDifference * -1,
            'commission' => $contect->price * doubleval($settings->commission),
            'price_with_day' => $contect->price * $daysDifference,
            'user_uuid' => $user->uuid,
            'content_uuid' => $contect->uuid,
        ]);
        if (Cart::query()->where('user_uuid', $user->uuid)->where('content_uuid', $contect->uuid)->exists()) {
            return mainResponse(false, 'this content in found', [], ['uuid not found'], 101);

        }
        Cart::query()->create($request->only('user_uuid', 'multi_day_discounts', 'price_with_day', 'commission', 'end', 'start', 'type', 'content_uuid'));
        return mainResponse(true, 'done', [], [], 200);

    }


    public function getCart($uuid = null)
    {
        $user = Auth::guard('sanctum')->user();
        $cart = Cart::query()->select('uuid', 'content_uuid', 'type')

            ->where('user_uuid', $user->uuid)
            ->get();
        $array = [];
        foreach ($cart as $item) {
            $uuid_user=$item->content_owner_uuid;
            $array[] = [
                'name' => $item->content_owner_name,
                'uuid' => $item->content_owner_uuid,
                'image' => $item->content_owner_image,
                'count'=>Cart::query()->where('user_uuid', $user->uuid) ->WhereHas('products', function (Builder $query) use ($uuid_user) {
                    $query->where('user_uuid', $uuid_user);
                })
                    ->orWhereHas('locations', function (Builder $query) use ($uuid_user) {
                        $query->where('user_uuid', $uuid_user);
                    })->count()
            ];
        }
        $uniqueArray = array_map('serialize', $array);
        $uniqueArray = array_unique($uniqueArray);
        $uniqueArray = array_map('unserialize', $uniqueArray);
        $users = $uniqueArray;
        $uuid_user = $uuid ?? @$cart[0]->content_owner_uuid;

        $cart = Cart::query()
            ->WhereHas('products', function (Builder $query) use ($uuid_user) {
                $query->where('user_uuid', $uuid_user);
            })
            ->orWhereHas('locations', function (Builder $query) use ($uuid_user) {
                $query->where('user_uuid', $uuid_user);
            })
            ->where('user_uuid', $user->uuid)
            ->get();
        $settings = Setting::query()->first();
        $commission = $cart->sum('commission');
        $price_with_day = $cart->sum('price_with_day');
        $multi_day_discounts = $cart->sum('multi_day_discounts');
        $data = [
            'commission' => $commission,
            'price_with_day' => $price_with_day,
            'multi_day_discounts' => $multi_day_discounts,
            'all' => $commission + $price_with_day + $multi_day_discounts
        ];

        $cart = \App\Http\Resources\CartContent::collection($cart);

        return mainResponse(true, 'done', compact('users', 'cart', 'data'), [], 200);

    }


    public function deteteCart($uuid)
    {
        Cart::destroy($uuid);
        return mainResponse(true, 'done', [], [], 200);
    }

    public function updateCart(Request $request, $uuid)
    {
        $cart = Cart::query()->find($uuid);
        $request;
        if ($cart) {
            $startDate = Carbon::parse($request->start);
            $endDate = Carbon::parse($request->end);
            $daysDifference = $endDate->diffInDays($startDate);
            $cart->update([
                'multi_day_discounts' => 3 * $daysDifference * -1,
                'price_with_day' => $cart->content->price * $daysDifference,
                'start' => $request->start,
                'end' => $request->end,
            ]);
            return mainResponse(true, 'ok', compact('cart'), []);

        } else {
            return mainResponse(false, 'cart not found', [], [], 101);

        }
    }

    public function getPagePay()
    {
        $user = Auth::guard('sanctum')->user();
       $Delivery_Addresses= DeliveryAddresses::query()->where('user_uuid',$user->uuid)->where('default',1)->select('address','uuid','country_uuid','city_uuid')->first();
       $Payment_Gateway=PaymentGateway::all();
        return mainResponse(true, 'ok', compact('Payment_Gateway', 'Delivery_Addresses'), []);

    }

    public function checkout(Request $request)
    {
        $rules = [
            'payment_method_id' => ['required',
                Rule::exists(PaymentGateway::class, 'id')->where(function ($q) {
                    $q->where('status', 1);
                })],
            'delivery_addresses_uuid' => 'required|exists:delivery_addresses,uuid',
            'content_type' => 'nullable|in:Product,Serving,Course',
            'content_uuid' => 'nullable',
            'user_uuid' => 'nullable|exists:users,uuid',
            'start' => 'nullable|date',
            'end' => 'nullable|date',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }


        $user = Auth::guard('sanctum')->user();


        if ($request->has('content_type') && $request->has('content_uuid')) {

            if ($request->content_type == "Product") {
                $content = Product::query()->find($request->content_uuid);
            } elseif ($request->content_type == "Course") {
                $content = Course::query()->find($request->content_uuid);
            } elseif ($request->content_type == "Serving") {
                $content = Serving::query()->find($request->content_uuid);
            }
            if (!$content) {
                return mainResponse(false, 'content not found', [], [], 101);
            }
            $settings = Setting::query()->first();

            $data = [
                'order_number' => Carbon::now()->timestamp . '' . rand(1000, 9999),
                'delivery' => 10,
                'commission' => $settings->commission,
                'user_uuid' => $user->uuid,
                'content_type' => $request->content_type,
                'delivery_addresses_uuid' => $request->delivery_addresses_uuid,
                'content_uuid' => $request->content_uuid,
            ];
            if ($request->content_type == 'Product') {
                $data['type'] = 'sale';
                $balnce = 10 + $content->price * $settings->commission;//10 delivery
            } elseif ($request->content_type == 'Serving' && $request->has('stary') && $request->has('end')) {
                $data_['start'] = $request->start;
                $data['end'] = $request->end;
                $balnce = $content->price * $settings->commission;
            } else {
                return mainResponse(false, 'Serving not found', [], [], 404);
            }
            $order = Order::create($data);

        } elseif ($request->has('user_uuid')) {
            $uuid = $request->user_uuid;
            $cart = Cart::query()
                ->WhereHas('products', function (Builder $query) use ($uuid) {
                    $query->where('user_uuid', $uuid);
                })
                ->orWhereHas('locations', function (Builder $query) use ($uuid) {
                    $query->where('user_uuid', $uuid);
                })
                ->where('user_uuid', $user->uuid)
                ->get();

            //check day





            $order_number = Carbon::now()->timestamp . '' . rand(1000, 9999);
            foreach ($cart as $item) {
                $order = Order::create([
                    'delivery_addresses_uuid' => $request->delivery_addresses_uuid,
                    'order_number' => $order_number,
                    'price_with_day' => $item->price_with_day,
                    'multi_day_discounts' => $item->multi_day_discounts,
                    'user_uuid' => $item->user_uuid,
                    'commission' => $item->commission,
                    'content_type' => $item->type,
                    'content_uuid' => $item->content_uuid,
                    'start' => $item->start,
                    'end' => $item->end,
                ]);
            }
            $commission = $cart->sum('commission');
            $price_with_day = $cart->sum('price_with_day');
            $multi_day_discounts = $cart->sum('multi_day_discounts');
            $balnce = intval($commission) + intval($price_with_day) + intval($multi_day_discounts);
        } else {
            return mainResponse(false, 'content not found', [], [], 101);
        }


        if ($request->payment_method_id == PaymentGateway::MADA) {
            $amount = $balnce;
            $url = "https://eu-test.oppwa.com/v1/checkouts";
            $data = "entityId=8a8294174b7ecb28014b9699220015ca" .
                "&amount=$amount" .
                "&currency=EUR" .
                "&paymentType=DB";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization:Bearer OGE4Mjk0MTc0YjdlY2IyODAxNGI5Njk5MjIwMDE1Y2N8c3k2S0pzVDg='));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $responseData = curl_exec($ch);
            if (curl_errno($ch)) {
                return curl_error($ch);
            }
            curl_close($ch);
            $data = json_decode($responseData);
            $id = $data->id;

        } elseif ($request->payment_method_id == PaymentGateway::ABLEPAY) {
            $amount = $balnce;
            $url = "https://eu-test.oppwa.com/v1/checkouts";
            $data = "entityId=8a8294174b7ecb28014b9699220015ca" .
                "&amount=$amount" .
                "&currency=EUR" .
                "&paymentType=DB";

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Authorization:Bearer OGE4Mjk0MTc0YjdlY2IyODAxNGI5Njk5MjIwMDE1Y2N8c3k2S0pzVDg='));
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $responseData = curl_exec($ch);
            if (curl_errno($ch)) {
                return curl_error($ch);
            }
            curl_close($ch);
            $data = json_decode($responseData);
            $id = $data->id;

        } else {
            return mainResponse(false, "payment_method not found", [], [], 404);

        }

        $payment = Payment::query()->updateOrCreate([
            'user_uuid' => $user->uuid,
            'price' => $balnce,
            'status' => Payment::PENDING,

        ], [
            'order_number' => $order->order_number,
            'transaction_id' => $id,
            'payment_method_id' => $request->payment_method_id,

        ]);

        $url = route('paymentGateways.checkout', $payment->uuid);
        $status = 'url';

        return mainResponse(true, 'ok', compact('status', 'url'), []);
    }
}
