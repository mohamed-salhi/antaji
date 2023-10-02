<?php

namespace App\Http\Controllers\Api\Profile;

use App\Http\Controllers\Controller;
use App\Http\Resources\acountSetting;
use App\Http\Resources\BusinessVideoProfileResource;
use App\Http\Resources\BusinessVideoResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\EditBusinessVideoResource;
use App\Http\Resources\ProductHomeResource;
use App\Http\Resources\profileArtistResource;
use App\Http\Resources\profileEditResource;
use App\Http\Resources\profileUserResource;
use App\Models\Busines;
use App\Models\Businessimages;
use App\Models\BusinessVideo;
use App\Models\Course;
use App\Models\Product;
use App\Models\Skill;
use App\Models\Specialization;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use function PHPUnit\Framework\isEmpty;


class ProfileController extends Controller
{
//    public function profile(Request $request, $uuid=null)
//    {
//
//        if (User::query()->where('uuid',$uuid)->exists()){
//            $user=User::query()->find($uuid);
//        }else{
//            $user = Auth::guard('sanctum')->user();
//        }
//        if ($user->type == 'artist') {
//            return mainResponse(true, 'ok', new profileArtistResource($user), []);
//        }
//        return mainResponse(true, 'ok', new profileUserResource($user), []);
//    }
    public function accountSettingsGet()
    {
        $user = Auth::guard('sanctum')->user();
        return mainResponse(true, "done", new acountSetting($user), [], 201);
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
            'country_uuid' => 'required|exists:countries,uuid',
            'city_uuid' => 'required|exists:cities,uuid',
            'id_image' => 'nullable|image',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
if ($user->documentation!=User::ACCEPT) {
    if ($request->hasFile('id_image')) {
        Storage::delete('public/' . @$user->idUserImage->path);

        $user->idUserImage()->delete();

        $user->update([
            'documentation' => User::PENDING
        ]);
        UploadImage($request->id_image, User::PATH_ID, User::class, $user->uuid, false, null, Upload::IMAGE, 'id_image');
    }
}
else{
    return mainResponse(false, __('you are already documentation'), [],[], 300);
}

        $user->update($request->only('name', 'email', 'country_uuid', 'city_uuid'));
        if ($user) {
            return mainResponse(true, "done", [], [], 201);
        } else {
            return mainResponse(false, 'حصل خطا ما', [], ['حصل خطا ما'], 500);
        }

    }

