<?php

namespace App\Http\Controllers\Api\Content;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoriesLocation;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\CourseEdittResource;
use App\Http\Resources\CourseResource;
use App\Http\Resources\LocationEdittResource;
use App\Http\Resources\LocationResource;
use App\Http\Resources\MyCourseResource;
use App\Http\Resources\MyLocationResource;
use App\Http\Resources\MyProductResource;
use App\Http\Resources\ProductEditResource;
use App\Http\Resources\ProductResource;
use App\Http\Resources\ServingContentResource;
use App\Http\Resources\ServingEditResource;
use App\Http\Resources\ServingEditResourcesource;
use App\Http\Resources\ServingOrderResource;
use App\Http\Resources\ServingResource;
use App\Models\Category;
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
    public function categories(Request $request)
    {
        $categories = Category::query();
        if ($request->with_sub) {
            $countries = $categories->with('sub');
        }
        $categories = $categories->get();
        return mainResponse(true, 'ok', compact('categories'), []);
    }

    public function categoriesContent($type)
    {

        $categories = CategoryContent::query()->where('type', $type)->get();
        $categories = CategoryResource::collection($categories);
        return mainResponse(true, 'ok', compact('categories'), []);
    }

    public function addLocation(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'category_contents_uuid' => 'required',
            'category_contents_uuid.*' => 'required|exists:category_contents,uuid',
            'lat' => 'required',
            'lng' => 'required',
            'images' => 'required|array',
            'images.*' => 'required|mimes:jpeg,jpg,png|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
        $request->merge([
            'user_uuid' => $user->uuid
        ]);

        $location = Location::query()->create($request->only('name', 'user_uuid', 'price', 'details', 'lng', 'lat'));
        $location->categories()->sync($request->category_contents_uuid);

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
            'category_contents_uuid' => ['required',
                Rule::exists(CategoryContent::class, 'uuid')->where('type', 'serving'),
            ], 'city_uuid' => 'required|exists:cities,uuid',
            'from' => 'required|date|after:' . date('Y/m/d'),
            'to' => 'required|date|after:' . $request->from,
            'working_condition' => 'required|in:contract,Fixed_price,hour',
            'lat' => 'required',
            'lng' => 'required',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
        $request->merge([
            'user_uuid' => $user->uuid
        ]);
        $serving = Serving::query()->create($request->only('name', 'user_uuid', 'price', 'details', 'category_contents_uuid', 'city_uuid', 'to', 'from', 'working_condition', 'lng', 'lat'));
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
            'demonstration_video' => 'required|mimes:mp4,mov,ogg,qt',
            'videos' => 'required',
            'videos.*' => 'mimes:mp4,mov,ogg,qt',
            'image' => 'required|image',
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
        if ($request->hasFile('demonstration_video')) {
//            $file=$request->file('video');
//            $path=$file->store('upload','public');
//            Upload::create([
//                'filename' =>  $path,
//                'imageable_id' => $course->uuid,
//                'imageable_type' => Course::class,
//                'type'=>Upload::VIDEO
//            ]);
            UploadImage($request->demonstration_video, Course::PATH_COURSE_VIDEO, Course::class, $course->uuid, false, null, Upload::VIDEO, 'demonstration video');
        }
        if ($request->hasFile('videos')) {
            foreach ($request->videos as $item) {
                $video = UploadImage($item, Course::PATH_COURSE_VIDEO, Course::class, $course->uuid, false, null, Upload::VIDEO);
                $getID3 = new \getID3;
                $video_file = $getID3->analyze('upload/course/video/' . $video->filename);
                $duration_string = $video_file['playtime_string'];
                $video->duration = $duration_string;
                $video->save();
            }
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
            'sub_category_uuid' => ['required',
                Rule::exists(SupCategory::class, 'uuid')->where(function ($query) use ($request) {
                    $query->where('category_uuid', $request->category_uuid);
                }),
            ],
            'lat' => 'required',
            'lng' => 'required',
            'type' => 'required|in:sale,rent',
            'keys' => 'required',
            'keys.*' => 'string',
            'values' => 'required',
            'values.*' => 'string',
            'images' => 'required',
            'images.*' => 'mimes:jpeg,jpg,png|max:2048'

        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
        $request->merge([
            'user_uuid' => $user->uuid
        ]);
        $product = Product::query()->create($request->only('user_uuid', 'name', 'price', 'details', 'sub_category_uuid', 'category_uuid', 'type', 'lng', 'lat'));
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
        return mainResponse(true, 'done', [], []);
    }

    public function updateServing(Request $request,$uuid)
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
            'lat' => 'required',
            'lng' => 'required',

        ];
        $serving = Serving::query()->findOrFail($uuid);

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
        $request->merge([
            'user_uuid' => $user->uuid
        ]);
        $serving->update($request->only('name', 'user_uuid', 'price', 'details', 'category_contents_uuid', 'city_uuid', 'to', 'from', 'working_condition', 'lng', 'lat'));
        return mainResponse(true, 'done', [], []);


    }

    public function updateLocation(Request $request,$uuid)
    {
        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'category_contents_uuid' => 'required|exists:category_contents,uuid',
            'lat' => 'required',
            'lng' => 'required',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
        $request->merge([
            'user_uuid' => $user->uuid
        ]);
        $location = Location::query()->findOrFail($uuid);

        $location->update($request->only('name', 'details', 'price', 'user_uuid', 'lng', 'lat'));
        $location->categories()->sync($request->category_contents_uuid);

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


        return mainResponse(true, 'done', [], []);

    }

    public function updateProduct(Request $request,$uuid)
    {

        $product = Product::query()->findOrFail($uuid);
        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'category_uuid' => 'required|exists:categories,uuid',
            'sub_category_uuid' => ['required',
                Rule::exists(SupCategory::class, 'uuid')->where(function ($query) use ($request) {
                    $query->where('category_uuid', $request->category_uuid);
                }),
            ],
            'lat' => 'required',
            'lng' => 'required',
            'keys' => 'required|array',
            'keys.*' => 'required|string|max:255',
            'values' => 'required|array',
            'values.*' => 'required|string|max:255',
            'delete_images' => 'nullable|array',
            'delete_images.*' => ['required', Rule::exists(Upload::class, 'uuid')->where(function ($q) use ($product) {
                $q->where('imageable_type', Product::class);
                $q->where('imageable_id', $product->uuid);
            })],
            'images' => 'nullable|array',
            'images.*' => 'required|mimes:jpeg,jpg,png|max:2048',
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }

        $user = Auth::guard('sanctum')->user();
        $request->merge([
            'user_uuid' => $user->uuid
        ]);
        $product->update($request->only('name', 'details', 'price', 'user_uuid', 'category_content_uuid', 'lng', 'lat'));
        if ($request->hasFile('images')) {
            foreach ($request->images as $item) {
                UploadImage($item, Product::PATH_PRODUCT, Product::class, $product->uuid, false, null, Upload::IMAGE);
            }
        }
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $item) {
                $image = Upload::query()->where('uuid', $item)->first();
                File::delete(public_path(Product::PATH_PRODUCT . @$image->filename));
                if ($image) {
                    $image->delete();
                }
            }
        }

        Specification::query()->where('product_uuid', $product->uuid)->delete();

        for ($i = 0; $i < count($request->keys); $i++) {
            Specification::query()->create([
                'key' => $request->keys[$i],
                'value' => $request->values[$i],
                'product_uuid' => $product->uuid
            ]);
        }

        return mainResponse(true, 'done', [], [], 101);
    }

    public function updateCourse(Request $request,$uuid)
    {
        ;

        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'demonstration_video' => 'required|mimes:mp4,mov,ogg,qt',
            'videos' => 'required',
            'videos.*' => 'mimes:mp4,mov,ogg,qt',
            'image' => 'required|image',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $course = Course::findOrFail($uuid);

        $user = Auth::guard('sanctum')->user();
        $request->merge([
            'user_uuid' => $user->uuid
        ]);
        $course->update($request->only('name', 'details', 'price', 'user_uuid'));
        if ($request->hasFile('image')) {
            UploadImage($request->image, Course::PATH_COURSE, Course::class, $course->uuid, true, null, Upload::IMAGE);
        }
        if ($request->hasFile('demonstration_video')) {
            UploadImage($request->demonstration_video, Course::PATH_COURSE_VIDEO, Course::class, $course->uuid, true, null, Upload::VIDEO, 'demonstration video');
        }
        if ($request->hasFile('video')) {
            foreach ($request->video as $item) {
                UploadImage($item, Course::PATH_COURSE_VIDEO, Course::class, $course->uuid, false, null, Upload::VIDEO);
            }
        }
        if ($request->has('deletevideo')) {
            foreach ($request->deletevideo as $item) {
                $video = Upload::query()->where('uuid', $item)->first();
                File::delete(public_path(Course::PATH_COURSE_VIDEO . @$video->filename));
                if ($video) {
                    $video->delete();
                }
            }
        }


        return mainResponse(true, 'done', $course, [], 101);


    }

    public function editProduct($uuid)
    {
        $product = Product::query()->find($uuid);
        if ($product) {
            $categories = Category::query()
                ->with('sub')
                ->get();
            $categories = CategoryResource::collection($categories);
            $item = ProductEditResource::make($product);
            return mainResponse(true, 'done', compact('item', 'categories'), [], 101);
        } else {
            return mainResponse(false, 'not found', [], [], 404);
        }
    }

    public function editLocation($uuid)
    {
        $categories = CategoriesLocation::collection(CategoryContent::query()->where('type', 'location')->get());
        $location = Location::query()->find($uuid);
        if ($location) {
            $location = LocationEdittResource::make($location);
            return mainResponse(true, 'done', compact('location', 'categories'), [], 101);
        } else {
            return mainResponse(false, 'not found', [], [], 404);
        }
    }

    public function editServing($uuid)
    {
        $categories = CategoriesLocation::collection(CategoryContent::query()->where('type', 'serving')->get());
        $serving = Serving::query()->find($uuid);
        if ($serving) {
            $serving = ServingEditResource::make($serving);
            return mainResponse(true, 'done', compact('serving', 'categories'), [], 101);
        } else {
            return mainResponse(false, 'not found', [], [], 404);
        }
    }

    public function editCourse($uuid)
    {
        $course = Course::query()->find($uuid);
        if ($course) {
            $course = CourseEdittResource::make($course);
            return mainResponse(true, 'done', compact('course'), [], 101);
        } else {
            return mainResponse(false, 'not found', [], [], 404);
        }
    }


    public function deleteLocation($uuid)
    {

        $location = Location::query()->find($uuid);
        if (isset($location)) {
            foreach ($location->imageLocation as $image) {
                File::delete(public_path(Location::PATH_LOCATION . $image->filename));
                $image->delete();
            }
            $location->categories()->detach();
            $location->delete();
            return mainResponse(true, 'done', [], [], 200);
        } else {
            return mainResponse(false, 'location not found', [], ['location not found'], 404);
        }

    }

    public function deleteProduct($uuid)
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
        } else {
            return mainResponse(false, 'product not found', [], ['product not found'], 404);
        }

    }

    public function deleteCourse($uuid)
    {
        {
            $course = Course::query()->find($uuid);
            if (isset($course)) {
                File::delete(public_path(Course::PATH_COURSE . $course->imageCourse->filename));
                File::delete(public_path(Course::PATH_COURSE_VIDEO . $course->videoCourse->filename));
                $course->imageCourse()->delete();
                $course->videoCourse()->delete();
                $course->delete();
                return mainResponse(true, 'done', [], [], 200);
            } else {
                return mainResponse(false, 'course not found', [], ['location not found'], 404);
            }
        }
    }

    public function deleteServing($uuid)
    {
        {
            $serving = Serving::query()->find($uuid);
            if (isset($serving)) {
                $serving->delete();
                return mainResponse(true, 'done', [], [], 200);
            } else {
                return mainResponse(false, 'serving not found', [], ['location not found'], 404);
            }
        }
    }


    public function getMyLocation(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $name = $request->name ?? '';
        $locations = Location::query()->where('user_uuid', $user->uuid)
            ->with('categories')
            ->when($name, function (Builder $query, string $name) {
                $query->where('name', 'like', "%{$name}%");
            })->paginate();
        $items = pageResource($locations, MyLocationResource::class);
        return mainResponse(true, 'done', compact('items'), [], 200);
    }

    public function getMyServing(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $name = $request->name ?? '';
        $servings = Serving::query()->where('user_uuid', $user->uuid)
            ->when($name, function (Builder $query, string $name) {
                $query->where('name', 'like', "%{$name}%");
            })->paginate();
        $items = pageResource($servings, ServingContentResource::class);

        return mainResponse(true, 'done', compact('items'), [], 200);
    }

    public function getMyCourse(Request $request)
    {
        $user = Auth::guard('sanctum')->user();
        $name = $request->name ?? '';
        $courses = Course::query()->where('user_uuid', $user->uuid)
            ->when($name, function (Builder $query, string $name) {
                $query->where('name', 'like', "%{$name}%");
            })->paginate();
        $items = pageResource($courses, MyCourseResource::class);

        return mainResponse(true, 'done', compact('items'), [], 200);
    }

    public function getMyProduct(Request $request, $type)
    {
        if ($type == "rent" || $type == 'sale') {
            $user = Auth::guard('sanctum')->user();
            $name = $request->name ?? '';
            $products = Product::query()->where('user_uuid', $user->uuid)->where('type', $type)
                ->when($name, function (Builder $query, string $name) {
                    $query->where('name', 'like', "%{$name}%");
                })->paginate();
            $items = pageResource($products, MyProductResource::class);

            return mainResponse(true, 'done', compact('items'), [], 200);
        } else {
            return mainResponse(true, 'type not sale || rent', [], ['type not sale || rent'], 404);

        }
    }
}
