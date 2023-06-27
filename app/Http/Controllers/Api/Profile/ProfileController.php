<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\acountSetting;
use App\Http\Resources\ProductHomeResource;
use App\Http\Resources\profileArtistResource;
use App\Http\Resources\profileUserResource;
use App\Models\Busines;
use App\Models\Businessimages;
use App\Models\BusinessVideo;
use App\Models\Product;
use App\Models\Skill;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;


class ProfileController extends Controller
{
    public function profile()
    {
        $user = Auth::guard('sanctum')->user();
        if ($user->type == 'artist') {
            return mainResponse(true, 'ok', new profileArtistResource($user), []);
        }
        return mainResponse(true, 'ok', new profileUserResource($user), []);

    }
    public function accountSettingsGet()
    {
        $user = Auth::guard('sanctum')->user();
        return mainResponse(true, "done", acountSetting::collection($user->select('uuid', 'name', 'email', 'mobile', 'country_uuid', 'city_uuid')->get()), [], 201);
    }

    public function updateAccountSetting(Request $request)
    {
        $user = Auth::guard('sanctum')->user();

        $rules = [
            'name' => 'required',
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->uuid, 'uuid')
            ],
            'mobile' => [
                'required',
                'max:12',
                Rule::unique('users', 'mobile')->ignore($user->uuid, 'uuid')
            ],
            'country_uuid' => 'required|exists:countries,uuid',
            'city_uuid' => 'required|exists:cities,uuid',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user->update($request->only('name', 'email', 'mobile', 'country_uuid', 'city_uuid'));
        if ($user) {
            return mainResponse(true, "done", $user, [], 201);

        } else {
            return mainResponse(false, 'حصل خطا ما', [], ['حصل خطا ما'], 500);

        }

    }

    public function updateProfile(Request $request)
    {
        $rules = [
            'personal_photo' => 'required',
            'cover_Photo' => 'required',
            'video' => 'required',
            'brief' => 'required',
            'lat' => 'required',
            'lng' => 'required',
            'address' => 'required',
        ];
        $user = Auth::guard('sanctum')->user();
        if ($user->type == 'artist') {
            $rules['skills'] = 'required';
            $rules['specialization_uuid'] = 'required|exists:specializations,uuid';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        if ($user->type == 'artist') {
            $user->update($request->only('brief', 'lat', 'lng', 'specialization_uuid', 'address'));
            $user->skills()->sync($request->skills);
        }else{
            $user->update($request->only('brief', 'lat', 'lng', 'address'));
        }
        UploadImage($request->personal_photo, "upload/user/personal", User::class, $user->uuid, true, null, Upload::IMAGE, 'personal_photo');
        UploadImage($request->cover_Photo, "upload/user/cover", User::class, $user->uuid, true, null, Upload::IMAGE, 'cover_photo');

        if ($request->has('video')) {
            UploadImage($request->video, "upload/user/video", User::class, $user->uuid, true, null, Upload::VIDEO);
        }
        return mainResponse(true, "done", $user, [], 201);

    }

    public function addBusinessVideo(Request $request)
    {
        $rules['video'] = 'required';
        $rules['title'] = 'required|string|max:100';
        $rules['image'] = 'required|image';


        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();

        $request->merge([
            'user_uuid' => $user->uuid
        ]);
        $busines = BusinessVideo::query()->create($request->only('title', 'user_uuid'));
        if ($request->hasFile('image')) {
            UploadImage($request->image, BusinessVideo::PATH_IMAGE, BusinessVideo::class, $busines->uuid, false, null, Upload::IMAGE);
        }
        if ($request->hasFile('video')) {

            $video = UploadImage($request->video, BusinessVideo::PATH_VIDEO, BusinessVideo::class, $busines->uuid, false, null, Upload::VIDEO);
            $getID3 = new \getID3;
            $video_file = $getID3->analyze('upload/business/video/' . $video->filename);
            $duration_string = $video_file['playtime_string'];
            $busines->time = $duration_string;
            $busines->save();
        }


        return mainResponse(true, 'done', $busines, [], 101);


    }

    public function addBusinessImages(Request $request)
    {
        $rules['images'] = 'required';
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();

        $request->merge([
            'user_uuid' => $user->uuid
        ]);
        $busines = Businessimages::firstOrNew(
            ['user_uuid' => request('user_uuid')],
        );
        $busines->save();
        if ($request->hasFile('images')) {
            foreach ($request->images as $item) {
                UploadImage($item, Businessimages::PATH, Businessimages::class, $busines->uuid, false, null, Upload::IMAGE); // one يعني انو هذه الصورة تابعة لمعرض الاعمال الي من نوع الفيديوهات

            }
        }

        return mainResponse(true, 'done', $busines, [], 101);


    }

    public function deleteBusinessImage($image)
    {
        $attachment = Upload::query()->find($image);
        if ($attachment) {
            File::delete(public_path(Businessimages::PATH . @$attachment->filename));
            $attachment->delete();
            return mainResponse(true, 'done', [], [], 101);

        } else {
            return mainResponse(false, "image not found", [], ["image not found"], 101);
        }
    }

    public function deleteBusinessVideo($Business)
    {
        $businessVideo = BusinessVideo::query()->find($Business);
        if ($businessVideo) {
            File::delete(public_path(BusinessVideo::PATH_IMAGE . @$businessVideo->imageBusiness->filename));
            File::delete(public_path(BusinessVideo::PATH_VIDEO . @$businessVideo->videoBusiness->filename));
            $businessVideo->videoBusiness()->delete();
            $businessVideo->imageBusiness()->delete();
            $businessVideo->delete();
            return mainResponse(true, 'done', [], [], 101);

        } else {
            return mainResponse(false, "Business not found", [], ["Business not found"], 101);
        }
    }

    public function getBusiness($type)
    {
        $user = Auth::guard('sanctum')->user();
        $business = "";
        if ($type == "video") {
            $business = BusinessVideo::query()->where('user_uuid', $user->uuid)->get();
        } elseif ($type == "images") {
            $business = Businessimages::query()->where('user_uuid', $user->uuid)->first();
        } else {
            return mainResponse(false, 'type must video||images', [], ['type must video||images'], 404);
        }
        return mainResponse(true, 'done', $business, [], 200);

    }

    public function getProfile($uuid)
    {
        $user = User::query()->find($uuid);
        if ($user->type == 'artist') {
            return mainResponse(true, 'ok', new profileArtistResource($user), []);
        }
        return mainResponse(true, 'ok', new profileUserResource($user), []);
    }

    public function getBusinessProfile($uuid, $type)
    {
        if ($type == "video") {
            $business = BusinessVideo::query()->where('user_uuid', $uuid)->get();
        } elseif ($type == "images") {
            $business = Businessimages::query()->where('user_uuid', $uuid)->first();
        } else {
            return mainResponse(false, 'type must video||images', [], ['type must video||images'], 404);
        }
        return mainResponse(true, 'done', $business, [], 200);
    }

    public function getProductProfile($uuid, $type)
    {
        if ($type == "sale") {
            $products = Product::query()
                ->where('type', 'sale')
                ->where('user_uuid', $uuid)
                ->get();
        } elseif ($type == "leasing") {
            $products = Product::query()
                ->where('type', 'leasing')
                ->where('user_uuid', $uuid)
                ->get();
        } else {
            return mainResponse(false, 'type must sale||leasing', [], ['type must video||images'], 404);
        }
        $products = ProductHomeResource::collection($products);
        return mainResponse(true, 'done', $products, [], 200);
    }
}
