<?php

namespace App\Http\Controllers\Api\Contact;

use App\Http\Controllers\Controller;
use App\Models\Contact;
use App\Models\Upload;
use Illuminate\Http\Request;
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
}
