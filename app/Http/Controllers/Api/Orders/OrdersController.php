<?php

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseMyResource;
use App\Http\Resources\CourseOrderResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\CourseTrakingResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderResourcer;
use App\Http\Resources\ProductHomeResource;
use App\Http\Resources\ServingOrderResource;
use App\Http\Resources\ServingTrakingResource;
use App\Http\Resources\UserOrderResource;
use App\Models\BookingDay;
use App\Models\Cart;
use App\Models\Course;
use App\Models\DeliveryAddresses;
use App\Models\Discount;
use App\Models\DiscountUser;
use App\Models\FcmToken;
use App\Models\Location;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\Order;
use App\Models\OrderStatus;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\Product;
use App\Models\Reviews;
use App\Models\Serving;
use App\Models\Setting;
use App\Models\User;


use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use DateTime;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class OrdersController extends Controller
{
    public function prepareCart(Request $request)
    {
        $rules = [
            'start' => 'required|date_format:"Y-m-d"|after:' . date('Y/m/d'),
            'end' => 'required|date_format:"Y-m-d"|after_or_equal:' . $request->start,
            'uuid' => 'required',
            'type' => 'required|in:product,location',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $startDate = Carbon::parse($request->start);
        $endDate = Carbon::parse($request->end);
        $daysDifference = $endDate->diffInDays($startDate);
        $daysDifference = ($daysDifference != 0) ? $daysDifference : 1;
        if ($request->type == 'product') {
            $content = Product::query()
                ->where('uuid', $request->uuid)
                ->where('type', 'rent')
                ->first();
            if (!$content) {
                return mainResponse(false, 'uuid not found', [], ['uuid not found'], 101);
            }
        }
        if ($request->type == 'location') {
            $content = Location::query()->find($request->uuid);
            if (!$content) {
                return mainResponse(false, 'uuid not found', [], ['uuid not found'], 101);
            }
        }
        $user = auth('sanctum')->user();


        $total = intval(number_format($content->price * $daysDifference, 0, '.', ''));
        $total += intval(number_format($content->price * (@$content->multiDayDiscount->rate / 100) * -1, 0, '.', ''));
        $total += intval(number_format($content->price * doubleval($user->commission), 0, '.', ''));

        $amount = number_format($content->price);
        $bill = [
            [
                'title' => $content->price . ' x ' . $daysDifference . __('days'),
                'amount' => number_format($content->price * $daysDifference)
            ],
            [
                'title' => __('Multi-day discounts'),
                'amount' => number_format($content->price * (@$content->multiDayDiscount->rate / 100) * -1)
            ],
            [
                'title' => __('commission'),
                'amount' => number_format($content->price * doubleval($user->commission))
            ],
            [
                'title' => __('total'),
                'amount' => number_format($total)
            ],
        ];

        $currency = __('sr');

        return mainResponse(true, 'done', compact('amount', 'bill', 'currency'), [], 200);
    }

    public function addCart(Request $request)
    {

        $rules = [
            'start' => 'required|date_format:"Y-m-d"|after:' . date('Y/m/d'),
            'end' => 'required|date_format:"Y-m-d"|after_or_equal:' . $request->start,
            'uuid' => 'required',
            'type' => 'required|in:product,location',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }

        if ($request->type == 'product') {
            $content = Product::query()
                ->where('uuid', $request->uuid)
                ->where('type', 'rent')
                ->first();
            if (!$content) {
                return mainResponse(false, 'uuid not found', [], ['uuid not found'], 101);
            }
        }
        if ($request->type == 'location') {
            $content = Location::query()->find($request->uuid);
            if (!$content) {
                return mainResponse(false, 'uuid not found', [], ['uuid not found'], 101);
            }
        }
        $user = Auth::guard('sanctum')->user();

        $request->merge([
            'multi_day_discounts' => $content->price * (@$content->multiDayDiscount->rate / 100) * -1,
            'delivery_uuid' => @$content->delivery->delivery,
            'commission' => $content->price * doubleval($user->commission),
            'price' => $content->price,
            'user_uuid' => $user->uuid,
            'content_uuid' => $content->uuid,
        ]);
        if (Cart::query()
            ->where('user_uuid', $user->uuid)
            ->where('content_uuid', $content->uuid)
            ->where('type', $request->type)
            ->exists()) {
            return mainResponse(false, 'this content in found', [], ['uuid not found'], 101);

        }
        Cart::query()->create($request->only('user_uuid', 'multi_day_discounts', 'delivery_uuid', 'price', 'commission', 'end', 'start', 'type', 'content_uuid'));
        return mainResponse(true, 'done', [], [], 200);

    }

    public function getCart(Request $request, $uuid = null)
    {
        $user = Auth::guard('sanctum')->user();
        $cart = Cart::query()->select('uuid', 'content_uuid', 'type')
            ->where('user_uuid', $user->uuid)
            ->get();
        $array = [];
        foreach ($cart as $item) {
            $user_uuid = $item->content_owner_uuid;
            $array[] = [
                'name' => $item->content_owner_name,
                'uuid' => $item->content_owner_uuid,
                'image' => $item->content_owner_image,
                'count' => Cart::query()
                    ->where(function (Builder $q) use ($user_uuid) {
                        $q->whereHas('products', function (Builder $query) use ($user_uuid) {
                            $query->where('user_uuid', $user_uuid);
                        })->orWhereHas('locations', function (Builder $query) use ($user_uuid) {
                            $query->where('user_uuid', $user_uuid);
                        });
                    })
                    ->where('user_uuid', $user->uuid)
                    ->count()
            ];
        }
        $uniqueArray = array_map('serialize', $array);
        $uniqueArray = array_unique($uniqueArray);
        $uniqueArray = array_map('unserialize', $uniqueArray);
        $users = $uniqueArray;
        $user_uuid = $uuid ?? @$cart[0]->content_owner_uuid;


        $cart = Cart::query()
            ->where(function (Builder $q) use ($user_uuid) {
                $q->whereHas('products', function (Builder $query) use ($user_uuid) {
                    $query->where('user_uuid', $user_uuid);
                })->orWhereHas('locations', function (Builder $query) use ($user_uuid) {
                    $query->where('user_uuid', $user_uuid);
                });
            })
            ->where('user_uuid', $user->uuid)
            ->get();


        $discount_amount = 0;
        $discount = null;
        if ($request->code) {
            $discount = Discount::query()
                ->where('code', $request->code)->first();
            if (!$discount) {
                return mainResponse(false, __('Code not found'), [], []);
            }
            $count = DiscountUser::query()
                ->where('user_uuid', $user->uuid)
                ->where('discount_uuid', $discount->uuid)
                ->count();

            if ($discount->number_uses >= $discount->number_of_usage) {
                return mainResponse(false, __('has expired'), [], []);
            }
            if ($count >= $discount->number_of_usage_for_user) {
                return mainResponse(false, __('has expired'), [], []);
            }
            if (DiscountUser::query()
                ->where('user_uuid', $user->uuid)
                ->where('discount_uuid', $discount->uuid)
                ->where('owner_user_uuid', $user_uuid)
                ->exists()) {
                return mainResponse(false, __('is already'), [], []);
            }
            DiscountUser::query()->create([
                'user_uuid' => $user->uuid,
                'discount_uuid' => $discount->uuid,
                'owner_user_uuid' => $user_uuid,
            ]);
            Cart::query()
                ->where(function (Builder $q) use ($user_uuid) {
                    $q->whereHas('products', function (Builder $query) use ($user_uuid) {
                        $query->where('user_uuid', $user_uuid);
                    })->orWhereHas('locations', function (Builder $query) use ($user_uuid) {
                        $query->where('user_uuid', $user_uuid);
                    });
                })
                ->where('user_uuid', $user->uuid)
                ->update([
                    'discount_uuid' => $discount->uuid,
                ]);

        }
        $bill = [];
        $multi_day_discounts = 0;
        $sub_total = 0;
        foreach ($cart as $item) {
            $bill[] = [
                'title' => $item->price . ' x ' . $item->days_count . __('days'),
                'amount' => number_format($item->price * $item->days_count),
            ];
            $multi_day_discounts += $item->multi_day_discounts;
            $sub_total += $item->price * $item->days_count;

        }

        $bill[] = [
            'title' => __('Multi-day discounts'),
            'amount' => $multi_day_discounts * -1
        ];
        $discount = Cart::query()
            ->where(function (Builder $q) use ($user_uuid) {
                $q->whereHas('products', function (Builder $query) use ($user_uuid) {
                    $query->where('user_uuid', $user_uuid);
                })->orWhereHas('locations', function (Builder $query) use ($user_uuid) {
                    $query->where('user_uuid', $user_uuid);
                });
            })
            ->where('user_uuid', $user->uuid)
            ->whereNotNull('discount_uuid')
            ->first();
        if ($discount) {
            $discount = Discount::query()
                ->where('code', $request->code)
                ->orWhere('uuid', $discount->discount->uuid)
                ->first();
            if ($discount->discount_type == Discount::FIXED_PRICE) {
                $discount_amount = $discount->discount;
            } else {
                $discount_amount = $sub_total * ($discount->discount / 100);
            }
            $uuid = DiscountUser::query()->where('user_uuid', $user->uuid)->where('discount_uuid', $discount->uuid)->value('uuid');
            $bill[] = [
                'title' => __('discounts'),
                'amount' => strval($discount_amount * -1),
                'uuid' => $uuid
            ];
            $sub_total -= $discount_amount;
        }

        $commission = number_format($sub_total * $user->commission);
        $bill[] = [
            'title' => __('commission'),
            'amount' => $commission
        ];

        $bill[] = [
            'title' => __('total'),
            'amount' => number_format($sub_total + intval($commission) - $multi_day_discounts)
        ];

        $cart = \App\Http\Resources\CartContent::collection($cart);
        $currency = __('sr');

        $promo_code_index = -1;
        if ($discount) {
            $promo_code_index = count($bill) - 3;
        }

        return mainResponse(true, 'done', compact('users', 'cart', 'bill', 'currency', 'promo_code_index'), [], 200);

    }

    public function deteteCart($uuid)
    {
        Cart::query()
            ->where('user_uuid', \auth('sanctum')->id())
            ->where('uuid', $uuid)
            ->delete();
        return mainResponse(true, 'done', [], [], 200);
    }

    public function editCart($uuid)
    {
        $cart = Cart::query()->findOrFail($uuid);
        $item = [
            'start' => $cart->start,
            'end' => $cart->end,
        ];
        $bill = [];
        $multi_day_discounts = 0;
        $sub_total = 0;

        $bill[] = [
            'title' => $cart->price . ' x ' . $cart->days_count . __('days'),
            'amount' => number_format($cart->price * $cart->days_count),
        ];
        $multi_day_discounts += $cart->multi_day_discounts;
        $sub_total .= number_format($cart->price * $cart->days_count, 0, '.', '');


        $bill[] = [
            'title' => __('Multi-day discounts'),
            'amount' => number_format($multi_day_discounts * -1, 0, '.', '')
        ];


//        $bill[] = [
//            'title' => __('commission'),
//            'amount' => strval($cart->commission)
//        ];

        $bill[] = [
            'title' => __('total'),
            'amount' => strval($sub_total - $multi_day_discounts)
        ];
        $currency = __('sr');

        return mainResponse(true, 'ok', compact('item', 'bill', 'currency'), []);

    }

    public function updateCart(Request $request, $uuid)
    {
        $rules = [
            'start' => 'required|date_format:"Y-m-d"|after:' . date('Y/m/d'),
            'end' => 'required|date_format:"Y-m-d"|after_or_equal:' . $request->start,
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }

        $cart = Cart::query()->find($uuid);

        if ($cart) {
            $startDate = Carbon::parse($request->start);
            $endDate = Carbon::parse($request->end);
            $daysDifference = $endDate->diffInDays($startDate);
            if ($cart->content->multiDayDiscount->minimal_day ?? 100000 <= $daysDifference) {
                $multiDayDiscount = $cart->price * ($cart->content->multiDayDiscount->rate / 100);
            }
            $cart->update([
                'multi_day_discounts' => $multiDayDiscount ?? 0,
                'start' => $request->start,
                'end' => $request->end,
            ]);
            return mainResponse(true, 'ok', [], []);

        } else {
            return mainResponse(false, 'cart not found', [], [], 101);

        }
    }

    public function pledge($user_uuid)
    {
        $user = Auth::guard('sanctum')->user();
        $between = [
            UserOrderResource::make($user),
            UserOrderResource::make(User::query()->findOrFail($user_uuid))
        ];
        $cart = Cart::query()
            ->where(function (Builder $q) use ($user_uuid) {
                $q->whereHas('products', function (Builder $query) use ($user_uuid) {
                    $query->where('user_uuid', $user_uuid);
                })->orWhereHas('locations', function (Builder $query) use ($user_uuid) {
                    $query->where('user_uuid', $user_uuid);
                });
            })
            ->where('user_uuid', $user->uuid)
            ->get();
        $rentalDates = [];

        foreach ($cart as $item) {
            $rentalDates[] = [
                'count' => $item->days_count,
                'start' => $item->start,
                'end' => $item->end,
            ];
        }

        return mainResponse(true, 'ok', compact('between', 'rentalDates'), []);


    }

    public function getPagePayRent(Request $request)
    {
        $user_uuid = $request->user_uuid;
        $content_uuid = $request->content_uuid;

        $user = Auth::guard('sanctum')->user();
        $delivery_addresses = DeliveryAddresses::query()->where('user_uuid', $user->uuid)->where('default', 1)->select('address', 'uuid', 'country_uuid', 'city_uuid')->first();
        $payment_methods = PaymentGateway::all();

        if ($request->has('user_uuid')) {
            if (User::query()->where('uuid', $user_uuid)->doesntExist()) {
                return mainResponse(false, 'user not found', [], [], 101);
            }
            $cart = Cart::query()
                ->where(function (Builder $q) use ($user_uuid) {
                    $q->whereHas('products', function (Builder $query) use ($user_uuid) {
                        $query->where('user_uuid', $user_uuid);
                    })->orWhereHas('locations', function (Builder $query) use ($user_uuid) {
                        $query->where('user_uuid', $user_uuid);
                    });
                })
                ->where('user_uuid', $user->uuid)
                ->get();
            if ($cart->isEmpty()) {
                return mainResponse(false, 'cart empty', [], [], 101);
            }

            $user_owner = User::query()->findOrFail($user_uuid);
            $user_owner = [
                'image' => $user_owner->image,
                'count' => $cart->count(),
                'name' => $user_owner->name,
            ];

            $bill = [];
            $multi_day_discounts = 0;
            $sub_total = 0;

            foreach ($cart as $item) {

                $multi_day_discounts += $item->multi_day_discounts;
                $sub_total += number_format($item->price * $item->days_count, 0, '.', '');
            }

            $bill[] = [
                'title' => __('products') . ',' . __('locations'),
                'amount' => number_format($sub_total),
            ];
            $bill[] = [
                'title' => __('Multi-day discounts'),
                'amount' => number_format($multi_day_discounts * -1, 0, '.', '')
            ];

            $discount = Cart::query()
                ->where(function (Builder $q) use ($user_uuid) {
                    $q->whereHas('products', function (Builder $query) use ($user_uuid) {
                        $query->where('user_uuid', $user_uuid);
                    })->orWhereHas('locations', function (Builder $query) use ($user_uuid) {
                        $query->where('user_uuid', $user_uuid);
                    });
                })
                ->where('user_uuid', \auth('sanctum')->id())
                ->whereNotNull('discount_uuid')
                ->first();

            if ($discount) {
                if ($discount->discount->discount_type == Discount::FIXED_PRICE) {
                    $discount_amount = $discount->discount->discount;
                } else {
                    $discount_amount = $sub_total * ($discount->discount->discount / 100);
                }

                $bill[] = [
                    'title' => __('discounts'),
                    'amount' => number_format($discount_amount * -1)
                ];
                $sub_total -= $discount_amount;
            }


            $commission = number_format($sub_total * $user->commission);
            $bill[] = [
                'title' => __('commission'),
                'amount' => $commission
            ];

            $bill[] = [
                'title' => __('total'),
                'amount' => number_format($sub_total + intval($commission) - $multi_day_discounts)
            ];

            $currency = __('sr');

            return mainResponse(true, 'ok', compact('payment_methods', 'delivery_addresses', 'bill', 'user_owner', 'currency'), []);

        } else {
            return mainResponse(false, 'user not found', [], [], 101);

        }


    }

    public function getPagePaySale(Request $request)
    {
        $content_uuid = $request->content_uuid;

        $user = Auth::guard('sanctum')->user();
        $payment_methods = PaymentGateway::all();
        if ($request->has('content_uuid') && $request->has('content_type')) {
            $bill = [];

            if ($request->content_type == "product") {
                $delivery_addresses = DeliveryAddresses::query()->where('user_uuid', $user->uuid)->where('default', 1)->select('address', 'uuid', 'country_uuid', 'city_uuid')->first();

                $content = Product::query()->findOrFail($content_uuid);
                $bill[] = [
                    'title' => __('products'),
                    'amount' => number_format($content->price, 0, '.', '')
                ];
                $sub_total = $content->price;
                $item = [
                    'name' => $content->name,
                    'image' => $content->image,
                    'category_name' => $content->category_name,
                    'price' => $content->price,
                ];

            } elseif ($request->content_type == "course") {
                $content = Course::query()->findOrFail($request->content_uuid);
                $bill[] = [
                    'title' => __('courses'),
                    'amount' => number_format($content->price, 0, '.', '')
                ];
                $sub_total = $content->price;

                $user_owner = [
                    'name' => $content->user->name,
                    'image' => $content->user->image,
                ];
                $item = [
                    'name' => $content->name,
                    'image' => $content->image,
                    'price' => $content->price,
                ];
            } elseif ($request->content_type == "service") {

                $content = Serving::query()->findOrFail($request->content_uuid);
                $bill[] = [
                    'title' => __('service'),
                    'amount' => number_format($content->price, 0, '.', '')
                ];
                $sub_total = $content->price;
                $startDate = Carbon::parse($content->from);
                $endDate = Carbon::parse($content->to);
                $daysDifference = $endDate->diffInDays($startDate);
//                $invoice = [
//                    'commission' => $settings->commission,
//                    'price' => $content->price,
//                    'all' => $settings->commission + 10 + $content->price
//                ];
                $user_owner = [
                    'name' => $content->user_name,
                    'image' => $content->user->image,
                ];
                $item = [
                    'name' => $content->name,
                    'start' => $content->from,
                    'end' => $content->to,
                    'price' => $content->price,
                    'count' => $daysDifference,
                    'currency' => __('sr')
                ];
            } else {
                return mainResponse(false, 'type must product,service,course', [], [], 101);
            }
            $discount = 0;
            if ($request->code) {
                $discount = Discount::query()
                    ->where('code', $request->code)
                    ->whereDate('date_from', '<', date('y-m-d'))
                    ->whereDate('date_to', '>', date('y-m-d'))
                    ->first();
                if (!$discount) {
                    return mainResponse(false, __('Code not found'), [], []);
                }
                $count = DiscountUser::query()
                    ->where('user_uuid', $user->uuid)
                    ->where('discount_uuid', $discount->uuid)
                    ->count();

                if ($discount->number_uses >= $discount->number_of_usage) {
                    return mainResponse(false, __('has expired'), [], []);
                }
                if ($count >= $discount->number_of_usage_for_user) {
                    return mainResponse(false, __('has expired'), [], []);
                }
                DiscountUser::query()->create([
                    'user_uuid' => $user->uuid,
                    'discount_uuid' => $discount->uuid,
                    'owner_user_uuid' => $content->user->uuid,
                ]);
            }
            if ($discount) {
                if ($discount->discount_type == Discount::FIXED_PRICE) {
                    $discount_amount = $discount->discount;
                } else {
                    $discount_amount = $sub_total * ($discount->discount / 100);
                }

                $bill[] = [
                    'title' => __('discounts'),
                    'amount' => number_format($discount_amount * -1)
                ];
                $sub_total -= $discount_amount;
            }

            $commission = number_format($sub_total * $content->user->commission);
            $bill[] = [
                'title' => __('commission'),
                'amount' => $commission
            ];

            $bill[] = [
                'title' => __('total'),
                'amount' => number_format($sub_total + intval($commission))
            ];
            if ($request->content_type == "product") {
                return mainResponse(true, 'ok', compact('payment_methods', 'delivery_addresses', 'item', 'bill'), []);

            } else {
                return mainResponse(true, 'ok', compact('payment_methods', 'user_owner', 'item', 'bill'), []);

            }
        } else {
            return mainResponse(false, 'type not found', [], [], 101);
        }


    }

    public function deleteDiscount($uuid, $user_uuid)
    {
        Cart::query()
            ->where(function (Builder $q) use ($user_uuid) {
                $q->whereHas('products', function (Builder $query) use ($user_uuid) {
                    $query->where('user_uuid', $user_uuid);
                })->orWhereHas('locations', function (Builder $query) use ($user_uuid) {
                    $query->where('user_uuid', $user_uuid);
                });
            })
            ->where('user_uuid', \auth('sanctum')->id())
            ->update([
                'discount_uuid' => null
            ]);
        DiscountUser::query()->where('user_uuid', \auth('sanctum')->id())->where('uuid', $uuid)->delete();
        return mainResponse(true, 'done', [], [], 200);

    }

    public function checkout(Request $request)
    {
        $rules = [
            'payment_method_id' => ['required',
                Rule::exists(PaymentGateway::class, 'id')->where(function ($q) {
                    $q->where('status', 1);
                })],
//            'delivery_addresses_uuid' => 'nullable|exists:delivery_addresses,uuid',
            'content_type' => 'nullable|in:product,service,course',
            'content_uuid' => 'nullable',
            'user_uuid' => 'nullable|exists:users,uuid',
            'start' => 'nullable|date_format:"Y-m-d"|after:' . date('Y/m/d'),
            'end' => 'nullable|date_format:"Y-m-d"|after_or_equal:' . $request->start,
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }

        $user = Auth::guard('sanctum')->user();


        if ($request->has('content_type') && $request->has('content_uuid')) {

            if ($request->content_type == "product") {
                $content = Product::query()->find($request->content_uuid);
                $delivery_addresses_uuid = DeliveryAddresses::query()
                    ->where('user_uuid', $user->uuid)
                    ->where('default', 1)
                    ->value('uuid');
            } elseif ($request->content_type == "course") {
                $content = Course::query()->find($request->content_uuid);
            } elseif ($request->content_type == "service") {
                $content = Serving::query()->find($request->content_uuid);
            }
            if (!$content) {
                return mainResponse(false, 'content not found', [], [], 101);
            }
            $commission = number_format($content->price * $user->commission);
            $balance = $content->price + $commission;

            $data = [
                'order_number' => Carbon::now()->timestamp . '' . rand(1000, 9999),
                'commission' => $user->commission,
                'user_uuid' => $user->uuid,
                'content_type' => $request->content_type,
                'delivery_addresses_uuid' => @$delivery_addresses_uuid,
                'content_uuid' => $request->content_uuid,
                'price' => $content->price,
                'payment_method_id' => $request->payment_method_id,
            ];
            if ($request->content_type == 'product') {
                $data['type'] = 'sale';
                $data['delivery'] = 10;
                $balance = 10 + $balance;//10 delivery
            } elseif ($request->content_type == 'service' && $request->has('start') && $request->has('end')) {
                $data['start'] = $request->start;
                $data['end'] = $request->end;


            } elseif ($request->content_type == 'course') {
                unset($data['delivery_addresses_uuid']);
            } else {
                return mainResponse(false, 'content not found', [], [], 404);
            }
            $order = Order::create($data);

        } elseif ($request->has('user_uuid')) {
            $uuid = $request->user_uuid;
            $cart = Cart::query()
                ->where(function (Builder $q) use ($uuid) {
                    $q->whereHas('products', function (Builder $query) use ($uuid) {
                        $query->where('user_uuid', $uuid);
                    })->orWhereHas('locations', function (Builder $query) use ($uuid) {
                        $query->where('user_uuid', $uuid);
                    });
                })
                ->where('user_uuid', $user->uuid)
                ->get();
            if ($cart->isEmpty()) {
                return mainResponse(false, 'cart not found', [], [], 101);
            }
            //check day
            $err = [];
            foreach ($cart as $item) {
                $check = BookingDay::query()
                    ->where('content_uuid', $item->content_uuid)
                    ->where(function ($query) use ($item) {
                        $query->where('date', $item->start)
                            ->orWhere('date', $item->end);
                    })
                    ->exists();
                if ($check) {
                    $err[] = 'محجوز في هذا التاريخ' . @$item->location->name ?? @$item->product->name;
                }
            }
            if (!empty($err)) {
                return mainResponse(false, 'محجوز من قبل', [], $err, 101);
            }

            $order_number = Carbon::now()->timestamp . '' . rand(1000, 9999);
            $multi_day_discounts = 0;
            $sub_total = 0;

            foreach ($cart as $item) {
                $order = Order::create([
                    'delivery_addresses_uuid' => $request->delivery_addresses_uuid,
                    'order_number' => $order_number,
                    'price' => $item->price,
                    'multi_day_discounts' => $item->multi_day_discounts,
                    'discount_uuid' => $item->discount_uuid,
                    'payment_method_id' => $request->payment_method_id,
                    'user_uuid' => $item->user_uuid,
                    'commission' => $item->commission,
                    'content_type' => $item->type,
                    'content_uuid' => $item->content_uuid,
                    'start' => $item->start,
                    'end' => $item->end,
                ]);

                $multi_day_discounts += $item->multi_day_discounts;
                $sub_total += $item->price * $item->days_count;

            }
//            $discount = Discount::query()
//                ->where('code', $request->code)
//                ->orWhere('uuid', $discount->discount->uuid)
//                ->first();
            @$cart[0]->discount->discount;
            if (@$cart[0]->discount->discount_type == Discount::FIXED_PRICE) {
                $discount_amount = $cart[0]->discount->discount;
            } else {
                $discount_amount = $sub_total * (@$cart[0]->discount->discount / 100);
            }
            $sub_total -= $discount_amount;

            $commission = number_format($sub_total * $user->commission);
            $price_with_day = $sub_total;
//            number_format($sub_total + $commission - $multi_day_discounts);
            $balance = $sub_total + $commission - $multi_day_discounts;
        } else {
            return mainResponse(false, 'content not found', [], [], 101);
        }

        if ($request->payment_method_id == PaymentGateway::MADA) {
            $amount = $balance;
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
//            return $data;
            $id = $data->id;

        } elseif ($request->payment_method_id == PaymentGateway::ABLEPAY) {
            $amount = $balance;
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

        } elseif ($request->payment_method_id == PaymentGateway::VISA) {
            $amount = $balance;
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
            'price' => $balance,
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

    public function ordersBuyer($type)
    {
        $user = Auth::guard('sanctum')->user();
        if ($type == 'product') {
            $orders = Order::query()
                ->where('user_uuid', $user->uuid)
                ->where('content_type', 'product')
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->created_at)
                        ->format('Y-m-d');
                });

        } elseif ($type == 'location') {


            $orders = Order::query()
                ->where('user_uuid', $user->uuid)
                ->where('content_type', 'location')
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->created_at)->format('Y-m-d');
                });
        } elseif ($type == 'service') {


            $orders = Order::query()
                ->where('user_uuid', $user->uuid)
                ->where('content_type', 'service')
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->created_at)
                        ->format('Y-m-d');
                });

            $orders = paginateOrder($orders);
            $items = $orders->getCollection();
            $data = [];
            foreach ($items as $key => $order) {
                $data[] = [
                    'day' => $key,
                    'items' => ServingOrderResource::collection($order),
                ];
            }
            $orders->setCollection(collect($data));
            $items = $orders;
            return mainResponse(true, 'ok', compact('items'), []);
        } elseif ($type == 'course') {


            $orders = Order::query()
                ->where('user_uuid', $user->uuid)
                ->where('content_type', 'course')
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->created_at)
                        ->format('Y-m-d');
                });
            $orders = paginateOrder($orders);
            $items = $orders->getCollection();
            $data = [];
            foreach ($items as $key => $order) {
                $data[] = [
                    'day' => $key,
                    'items' => CourseOrderResource::collection($order),
                ];
            }
            $orders->setCollection(collect($data));
            $items = $orders;
            return mainResponse(true, 'ok', compact('items'), []);
        } else {
            return mainResponse(false, 'type not found', [], [], 403);

        }

        $orders = paginateOrder($orders);
        $items = $orders->getCollection();
        $data = [];
        foreach ($items as $key => $order) {
            $data[] = [
                'day' => $key,
                'items' => OrderResource::collection($order),
            ];
        }
        $orders->setCollection(collect($data));
        $items = $orders;
        return mainResponse(true, 'ok', compact('items'), []);

    }

    public function ordersOwner(Request $request, $type)
    {
        $request->merge([
            'owner' => true
        ]);
        $user = Auth::guard('sanctum')->user();
        $user_uuid = $user->uuid;
        if ($type == 'product') {
            $orders = Order::query()
                ->WhereHas('product', function (Builder $query) use ($user_uuid) {
                    $query->where('user_uuid', $user_uuid);
                })
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->created_at)
                        ->format('Y-m-d');
                });
        } elseif ($type == 'location') {


            $orders = Order::query()
                ->WhereHas('location', function (Builder $query) use ($user_uuid) {
                    $query->where('user_uuid', $user_uuid);
                })
                ->get()->groupBy(function ($item) {
                    return Carbon::parse($item->created_at)->format('Y-m-d');
                });

        } elseif ($type == 'service') {


            $orders = Order::query()
                ->WhereHas('service', function (Builder $query) use ($user_uuid) {
                    $query->where('user_uuid', $user_uuid);
                })
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->created_at)
                        ->format('Y-m-d');
                });

            $orders = paginateOrder($orders);
            $items = $orders->getCollection();
            $data = [];
            foreach ($items as $key => $order) {
                $data[] = [
                    'day' => $key,
                    'items' => ServingOrderResource::collection($order),
                ];
            }
            $orders->setCollection(collect($data));
            $items = $orders;
            return mainResponse(true, 'ok', compact('items'), []);
        } elseif ($type == 'course') {


            $orders = Order::query()
                ->WhereHas('course', function (Builder $query) use ($user_uuid) {
                    $query->where('user_uuid', $user_uuid);
                })
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->created_at)
                        ->format('Y-m-d');
                });
            $orders = paginateOrder($orders);
            $items = $orders->getCollection();
            $data = [];
            foreach ($items as $key => $order) {
                $data[] = [
                    'day' => $key,
                    'items' => CourseOrderResource::collection($order),
                ];
            }
            $orders->setCollection(collect($data));
            $items = $orders;
            return mainResponse(true, 'ok', compact('items'), []);
        } else {
            return mainResponse(false, 'type not found', [], [], 403);

        }

        $orders = paginateOrder($orders);
        $items = $orders->getCollection();
        $data = [];
        foreach ($items as $key => $order) {
            $data[] = [
                'day' => $key,
                'items' => OrderResource::collection($order),
            ];
        }
        $orders->setCollection(collect($data));
        $items = $orders;
        return mainResponse(true, 'ok', compact('items'), []);

    }

    public function AddReviews(Request $request)
    {

        $rules = [
            'title' => 'required|string',
            'content_uuid' => 'required',
            'content_type' => 'required|in:product,service,course,location',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        if ($request->content_type == "product") {
            $content = Product::query()->findOrFail($request->content_uuid);
        } elseif ($request->content_type == "course") {
            $content = Course::query()->findOrFail($request->content_uuid);
        } elseif ($request->content_type == "service") {
            $content = Serving::query()->findOrFail($request->content_uuid);
        } elseif ($request->content_type == "location") {
            $content = Location::query()->findOrFail($request->content_uuid);
        } else {
            return mainResponse(false, 'content not found', [], [], 101);
        }
        $user = Auth::guard('sanctum')->user();
        $item = Reviews::query()->create([
            'title' => $request->title,
            'content_uuid' => $request->content_uuid,
            'user_uuid' => $user->uuid,
            'reference_uuid' => $content->user_uuid,
        ]);
        return mainResponse(true, 'ok', [], []);

    }

    public function orderTrackingSale(Request $request)
    {
        $rules = [
            'order_uuid' => 'required|exists:orders,uuid',
            'type' => 'required|in:seller,buyer',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $orders = Order::query()->findOrFail($request->order_uuid);
        $status = $orders->status;
        $request->merge([
            'product' => true
        ]);
        $product = ProductHomeResource::make($orders->product);

        $delivery_addresses = $orders->deliveryAddresses()->select('address', 'uuid', 'country_uuid', 'city_uuid')->first();
        $payment_method = $orders->paymentMethod;
        $price = $orders->price;
        $commission = $orders->price * $orders->commission;
        $bill = [
            [
                'title' => __('product'),
                'amount' => number_format($orders->price, 0, '.', '')
            ],
            [
                'title' => __('commission'),
                'amount' => number_format($commission, 0, '.', '')
            ],
        ];
        if ($orders->discount) {
            $bill[] = [
                'title' => __('discount'),
                'amount' => number_format($orders->discount, 0, '.', '')
            ];
        }
        if ($orders->delivery) {
            $bill[] = [
                'title' => __('delivery'),
                'amount' => number_format($orders->delivery, 0, '.', '')
            ];
        }
        $bill[] = [
            'title' => __('all'),
            'amount' => number_format($orders->price + $orders->delivery - $orders->discount + $commission, 0, '.', '')
        ];

        if ($request->Buyer) {
            $user = UserOrderResource::make($orders->user);
        } else {
            $user = UserOrderResource::make($orders->product->user);
        }
        return mainResponse(true, 'ok', compact('status', 'product', 'user', 'delivery_addresses', 'payment_method', 'bill'), []);


    }

    public function orderTrackingService(Request $request)
    {
        $rules = [
            'order_uuid' => 'required|exists:orders,uuid',
            'type' => 'required|in:seller,buyer',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $orders = Order::query()->findOrFail($request->order_uuid);
        $status = $orders->status;

        $serving = ServingTrakingResource::make($orders->content);

        $payment_method = $orders->paymentMethod;
        $rentalDates = [
            'count' => $orders->days_count,
            'start' => $orders->start,
            'end' => $orders->end,
        ];
        $sub_total = $orders->price * $orders->days_count;
        $commission = $orders->commission;
        $bill = [
            [
                'title' => $orders->price . ' x ' . $orders->days_count . __('days'),
                'amount' => number_format($orders->price * $orders->days_count, 0, '.', '')
            ],
            [
                'title' => __('commission'),
                'amount' => number_format($commission, 0, '.', '')
            ]
        ];

        $bill[] = [
            'title' => __('all'),
            'amount' => number_format($sub_total + $orders->delivery - $orders->discount + $commission, 0, '.', '')
        ];
        if ($request->type == "buyer") {

            $user = UserOrderResource::make($orders->user);
        } else {
            $user = UserOrderResource::make($orders->product->user);
        }
        return mainResponse(true, 'ok', compact('status', 'serving', 'user', 'payment_method', 'rentalDates', 'bill'), []);


    }

    public function orderTrackingRent(Request $request)
    {
        $rules = [
            'order_uuid' => 'required|exists:orders,uuid',
            'type' => 'required|in:seller,buyer',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $orders = Order::query()->findOrFail($request->order_uuid);
        $status = $orders->status;
        if ($orders->type == 'location') {
            $request->merge([
                'product' => true
            ]);
        }
        $product = ProductHomeResource::make($orders->content);

        $payment_method = $orders->paymentMethod;
        $rentalDates = [
            'count' => $orders->days_count,
            'start' => $orders->start,
            'end' => $orders->end,
        ];
        $sub_total = $orders->price * $orders->days_count;
        $commission = $orders->commission;

        if ($orders->content_type == Order::PRODUCT) {
            $delivery_addresses = $orders->deliveryAddresses()->select('address', 'uuid', 'country_uuid', 'city_uuid')->first();
        }
        $bill = [
            [
                'title' => $orders->price . ' x ' . $orders->days_count . __('days'),
                'amount' => number_format($orders->price * $orders->days_count, 0, '.', '')
            ],
            [
                'title' => __('commission'),
                'amount' => number_format($commission, 0, '.', '')
            ]
        ];


        if ($orders->discount) {
            $bill[] = [
                'title' => __('discount'),
                'amount' => number_format($orders->discount, 0, '.', '')
            ];
        }
        if ($orders->delivery) {
            $bill[] = [
                'title' => __('delivery'),
                'amount' => number_format($orders->delivery, 0, '.', '')
            ];
        }
        $bill[] = [
            'title' => __('all'),
            'amount' => number_format($sub_total + $orders->delivery - $orders->discount + $commission, 0, '.', '')
        ];
        if ($request->type == "buyer") {

            $user = UserOrderResource::make($orders->user);
        } else {
            $user = UserOrderResource::make($orders->product->user);
        }
        return mainResponse(true, 'ok', compact('status', 'product', 'user', 'payment_method', 'rentalDates', 'bill'), []);


    }

    public function orderTrackingCourse(Request $request)
    {
        $rules = [
            'order_uuid' => 'required|exists:orders,uuid',
            'type' => 'required|in:seller,buyer',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $orders = Order::query()->findOrFail($request->order_uuid);
        $status = $orders->status;

        $course = CourseTrakingResource::make($orders->course);
        $payment_method = $orders->paymentMethod;
        $price = $orders->price;
        $commission = $orders->price * $orders->commission;
        $bill = [
            [
                'title' => __('course'),
                'amount' => number_format($orders->price, 0, '.', '')
            ],
            [
                'title' => __('commission'),
                'amount' => number_format($commission, 0, '.', '')
            ],
        ];
        if ($orders->discount) {
            $bill[] = [
                'title' => __('discount'),
                'amount' => number_format($orders->discount, 0, '.', '')
            ];
        }
        if ($orders->delivery) {
            $bill[] = [
                'title' => __('delivery'),
                'amount' => number_format($orders->delivery, 0, '.', '')
            ];
        }
        $bill[] = [
            'title' => __('all'),
            'amount' => number_format($orders->price + $orders->delivery - $orders->discount + $commission, 0, '.', '')
        ];

        if ($request->Buyer) {
            $user = UserOrderResource::make($orders->user);
        } else {
            $user = UserOrderResource::make($orders->course->user);
        }
        return mainResponse(true, 'ok', compact('status', 'course', 'user', 'payment_method', 'bill'), []);


    }

    public function acceptStatusOrder($uuid)
    {

        $order = Order::query()->findOrFail($uuid);
        if ($order->product->user_uuid == Auth::id()) {
            $order->update([
                'status' => Order::ACCEPT
            ]);
            OrderStatus::query()->updateOrCreate([
                'order_uuid' => $order->uuid,
            ], [
                'status' => Order::ACCEPT

            ]);
            $ios_tokens = FcmToken::query()
                ->where("user_uuid", $order->user_uuid)
                ->where('fcm_tokens', 'ios')
                ->pluck('fcm_token')->toArray();
            $android_tokens = FcmToken::query()
                ->where("user_uuid", $order->user_uuid)
                ->where('fcm_tokens', 'android')
                ->pluck('fcm_token')->toArray();
            $msg = [$order->product->name . __('purchase order accepted')];
            NotificationUser::query()->create([
                'receiver_uuid' => $order->user_uuid,
                'sender_uuid' => $order->product->user_uuid,
                'content' => $order->product->name . __('purchase order accepted'),
                'type' => ('purchase_order_accepted')
            ]);
            if ($ios_tokens) {
                sendFCM($msg, $ios_tokens, "ios");
            }
            if ($android_tokens) {
                sendFCM($msg, $android_tokens, "android");
            }
        } else {
            return mainResponse(false, 'err', [], [], 403);

        }

        return mainResponse(true, 'ok', [], []);

    }

    public function receiveStatusOrder($uuid)
    {
        $order = Order::query()->findOrFail($uuid);
        if ($order->user_uuid == Auth::id()) {
            $order->update([
                'status' => Order::COMPLETE
            ]);
            OrderStatus::query()->updateOrCreate([
                'order_uuid' => $order->uuid,
            ], [
                'status' => Order::COMPLETE

            ]);
        } else {
            return mainResponse(false, 'err', [], [], 403);

        }

        return mainResponse(true, 'ok', [], []);

    }
}

