<?php

namespace App\Http\Controllers\Api\Content;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Http\Resources\LocationResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ServingResource;
use App\Models\CategoryContent;
use App\Models\Content;
use App\Models\Course;
use App\Models\Location;
use App\Models\Product;
use App\Models\Serving;
use App\Models\Specification;
use App\Models\SupCategory;
use App\Models\Upload;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ContentController extends Controller
{
    public function addLocation(Request $request)
    {

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
            'user_uuid' => $user->uuid
        ]);
        $location = Location::query()->create($request->only('name', 'user_uuid', 'price', 'details', 'category_contents_uuid'));

        $content = Content::query()->create([
            'content_uuid' => $location->uuid,
            'user_uuid' => $user->uuid,
        ]);
        if ($request->hasFile('images')) {
            foreach ($request->images as $item) {
                UploadImage($item, Location::PATH_LOCATION, Location::class, $location->uuid, false, null, Upload::IMAGE);

            }
        }

        return mainResponse(true, 'done', $location, [], 101);
    }
    public function addServing(Request $request)
    {

        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'category_contents_uuid' => 'required|exists:category_contents,uuid',
            'city_uuid' => 'required|exists:cities,uuid',
            'from' => 'required|date|after:' . date('Y/m/d'),
            'to' => 'required|date|after:' . $request->form,
            'working_condition' => 'required|in:contract,Fixed_price,hour',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
        $request->merge([
            'user_uuid' => $user->uuid
        ]);
        $serving = Serving::query()->create($request->only('name', 'user_uuid', 'price', 'details', 'category_contents_uuid', 'city_uuid', 'to', 'from', 'working_condition'));
        $content = Content::query()->create([
            'content_uuid' => $serving->uuid,
            'user_uuid' => $user->uuid,
        ]);
        return mainResponse(true, 'done', $serving, [], 101);
    }
    public function addCourse(Request $request)
    {

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
            'user_uuid' => $user->uuid
        ]);
        $course = Course::query()->create($request->only('name', 'price', 'details', 'user_uuid'));
        $content = Content::query()->create([
            'content_uuid' => $course->uuid,
            'user_uuid' => $user->uuid,
        ]);
        if ($request->hasFile('image')) {
            UploadImage($request->image, Course::PATH_COURSE, Course::class, $course->uuid, false, null, Upload::IMAGE);
        }
        if ($request->hasFile('video')) {
            UploadImage($request->video, Course::PATH_COURSE_VIDEO, Course::class, $course->uuid, false, null, Upload::VIDEO);
        }
        return mainResponse(true, 'done', $course, [], 101);
    }
    public function addProduct(Request $request)
    {

        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'category_uuid' => 'required|exists:categories,uuid',
            'sup_category_uuid' =>['required',
                Rule::exists(SupCategory::class, 'uuid')->where(function ($query) use ($request) {
                    $query->where('category_uuid', $request->category_uuid);
                }),
            ],
            'type' => 'required|in:sale,leasing',

        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
        $request->merge([
            'user_uuid' => $user->uuid
        ]);
        $product = Product::query()->create($request->only('user_uuid', 'name', 'price', 'details', 'sup_category_uuid', 'category_uuid', 'type'));
        for ($i = 0; $i < count($request->keys); $i++) {
            Specification::query()->create([
                'key' => $request->keys[$i],
                'value' => $request->values[$i],
                'product_uuid' => $product->uuid
            ]);
        }

        $content = Content::query()->create([
            'content_uuid' => $product->uuid,
            'user_uuid' => $user->uuid,
        ]);
        if ($request->hasFile('images')) {
            foreach ($request->images as $item) {
                UploadImage($item, Product::PATH_PRODUCT, Product::class, $product->uuid, false, null, Upload::IMAGE); // one يعني انو هذه الصورة تابعة لمعرض الاعمال الي من نوع الفيديوهات

            }
        }
        return mainResponse(true, 'done', $product, [], 101);
    }

    public function updateServing(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'category_contents_uuid' => ['required',
                Rule::exists(CategoryContent::class, 'uuid')->where('type', 'serving'),
            ],
            'city_uuid' => 'required|exists:cities,uuid',
            'from' => 'required|date|after:' . date('Y/m/d'),
            'to' => 'required|date|after:' . $request->form,
            'working_condition' => 'required|in:contract,Fixed_price,hour',
        ];
        $serving = Serving::query()->findOrFail($request->uuid);

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
        $request->merge([
            'user_uuid' => $user->uuid
        ]);
        $serving->update($request->only('name', 'user_uuid', 'price', 'details', 'category_contents_uuid', 'city_uuid', 'to', 'from', 'working_condition'));
        return mainResponse(true, 'done', $serving, [], 101);


    }
    public function updateLocation(Request $request)
    {
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
            'user_uuid' => $user->uuid
        ]);
        $location = Location::query()->findOrFail($request->uuid);

        $location->update($request->only('name', 'details', 'price', 'user_uuid', 'category_contents_uuid'));
        if ($request->hasFile('images')) {
            foreach ($request->images as $item) {
                UploadImage($item, Location::PATH_LOCATION, Location::class, $location->uuid, false, null, Upload::IMAGE);
            }
        }
        if ($request->has('deleteImages')) {
            foreach ($request->deleteImages as $item) {
                $image = Upload::query()->where('uuid', $item)->first();
                File::delete(public_path(Location::PATH_LOCATION . $image->filename));
                $image->delete();
            }
        }


        return mainResponse(true, 'done', $location, [], 101);

    }
    public function updateProduct(Request $request)
    {

        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'category_uuid' => 'required|exists:categories,uuid',
            'sup_category_uuid' =>['required',
                Rule::exists(SupCategory::class, 'uuid')->where(function ($query) use ($request) {
                    $query->where('category_uuid', $request->category_uuid);
                }),
            ],
            ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $product = Product::findOrFail($request->uuid);

        $user = Auth::guard('sanctum')->user();
        $request->merge([
            'user_uuid' => $user->uuid
        ]);
        $product->update($request->only('name','details','price','user_uuid','category_content_uuid'));
        if ($request->hasFile('images')) {
            foreach ($request->images as $item) {
                UploadImage($item, Product::PATH_PRODUCT, Product::class, $product->uuid, false, null, Upload::IMAGE);
            }
        }
        if ($request->has('deleteImages')) {
            foreach ($request->deleteImages as $item) {
                $image = Upload::query()->where('uuid', $item)->first();
                File::delete(public_path(Product::PATH_PRODUCT . @$image->filename));
               if ($image){
                   $image->delete();
               }
            }
        }
        for ($i = 0; $i < count($request->keys); $i++) {
            Specification::query()->create([
                'key' => $request->keys[$i],
                'value' => $request->values[$i],
                'product_uuid' => $product->uuid
            ]);
        }
        for ($i = 0; $i < count($request->deleteSpecification); $i++) {
            Specification::destroy($i);
        }
        return mainResponse(true, 'done', $product, [], 101);


    }
    public function updateCourse(Request $request)
    {
        ;

        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'image' => 'required|image',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $course = Course::findOrFail($request->uuid);

        $user = Auth::guard('sanctum')->user();
        $request->merge([
            'user_uuid' => $user->uuid
        ]);
        $course->update($request->only('name','details','price','user_uuid'));
        if ($request->hasFile('image')) {
            UploadImage($request->image, Course::PATH_COURSE, Course::class, $course->uuid, true, null, Upload::IMAGE);
        }
        if ($request->hasFile('video')) {
            UploadImage($request->video, Course::PATH_COURSE_VIDEO, Course::class, $course->uuid, true, null, Upload::VIDEO);
        }
        return mainResponse(true, 'done', $course, [], 101);


    }

    public function deleteLocation($uuid){
        {
            $location = Location::query()->find($uuid);
           if (isset($location)) {
               foreach ($location->imageLocation as $image) {
                   File::delete(public_path(Location::PATH_LOCATION . $image->filename));
                   $image->delete();
               }
               $location->delete();
               return mainResponse(true, 'done', [], [], 200);
           }else{
               return mainResponse(false, 'location not found', [], ['location not found'], 404);
           }
        }
    }
    public function deleteProduct($uuid){
        {
            $product = Product::query()->find($uuid);
            if (isset($product)) {
                foreach ($product->imageProduct as $image) {
                    File::delete(public_path(Product::PATH_PRODUCT . $image->filename));
                    $image->delete();
                }
                $product->specifications()->delete();
                $product->delete();
                return mainResponse(true, 'done', [], [], 200);
            }else{
                return mainResponse(false, 'product not found', [], ['product not found'], 404);
            }
        }
    }
    public function deleteCourse($uuid){
        {
            $course = Course::query()->find($uuid);
            if (isset($course)) {
                File::delete(public_path(Course::PATH_COURSE . $course->imageCourse->filename));
                File::delete(public_path(Course::PATH_COURSE_VIDEO . $course->videoCourse->filename));
                $course->imageCourse()->delete();
                $course->videoCourse()->delete();
                $course->delete();
                return mainResponse(true, 'done', [], [], 200);
            }else{
                return mainResponse(false, 'course not found', [], ['location not found'], 404);
            }
        }
    }
    public function deleteServing($uuid){
        {
            $serving = Serving::query()->find($uuid);
            if (isset($serving)) {
                $serving->delete();
                return mainResponse(true, 'done', [], [], 200);
            }else{
                return mainResponse(false, 'serving not found', [], ['location not found'], 404);
            }
        }
    }


    public function getMyLocation(Request $request){
        $user = Auth::guard('sanctum')->user();
        $name=$request->name??'';
        $location=Location::query()->where('user_uuid',$user->uuid)
            ->when($name, function (Builder $query, string $name) {
                $query->where('name', 'like', "%{$name}%");
            })->get();
        return mainResponse(true, 'done', LocationResource::collection($location), [], 200);
    }
    public function getMyServing(Request $request){
        $user = Auth::guard('sanctum')->user();
        $name=$request->name??'';
        $serving=Serving::query()->where('user_uuid',$user->uuid)
            ->when($name, function (Builder $query, string $name) {
                $query->where('name', 'like', "%{$name}%");
            })->get();
        return mainResponse(true, 'done', ServingResource::collection($serving), [], 200);
    }
    public function getMyCourse(Request $request){
        $user = Auth::guard('sanctum')->user();
        $name=$request->name??'';
        $course=Course::query()->where('user_uuid',$user->uuid)
            ->when($name, function (Builder $query, string $name) {
                $query->where('name', 'like', "%{$name}%");
            })->get();
        return mainResponse(true, 'done', CourseResource::collection($course), [], 200);
    }
    public function getMyProduct(Request $request,$type){
        if ($type=="leasing" ||$type=='sale') {
            $user = Auth::guard('sanctum')->user();
            $name = $request->name ?? '';
            $product = Product::query()->where('user_uuid', $user->uuid)->where('type', $type)
                ->when($name, function (Builder $query, string $name) {
                    $query->where('name', 'like', "%{$name}%");
                })->get();
            return mainResponse(true, 'done', ProductResource::collection($product), [], 200);
        }else{
            return mainResponse(true, 'type not sale || leasing', [], ['type not sale || leasing'], 404);

        }
        }
}
