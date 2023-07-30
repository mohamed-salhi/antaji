<?php

namespace App\Http\Controllers\Admin\PaymentGateway;


use App\Http\Controllers\Controller;
use App\Models\BookingDay;
use App\Models\Cart;
use App\Models\FcmToken;
use App\Models\NotificationUser;
use App\Models\Order;
use App\Models\PackageUser;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;

class PaymentGatewayController extends Controller
{
//    use ResponseTrait;
//    function __construct()
//    {
//        $this->middleware('permission:paymentGateway-list|paymentGateway-edit', ['only' => ['index','activate']]);
//        $this->middleware('permission:paymentGateway-edit', ['only' => ['activate']]);
//    }
    public function index()
    {
        return view('admin.paymentGateways.index');
    }

    public function getData(Request $request)
    {
        $countrys = PaymentGateway::query()->withoutGlobalScope('gateway')->orderByDesc('created_at');
        return Datatables::of($countrys)
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->uuid . '" ';
                $data_attr .= 'data-image="' . $que->image . '" ';
                foreach (locales() as $key => $value) {
                    $data_attr .= 'data-name_' . $key . '="' . $que->getTranslation('name', $key) . '" ';
                }
                $string = '';
                return $string;
            })->addColumn('status', function ($que) {
                $currentUrl = url('/');
                return '<div class="checkbox">
                <input class="activate-row"  url="' . $currentUrl . "/admin/paymentGateways/activate/" . $que->uuid . '" type="checkbox" id="checkbox' . $que->id . '" ' .
                    ($que->status ? 'checked' : '')
                    . '>
                <label for="checkbox' . $que->uuid . '"><span class="checkbox-icon"></span> </label>
            </div>';
            })
            ->rawColumns(['action', 'status'])->toJson();
    }

    public function activate($uuid)
    {

        $activate = PaymentGateway::withoutGlobalScope('gateway')->findOrFail($uuid);
        $activate->status = !$activate->status;
        if (isset($activate) && $activate->save()) {
            return $this->sendResponse(null, __('item_edited'));
        } else {
            return $this->sendResponse('error', null);
        }
    }

    public function checkout($uuid)
    {
        $payment = Payment::query()->find($uuid);

        return view('admin.paymentGateways.payment', compact('payment'));
    }


    public function pay(Request $request, $uuid)
    {
        $payment = Payment::query()->where('transaction_id', $request->id)->where('status',Payment::COMPLETE)->exists();
        if ($payment) {
            return 'finished';

        }

        $resourcePath = $request->resourcePath;
        $url = "https://eu-test.oppwa.com/$resourcePath";
        $url .= "?entityId=8a8294174b7ecb28014b9699220015ca";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer OGE4Mjk0MTc0YjdlY2IyODAxNGI5Njk5MjIwMDE1Y2N8c3k2S0pzVDg='));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            return curl_error($ch);
        }
        curl_close($ch);
        $responseData = json_decode($responseData, true);

        if ($responseData['result']['code'] == '000.100.110') {

            $payment = Payment::query()->where('transaction_id', $request->id)->first();
            if ($payment->package_uuid) {
                User::query()->where('uuid',$payment->user_uuid)->update([
                    'package_uuid' => $payment->package_uuid
                ]);
//                PackageUser::query()->where('user_uuid',$payment->user_uuid)->update([
//                    'status' => false
//                ]);
//                PackageUser::query()->create([
//                    'package_uuid' => $payment->package_uuid,
//                    'user_uuid' => $payment->user_uuid,
//                ]);
                $payment->update([
                    'status' => Payment::COMPLETE
                ]);
                return 'Payment Done';
            }

            $orders = Order::query()
                ->where('order_number', $payment->order_number)
                ->withoutGlobalScope('status')
                ->get();
            foreach ($orders as $item) {
                $ios_tokens = FcmToken::query()
                    ->where("user_uuid", $item->content->user->uuid)
                    ->where('fcm_device', 'ios')
                    ->pluck('fcm_token')->toArray();
                $android_tokens = FcmToken::query()
                    ->where("user_uuid", $item->content->user->uuid)
                    ->where('fcm_device', 'android')
                    ->pluck('fcm_token')->toArray();
                $msg = [$item->content->name . __('There is a new order')];
                NotificationUser::query()->create([
                    'receiver_uuid' => $item->content->user_uuid,
                    'sender_uuid' => $item->user_uuid,
                    'content' => ['en' => $item->content->name . 'There is a new order', 'ar' => $item->content->name . 'هناك طلب جديد'],
                    'type' => ('There_is_a_new_order')
                ]);
                if ($ios_tokens) {
                    sendFCM($msg, $ios_tokens, "ios");
                }
                if ($android_tokens) {
                    sendFCM($msg, $android_tokens, "android");
                }


                if (isset($item->start) && isset($item->end)) {
                    $startDate = Carbon::parse($item->start);
                    $endDate = Carbon::parse($item->end);
                    $dates = [];
                    // قم بإضافة كل يوم بين التاريخين إلى المصفوفة
                    for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                        $dates[] = $date->toDateString();
                        BookingDay::query()->create([
                            'date' => $date->toDateString(),
                            'user_uuid' => $payment->user_uuid,
                            'content_uuid' => $item->content_uuid,
                        ]);
                    }
                }
            }

            if ($orders[0]->content_type == 'course') {
                Order::query()
                    ->where('order_number', $payment->order_number)
                    ->withoutGlobalScope('status')
                    ->update([
                        'status' => Order::BUGING_SUCCEEDED
                    ]);
            } else {
                Order::query()
                    ->where('order_number', $payment->order_number)
                    ->withoutGlobalScope('status')
                    ->update([
                        'status' => Order::PENDING
                    ]);
            }
            $content_uuid = Order::query()->where('order_number', $payment->order_number)->withoutGlobalScope('status')->pluck('content_uuid');
            Cart::query()
                ->whereIn('content_uuid', $content_uuid)
                ->where('user_uuid', $payment->user_uuid)
                ->delete();
            $payment->update([
                'status' => Payment::COMPLETE
            ]);
            return 'Payment Done';

        } else {
            return 'Payment false';

        }
    }
}