    public function updateProfile(Request $request)
    {
        $rules = [
            'cover_photo' => 'nullable|image',
            'personal_photo' => 'nullable|image',
            'video' => 'nullable',
            'brief' => 'required|string',
            'lat' => 'required',
            'lng' => 'required',
            'address' => 'required|string',
        ];
        $user = Auth::guard('sanctum')->user();
        if ($user->type == User::ARTIST) {
            $rules['skills'] = 'required|array';
            $rules['skills.*'] = 'required|exists:skills,uuid';
            $rules['specialization_uuid'] = 'required|exists:specializations,uuid';
        }
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        if ($user->type == User::ARTIST) {
            $user->update($request->only('brief', 'lat', 'lng', 'specialization_uuid', 'address'));
            $user->skills()->sync($request->skills);
        } else {
            $user->update($request->only('brief', 'lat', 'lng', 'address'));
        }
        if ($request->hasFile('cover_photo')) {
            UploadImage($request->cover_photo, "upload/user/cover", User::class, $user->uuid, true, null, Upload::IMAGE, 'cover_photo');
        }

        if ($request->hasFile('personal_photo')) {
            UploadImage($request->personal_photo, "upload/user/personal", User::class, $user->uuid, true, null, Upload::IMAGE, 'personal_photo');
        }

        if ($request->has('video')) {
            UploadImage($request->video, "upload/user/video", User::class, $user->uuid, true, null, Upload::VIDEO);
        }
        return mainResponse(true, "done", [], [], 201);

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
            $video_file = $getID3->analyze('storage/' . $video->path);

//            $video_file = $getID3->analyze('upload/business/video/' . $video->filename);
            $duration_string = $video_file['playtime_string'];
            $busines->time = $duration_string;
            $busines->save();
        }
        return mainResponse(true, 'done', [], [], 101);
    }

    public function editBusinessVideo($uuid)
    {
        $video = BusinessVideo::query()->select('uuid', 'user_uuid', 'title')->where('uuid', $uuid)->firstOrFail();
        if (\auth('sanctum')->id() == $video->user_uuid) {
            $item = EditBusinessVideoResource::make($video);
            return mainResponse(true, 'done', $item, [], 101);
        } else {
            return mainResponse(false, __('You do not have validity'), [], [], 101);

        }
    }

    public function updateBusinessVideo(Request $request, $uuid)
    {
        $rules['video'] = 'nullable';
        $rules['title'] = 'required|string|max:100';
        $rules['image'] = 'nullable|image';
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
        $business = BusinessVideo::query()->findOrFail($uuid);

        if ($user->uuid == $business->user_uuid) {
            $business->update($request->only('title'));
            if ($request->hasFile('image')) {
                UploadImage($request->image, BusinessVideo::PATH_IMAGE, BusinessVideo::class, $business->uuid, true, $business->imageBusiness->uuid, Upload::IMAGE);
            }
            if ($request->hasFile('video')) {
                $video=  UploadImage($request->video, BusinessVideo::PATH_VIDEO, BusinessVideo::class, $business->uuid, true, $business->videoBusiness->uuid, Upload::VIDEO);
                $getID3 = new \getID3;
                $video_file = $getID3->analyze('storage/' . $video->path);

//                $video_file = $getID3->analyze('upload/business/video/'.$video);
                $duration_string = $video_file['playtime_string'];
                $business->time=$duration_string;
                $business->save();
            }
//            if ($request->hasFile('image')) {
//                File::delete(public_path(BusinessVideo::PATH_IMAGE . @$business->imageBusiness->filename));
//                $imagename = uniqid() . '.' . $request->image->getClientOriginalExtension();
//                $request->image->move(public_path(BusinessVideo::PATH_IMAGE), $imagename);
//                Upload::query()->find($business->imageBusiness->uuid)->update(
//                    [
//                        'filename' => $imagename,
//                    ]
//                );
////                UploadImage($request->image, BusinessVideo::PATH_IMAGE, BusinessVideo::class, $business->uuid, true, null, Upload::IMAGE);
//            }
//            if ($request->hasFile('video')) {
//                UploadImage($request->video, BusinessVideo::PATH_VIDEO, BusinessVideo::class, $business->uuid, true, null, Upload::VIDEO);
//                $getID3 = new \getID3;
//                $video_file = $getID3->analyze('upload/business/video/' . $business->videoBusiness->filename);
//                $duration_string = $video_file['playtime_string'];
//                $business->time = $duration_string;
//                $business->save();
//            }
            return mainResponse(true, 'done', [], [], 101);
        }

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

        return mainResponse(true, 'done', [], [], 101);


    }

    public function deleteBusinessImage($image)
    {
        $attachment = Upload::query()->find($image);
        if ($attachment) {
            Storage::delete('public/' . @$attachment->path);

//            File::delete(public_path(Businessimages::PATH . @$attachment->filename));
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
            Storage::delete('public/' . @$businessVideo->imageBusiness->path);
            Storage::delete('public/' . @$businessVideo->videoBusiness->path);
//
//            File::delete(public_path(BusinessVideo::PATH_IMAGE . @$businessVideo->imageBusiness->filename));
//            File::delete(public_path(BusinessVideo::PATH_VIDEO . @$businessVideo->videoBusiness->filename));
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
        $items = [];
        if ($type == "videos") {
            $items = BusinessVideo::query()->where('user_uuid', $user->uuid)->paginate();
            pageResource($items, BusinessVideoProfileResource::class);
        } elseif ($type == "images") {
            $item = Businessimages::query()->where('user_uuid', $user->uuid)->first();
            if ($item) {
                $items = paginate(collect($item->images));
            }
        } else {
            return mainResponse(false, 'type must videos||images', [], ['type must videos||images'], 404);
        }
        return mainResponse(true, 'done', compact('items'), [], 200);

    }

    public function editProfile()
    {
        $user = Auth::guard('sanctum')->user();
        $user = new profileEditResource($user);

        if ($user->type == User::ARTIST) {
            $specializations = Specialization::all();
            $skills = Skill::all();
            return mainResponse(true, 'ok', $user, []);
        }
        return mainResponse(true, 'ok', $user, []);
    }

    public function editProfileSpecializations()
    {
        $items = Specialization::all();
        return mainResponse(true, 'ok', compact('items'), []);
    }

    public function editProfileSkills()
    {
        $items = Skill::all();
        return mainResponse(true, 'ok', compact('items'), []);
    }

    public function getProfile()
    {
        $user = Auth::guard('sanctum')->user();
        $user = new profileEditResource($user);

        return mainResponse(true, 'ok', $user, []);
    }

    public function getBusinessProfile($user_uuid, $type)
    {

        $items = [];
        if ($type == "videos") {
            $items = BusinessVideo::query()->where('user_uuid', $user_uuid)->paginate();

        } elseif ($type == "images") {

            $items = Businessimages::query()->where('user_uuid', $user_uuid)->first();
            if ($items){
                $items= $items->images;
            }else{
                $items=[];
            }
            $items = paginate($items);
        } else {
            return mainResponse(false, 'type must videos||images', [], ['type must videos||images'], 404);
        }
        return mainResponse(true, 'done', compact('items'), [], 200);
    }

    public function getProductProfile($user_uuid, $type)
    {
        if ($type == Product::SALE) {
            $products = Product::query()
                ->where('type', Product::SALE)
                ->where('user_uuid', $user_uuid)
                ->paginate();
        } elseif ($type == Product::RENT) {
            $products = Product::query()
                ->where('type', Product::RENT)
                ->where('user_uuid', $user_uuid)
                ->paginate();
        } else {
            return mainResponse(false, 'type must sale||leasing', [], ['type must video||images'], 404);
        }
        $items = pageResource($products, ProductHomeResource::class);
        return mainResponse(true, 'done', compact('items'), [], 200);
    }

    public function getCourseProfile($user_uuid)
    {
        $courses = Course::query()->where('user_uuid', $user_uuid)->paginate();
        $items = $courses->getCollection();
        $items = CourseResource::collection($items);
        $courses->setCollection(collect($items));
        $items = $courses;
        return mainResponse(true, 'done', compact('items'), [], 200);

    }

    public function deleteUser()
    {
        $user = Auth::guard('sanctum')->user();
        $user->update([
            'mobile' => $user->mobile . '_delete' . rand(1000, 9999),
            'email' => $user->email . '_delete' . rand(1000, 9999)
        ]);
        $user->fcm_tokens()->delete();
        $user->tokens()->delete();
        $user->delete();
        return mainResponse(true, 'done', [], [], 200);

    }
}
