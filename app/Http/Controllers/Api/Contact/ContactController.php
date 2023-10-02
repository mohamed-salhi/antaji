<?php

namespace App\Http\Controllers\Api\Contact;

use App\Http\Controllers\Controller;
use App\Http\Resources\MessageResource;
use App\Models\Contact;
use App\Models\Message;
use App\Models\Social;
use App\Models\Upload;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function contactUs(Request $request)
    {
        $social_medias = Social::query()->select('uuid', 'link')->get();

        return mainResponse(true, 'ok', compact('social_medias'), []);
    }

    public function contact(Request $request)
    {
        $rules = [
            'name' => 'required|max:100',
            'email' => 'required|email',
            'description' => 'required|string',
            'images' => 'nullable|array',
            'images.*' => 'required|image',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
//        $user = Auth::guard('sanctum')->user();
//        $request->merge([
//            'user_uuid'=>$user->uuid
//        ]);
        $contact = Contact::create($request->only('name', 'description', 'email'));
        if ($request->hasFile('images')) {
            foreach ($request->images as $item) {
                UploadImage($item, "upload/contact/", Contact::class, $contact->uuid, false, null, Upload::IMAGE);
            }
        }

        if ($contact) {
//            $not= NotificationAdmin::query()->create([
//                'not_uuid'=>$contact->uuid,
//                'type'=>'help-list',
//                'content'=>['en'=>__('You have a message of help from '.$user->name),'ar'=> $user->name.'لديك رسالة مساعدة من '],
//            ]);
//            event(new NotificationAdminEvent('help-list',__('You have a message of help from '.$user->name),null,route('helps.index')."?uuid=".$not->uuid));
            return mainResponse(true, 'ok', [], []);
        }
        return mainResponse(false, 'error', [], []);

    }

    public function message(Request $request)
    {
        $rules = [
            'type' => 'required|in:1,2,3,4,5',
            'body' => 'required',
        ];

        if ($request->type == Message::TEXT) {
            $rules['body'] = 'required|string|max:100';
        } elseif ($request->type == Message::IMAGE) {
            $rules['body'] = 'required|image';
        } elseif ($request->type == Message::LOCATION) {
            $rules['body'] = 'required|string';
        }

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }

        $user = Auth::guard('sanctum')->user();
        $request->merge([
            'user_uuid' => $user->uuid,
            'status' => 'user',
            'view_user' => date('Y-m-d H:i:s')
        ]);

        if ($request->type == Message::TEXT) {
            $request->merge([
                'message' => $request->body,
            ]);

            $msg = Message::create($request->only('message', 'user_uuid', 'status', 'type'));
        } elseif ($request->type == Message::IMAGE) {
            if ($request->hasFile('body')) {
                $msg = Message::create($request->only('user_uuid', 'status', 'type'));
                UploadImage($request->body, Message::PATH_IMAGE, Message::class, $msg->uuid, false, null, Upload::IMAGE);
            } else {
                return mainResponse(false, 'Image not fount', [], [], 101);
            }
        } elseif ($request->type == Message::VOICE) {
            if ($request->hasFile('body')) {
                $msg = Message::create($request->only('user_uuid', 'status', 'type'));
                UploadImage($request->body, Message::PATH_VOICE, Message::class, $msg->uuid, false, null, Upload::VOICE);
            } else {
                return mainResponse(false, 'Voice not fount', [], [], 101);

            }
        } elseif ($request->type == Message::LOCATION) {
            $request->merge([
                'lat_lng' => $request->body,
            ]);
            $latLng = explode(',', $request->body);
            if (count($latLng) == 2) {
                if (intval($latLng[0]) && intval($latLng[1])) {
                    $msg = Message::create($request->only('lat_lng', 'user_uuid', 'status', 'type'));
                } else {
                    return mainResponse(false, 'Lat lng is invalid', [], [], 101);
                }
            } else {
                return mainResponse(false, 'Lat lng is invalid', [], [], 101);
            }
        } elseif ($request->type == Message::ATTACHMENT) {
            if ($request->hasFile('body')) {
                $msg = Message::create($request->only('user_uuid', 'status', 'type'));
                UploadImage($request->body, Message::PATH_ATTACHMENT, Message::class, $msg->uuid, false, null, Upload::ATTACHMENT);
            } else {
                return mainResponse(false, 'Attachment not fount', [], [], 101);
            }
        }
        event(new \App\Events\Msg($msg->content, $user->name, "user", $user->uuid, $user->image, $request->type, $msg->created_at,$msg->type_text));
        if ($msg) {
            return mainResponse(true, 'ok', [], []);
        }
        return mainResponse(false, 'error', [], []);
    }

    public function messages()
    {
        $user = Auth::guard('sanctum')->user();
        $messages = Message::query()->where('user_uuid', $user->uuid)->orderByDesc('created_at')->paginate(100);
        $messages = pageResource($messages, MessageResource::class);
        $items = [];
        foreach ($messages as $i => $message) {
            if ($i == 0) {
                if (Carbon::parse($message->created_at)->isCurrentDay()) {
                    $items[] = ['type' => 0, 'time' => 'Today'];
                } else {
                    $items[] = ['type' => 0, 'time' => Carbon::parse($message->created_at)->format('Y-m-d')];
                }
            } else {
                if (!Carbon::parse($message->created_at)->isSameDay(Carbon::parse($messages[$i - 1]->created_at))) {
                    if (Carbon::parse($message->created_at)->isCurrentDay()) {
                        $items[] = ['type' => 0, 'time' => 'Today'];
                    } else {
                        $items[] = ['type' => 0, 'time' => Carbon::parse($message->created_at)->format('Y-m-d')];
                    }
                }
            }
            $items[] = $message;
        }

        Message::query()->where('user_uuid', $user->uuid)->whereNull('view_user')->where('status', 'admin')->update([
            'view_user' => date('Y-m-d H:i:s')
        ]);

        return mainResponse(true, __('ok'), compact('items'), []);
    }
}
