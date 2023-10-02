<?php

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Http\Resources\ConversationsOrderResource;
use App\Http\Resources\ConversationsResource;
use App\Models\BillService;
use App\Models\Chat;
use App\Models\ChatOrder;
use App\Models\Conversation;
use App\Models\Notification;
use App\Models\Order;
use App\Models\OrderConversation;
use App\Models\Serving;
use App\Models\Upload;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

class OrderServiceController extends Controller
{
    public function addOffer(Request $request, $service_uuid)
    {
        $rules = [
            'message' => 'required|string|min:10',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $service = Serving::query()->findOrFail($service_uuid);

        $owner_uuid = $service->user_uuid;
        $customer = Auth::guard('sanctum')->user();

        $check = OrderConversation::query()
            ->where('customer_uuid', $customer->uuid)
            ->where('service_uuid', $service_uuid)
            ->where('owner_uuid', $owner_uuid)
            ->exists();
        if ($check) {
            return mainResponse(false, __('You have submitted an offer before'), [], [], 101);
        }
        $user = Auth::guard('sanctum')->user();

        $data = [
            'order_number' => Carbon::now()->timestamp . '' . rand(1000, 9999),
            'commission' => $service->price * doubleval($user->commission),
            'user_uuid' => $user->uuid,
            'content_type' => Order::SERVICE,
            'content_uuid' => $service->uuid,
            'price' => $service->price,
            'payment_method_id' => 3,
        ];
        $order = Order::query()->create($data);
        $conversation = OrderConversation::query()->create([
            'customer_uuid' => $customer->uuid,
//            'service_uuid' => $service_uuid,
            'owner_uuid' => $owner_uuid,
            'order_number' => $order->order_number
        ]);
        ChatOrder::query()->create([
            'user_uuid' => $customer->uuid,
            'order_conversation_uuid' => $conversation->uuid,
            'type' => ChatOrder::OFFER
        ]);
        ChatOrder::query()->create([
            'message' => $request->message,
            'user_uuid' => $customer->uuid,
            'order_conversation_uuid' => $conversation->uuid,
            'type' => ChatOrder::TEXT
        ]);
        $uuids = [$owner_uuid];
//       notfication($uuids,$customer,Notification::NEW_OFFER,'has submitted a new offer',$customer->name,null);
        return mainResponse(true, __('The offer has been submitted successfully'), [], [], 101);

    }


    public function sendMsg(Request $request)
    {

        $rules = [
            'order_conversation_uuid' => 'required|exists:order_conversations,uuid',
            'type' => 'required|in:1,2,3,4,5',
            'body' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();

        if ($request->type == ChatOrder::TEXT) {
            if ($request->has('body')) {

                $msg = ChatOrder::create([
                    'user_uuid' => $user->uuid,
                    'message' => $request->body,
                    'type' => $request->type,
                    'order_conversation_uuid' => $request->order_conversation_uuid
                ]);
            } else {
                return mainResponse(false, 'msg not found', [], [], 101);
            }
        } elseif ($request->type == ChatOrder::IMAGE) {
            if ($request->hasFile('body')) {
                $msg = ChatOrder::create([
                    'user_uuid' => $user->uuid,
                    'type' => $request->type,
                    'order_conversation_uuid' => $request->order_conversation_uuid
                ]);
                UploadImage($request->body, ChatOrder::PATH_IMAGE, ChatOrder::class, $msg->uuid, false, null, Upload::IMAGE);
            } else {
                return mainResponse(false, 'image not fount', [], [], 101);
            }
        } elseif ($request->type == ChatOrder::VOICE) {
            if ($request->hasFile('body')) {
                $msg = ChatOrder::create([
                    'user_uuid' => $user->uuid,
                    'type' => $request->type,
                    'order_conversation_uuid' => $request->order_conversation_uuid
                ]);
                UploadImage($request->body, ChatOrder::PATH_VOICE, ChatOrder::class, $msg->uuid, false, null, Upload::VOICE);

            } else {
                return mainResponse(false, 'voice not found', [], [], 101);

            }
        } elseif ($request->type == ChatOrder::LOCATION) {
            $request->merge([
                'lat_lng' => $request->body,
            ]);
            $latLng = explode(',', $request->body);
            if (count($latLng) == 2) {
                if (intval($latLng[0]) && intval($latLng[1])) {
                    $msg = ChatOrder::create([
                        'user_uuid' => $user->uuid,
                        'type' => $request->type,
                        'order_conversation_uuid' => $request->order_conversation_uuid,
                        'lat_lng' => $request->body,
                    ]);
                } else {
                    return mainResponse(false, 'Lat lng is invalid', [], [], 101);
                }
            } else {
                return mainResponse(false, 'Lat lng is invalid', [], [], 101);
            }


        } elseif ($request->type == ChatOrder::ATTACHMENT) {
            if ($request->hasFile('body')) {
                $msg = ChatOrder::create([
                    'user_uuid' => $user->uuid,
                    'type' => $request->type,
                    'order_conversation_uuid' => $request->order_conversation_uuid,
                ]);
                UploadImage($request->body, ChatOrder::PATH_ATTACHMENT, ChatOrder::class, $msg->uuid, false, null, Upload::ATTACHMENT);


            } else {
                return mainResponse(false, 'attachment not found', [], [], 101);
            }
        }
        if ($msg) {
//            event(new \App\Events\ChatOrderEvent($msg->content, $user->uuid, $request->order_conversation_uuid));

            return mainResponse(true, 'ok', [], []);
        }
        return mainResponse(false, 'error', [], []);

    }

    public function chat(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $conversation = OrderConversation::query()->findOrFail($request->order_conversation_uuid);
        $order = Order::query()->withoutGlobalScope('status')->where('order_number',$conversation->order_number)->first();
        $service_uuid=$order->content->uuid;
        $chat = $conversation->chat()
            ->orderBy('created_at')->paginate(100);

        $chat = ChatResource::collection($chat);

        return mainResponse(true, 'ok', compact('service_uuid','chat'), []);
    }

    public function storeOffer(Request $request)
    {
        $rules = [
            'order_conversation_uuid' => 'required|exists:order_conversations,uuid',
            'from' => 'required|date|after:yesterday',
            'to' => 'required|date_format:"Y-m-d"|after:' . $request->from,
            'price' => 'required|int',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $bill = BillService::query()->create([
            'order_conversation_uuid' => $request->order_conversation_uuid,
            'from' => $request->from,
            'to' => $request->to,
            'price' => $request->price
        ]);
        $msg = ChatOrder::create([
            'user_uuid' => \auth('sanctum')->id(),
            'type' => ChatOrder::BILL,
            'order_conversation_uuid' => $request->order_conversation_uuid,
            'bill_service_uuid' => $bill->uuid,

        ]);
        return mainResponse(true, 'ok', [], []);

//        event(new \App\Events\ChatOrderEvent($msg->content, $user->uuid, $request->order_conversation_uuid));

    }

    public function reject(Request $request)
    {
        $bill = BillService::query()->findOrFail($request->uuid)->update([
            'status' => BillService::REJECT,
        ]);
        return mainResponse(true, 'ok', [], []);
    }


}

