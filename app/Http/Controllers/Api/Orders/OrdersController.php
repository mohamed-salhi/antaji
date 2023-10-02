<?php

namespace App\Http\Controllers\Api\Orders;

use App\Events\Conversation;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseMyResource;
use App\Http\Resources\CourseOrderResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\CourseTrakingResource;
use App\Http\Resources\MyLocationResource;
use App\Http\Resources\OrderResource;
use App\Http\Resources\OrderResourcer;
use App\Http\Resources\ProductHomeResource;
use App\Http\Resources\ReviewResource;
use App\Http\Resources\ServingOrderResource;
use App\Http\Resources\ServingTrakingResource;
use App\Http\Resources\UserOrderResource;
use App\Models\BillService;
use App\Models\BookingDay;
use App\Models\Cart;
use App\Models\Category;
use App\Models\Course;
use App\Models\Delivery;
use App\Models\DeliveryAddresses;
use App\Models\Discount;
use App\Models\DiscountUser;
use App\Models\FcmToken;
use App\Models\Location;
use App\Models\MultiDayDiscount;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\Order;
use App\Models\OrderConversation;
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
use Yajra\DataTables\Tests\Http\Resources\UserResource;

class OrdersController extends Controller
{
    public function prepareCart(Request $request)
    {
        $rules = [
            'start' => 'required|date_format:"Y-m-d"|after_or_equal:' . date('Y/m/d'),
            'end' => 'required|date_format:"Y-m-d"|after_or_equal:' . $request->start,
            'uuid' => 'required',
            'type' => 'required|in:product,location,cart',
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

        if ($request->type == 'cart') {
            $content = Cart::query()->find($request->uuid);
            if (!$content) {
                return mainResponse(false, 'uuid not found', [], ['uuid not found'], 101);
            }
            $content = $content->content;
            if (!$content) {
                return mainResponse(false, 'uuid not found', [], ['uuid not found'], 101);
            }
        }

        $user = auth('sanctum')->user();

        $total = intval($content->price * $daysDifference);
        $commission = $content->price * $daysDifference * $user->commission;
        $total = intval($total + $commission);
        $amount = number_format($content->price);
        $bill = [
            [
                'title' => $content->price . ' x ' . $daysDifference . __('days'),
                'amount' => number_format($content->price * $daysDifference)
            ],


        ];
        $multidaydiscount = MultiDayDiscount::query()
            ->where('minimal_day', '<=', $daysDifference)
            ->orderByDesc('minimal_day')
            ->first();
        if ($content->multi_day_discount_uuid && $multidaydiscount->minimal_day <= $daysDifference) {
            $bill[] = [
                'title' => __('Multi-day discounts'),
                'amount' => number_format($content->price * ($multidaydiscount->rate / 100) * -1)
            ];
            $total -= $content->price * ($multidaydiscount->rate / 100);

        }
        $bill[] = [
            'title' => __('commission'),
            'amount' => number_format($content->price * $daysDifference * $user->commission, 0, '.', '')
        ];
        $bill[] = [
            'title' => __('total'),
            'amount' => number_format($total)
        ];
        $currency = __('sr');

        return mainResponse(true, 'done', compact('amount', 'bill', 'currency'), [], 200);
    }

    public function addCart(Request $request)
    {

        $rules = [
            'start' => 'required|date_format:"Y-m-d"|after_or_equal:' . date('Y/m/d'),
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
        $startDate = Carbon::parse($request->start);
        $endDate = Carbon::parse($request->end);
        $daysDifference = $endDate->diffInDays($startDate);
        $daysDifference = ($daysDifference != 0) ? $daysDifference : 1;
        $request->merge([

            'delivery_uuid' => @$content->delivery->delivery,
            'commission' => $content->price * $daysDifference * doubleval($user->commission),
            'price' => $content->price,
            'user_uuid' => $user->uuid,
            'content_uuid' => $content->uuid,
        ]);

        $multidaydiscount = MultiDayDiscount::query()
            ->where('minimal_day', '<=', $daysDifference)
            ->orderByDesc('minimal_day')
            ->first();


        if ($content->multi_day_discount_uuid && $multidaydiscount->minimal_day <= $daysDifference) {
            $request->merge([
                'multi_day_discounts' => $content->price * ($multidaydiscount->rate / 100) * -1,
            ]);
        }

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
        $users = array_values($uniqueArray);
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


        if (!count($cart)) {
            $users = [];
            return mainResponse(true, 'ok', compact('users'), []);


        }


        $bill = [];
        $multi_day_discounts = 0;
        $commission = 0;
        $sub_total = 0;
        foreach ($cart as $item) {
            $bill[] = [
                'title' => $item->price . ' x ' . $item->days_count . __('days'),
                'amount' => number_format($item->price * $item->days_count),
            ];
            $multi_day_discounts += $item->multi_day_discounts;
            $sub_total += $item->price * $item->days_count;
            $commission += $item->commission;

        }
        $discount_amount = 0;
        $discount = null;
        $sub_total_location = 0;
        $sub_total_product = 0;
        if ($request->code) {
            $discount_prodcut = Cart::query()
                ->where(function (Builder $q) use ($user_uuid) {
                    $q->whereHas('products', function (Builder $query) use ($user_uuid) {
                        $query->where('user_uuid', $user_uuid);
                    });
                })
                ->where('user_uuid', $user->uuid)
                ->get();
            $discount_location = Cart::query()
                ->where(function (Builder $q) use ($user_uuid) {
                    $q->WhereHas('locations', function (Builder $query) use ($user_uuid) {
                        $query->where('user_uuid', $user_uuid);
                    });
                })
                ->where('user_uuid', $user->uuid)
                ->get();

            foreach ($discount_prodcut as $item) {
                $sub_total_product += $item->price * $item->days_count;
            }
            foreach ($discount_location as $item) {
                $sub_total_location += $item->price * $item->days_count;
            }


            if ($discount_prodcut->isNotEmpty()) {
                $discount_prodcut = true;
            } else {
                $discount_prodcut = false;

            }
            if ($discount_location->isNotEmpty()) {
                $discount_location = true;
            } else {
                $discount_location = false;
            }

            $discount = Discount::query()
                ->where('code', $request->code)
//                ->whereDate('date_from', '<', Carbon::now())
//                ->whereDate('date_to', '>', Carbon::now())
                ->when($discount_prodcut, function ($q) {
                    $q->whereHas('discountContent', function ($q) {
                        $q->where('type', 'product');
                    });
                })
                ->when($discount_location, function ($q) {
                    $q->whereHas('discountContent', function ($q) {
                        $q->where('type', 'location');
                    });
                })
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
//            if (DiscountUser::query()
//                ->where('user_uuid', $user->uuid)
//                ->where('discount_uuid', $discount->uuid)
////                ->where('owner_user_uuid', $user_uuid)
//                ->exists()) {
//                return mainResponse(false, __('is already'), [], []);
//            }
//
//dd($discount_prodcut , $discount_location);
            if ($discount_prodcut && $discount_location) {

                if ($discount->discount_type == Discount::FIXED_PRICE) {
                    $discount_amount = $discount->discount;
                } else {
                    $discount_amount = $sub_total * ($discount->discount / 100);
                }
            } elseif ($discount_location) {

                if ($discount->discount_type == Discount::FIXED_PRICE) {
                    $discount_amount = $discount->discount;
                } else {
                    $discount_amount = $sub_total_location * ($discount->discount / 100);
                }
            } elseif ($discount_prodcut) {
                if ($discount->discount_type == Discount::FIXED_PRICE) {
                    $discount_amount = $discount->discount;
                } else {
                    $discount_amount = $sub_total_product * ($discount->discount / 100);
                }
            }


            $bill[] = [
                'title' => __('discounts'),
                'amount' => number_format($discount_amount * -1)
            ];
            $sub_total -= $discount_amount;


        }


//        $commission = intval(($sub_total) * $user->commission);
        $bill[] = [
            'title' => __('Multi-day discounts'),
            'amount' => number_format($multi_day_discounts)
        ];
        $sub_total += $multi_day_discounts;

        $bill[] = [
            'title' => __('commission'),
            'amount' => number_format($commission, 0, '.', '')
        ];
        $sub_total += $commission;
        $bill[] = [
            'title' => __('total'),
            'amount' => number_format($sub_total)
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

        $cart = Cart::query()
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
        $amount = number_format($cart->price);

        $bill[] = [
            'title' => $cart->price . ' x ' . $cart->days_count . ' ' . __('days'),
            'amount' => number_format($cart->price * $cart->days_count),
        ];
        $multi_day_discounts += $cart->multi_day_discounts;
        $sub_total .= number_format($cart->price * $cart->days_count, 0, '.', '');


        $bill[] = [
            'title' => __('Multi-day discounts'),
            'amount' => number_format($multi_day_discounts * -1, 0, '.', '')
        ];


        $bill[] = [
            'title' => __('commission'),
            'amount' => number_format($cart->commission, 0, '.', '')
        ];
        $sub_total += $cart->commission;
        $bill[] = [
            'title' => __('total'),
            'amount' => number_format($sub_total - $multi_day_discounts, 0, '.', '')
        ];
        $currency = __('sr');

        return mainResponse(true, 'ok', compact('amount', 'item', 'bill', 'currency'), []);

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
            $user = Auth::guard('sanctum')->user();

            $cart->update([
                'commission' => $cart->price * $daysDifference * doubleval($user->commission),
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
        $from_user = UserOrderResource::make($user);
        $to_user = UserOrderResource::make(User::query()->findOrFail($user_uuid));

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

        if (!count($cart)) {
            return mainResponse(false, 'user not found', [], []);
        }

        $items = [];

        foreach ($cart as $item) {
            $items[] = [
                'count' => $item->days_count . ' ' . __('days'),
                'start' => Carbon::parse($item->start)->format('d/m/Y'),
                'end' => Carbon::parse($item->end)->format('d/m/Y'),
            ];
        }

        $content = 'html';

        $time = Carbon::now()->format('Y/m/d . h:m A');

        return mainResponse(true, 'ok', compact('time', 'from_user', 'to_user', 'items', 'content'), []);
    }

    public function getPagePayRent(Request $request)
    {
        $user_uuid = $request->user_uuid;
        $content_uuid = $request->content_uuid;

        $user = Auth::guard('sanctum')->user();
        $delivery_address = DeliveryAddresses::query()->where('user_uuid', $user->uuid)->where('default', 1)->select('address', 'uuid', 'country_uuid', 'city_uuid')->first();
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
            $owner = [
                'image' => $user_owner->image,
                'name' => $user_owner->name,
                'count' => $cart->count(),
            ];

            $bill = [];
            $multi_day_discounts = 0;
            $sub_total = 0;
            $mony = 0;
            foreach ($cart as $item) {

                $multi_day_discounts += $item->multi_day_discounts;

                $sub_total += number_format($item->price * $item->days_count, 0, '.', '');
                $mony += $item->price * $item->days_count;
            }

            $bill[] = [
                'title' => __('products') . ',' . __('locations'),
                'amount' => number_format($sub_total),
            ];
            $bill[] = [
                'title' => __('Multi-day discounts'),
                'amount' => number_format($multi_day_discounts, 0, '.', '')
            ];
            $sub_total += $multi_day_discounts;
            $discount = 0;
            $sub_total_location = 0;
            $sub_total_product = 0;
            if ($request->code) {
                $discount_prodcut = Cart::query()
                    ->where(function (Builder $q) use ($user_uuid) {
                        $q->whereHas('products', function (Builder $query) use ($user_uuid) {
                            $query->where('user_uuid', $user_uuid);
                        });
                    })
                    ->where('user_uuid', $user->uuid)
                    ->get();
                $discount_location = Cart::query()
                    ->where(function (Builder $q) use ($user_uuid) {
                        $q->WhereHas('locations', function (Builder $query) use ($user_uuid) {
                            $query->where('user_uuid', $user_uuid);
                        });
                    })
                    ->where('user_uuid', $user->uuid)
                    ->get();

                foreach ($discount_prodcut as $item) {
                    $sub_total_product += $item->price * $item->days_count;
                }
                foreach ($discount_location as $item) {
                    $sub_total_location += $item->price * $item->days_count;
                }


                if ($discount_prodcut->isNotEmpty()) {
                    $discount_prodcut = true;
                } else {
                    $discount_prodcut = false;

                }
                if ($discount_location->isNotEmpty()) {
                    $discount_location = true;
                } else {
                    $discount_location = false;
                }

                $discount = Discount::query()
                    ->where('code', $request->code)
//                ->whereDate('date_from', '<', Carbon::now())
//                ->whereDate('date_to', '>', Carbon::now())
                    ->when($discount_prodcut, function ($q) {
                        $q->whereHas('discountContent', function ($q) {
                            $q->where('type', 'product');
                        });
                    })
                    ->when($discount_location, function ($q) {
                        $q->whereHas('discountContent', function ($q) {
                            $q->where('type', 'location');
                        });
                    })
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
//            if (DiscountUser::query()
//                ->where('user_uuid', $user->uuid)
//                ->where('discount_uuid', $discount->uuid)
////                ->where('owner_user_uuid', $user_uuid)
//                ->exists()) {
//                return mainResponse(false, __('is already'), [], []);
//            }
//
//dd($discount_prodcut , $discount_location);
                if ($discount_prodcut && $discount_location) {

                    if ($discount->discount_type == Discount::FIXED_PRICE) {
                        $discount_amount = $discount->discount;
                    } else {
                        $discount_amount = $sub_total * ($discount->discount / 100);
                    }
                } elseif ($discount_location) {

                    if ($discount->discount_type == Discount::FIXED_PRICE) {
                        $discount_amount = $discount->discount;
                    } else {
                        $discount_amount = $sub_total_location * ($discount->discount / 100);
                    }
                } elseif ($discount_prodcut) {
                    if ($discount->discount_type == Discount::FIXED_PRICE) {
                        $discount_amount = $discount->discount;
                    } else {
                        $discount_amount = $sub_total_product * ($discount->discount / 100);
                    }
                }


                $bill[] = [
                    'title' => __('discounts'),
                    'amount' => number_format($discount_amount * -1)
                ];
                $sub_total -= $discount_amount;


            }


            $commission = $mony * $user->commission;
            $bill[] = [
                'title' => __('commission'),
                'amount' => number_format($commission)
            ];

            $sub_total += $commission;
            $bill[] = [
                'title' => __('total'),
                'amount' => number_format($sub_total)
            ];

            $currency = __('sr');

            $promo_code_index = -1;
            if ($discount) {
                $promo_code_index = count($bill) - 3;
            }

            return mainResponse(true, 'ok', compact('owner', 'delivery_address', 'payment_methods', 'bill', 'currency', 'promo_code_index'), []);

        } else {
            return mainResponse(false, 'user not found', [], [], 101);

        }


    }

    public function getPagePaySale(Request $request)
    {
        $content_uuid = $request->content_uuid;
        $discount_service = false;
        $discount_course = false;
        $discount_product = false;
        $user = Auth::guard('sanctum')->user();
        $payment_methods = PaymentGateway::all();
        if ($request->has('content_uuid') && $request->has('content_type')) {
            $bill = [];

            if ($request->content_type == "product") {
                $delivery_address = DeliveryAddresses::query()->where('user_uuid', $user->uuid)->where('default', 1)->select('address', 'uuid', 'country_uuid', 'city_uuid')->first();

                $content = Product::query()->findOrFail($content_uuid);
                $bill[] = [
                    'title' => __('products'),
                    'amount' => number_format($content->price, 0, '.', '')
                ];
                $delivery = DB::table('deliveries')->where('id', 1)->value('delivery');
                if ($delivery) {
                    $bill[] = [
                        'title' => __('deliver'),
                        'amount' => number_format($delivery)
                    ];
                }

                $sub_total = $content->price;
                $item = [
                    'name' => $content->name,
                    'image' => $content->image,
                    'category_name' => $content->category_name,
                    'price' => $content->price,
                    'currency' => __('sr'),
                ];
                $discount_product = true;
            } elseif ($request->content_type == "course") {
                $discount_course = true;
                $content = Course::query()->findOrFail($request->content_uuid);
                $bill[] = [
                    'title' => __('courses'),
                    'amount' => number_format($content->price, 0, '.', '')
                ];
                $sub_total = $content->price;

                $owner = [
                    'name' => $content->user->name,
                    'image' => $content->user->image,
                ];
                $item = [
                    'name' => $content->name,
                    'image' => $content->image,
                    'price' => $content->price,
                    'currency' => __('sr'),
                ];
            } elseif ($request->content_type == "service") {
                $discount_service = true;
                $bill_service = BillService::query()->find($request->bill_service_uuid);

                $content = Serving::query()->findOrFail($request->content_uuid);
                $bill[] = [
                    'title' => __('service'),
                    'amount' => number_format($bill_service->price, 0, '.', '')
                ];
                $sub_total = $bill_service->price;
                $startDate = Carbon::parse($bill_service->from);
                $endDate = Carbon::parse($bill_service->to);
                $daysDifference = $endDate->diffInDays($startDate);
//                $invoice = [
//                    'commission' => $settings->commission,
//                    'price' => $content->price,
//                    'all' => $settings->commission + 10 + $content->price
//                ];
                $owner = [
                    'name' => @$content->user_name,
                    'image' => @$content->user->image,
                    'specialization_name' => @$content->user->specialization_name,
                ];
                $item = [
                    'name' => $content->name,
                    'details' => $bill_service->price . ' ' . __('sr') . ' x ' . $daysDifference . ' ' . __('days'),
                    'start' => $bill_service->from,
                    'end' => $bill_service->to,
                    'price' => $bill_service->price,
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
                    ->when($discount_product, function ($q) {
                        $q->whereHas('discountContent', function ($q) {
                            $q->where('type', 'product');
                        });
                    })
                    ->when($discount_service, function ($q) {
                        $q->whereHas('discountContent', function ($q) {
                            $q->where('type', 'service');
                        });
                    })->when($discount_course, function ($q) {
                        $q->whereHas('discountContent', function ($q) {
                            $q->where('type', 'course');
                        });
                    })
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
//                DiscountUser::query()->create([
//                    'user_uuid' => $user->uuid,
//                    'discount_uuid' => $discount->uuid,
//                    'owner_user_uuid' => $content->user->uuid,
//                ]);
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

            $commission = $content->price * $content->user->commission;
            $bill[] = [
                'title' => __('commission'),
                'amount' => number_format($content->price * $content->user->commission)
            ];

            $bill[] = [
                'title' => __('total'),
                'amount' => number_format($sub_total + intval($commission) + @$delivery)
            ];

            $currency = __('sr');
            $promo_code_index = -1;
            if ($discount) {
                $promo_code_index = count($bill) - 3;
            }

            if ($request->content_type == "product") {
                return mainResponse(true, 'ok', compact('item', 'delivery_address', 'payment_methods', 'bill', 'currency', 'promo_code_index'), []);

            } else {
                return mainResponse(true, 'ok', compact('owner', 'item', 'payment_methods', 'bill', 'currency', 'promo_code_index'), []);

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
            'type' => 'required|in:sale,rent',
            'payment_method_id' => ['required',
                Rule::exists(PaymentGateway::class, 'id')->where(function ($q) {
                    $q->where('status', 1);
                })],
            'content_type' => 'required_if:type,==,sale|in:product,service,course',
            'content_uuid' => 'required_if:type,==,sale',
            'user_uuid' => 'required_if:type,==,rent|exists:users,uuid',
//            'delivery_address_uuid' =>  [Rule::requiredIf( function () use ($request){
//                return $request->input('content_type') != 'service';
//            }), 'exists:delivery_addresses,uuid'],
//            'start' => 'required_if:content_type,==,service|date_format:"Y-m-d"|after:' . date('Y/m/d'),
//            'end' => 'required_if:content_type,==,service|date_format:"Y-m-d"|after_or_equal:' . $request->start,
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }

        $user = Auth::guard('sanctum')->user();
        $user_uuid = $user->uuid;

        if ($request->type == 'sale' && $request->has('content_type') && $request->has('content_uuid')) {
            $discount_service = false;
            $discount_course = false;
            $discount_product = false;
            if ($request->content_type == "product") {
                $discount_product = true;

                $content = Product::query()->find($request->content_uuid);
                $delivery_address_uuid = DeliveryAddresses::query()
                    ->where('user_uuid', $user->uuid)
                    ->where('default', 1)
                    ->value('uuid');
            } elseif ($request->content_type == "course") {
                $discount_course = true;

                $content = Course::query()->find($request->content_uuid);
            } elseif ($request->content_type == "service") {
                $discount_service = true;
                $content = BillService::query()->find($request->bill_service_uuid);
            }
            if (!$content) {
                return 1;
                return mainResponse(false, 'content not found', [], [], 101);
            }
//            return $content;
            $commission = $content->price * $user->commission;
            $balance = $content->price + $commission;
            $discount = 0;
            if ($request->code) {
                $discount = Discount::query()
                    ->where('code', $request->code)
                    ->whereDate('date_from', '<', date('y-m-d'))
                    ->whereDate('date_to', '>', date('y-m-d'))
                    ->when($discount_product, function ($q) {
                        $q->whereHas('discountContent', function ($q) {
                            $q->where('type', 'product');
                        });
                    })
                    ->when($discount_service, function ($q) {
                        $q->whereHas('discountContent', function ($q) {
                            $q->where('type', 'service');
                        });
                    })->when($discount_course, function ($q) {
                        $q->whereHas('discountContent', function ($q) {
                            $q->where('type', 'course');
                        });
                    })
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
//                DiscountUser::query()->create([
//                    'user_uuid' => $user->uuid,
//                    'discount_uuid' => $discount->uuid,
////                    'owner_user_uuid' => $content->user->uuid,
//                ]);
            }
            if ($discount) {
                if ($discount->discount_type == Discount::FIXED_PRICE) {
                    $discount_amount = $discount->discount;
                } else {
                    $discount_amount = $content->price * ($discount->discount / 100);
                }
                $balance -= $discount_amount;
            }
            $data = [
                'order_number' => Carbon::now()->timestamp . '' . rand(1000, 9999),
                'commission' => $content->price * doubleval($user->commission),
                'user_uuid' => $user->uuid,
                'discount_uuid' => $discount_amount ?? null,

                'content_type' => $request->content_type,
                'delivery_address_uuid' => @$delivery_address_uuid,
                'content_uuid' => $request->content_uuid,
                'price' => $content->price,
                'payment_method_id' => $request->payment_method_id,
            ];
            if ($request->content_type == 'product') {
                $data['type'] = 'sale';
                $data['delivery'] = 20;
                $balance = 20 + $balance;//10 delivery
            } elseif ($request->content_type == 'service' && $request->has('bill_service_uuid')) {
                $data['start'] = $content->start;
                $data['end'] = $content->end;
//                return $data->except('order_number');
                $order = Order::query()->withoutGlobalScope('status')->where('order_number', $content->conversations->order_number)->first();
                $data_service = [
                    'commission' => $data['commission'],
                    'user_uuid' => $data['user_uuid'],
                    'discount_uuid' => $data['discount_uuid'],
                    'content_type' => $data['content_type'],
                    'delivery_address_uuid' => $data['delivery_address_uuid'],
                    'content_uuid' => $data['content_uuid'],
                    'price' => $data['price'],
                    'payment_method_id' => $data['payment_method_id'],
                ];
                $order->update($data_service);

            } elseif ($request->content_type == 'course') {
                unset($data['delivery_address_uuid']);
            } else {
                return mainResponse(false, 'content not found', [], [], 404);
            }
            if ($request->content_type != 'service'){
                $order = Order::create($data);
            }

        } elseif ($request->type == 'rent' && $request->has('user_uuid')) {
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
            $commission = 0;
            $sub_total = 0;
            $sub_total_location = 0;
            $sub_total_product = 0;
            if ($request->code) {
                $discount = Discount::query()
                    ->where('code', $request->code)
                    ->whereDate('date_from', '<', date('y-m-d'))
                    ->whereDate('date_to', '>', date('y-m-d'))
                    ->first();
                if (!$discount) {
                    return mainResponse(false, __('Code not found'), [], []);
                }

                $discount_prodcut = Cart::query()
                    ->where(function (Builder $q) use ($request) {
                        $q->whereHas('products', function (Builder $query) use ($request) {
                            $query->where('user_uuid', $request->user_uuid);
                        });
                    })
                    ->where('user_uuid', $user->uuid)
                    ->get();
                $discount_location = Cart::query()
                    ->where(function (Builder $q) use ($request) {
                        $q->WhereHas('locations', function (Builder $query) use ($request) {
                            $query->where('user_uuid', $request->user_uuid);
                        });
                    })
                    ->where('user_uuid', $user->uuid)
                    ->get();

                foreach ($discount_prodcut as $item) {
                    $sub_total_product += $item->price * $item->days_count;
                }
                foreach ($discount_location as $item) {
                    $sub_total_location += $item->price * $item->days_count;
                }


                if ($discount_prodcut->isNotEmpty()) {
                    $check_discount_prodcut = true;
                } else {
                    $check_discount_prodcut = false;

                }
                if ($discount_location->isNotEmpty()) {
                    $check_discount_location = true;
                } else {
                    $check_discount_location = false;
                }


                $count_check = DiscountUser::query()
                    ->where('user_uuid', $user->uuid)
                    ->where('discount_uuid', $discount->uuid)
                    ->count();

                if ($discount->number_uses >= $discount->number_of_usage) {
                    return mainResponse(false, __('has expired'), [], []);
                }
                if ($count_check >= $discount->number_of_usage_for_user) {
                    return mainResponse(false, __('has expired'), [], []);
                }

                DiscountUser::query()->create([
                    'user_uuid' => $user->uuid,
                    'discount_uuid' => $discount->uuid,
                    'owner_user_uuid' => $uuid,
                ]);
//                $count = Cart::query()
//                    ->where(function (Builder $q) use ($uuid) {
//                        $q->whereHas('products', function (Builder $query) use ($uuid) {
//                            $query->where('user_uuid', $uuid);
//                        })->orWhereHas('locations', function (Builder $query) use ($uuid) {
//                            $query->where('user_uuid', $uuid);
//                        });
//                    })
//                    ->where('user_uuid', $user->uuid)
//                    ->count();

//                if ($discount->discount_type == Discount::FIXED_PRICE) {
//                    $discount_amount = $discount->discount;
//                } else {
//                    $discount_amount = ($sub_total * ($discount->discount / 100)) ?? 0;
//                }
//

                if ($check_discount_prodcut && $check_discount_location) {

                    if ($discount->discount_type == Discount::FIXED_PRICE) {
                        $discount_amount = $discount->discount;
                    } else {
                        $discount_amount = ($sub_total_product + $sub_total_location) * ($discount->discount / 100);
                    }
                    $count = $discount_location->count() + $discount_prodcut->count();
                    $discount = $discount_amount;
                    Cart::query()
                        ->where(function (Builder $q) use ($uuid) {
                            $q->whereHas('products', function (Builder $query) use ($uuid) {
                                $query->where('user_uuid', $uuid);
                            })->orWhereHas('locations', function (Builder $query) use ($uuid) {
                                $query->where('user_uuid', $uuid);
                            });
                        })
                        ->where('user_uuid', $user->uuid)
                        ->update([
                            'discount_uuid' => $discount / ($discount_location->count() + $discount_prodcut->count()),
                        ]);
                } elseif ($check_discount_location) {

                    if ($discount->discount_type == Discount::FIXED_PRICE) {
                        $discount_amount = $discount->discount;
                    } else {
                        $discount_amount = $sub_total_location * ($discount->discount / 100);
                    }
                    $discount = $discount_amount;
                    $count = $discount_location->count();

                    Cart::query()
                        ->where(function (Builder $q) use ($uuid) {
                            $q->whereHas('products', function (Builder $query) use ($uuid) {
                                $query->where('user_uuid', $uuid);
                            })->orWhereHas('locations', function (Builder $query) use ($uuid) {
                                $query->where('user_uuid', $uuid);
                            });
                        })
                        ->where('user_uuid', $user->uuid)
                        ->update([
                            'discount_uuid' => $discount / $discount_location->count(),
                        ]);
                } elseif ($check_discount_prodcut) {
                    if ($discount->discount_type == Discount::FIXED_PRICE) {
                        $discount_amount = $discount->discount;
                    } else {
                        $discount_amount = $sub_total_product * ($discount->discount / 100);
                    }
                    $discount = $discount_amount;
                    $count = $discount_prodcut->count();

                    Cart::query()
                        ->where(function (Builder $q) use ($uuid) {
                            $q->whereHas('products', function (Builder $query) use ($uuid) {
                                $query->where('user_uuid', $uuid);
                            })->orWhereHas('locations', function (Builder $query) use ($uuid) {
                                $query->where('user_uuid', $uuid);
                            });
                        })
                        ->where('user_uuid', $user->uuid)
                        ->update([
                            'discount_uuid' => $discount / $discount_prodcut->count(),
                        ]);
                }


            }
            foreach ($cart as $item) {
                $order = Order::create([
                    'delivery_address_uuid' => $request->delivery_address_uuid,
                    'order_number' => $order_number,
                    'price' => $item->price,
                    'multi_day_discounts' => $item->multi_day_discounts,
                    'discount_uuid' => ($request->code) ? $discount / $count : null,
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
                $commission += $item->commission;
            }

//            $commission = $sub_total * $user->commission;
            $balance = $sub_total + $commission + $multi_day_discounts;
            if ($request->code) {

                $balance -= $discount;
            }
        } else {
            return mainResponse(false, 'content not found', [], [], 101);
        }
//        return $balance;
        $entityId = Payment::PAYMENT_ENTITY_ID_DEFAULT;
        if ($request->payment_method_id == PaymentGateway::MADA) {
            $entityId = Payment::PAYMENT_ENTITY_ID_MADA;
        } elseif ($request->payment_method_id == PaymentGateway::APPLE_PAY) {
            $entityId = Payment::PAYMENT_ENTITY_ID_APPLE_PAY;
        }
        $amount = intval($balance);
        $url = Payment::PAYMENT_BASE_URL . "/v1/checkouts";
        $data = "entityId=" . $entityId .
            "&amount=$amount" .
            "&currency=EUR" .
            "&paymentType=DB";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer ' . Payment::PAYMENT_TOKEN));
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, Payment::PAYMENT_IS_LIVE);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        $data = json_decode($responseData);
//            return $data;
        $id = $data->id;


        $payment = Payment::query()->updateOrCreate([
            'user_uuid' => $user->uuid,
            'price' => $balance,
            'status' => Payment::PENDING,

        ], [
            'order_number' => $order->order_number,
            'transaction_id' => $id,
            'payment_method_id' => $request->payment_method_id,

        ]);


        if ($request->content_type == 'service'){
            $content->update([
                'payment_uuid' => $payment->uuid,
            ]);
        }
        $payment_method_id = intval($request->payment_method_id);
        $url = route('paymentGateways.checkout', $payment->uuid);
        $status = 'url';
        $checkout_id = '';
        $payment_uuid = $payment->uuid;
        $payment_is_live = Payment::PAYMENT_IS_LIVE;
        if ($payment_method_id == PaymentGateway::APPLE_PAY) {
            $status = 'apple_pay';
            $url = '';
            $checkout_id = 'checkout_id_here';
        }

        return mainResponse(true, 'ok', compact('payment_is_live', 'payment_uuid', 'payment_method_id', 'status', 'url', 'checkout_id'), []);
    }


    public function paymentDetails($uuid)
    {
        $payment = Payment::query()->where('user_uuid', auth('sanctum')->id())->findOrFail($uuid);
        $order = Order::query()->where('user_uuid', auth('sanctum')->id())->where('order_number', $payment->order_number)->get();

        $order_uuid = null;
        $type = null;

        if ($order->count() > 1) {
            $locations = $order->where('content_type', Order::LOCATION)->count();
            $products = $order->where('content_type', Order::PRODUCT)->count();
            if ($locations >= $products) {
                $type = Order::PRODUCT;
            } else {
                $type = Order::LOCATION;
            }
        } else {
            $order_uuid = $order[0]->uuid;
        }
        $data = [
            'order_number' => $payment->order_number,
            'date' => $payment->created_at->format('d/m/Y'),
            'time' => $payment->created_at->format('h:m A'),
            'type' => $type,
            'order_uuid' => $order_uuid
        ];

        return mainResponse(true, 'ok', $data);
    }

    public function ordersBuyer(Request $request, $type)
    {
        $filter_type = $request->filter;
        $user = Auth::guard('sanctum')->user();
        $user_uuid = $user->uuid;
        if ($type == 'product') {
            $orders = Order::query()
                ->where('content_type', 'product')
                ->where(function ($q) use ($user, $filter_type, $user_uuid) {
                    if (is_null($filter_type) || $filter_type == 'buyer') {
                        $q->where('user_uuid', $user->uuid);
                    }
                    if (is_null($filter_type) || $filter_type == 'owner') {
                        $q->orWhereHas('product', function (Builder $query) use ($user_uuid) {
                            $query->where('user_uuid', $user_uuid);
                        });
                    }
                })
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->created_at)
                        ->format('Y-m-d');
                });

        } elseif ($type == 'location') {


            $orders = Order::query()
                ->where('content_type', 'location')
                ->where(function ($q) use ($user, $filter_type, $user_uuid) {
                    if (is_null($filter_type) || $filter_type == 'buyer') {
                        $q->where('user_uuid', $user->uuid);
                    }
                    if (is_null($filter_type) || $filter_type == 'owner') {
                        $q->orWhereHas('location', function (Builder $query) use ($user_uuid) {
                            $query->where('user_uuid', $user_uuid);
                        });
                    }
                })
                ->get()
                ->groupBy(function ($item) {
                    return Carbon::parse($item->created_at)->format('Y-m-d');
                });
        } elseif ($type == 'service') {


            $orders = Order::query()
                ->withoutGlobalScopes(['status'])
                ->where('content_type', 'service')
                ->where(function ($q) use ($user, $filter_type, $user_uuid) {
                    if (is_null($filter_type) || $filter_type == 'buyer') {
                        $q->where('user_uuid', $user->uuid);
                    }
                    if (is_null($filter_type) || $filter_type == 'owner') {
                        $q->orWhereHas('service', function (Builder $query) use ($user_uuid) {
                            $query->where('user_uuid', $user_uuid);
                        });
                    }
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
                ->where('content_type', 'course')
                ->where(function ($q) use ($user, $filter_type, $user_uuid) {
                    if (is_null($filter_type) || $filter_type == 'buyer') {
                        $q->where('user_uuid', $user->uuid);
                    }
                    if (is_null($filter_type) || $filter_type == 'owner') {
                        $q->orWhereHas('course', function (Builder $query) use ($user_uuid) {
                            $query->where('user_uuid', $user_uuid);
                        });
                    }
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

//    public function ordersOwner(Request $request, $type)
//    {
//        $request->merge([
//            'owner' => true
//        ]);
//        $user = Auth::guard('sanctum')->user();
//        $user_uuid = $user->uuid;
//        if ($type == 'product') {
//            $orders = Order::query()
//                ->WhereHas('product', function (Builder $query) use ($user_uuid) {
//                    $query->where('user_uuid', $user_uuid);
//                })
//                ->get()
//                ->groupBy(function ($item) {
//                    return Carbon::parse($item->created_at)
//                        ->format('Y-m-d');
//                });
//        } elseif ($type == 'location') {
//
//
//            $orders = Order::query()
//                ->WhereHas('location', function (Builder $query) use ($user_uuid) {
//                    $query->where('user_uuid', $user_uuid);
//                })
//                ->get()->groupBy(function ($item) {
//                    return Carbon::parse($item->created_at)->format('Y-m-d');
//                });
//
//        } elseif ($type == 'service') {
//
//
//            $orders = Order::query()
//                ->WhereHas('service', function (Builder $query) use ($user_uuid) {
//                    $query->where('user_uuid', $user_uuid);
//                })
//                ->get()
//                ->groupBy(function ($item) {
//                    return Carbon::parse($item->created_at)
//                        ->format('Y-m-d');
//                });
//
//            $orders = paginateOrder($orders);
//            $items = $orders->getCollection();
//            $data = [];
//            foreach ($items as $key => $order) {
//                $data[] = [
//                    'day' => $key,
//                    'items' => ServingOrderResource::collection($order),
//                ];
//            }
//            $orders->setCollection(collect($data));
//            $items = $orders;
//            return mainResponse(true, 'ok', compact('items'), []);
//        } elseif ($type == 'course') {
//
//
//            $orders = Order::query()
//                ->WhereHas('course', function (Builder $query) use ($user_uuid) {
//                    $query->where('user_uuid', $user_uuid);
//                })
//                ->get()
//                ->groupBy(function ($item) {
//                    return Carbon::parse($item->created_at)
//                        ->format('Y-m-d');
//                });
//            $orders = paginateOrder($orders);
//            $items = $orders->getCollection();
//            $data = [];
//            foreach ($items as $key => $order) {
//                $data[] = [
//                    'day' => $key,
//                    'order_uuid' => $order->uuid,
//                    'items' => CourseOrderResource::collection($order),
//                ];
//            }
//            $orders->setCollection(collect($data));
//            $items = $orders;
//            return mainResponse(true, 'ok', compact('items'), []);
//        } else {
//            return mainResponse(false, 'type not found', [], [], 403);
//
//        }
//
//        $orders = paginateOrder($orders);
//        $items = $orders->getCollection();
//        $data = [];
//        foreach ($items as $key => $order) {
//            $data[] = [
//                'day' => $key,
//                'items' => OrderResource::collection($order),
//            ];
//        }
//        $orders->setCollection(collect($data));
//        $items = $orders;
//        return mainResponse(true, 'ok', compact('items'), []);
//
//    }

    public function AddReviews($uuid, Request $request)
    {
        $rules = [
            'title' => 'required|string',
            'order_uuid' => 'required|exists:orders,uuid',
        ];
        $request->merge(['order_uuid' => $uuid]);
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $order = Order::query()->find($request->order_uuid);
        $item = Reviews::query()->create([
            'title' => $request->title,
            'content_uuid' => $order->content->uuid,
            'user_uuid' => \auth('sanctum')->id(),
            'reference_uuid' => $order->content->uuid,
        ]);
        return mainResponse(true, 'ok', [], []);
    }

    public function getReviews($uuid)
    {
        $order = Order::query()->findOrFail($uuid);
        if (@$order->content->user) {
            $user = UserOrderResource::make(@$order->content->user);
        } else {
            $user = 'not found';
        }

        $content = ReviewResource::make($order);
        $name = __('Tell us how was your experience with ') . $order->content->name;

        return mainResponse(true, 'ok', compact('user', 'content', 'name'), []);


    }

    public function orderTracking(Request $request, $uuid)
    {
        $order = Order::query()->withoutGlobalScopes(['status'])->findOrFail($uuid);
        $balance = 0;
        $status = $order->status;
        $status_text = $order->status_text;
        $status_color = $order->status_color;
        $status_bg_color = $order->status_bg_color;
        $order_number = "#$order->order_number";
        $delivery_address = null;
        $rent_details = null;
        $type = null;
        $type_text = null;
        $content_type = $order->content_type;
        $item_text = __($content_type);
        $show_support = true;
        $show_receive = false;
        $show_cancel = false;
        $show_accept = false;
        if (@$order->status == Order::PENDING && @$order->user_uuid == Auth::id()) {
            $show_cancel = true;
        }
        if (@$order->status == Order::PENDING && @$order->content->user_uuid == Auth::id()) {
            $show_accept = true;
        }
        if (@$order->status == Order::ACCEPT && @$order->user_uuid == Auth::id()) {
            $show_receive = true;
        }
        $statuses = [
            [
                'title' => 'تم الطلب',
                'image' => '',
                'status' => 'completed',
            ],
            [
                'title' => 'قيد التنفيذ',
                'image' => '',
                'status' => 'in_progress',
            ],
            [
                'title' => 'مكتمل',
                'image' => '',
                'status' => 'pending',
            ],
        ];

        if ($order->content_type == Order::PRODUCT) {
            $type = $order->type;
            $request->merge([
                'product' => true
            ]);
            $item = ProductHomeResource::make($order->content);
            $delivery_address = $order->deliveryAddress()->select('address', 'uuid', 'country_uuid', 'city_uuid')->first();

            if (!$type || $type == Product::RENT) {
                $type = Product::RENT;
                $type_text = __(Product::RENT) . ' , ' . $order->days_count . ' ' . __('days');
                $rent_details = [
                    'count' => $order->days_count . ' ' . __('days'),
                    'start' => Carbon::parse($order->start)->format('d/m/Y'),
                    'end' => Carbon::parse($order->end)->format('d/m/Y'),
                ];
                $bill[] =
                    [
                        'title' => $order->price . ' x ' . $order->days_count . __('days'),
                        'amount' => number_format($order->price * $order->days_count, 0, '.', ''),
                        'currency' => __('sr')

                    ];
                $balance = $order->price * $order->days_count;
            } else {
                $type = Product::SALE;
                $type_text = __(Product::SALE);
                $bill[] = [
                    'title' => __('product'),
                    'amount' => number_format($order->price, 0, '.', ''),
                    'currency' => __('sr')
                ];
                $balance = $order->price;

            }

        } elseif ($order->content_type == Order::LOCATION) {
            $type = Product::RENT;
            $type_text = __(Product::RENT) . ' , ' . $order->days_count . ' ' . __('days');
            $request->merge([
                'location' => true
            ]);
            $item = MyLocationResource::make($order->content);

            $rent_details = [
                'count' => $order->days_count . ' ' . __('days'),
                'start' => Carbon::parse($order->start)->format('d/m/Y'),
                'end' => Carbon::parse($order->end)->format('d/m/Y'),
            ];
            $bill[] = [
                'title' => $order->price . ' x ' . $order->days_count . __('days'),
                'amount' => number_format($order->price * $order->days_count, 0, '.', ''),
                'currency' => __('sr')
            ];
            $balance = $order->price * $order->days_count;

        } elseif ($order->content_type == Order::SERVICE) {
            $item = ServingTrakingResource::make(@$order->content);
            $bill[] = [
                'title' => 'price',
                'amount' => number_format($order->price, 0, '.', ''),
                'currency' => __('sr')
            ];
            $rent_details = [
                'count' => $order->days_count . ' ' . __('days'),
                'start' => Carbon::parse($order->start)->format('d/m/Y'),
                'end' => Carbon::parse($order->end)->format('d/m/Y'),
            ];
//            $balance = $order->price * $order->days_count;
            $balance = $order->price;

//            return $order->order_number;
            $conversation_uuid = OrderConversation::query()
//                ->where('service_uuid',$order->content->uuid)
                ->where("customer_uuid", $order->user_uuid)
                ->where("owner_uuid", $order->content->user_uuid)
                ->where("order_number", $order->order_number)
                ->value('uuid');

        } elseif ($order->content_type == Order::COURSE) {
            $item = CourseTrakingResource::make($order->course);
            $bill[] = [
                'title' => __('course'),
                'amount' => number_format($order->price, 0, '.', ''),
                'currency' => __('sr')
            ];
            $balance = $order->price;

        }


        $payment_method = @$order->paymentMethod;
        $price = $order->price;
        $commission = $order->commission;
        $bill[] =
            [
                'title' => __('commission'),
                'amount' => number_format($commission, 0, '.', ''),
                'currency' => __('sr')

            ];
        $balance += $commission;
        if ($order->discount_uuid) {
            $bill[] = [
                'title' => __('discount'),
                'amount' => number_format($order->discount_uuid, 0, '.', ''),
                'currency' => __('sr')
            ];
            $balance -= $order->discount_uuid;

        }
        if ($order->delivery) {
            $bill[] = [
                'title' => __('delivery'),
                'amount' => number_format($order->delivery, 0, '.', ''),
                'currency' => __('sr')
            ];
            $balance += $order->delivery;
        }
        if ($order->multi_day_discounts) {
            $bill[] = [
                'title' => __('multi_day_discounts'),
                'amount' => number_format($order->multi_day_discounts, 0, '.', ''),
                'currency' => __('sr')
            ];
            $balance += $order->multi_day_discounts;

        }
        $bill[] = [
            'title' => __('all'),
            'amount' => number_format($balance, 0, '.', ''),
            'currency' => __('sr')
        ];

        if (@$order->content->user_uuid == auth('sanctum')->id()) {
            $user = UserOrderResource::make(@$order->user);
        } else {

            $user = UserOrderResource::make(@$order->content->user);
        }
        $title = __('order_details');
        $date = Carbon::parse($order->created_at)->format('Y/m/d . h:m A');
        return mainResponse(true, 'ok',
            compact('title',
                'date',
                'status',
                'status_text',
                'status_color',
                'status_bg_color',
                'content_type',
                'type',
                'type_text',
                'order_number',
                'statuses',
                'item_text',
                'item',
                'conversation_uuid',
                'user',
                'rent_details',
                'delivery_address',
                'payment_method',
                'bill',
                'show_support',
                'show_receive',
                'show_cancel',
                'show_accept'
            ), []);
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
        if ($orders->content_type == 'location') {
            $request->merge([
                'location' => true
            ]);
        }
        $product = ProductHomeResource::make($orders->content);
        $order_numbe = $orders->order_number;
        $payment_method = $orders->paymentMethod;
        $rentalDates = [
            'count' => $orders->days_count . ' ' . __('days'),
            'start' => Carbon::parse($orders->start)->format('d/m/Y'),
            'end' => Carbon::parse($orders->end)->format('d/m/Y'),
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
        return mainResponse(true, 'ok', compact('order_numbe', 'status', 'product', 'user', 'payment_method', 'rentalDates', 'bill'), []);


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
        if ($order->content->user_uuid == Auth::id()) {
            $order->update([
                'status' => Order::ACCEPT
            ]);
            OrderStatus::query()->updateOrCreate([
                'order_uuid' => $order->uuid,
            ], [
                'status' => Order::ACCEPT

            ]);
            $this->sendNotification($order->uuid, Order::class, Auth::id(), $order->user_uuid, Notification::ACCEPT_ORDER, User::USER, User::USER);
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

            $this->sendNotification($order->uuid, Order::class, Auth::id(), $order->content->user_uuid, Notification::RECEIVE_ORDER, User::USER, User::USER, $order);
            $this->sendNotification($order->uuid, Reviews::class, $order->content->user_uuid, $order->user_uuid, Notification::REVIEW_ORDER, User::USER, User::USER, $order);

        } else {
            return mainResponse(false, 'err', [], [], 403);

        }

        return mainResponse(true, 'ok', [], []);

    }

    public function cancelStatusOrder($uuid)
    {
        $order = Order::query()->findOrFail($uuid);
        if ($order->user_uuid == Auth::id() && $order->content->user_uuid == Auth::id()) {
            $order->update([
                'status' => Order::REJECT
            ]);
            OrderStatus::query()->updateOrCreate([
                'order_uuid' => $order->uuid,
            ], [
                'status' => Order::REJECT

            ]);
            $this->sendNotification($order->uuid, Order::class, Auth::id(), $order->user_uuid, Notification::REJECT_ORDER, User::USER, User::USER);

        } else {
            return mainResponse(false, 'err', [], [], 403);

        }

        return mainResponse(true, 'ok', [], []);

    }

    public function job()
    {
        $orders = Order::query()
            ->whereNotNull('start')
            ->where('status', Order::ACCEPT)
            ->where('end', '<', date('y-m-d'))
            ->get();

        foreach ($orders as $order) {
            $order->update([
                'status' => Order::REJECT
            ]);
            if ($order->content) {
                $this->sendNotification($order->uuid, Order::class, @$order->content->user_uuid, $order->user_uuid, Notification::COMPLETE_ORDER, User::USER, User::USER, $order);

                $this->sendNotification($order->uuid, Order::class, $order->user_uuid, @$order->content->user_uuid, Notification::COMPLETE_ORDER, User::USER, User::USER, $order);
                $this->sendNotification($order->uuid, Reviews::class, @$order->content->user_uuid, $order->user_uuid, Notification::REVIEW_ORDER, User::USER, User::USER, $order);

            }

        }
        return mainResponse(true, 'ok', [], []);

    }

}


