<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProfileResource;
use App\Models\Busines;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function profile()
    {
        $user = Auth::guard('sanctum')->user();
        return mainResponse(true, 'ok', new profileResource($user), []);
    }

    public function updateProfile(Request $request)
    {
        $rules = [
            'personal_photo' => 'required',
            'cover_Photo' => 'required',
            'video' => 'required',
            'skills' => 'required',
            'brief' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'address' => 'required',
            'specialization_uuid' => 'required|exists:specializations,uuid',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
        $user->update($request->only('brief', 'lat', 'lng', 'specialization_uuid', 'address'));
        UploadImage($request->personal_photo, "upload/user/personal", User::class, $user->uuid, true, null, Upload::IMAGE, 'personal_photo');
        UploadImage($request->cover_Photo, "upload/user/cover", User::class, $user->uuid, true, null, Upload::IMAGE, 'cover_photo');

        if ($request->has('video')) {
            UploadImage($request->video, "upload/user/video", User::class, $user->uuid, true, null, Upload::VIDEO);
        }
        $user->skills()->sync($request->skills);
        return mainResponse(true, "done", $user, [], 201);

    }

    public function addBusiness(Request $request)
    {
        if (isset($request->type) && $request->type == "video" || $request->type == "images") {
            if ($request->type == "images") {
                $rules = [
                    'images' => 'required',
                ];
            }
            if ($request->type == "video") {
                $rules['video'] = 'required';
                $rules['title'] = 'required|string|max:100';
                $rules['image'] = 'required|image';
                $rules['time'] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
            }
            $user = Auth::guard('sanctum')->user();

            $request->merge([
                'user_uuid' => $user->uuid
            ]);
            $busines = Busines::query()->create($request->only('title', 'user_uuid', 'type','time'));
            if ($request->hasFile('video')) {
                UploadImage($request->video, "upload/business/video", Busines::class, $busines->uuid, false, null, Upload::VIDEO);
            }
            if ($request->hasFile('image')) {
                UploadImage($request->image, "upload/business/image", Busines::class, $busines->uuid, false, null, Upload::IMAGE, 'one'); // one يعني انو هذه الصورة تابعة لمعرض الاعمال الي من نوع الفيديوهات
            }
            if ($request->hasFile('images')) {
                foreach ($request->images as $item){
                    UploadImage($item, "upload/business/images", Busines::class, $busines->uuid, false, null, Upload::IMAGE); // one يعني انو هذه الصورة تابعة لمعرض الاعمال الي من نوع الفيديوهات

                }
            }
            return mainResponse(true, 'done', $busines, [], 101);

        } else {
            return mainResponse(false, 'type is empty', [], ['type is empty'], 101);

        }
    }

//    public function getBusiness($type){
//
//    }

}
