<?php

namespace App\Http\Controllers\Api\Orders;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Models\Chat;
use App\Models\ChatOrder;
use App\Models\Conversation;
use App\Models\Notification;
use App\Models\OrderConversation;
use App\Models\Upload;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Validator;

class OrderServiceController extends Controller
{
   public function addOffer(Request $request,$service_uuid){
       $rules = [
           'message' => 'required|string|min:10',
       ];
       $validator = Validator::make($request->all(), $rules);
       if ($validator->fails()) {
           return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
       }
      $owner_uuid =DB::table('servings')->value('user_uuid');
       $customer= Auth::guard('sanctum')->user();

       $check=OrderConversation::query()
           ->where('customer_uuid',$customer->uuid)
           ->where('service_uuid',$service_uuid)
           ->where('owner_uuid',$owner_uuid)
           ->exists();
       if ($check){
           return mainResponse(false, __('You have submitted an offer before'), [], [], 101);
       }

        $conversation= OrderConversation::query()->create([
             'customer_uuid' =>$customer->uuid,
             'service_uuid' =>$service_uuid,
             'owner_uuid' =>$owner_uuid,
             'order_number'=>Carbon::now()->timestamp . '' . rand(1000, 9999)
         ]);
       ChatOrder::query()->create([
           'user_uuid'=>$customer->uuid,
           'order_conversation_uuid'=>$conversation->uuid,
           'type'=>ChatOrder::OFFER
       ]);
       ChatOrder::query()->create([
           'message'=>$request->message,
           'user_uuid'=>$customer->uuid,
           'order_conversation_uuid'=>$conversation->uuid,
           'type'=>ChatOrder::TEXT
       ]);
      $uuids=[$owner_uuid];
       notfication($uuids,$customer,Notification::NEWOFFER,'has submitted a new offer',$customer->name,null);
       return mainResponse(true, __('The offer has been submitted successfully'), [], [], 101);

   }


    public function sendMsg(Request $request)
    {

        $rules = [
            'order_conversation_uuid' => 'required|exists:order_conversations,uuid',
            'message' => 'nullable|max:100',
            'type' => 'required|in:1,2,3,4,5',
            'lat_lng' => 'nullable',
            'image' => 'nullable|mimes:jpeg,jpg,png|max:2048',
            'attachment' => 'nullable',
            'voice' => 'nullable'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();

        if ($request->type == ChatOrder::TEXT) {
            if ($request->has('message')) {

                $msg = ChatOrder::create([
                    'user_uuid' => $user->uuid,
                    'message' => $request->message,
                    'type' => $request->type,
                    'order_conversation_uuid' => $request->order_conversation_uuid
                ]);
            } else {
                return mainResponse(false, 'msg not found', [], [], 101);
            }
        } elseif ($request->type == ChatOrder::IMAGE) {
            if ($request->hasFile('image')) {
                $msg = ChatOrder::create([
                    'user_uuid' => $user->uuid,
                    'type' => $request->type,
                    'order_conversation_uuid' => $request->order_conversation_uuid
                ]);
                UploadImage($request->image, ChatOrder::PATH_IMAGE, ChatOrder::class, $msg->uuid, false, null, Upload::IMAGE);
            } else {
                return mainResponse(false, 'image not fount', [], [], 101);
            }
        } elseif ($request->type == ChatOrder::VOICE) {
            if ($request->hasFile('voice')) {
                $msg = ChatOrder::create([
                    'user_uuid' => $user->uuid,
                    'type' => $request->type,
                    'order_conversation_uuid' => $request->order_conversation_uuid
                ]);
                UploadImage($request->voice, ChatOrder::PATH_VOICE, ChatOrder::class, $msg->uuid, false, null, Upload::VOICE);

            } else {
                return mainResponse(false, 'voice not found', [], [], 101);

            }
        } elseif ($request->type == ChatOrder::LOCATION) {
            if ($request->has('lat_lng')) {
                $msg = ChatOrder::create([
                    'user_uuid' => $user->uuid,
                    'type' => $request->type,
                    'order_conversation_uuid' => $request->order_conversation_uuid,
                    'lat_lng' => $request->lat_lng,
                ]);
            } else {
                return mainResponse(false, 'lat_lng not found', [], [], 101);
            }
        } elseif ($request->type == ChatOrder::ATTACHMENT) {
            if ($request->hasFile('attachment')) {
                $msg = ChatOrder::create([
                    'user_uuid' => $user->uuid,
                    'type' => $request->type,
                    'order_conversation_uuid' => $request->order_conversation_uuid,
                ]);
                UploadImage($request->attachment, ChatOrder::PATH_ATTACHMENT, ChatOrder::class, $msg->uuid, false, null, Upload::ATTACHMENT);


            } else {
                return mainResponse(false, 'attachment not found', [], [], 101);
            }
        }
        if ($msg) {
            event(new \App\Events\ChatOrderEvent($msg->content, $user->uuid, $request->order_conversation_uuid));

            return mainResponse(true, 'ok', [], []);
        }
        return mainResponse(false, 'error', [], []);

    }

    public function chat(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $conversation = OrderConversation::query()->find($request->order_conversation);

        $chat = $conversation->chat()
            ->orderBy('created_at')->paginate(5);

        $chat = ChatResource::collection($chat);

        return mainResponse(true, 'ok', compact('chat'), []);
    }
}

