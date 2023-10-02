<?php

namespace App\Http\Controllers\Admin\PaymentGateway;


use App\Http\Controllers\Controller;
use App\Models\BillService;
use App\Models\BookingDay;
use App\Models\Cart;
use App\Models\FcmToken;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\Order;
use App\Models\OrderConversation;
use App\Models\PackageUser;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\Upload;
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
    function __construct()
    {
        $this->middleware('permission:payment', ['only' => ['index', 'store', 'create', 'destroy', 'edit', 'update']]);
    }

    public function index()
    {
        return view('admin.paymentGateways.index');
    }

    public function update(Request $request)
    {
        $method = PaymentGateway::findOrFail($request->uuid);

//        Gate::authorize('place.update');
        $rules = [];
        foreach (locales() as $key => $language) {
            $rules['name_' . $key] = 'required|string|max:255';
        }
        $rules['image'] = 'nullable|image';
        $this->validate($request, $rules);
        $data = [];
        foreach (locales() as $key => $language) {
            $data['name'][$key] = $request->get('name_' . $key);
        }
        $method->update($data);
        if ($request->hasFile('image')) {
            UploadImage($request->image, "upload/payment/", PaymentGateway::class, $method->id, true, null, Upload::IMAGE);
        }

        return response()->json([
            'item_edited'
        ]);

    }

    public function getData(Request $request)
    {
        $countrys = PaymentGateway::query()->withoutGlobalScope('status')->orderByDesc('created_at');
        return Datatables::of($countrys)
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->id . '" ';
                $data_attr .= 'data-image="' . $que->image . '" ';
                foreach (locales() as $key => $value) {
                    $data_attr .= 'data-name_' . $key . '="' . $que->getTranslation('name', $key) . '" ';
                }

                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary btn_edit" data-toggle="modal"
                    data-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                return $string;
            })->addColumn('checkbox', function ($que) {
                return $que->id;
            })->addColumn('status', function ($que) {
                $currentUrl = url('/');
                if ($que->status == 1) {
                    $data = '
<button type="button"  data-url="' . $currentUrl . "/admin/paymentGateways/updateStatus/0/" . $que->id . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('active') . '</button>
                    ';
                } else {
                    $data = '
<button type="button"  data-url="' . $currentUrl . "/admin/paymentGateways/updateStatus/1/" . $que->id . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-uuid="' . $que->uuid .
                        '">' . __('inactive') . '</button>
                    ';
                }
                return $data;
            })
            ->rawColumns(['action', 'status'])->toJson();
    }

    public function UpdateStatus($status, $sup)
    {
        $uuids = explode(',', $sup);

        PaymentGateway::query()->withoutGlobalScope('status')
            ->whereIn('id', $uuids)
            ->update([
                'status' => $status
            ]);
        return response()->json([
            'item_edited'
        ]);
    }

    public function checkout($uuid)
    {
        $payment = Payment::query()->find($uuid);

        return view('admin.paymentGateways.payment', compact('payment'));
    }


    public function pay(Request $request, $uuid)
    {
        $payment = Payment::query()->where('transaction_id', $request->id)->first();
        if (!$payment) {
            return redirect()->route('paymentGateways.status', $payment->status);
        }
        if ($payment->status != Payment::PENDING) {
            return redirect()->route('paymentGateways.status', $payment->status);
        }

        $resourcePath = $request->resourcePath;
        $url = Payment::PAYMENT_BASE_URL . "/$resourcePath";
        $entityId = Payment::PAYMENT_ENTITY_ID_DEFAULT;
        if ($payment->payment_method_id == PaymentGateway::MADA) {
            $entityId = Payment::PAYMENT_ENTITY_ID_MADA;
        } elseif ($payment->payment_method_id == PaymentGateway::APPLE_PAY) {
            $entityId = Payment::PAYMENT_ENTITY_ID_APPLE_PAY;
        }
        $url .= "?entityId=$entityId";

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Authorization:Bearer ' . Payment::PAYMENT_TOKEN));
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, Payment::PAYMENT_IS_LIVE);// this should be set to true in production
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $responseData = curl_exec($ch);
        if (curl_errno($ch)) {
            $payment->update([
                'status' => Payment::UN_COMPLETED
            ]);
            curl_close($ch);
            return redirect()->route('paymentGateways.status', $payment->status);
        }
        $responseData = json_decode($responseData, true);

        if ($responseData['result']['code'] == '000.100.110') {
//return $payment->status;
            if ($payment->package_uuid) {
                User::query()->where('uuid', $payment->user_uuid)->update([
                    'package_uuid' => $payment->package_uuid
                ]);
//                PackageUser::query()->where('user_uuid',$payment->user_uuid)->update([
//                    'status' => false
//                ]);
                PackageUser::query()->create([
                    'package_uuid' => $payment->package_uuid,
                    'user_uuid' => $payment->user_uuid,
                ]);
                $payment->update([
                    'status' => Payment::COMPLETE
                ]);
                return redirect()->route('paymentGateways.status', $payment->status);
            }

            $orders = Order::query()
                ->where('order_number', $payment->order_number)
                ->withoutGlobalScope('status')
                ->get();
            foreach ($orders as $item) {
                $this->sendNotification($item->uuid, Order::class, $item->user_uuid, $item->content->user_uuid, Notification::NEW_OFFER, User::USER, User::USER);




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
            if ($orders[0]->content_type == Order::SERVICE) {
                $bill = BillService::query()->where('payment_uuid',$payment->uuid)->first();
                $bill->update([
                    'status' => BillService::ACCEPT
                ]);
            }else{
                OrderConversation::query()->create([
                    'customer_uuid' => $item->user_uuid,
//            'service_uuid' => $service_uuid,
                    'owner_uuid' => $item->content->user_uuid,
                    'order_number' => $item->order_number
                ]);
            }

            Cart::query()
                ->whereIn('content_uuid', $content_uuid)
                ->where('user_uuid', $payment->user_uuid)
                ->delete();
            $payment->update([
                'status' => Payment::COMPLETE
            ]);
            return redirect()->route('paymentGateways.status', $payment->status);

        } else {

            $payment->update([
                'status' => Payment::FAILED
            ]);
            return redirect()->route('paymentGateways.status', $payment->status);
        }
    }

    public function status($status)
    {
        return $status;
    }

}
