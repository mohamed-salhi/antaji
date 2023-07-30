<?php

namespace App\Http\Controllers\Api\Contact;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Message;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ContactController extends Controller
{
    public function contact(Request $request){
        $rules = [
            'name' => 'required|max:100',
            'email' => 'required|email',
            'description' => 'required',
            'image' => 'required|image',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
//        $user = Auth::guard('sanctum')->user();
//        $request->merge([
//            'user_uuid'=>$user->uuid
//        ]);
        $contact=   Contact::create($request->only('name','description','email'));
        if ($request->hasFile('image')) {
            UploadImage($request->image, "upload/contact/", Contact::class, $contact->uuid, false ,null,Upload::IMAGE);
        }
        if ($contact){
//            $not= NotificationAdmin::query()->create([
//                'not_uuid'=>$contact->uuid,
//                'type'=>'help-list',
//                'content'=>['en'=>__('You have a message of help from '.$user->name),'ar'=> $user->name.'لديك رسالة مساعدة من '],
//            ]);
//            event(new NotificationAdminEvent('help-list',__('You have a message of help from '.$user->name),null,route('helps.index')."?uuid=".$not->uuid));
            return mainResponse(true, 'ok',[], []);
        }
        return mainResponse(false, 'error',[], []);

    }
    public function message(Request $request){
        $rules = [
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
        $request->merge([
            'user_uuid'=>$user->uuid,
            'status'=>'user',
            'view_user'=>date('Y-m-d H:i:s')
        ]);

        if ($request->type==Message::TEXT){
            if ($request->has('message')){
                $msg=   Message::create($request->only('message','user_uuid','status','type'));
            }else{
                return mainResponse(false, 'message not fount', [], [], 101);
            }
        }
        elseif ($request->type==Message::IMAGE){
            if ($request->hasFile('image')){
                $msg=   Message::create($request->only('user_uuid','status','type'));
                UploadImage($request->image, Message::PATH_IMAGE, Message::class, $msg->uuid, false, null, Upload::IMAGE);
            }else{
                return mainResponse(false, 'image not fount', [], [], 101);
            }
        }
        elseif ($request->type==Message::VOICE){
            if ($request->hasFile('voice')){
                $msg=   Message::create($request->only('user_uuid','status','type'));
                UploadImage($request->voice, Message::PATH_VOICE, Message::class, $msg->uuid, false, null, Upload::VOICE);

            }else{
                return mainResponse(false, 'image not fount', [], [], 101);

            }
        }
        elseif ($request->type==Message::LOCATION){
            if ($request->has('lat_lng')){
                $msg=Message::create($request->only('lat_lng','user_uuid','status','type'));
            }else{
                return mainResponse(false, 'lat_lng not fount', [], [], 101);
            }
        }
        elseif ($request->type==Message::ATTACHMENT){
            if ($request->hasFile('attachment')){
                $msg=   Message::create($request->only('user_uuid','status','type'));
                UploadImage($request->attachment, Message::PATH_ATTACHMENT, Message::class, $msg->uuid, false, null, Upload::ATTACHMENT);
            }else{
                return mainResponse(false, 'image not fount', [], [], 101);
            }
        }
        event (new \App\Events\Msg($msg->content,$user->name,"user",$user->uuid,$user->image,$request->type,$msg->created_at));
        if ($msg){
            return mainResponse(true, 'ok',[], []);
        }
        return mainResponse(false, 'error',[], []);

    }
    public function messages(){
        $user = Auth::guard('sanctum')->user();
        $msg=Message::query()->where('user_uuid',$user->uuid)->paginate(5);
        Message::query()->where('user_uuid',$user->uuid)->whereNull('view_user')->where('status','admin')->update([
            'view_user'=>date('Y-m-d H:i:s')
        ]);
        return mainResponse(true, __('ok'), compact('msg'), []);
    }
}
