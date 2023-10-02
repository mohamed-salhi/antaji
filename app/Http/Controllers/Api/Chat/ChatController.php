<?php

namespace App\Http\Controllers\Api\Chat;

use App\Http\Controllers\Controller;
use App\Http\Resources\ChatResource;
use App\Http\Resources\ConversationsResource;
use App\Models\Admin;
use App\Models\Chat;
use App\Models\Conversation;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ChatController extends Controller
{
    public function sendMsg(Request $request)
    {
        $rules = [
            'type' => 'required|in:1,2,3,4,5',
            'body' => 'required',
        ];

        if ($request->type == Chat::TEXT) {
            $rules['body'] = 'required|string|max:100';
        } elseif ($request->type == Chat::IMAGE) {
            $rules['body'] = 'required|image';
        } elseif ($request->type == Chat::LOCATION) {
            $rules['body'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();

        $conversation = Conversation::query()
            ->where(function ($q) use ($request, $user) {
                $q->where('one', $user->uuid)
                    ->where('tow', $request->user_uuid);
            })->orWhere(function ($q) use ($request, $user) {
                $q->where('tow', $user->uuid)
                    ->where('one', $request->user_uuid);
            })->first();
        if ($request->type == Chat::TEXT) {
            if ($request->has('body')) {

                $msg = Chat::create([
                    'user_uuid' => $user->uuid,
                    'msg' => $request->body,
                    'type' => $request->type,
                    'conversation_uuid' => $conversation->uuid
                ]);
            } else {
                return mainResponse(false, 'msg not fount', [], [], 101);
            }
        } elseif ($request->type == Chat::IMAGE) {
            if ($request->hasFile('body')) {
                $msg = Chat::create([
                    'user_uuid' => $user->uuid,
                    'type' => $request->type,
                    'conversation_uuid' => $conversation->uuid
                ]);
                UploadImage($request->image, Chat::PATH_IMAGE, Chat::class, $msg->uuid, false, null, Upload::IMAGE);
            } else {
                return mainResponse(false, 'image not fount', [], [], 101);
            }
        } elseif ($request->type == Chat::VOICE) {
            if ($request->hasFile('body')) {
                $msg = Chat::create([
                    'user_uuid' => $user->uuid,
                    'type' => $request->type,
                    'conversation_uuid' => $conversation->uuid
                ]);
                UploadImage($request->voice, Chat::PATH_VOICE, Chat::class, $msg->uuid, false, null, Upload::VOICE);

            } else {
                return mainResponse(false, 'image not fount', [], [], 101);

            }
        } elseif ($request->type == Chat::LOCATION) {


            $request->merge([
                'lat_lng' => $request->body,
            ]);
            $latLng = explode(',', $request->body);
            if (count($latLng) == 2) {
                if (intval($latLng[0]) && intval($latLng[1])) {
                    $msg = Chat::create([
                        'user_uuid' => $user->uuid,
                        'type' => $request->type,
                        'conversation_uuid' => $conversation->uuid,
                        'lat_lng' => $request->lat_lng,
                    ]);
                } else {
                    return mainResponse(false, 'Lat lng is invalid', [], [], 101);
                }
            } else {
                return mainResponse(false, 'Lat lng is invalid', [], [], 101);
            }

        } elseif ($request->type == Chat::ATTACHMENT) {
            if ($request->hasFile('body')) {
                $msg = Chat::create([
                    'user_uuid' => $user->uuid,
                    'type' => $request->type,
                    'conversation_uuid' => $conversation->uuid
                ]);
                UploadImage($request->attachment, Chat::PATH_ATTACHMENT, Chat::class, $msg->uuid, false, null, Upload::ATTACHMENT);


            } else {
                return mainResponse(false, 'image not fount', [], [], 101);
            }
        }
        if ($msg) {
            event(new \App\Events\Chat($msg->content, $user->uuid, $request->conversation_uuid));

            return mainResponse(true, 'ok', [], []);
        }
        return mainResponse(false, 'error', [], []);

    }

    public function chat(Request $request)
    {

        $user = Auth::guard('sanctum')->user();
//        if ($request->conversation_uuid){
//            $conversation=Conversation::query()->findOrFail($request->conversation_uuid);
//        }else{

        $conversation = Conversation::query()
            ->where(function ($q) use ($request, $user) {
                $q->where('one', $user->uuid)
                    ->where('tow', $request->user_uuid);
            })->orWhere(function ($q) use ($request, $user) {
                $q->where('tow', $user->uuid)
                    ->where('one', $request->user_uuid);
            })->first();
//        }

        if (!$conversation) {
            $conversation = Conversation::query()->create([
                'one' => $user->uuid,
                'tow' => $request->user_uuid
            ]);
        }
        $items = $conversation->chat()
            ->orderBy('created_at')->paginate(5);
        $items = ChatResource::collection($items);


//        $conversation_uuid = $conversation->uuid;
        return mainResponse(true, 'ok', compact('items'), []);
    }

    public function conversations()
    {
        $user = Auth::guard('sanctum')->user();
        $items = Conversation::query()
            ->where('one', $user->uuid)
            ->orWhere('tow', $user->uuid)
            ->has('chat')
            ->get();


        if ($items) {
            $req = new Request();
            $req->request->add(['user_uuid', $user->uuid]);
            $items = ConversationsResource::collection($items);

//            event(new \App\Events\Conversation($conversation));
//            $conversation = 'done';
            return mainResponse(true, 'ok', compact('items'), []);


        } else {
            return mainResponse(true, 'not found', [], []);
        }


    }
}
