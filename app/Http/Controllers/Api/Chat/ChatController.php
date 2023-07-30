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
            'conversation_uuid' => 'required|exists:conversations,uuid',
            'msg' => 'nullable|max:100',
            'type' => 'required|in:1,2,3,4,5',
            'lat_lng' => 'nullable',
            'image' => 'nullable|mimes:jpeg,jpg,png|max:2048',
            'attachment' => 'nullable',
            'voice' => 'nullable'
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->Chats(), 101);
        }

        $user = Auth::guard('sanctum')->user();

        if ($request->type == Chat::TEXT) {
            if ($request->has('msg')) {

                $msg = Chat::create([
                    'user_uuid' => $user->uuid,
                    'msg' => $request->msg,
                    'type' => $request->type,
                    'conversation_uuid' => $request->conversation_uuid
                ]);
            } else {
                return mainResponse(false, 'msg not fount', [], [], 101);
            }
        } elseif ($request->type == Chat::IMAGE) {
            if ($request->hasFile('image')) {
                $msg = Chat::create([
                    'user_uuid' => $user->uuid,
                    'type' => $request->type,
                    'conversation_uuid' => $request->conversation_uuid
                ]);
                UploadImage($request->image, Chat::PATH_IMAGE, Chat::class, $msg->uuid, false, null, Upload::IMAGE);
            } else {
                return mainResponse(false, 'image not fount', [], [], 101);
            }
        } elseif ($request->type == Chat::VOICE) {
            if ($request->hasFile('voice')) {
                $msg = Chat::create([
                    'user_uuid' => $user->uuid,
                    'type' => $request->type,
                    'conversation_uuid' => $request->conversation_uuid
                ]);
                UploadImage($request->voice, Chat::PATH_VOICE, Chat::class, $msg->uuid, false, null, Upload::VOICE);

            } else {
                return mainResponse(false, 'image not fount', [], [], 101);

            }
        } elseif ($request->type == Chat::LOCATION) {
            if ($request->has('lat_lng')) {
                $msg = Chat::create([
                    'user_uuid' => $user->uuid,
                    'type' => $request->type,
                    'conversation_uuid' => $request->conversation_uuid,
                    'lat_lng' => $request->lat_lng,
                ]);
            } else {
                return mainResponse(false, 'lat_lng not fount', [], [], 101);
            }
        } elseif ($request->type == Chat::ATTACHMENT) {
            if ($request->hasFile('attachment')) {
                $msg = Chat::create([
                    'user_uuid' => $user->uuid,
                    'type' => $request->type,
                    'conversation_uuid' => $request->conversation_uuid,
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
        $conversation = Conversation::query()
            ->where(function ($q) use ($request, $user) {
                $q->Where('one', $user->uuid)
                    ->orWhere('tow', $request->user_uuid);
            })->orWhere(function ($q) use ($request, $user) {
                $q->Where('tow', $user->uuid)
                    ->orWhere('one', $request->user_uuid);
            })->first();
        if (!$conversation) {
            $conversation = Conversation::query()->create([
                'one' => $user->uuid,
                'tow' => $request->user_uuid
            ]);
        }
        $chat = $conversation->chat()
            ->orderBy('created_at')->paginate(5);
        $chat = ChatResource::collection($chat);


//        $conversation_uuid = $conversation->uuid;
        return mainResponse(true, 'ok', compact('chat'), []);
    }

    public function conversations()
    {
        $user = Auth::guard('sanctum')->user();
        $conversation = Conversation::query()
            ->where('one', $user->uuid)
            ->orWhere('tow', $user->uuid)
            ->get();
        if ($conversation) {
            $req = new Request();
            $req->request->add(['user_uuid', $user->uuid]);
            $conversation = ConversationsResource::collection($conversation);
            event(new \App\Events\Conversation($conversation));
            $conversation = 'done';

        } else {
            $conversation = 'err';
        }
        return mainResponse(true, 'ok', compact('conversation'), []);


    }
}
