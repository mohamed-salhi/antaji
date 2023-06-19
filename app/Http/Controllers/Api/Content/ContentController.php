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
            'category_contents_uuid' => 'required|exists:category_contents,uuid',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
        $request->merge([
            'user_uuid'=>$user->uuid
        ]);
        $location= Location::query()->create($request->only('name','user_uuid','price','details','category_contents_uuid'));
        $content= Content::query()->create([
            'content_uuid'=>$location->uuid,
            'user_uuid'=>$user->uuid,
        ]);
        if ($request->hasFile('images')) {
            foreach ($request->images as $item) {
                UploadImage($item, Location::PATH_LOCATION, Location::class, $location->uuid, false, null, Upload::IMAGE);

            }
        }

        return mainResponse(true, 'done', $location, [], 101);
    }

    public function addServing(Request $request){

        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'category_contents_uuid' => 'required|exists:category_contents,uuid',
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
        $request->merge([
            'user_uuid'=>$user->uuid
        ]);
        $serving= Serving::query()->create($request->only('name','user_uuid','price','details','category_contents_uuid','city_uuid','to','from','working_condition'));
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
        $request->merge([
           'user_uuid'=>$user->uuid
        ]);
        $course= Course::query()->create($request->only('name','price','details','user_uuid'));
        $content= Content::query()->create([
            'content_uuid'=>$course->uuid,
            'user_uuid'=>$user->uuid,
        ]);
        if ($request->hasFile('image')) {
            UploadImage($request->image, Course::PATH_COURSE, Course::class, $course->uuid, false, null, Upload::IMAGE);
        }
        if ($request->hasFile('video')) {
            UploadImage($request->video, Course::PATH_COURSE_VIDEO, Course::class, $course->uuid, false, null, Upload::VIDEO);
        }
        return mainResponse(true, 'done', $course, [], 101);
    }

    public function addProduct(Request $request){

        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'category_uuid' => 'required|exists:categories,uuid',
            'sub_category_uuid' => 'required|exists:sup_categories,uuid',
            'type' => 'required|in:sale,Leasing',

        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
        $request->merge([
            'user_uuid'=>$user->uuid
        ]);
        $product= Product::query()->create($request->only('user_uuid','name','price','details','sub_category_uuid','category_uuid','type'));
        $content= Content::query()->create([
            'content_uuid'=>$product->uuid,
            'user_uuid'=>$user->uuid,
        ]);
        if ($request->hasFile('images')) {
            foreach ($request->images as $item) {
                UploadImage($item, Product::PATH_PRODUCT, Product::class, $product->uuid, false, null, Upload::IMAGE); // one يعني انو هذه الصورة تابعة لمعرض الاعمال الي من نوع الفيديوهات

            }
        }
        return mainResponse(true, 'done', $product, [], 101);
    }
}
