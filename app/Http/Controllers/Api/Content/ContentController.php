<?php

namespace App\Http\Controllers\Api\Content;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Course;
use App\Models\Location;
use App\Models\Product;
use App\Models\Serving;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ContentController extends Controller
{
    public function addLocation(Request $request){

        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'category_uuid' => 'required|exists:categories,uuid',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
        $location= Location::query()->create($request->only('name','price','details','category_uuid'));
        $content= Content::query()->create([
            'content_uuid'=>$location->uuid,
            'user_uuid'=>$user->uuid,
        ]);
        if ($request->hasFile('images')) {
            foreach ($request->images as $item) {
                UploadImage($item, Content::PATH_LOCATION, Content::class, $content->uuid, false, null, Upload::IMAGE);

            }
        }

        return mainResponse(true, 'done', $location, [], 101);
    }

    public function addServing(Request $request){

        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'category_uuid' => 'required|exists:categories,uuid',
            'city_uuid' =>'required|exists:cities,uuid',
            'from' => 'required|date|after:'.date('Y/m/d'),
            'to' => 'required|date|after:'.$request->form,
            'working_condition' => 'required|in:contract,Fixed_price,hour',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
        $serving= Serving::query()->create($request->only('name','price','details','category_uuid','city_uuid','to','from','working_condition'));
        $content= Content::query()->create([
            'content_uuid'=>$serving->uuid,
            'user_uuid'=>$user->uuid,
        ]);
        return mainResponse(true, 'done', $serving, [], 101);
    }

    public function addCourse(Request $request){

        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
        $course= Course::query()->create($request->only('name','price','details'));
        $content= Content::query()->create([
            'content_uuid'=>$course->uuid,
            'user_uuid'=>$user->uuid,
        ]);
        if ($request->hasFile('image')) {
            UploadImage($request->image, Content::PATH_COURSE, Content::class, $content->uuid, false, null, Upload::IMAGE);
        }
        if ($request->hasFile('video')) {
            UploadImage($request->video, Content::PATH_COURSE_VIDEO, Content::class, $content->uuid, false, null, Upload::VIDEO);
        }
        return mainResponse(true, 'done', $course, [], 101);
    }

    public function addProduct(Request $request){

        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'main_category' => 'required|exists:categories,uuid',
            'sub_category' => 'required|exists:categories,uuid',
            'type' => 'required|in:sale,Leasing',

        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
        $product= Product::query()->create($request->only('name','price','details','sub_category','main_category','type'));
        $content= Content::query()->create([
            'content_uuid'=>$product->uuid,
            'user_uuid'=>$user->uuid,
        ]);
        if ($request->hasFile('images')) {
            foreach ($request->images as $item) {
                UploadImage($item, Content::PATH_PRODUCT, Content::class, $content->uuid, false, null, Upload::IMAGE); // one يعني انو هذه الصورة تابعة لمعرض الاعمال الي من نوع الفيديوهات

            }
        }
        return mainResponse(true, 'done', $product, [], 101);
    }
}
