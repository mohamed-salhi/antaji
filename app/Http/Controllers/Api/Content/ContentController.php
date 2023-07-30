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
use App\Models\Delivery;
use App\Models\DeliveryAddresses;
use App\Models\Location;
use App\Models\MultiDayDiscount;
use App\Models\Package;
use App\Models\Product;
use App\Models\Serving;
use App\Models\Specification;
use App\Models\SubCategory;
use App\Models\Upload;
use Illuminate\Contracts\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ContentController extends Controller
{
    public function create(Request $request){
       $multi_day_discounts= MultiDayDiscount::query()->where('status',1)->exists();
       if ($request->product){
           $delivery=Delivery::query()->where('status',1)->exists();
           return mainResponse(true, 'done', compact('delivery','multi_day_discounts'), [], 101);
       }
        return mainResponse(true, 'done', compact('multi_day_discounts'), [], 101);
    }

    // START PRODUCTS
    public function productCategories(Request $request)
    {
        $name = $request->name;
        $categories = Category::query()
            ->when($name, function ($q) use ($name) {
                $q->where(function ($q) use ($name) {
                    $q->where('name->ar', 'like', '%' . $name . '%');
                    foreach (locales() as $key => $value) {
                        $q->orWhere('name->' . $key, 'like', '%' . $name . '%');
                    }
                });
            })
            ->get();
        $categories = CategoryResource::collection($categories);
        $items = $categories;
        return mainResponse(true, 'done', compact('items'), [], 101);
    }

    public function productSubCategories(Request $request, $uuid)
    {
        $name = $request->name;
        $categories = SubCategory::query()
            ->where('category_uuid', $uuid)
            ->when($name, function ($q) use ($name) {
                $q->where(function ($q) use ($name) {
                    $q->where('name->ar', 'like', '%' . $name . '%');
                    foreach (locales() as $key => $value) {
                        $q->orWhere('name->' . $key, 'like', '%' . $name . '%');
                    }
                });
            })
            ->get();
        $categories = CategoryResource::collection($categories);
        $items = $categories;
        return mainResponse(true, 'done', compact('items'), [], 101);
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

    public function addProduct(Request $request)
    {


        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'multi_day_discount_uuid' => 'nullable|exists:multi_day_discounts,uuid',
            'delivery_uuid' => 'nullable|exists:deliveries,uuid',

            'category_uuid' => 'required|exists:categories,uuid',
            'sub_category_uuid' => ['required',
                Rule::exists(SubCategory::class, 'uuid')->where(function ($query) use ($request) {
                    $query->where('category_uuid', $request->category_uuid);
                }),
            ],
            'lat' => 'required',
            'lng' => 'required',
            'address' => 'required',
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
        if ($user->package->type != Package::VIP) {
            $check = Product::query()->where('user_uuid', $user->uuid)->where('category_uuid', $request->category_uuid)->count();
            if ($user->package->number_of_products_in_each_section < $check) {
                return mainResponse(false, 'You do not have validity', [], []);

            }
        }
        $request->merge([
            'user_uuid' => $user->uuid
        ]);
        $product = Product::query()->create($request->only('address', 'user_uuid', 'name', 'price', 'details', 'sub_category_uuid', 'category_uuid', 'type', 'lng', 'lat','multi_day_discount_uuid','delivery_uuid'));
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
        $uuid = $product->uuid;
        return mainResponse(true, 'done', compact('uuid'), []);
    }

    public function editProduct($uuid)
    {
        $product = Product::query()->find($uuid);
        if ($product) {
            $item = ProductEditResource::make($product);
            return mainResponse(true, 'done', compact('item'), [], 101);
        } else {
            return mainResponse(false, 'not found', [], [], 404);
        }
    }

    public function updateProduct(Request $request, $uuid)
    {

        $product = Product::query()->findOrFail($uuid);
        $rules = [
            'name' => 'required|string|max:36',
            'type' => 'required|in:sale,rent',
            'price' => 'required|int',
            'details' => 'required',
            'multi_day_discount_uuid' => 'nullable|exists:multi_day_discounts,uuid',
            'delivery_uuid' => 'nullable|exists:deliveries,uuid',

            'category_uuid' => 'required|exists:categories,uuid',
            'sub_category_uuid' => ['required',
                Rule::exists(SubCategory::class, 'uuid')->where(function ($query) use ($request) {
                    $query->where('category_uuid', $request->category_uuid);
                }),
            ],
            'lat' => 'required',
            'lng' => 'required',
            'address' => 'required',

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
        $product->update($request->only('address', 'name', 'details', 'price', 'user_uuid', 'category_content_uuid', 'lng', 'lat', 'type','multi_day_discount_uuid','delivery_uuid'));
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
    // END PRODUCTS


    // START LOCATION
    public function locationCategories(Request $request)
    {
        $name = $request->name;
        $categories = CategoryContent::query()
            ->where('type', CategoryContent::LOCATION)
            ->when($name, function ($q) use ($name) {
                $q->where(function ($q) use ($name) {
                    $q->where('name->ar', 'like', '%' . $name . '%');
                    foreach (locales() as $key => $value) {
                        $q->orWhere('name->' . $key, 'like', '%' . $name . '%');
                    }
                });
            })
            ->get();
        $categories = CategoryResource::collection($categories);
        $items = $categories;
        return mainResponse(true, 'done', compact('items'), [], 101);
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

    public function addLocation(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'multi_day_discount_uuid' => 'nullable|exists:multi_day_discounts,uuid',

            'category_contents_uuid' => 'required',
            'category_contents_uuid.*' => 'required|exists:category_contents,uuid',
            'lat' => 'required',
            'lng' => 'required',
            'address' => 'required',
            'images' => 'required|array',
            'images.*' => 'required|mimes:jpeg,jpg,png|max:2048',
        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
        if ($user->package->type != Package::VIP) {
            foreach ($request->category_contents_uuid as $item) {
                $check = Location::query()
                    ->where('user_uuid', $user->uuid)
                    ->whereHas('categories', function ($q) use ($item) {
                        $q->where('category_contents_uuid', $item);
                    })->count();
                if ($user->package->number_of_products_in_each_section < $check) {
                    return mainResponse(false, 'You do not have validity', [], []);
                }
            }
        }
        $request->merge([
            'user_uuid' => $user->uuid
        ]);

        $location = Location::query()->create($request->only('address', 'name', 'user_uuid', 'price', 'details', 'lng', 'lat','multi_day_discount_uuid'));
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
        $uuid = $location->uuid;
        return mainResponse(true, 'done', compact('uuid'), [], 101);
    }

    public function editLocation($uuid)
    {
        $location = Location::query()->find($uuid);
        if ($location) {
            $item = LocationEdittResource::make($location);
            return mainResponse(true, 'done', compact('item'), []);
        } else {
            return mainResponse(false, 'not found', [], [], 404);
        }
    }

    public function updateLocation(Request $request, $uuid)
    {
        $location = Location::query()->findOrFail($uuid);

        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'category_contents_uuid' => 'required|exists:category_contents,uuid',
            'lat' => 'required',
            'lng' => 'required',
            'delete_images' => 'nullable|array',
            'delete_images.*' => ['required', Rule::exists(Upload::class, 'uuid')->where(function ($q) use ($location) {
                $q->where('imageable_type', Location::class);
                $q->where('imageable_id', $location->uuid);
            })
            ],
        ];
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
        $request->merge([
            'user_uuid' => $user->uuid
        ]);

        $location->update($request->only('name', 'details', 'price', 'user_uuid', 'lng', 'lat'));
        $location->categories()->sync($request->category_contents_uuid);

        if ($request->hasFile('images')) {
            foreach ($request->images as $item) {
                UploadImage($item, Location::PATH_LOCATION, Location::class, $location->uuid, false, null, Upload::IMAGE);
            }
        }
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $item) {
                $image = Upload::query()->where('uuid', $item)->first();
                File::delete(public_path(Location::PATH_LOCATION . $image->filename));
                $image->delete();
            }
        }


        return mainResponse(true, 'done', [], []);

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

    // END LOCATION


    // START SERVICE
    public function servingCategories(Request $request)
    {
        $name = $request->name;
        $categories = CategoryContent::query()
            ->where('type', CategoryContent::SERVING)
            ->when($name, function ($q) use ($name) {
                $q->where(function ($q) use ($name) {
                    $q->where('name->ar', 'like', '%' . $name . '%');
                    foreach (locales() as $key => $value) {
                        $q->orWhere('name->' . $key, 'like', '%' . $name . '%');
                    }
                });
            })
            ->get();
        $categories = CategoryResource::collection($categories);
        $items = $categories;
        return mainResponse(true, 'done', compact('items'), [], 101);
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
            'from' => 'required|date_format:"Y-m-d"|after:' . date('Y/m/d'),
            'to' => 'required|date_format:"Y-m-d"|after:' . $request->from,
            'working_condition' => 'required|in:contract,fixed_price,hour',
            'lat' => 'required',
            'lng' => 'required',
            'address' => 'required',

        ];

        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return mainResponse(false, $validator->errors()->first(), [], $validator->errors()->messages(), 101);
        }
        $user = Auth::guard('sanctum')->user();
        if ($user->package->type != Package::VIP) {
            $check = Serving::query()->where('user_uuid', $user->uuid)->where('category_contents_uuid', $request->category_contents_uuid)->count();
            if ($user->package->number_of_products_in_each_section < $check) {
                return mainResponse(false, 'You do not have validity', [], []);
            }
        }
        $request->merge([
            'user_uuid' => $user->uuid
        ]);
        $serving = Serving::query()->create($request->only('address', 'name', 'user_uuid', 'price', 'details', 'category_contents_uuid', 'city_uuid', 'to', 'from', 'working_condition', 'lng', 'lat'));
        $content = Content::query()->create([
            'content_uuid' => $serving->uuid,
            'user_uuid' => $user->uuid,
        ]);
        $uuid = $serving->uuid;

        return mainResponse(true, 'done', compact('uuid'), [], 101);
    }

    public function editServing($uuid)
    {
        $serving = Serving::query()->find($uuid);
        if ($serving) {
            $item = ServingEditResource::make($serving);
            return mainResponse(true, 'done', compact('item'), [], 101);
        } else {
            return mainResponse(false, 'not found', [], [], 404);
        }
    }

    public function updateServing(Request $request, $uuid)
    {
        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'category_contents_uuid' => ['required',
                Rule::exists(CategoryContent::class, 'uuid')->where('type', 'serving'),
            ],
            'city_uuid' => 'required|exists:cities,uuid',
            'from' => 'required|date_format:"Y-m-d"|after:' . date('Y/m/d'),
            'to' => 'required|date_format:"Y-m-d"|after:' . $request->from,
            'working_condition' => 'required|in:contract,fixed_price,hour',
            'lat' => 'required',
            'lng' => 'required',
            'address' => 'required',

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
        $serving->update($request->only('address', 'name', 'user_uuid', 'price', 'details', 'category_contents_uuid', 'city_uuid', 'to', 'from', 'working_condition', 'lng', 'lat'));
        return mainResponse(true, 'done', [], []);


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
    // END SERVICE


    // START COURSE
    public function addCourse(Request $request)
    {

        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'demonstration_video' => 'required',
            'videos' => 'required',
            'videos.*' => 'required',
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
        $uuid = $course->uuid;
        return mainResponse(true, 'done', compact('uuid'), []);
    }

    public function updateCourse(Request $request, $uuid)
    {
        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'demonstration_video' => 'nullable|mimes:mp4,mov,ogg,qt',
            'videos' => 'nullable|array',
            'videos.*' => 'required|mimes:mp4,mov,ogg,qt',
            'delete_videos' => 'nullable|array',
            'delete_videos.*' => 'required|exists:uploads,uuid',
            'image' => 'nullable|image',
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
        if ($request->hasFile('videos')) {
            foreach ($request->videos as $item) {
                UploadImage($item, Course::PATH_COURSE_VIDEO, Course::class, $course->uuid, false, null, Upload::VIDEO);
            }
        }
        if ($request->has('delete_videos')) {
            foreach ($request->delete_videos as $item) {
                $video = Upload::query()->where('uuid', $item)->first();
                File::delete(public_path(Course::PATH_COURSE_VIDEO . @$video->filename));
                if ($video) {
                    $video->delete();
                }
            }
        }


        return mainResponse(true, 'done', [], [], 101);
    }

    public function editCourse($uuid)
    {
        $course = Course::query()->find($uuid);
        if ($course) {
            $item = CourseEdittResource::make($course);
            return mainResponse(true, 'done', compact('item'), [], 101);
        } else {
            return mainResponse(false, 'not found', [], [], 404);
        }
    }

    public function deleteCourse($uuid)
    {
        {
            $course = Course::query()->find($uuid);
            if (isset($course)) {

                File::delete(public_path(Course::PATH_COURSE . @$course->imageCourse->filename));
                foreach ($course->videosCourse as $video) {
                    File::delete(public_path(Course::PATH_COURSE_VIDEO . @$video->filename));
                }
                File::delete(public_path(Course::PATH_COURSE_VIDEO . @$course->videoCourse->filename));
                $course->imageCourse()->delete();
                $course->videoCourse()->delete();
                $course->videosCourse()->delete();
                $course->delete();
                return mainResponse(true, 'done', [], [], 200);
            } else {
                return mainResponse(false, 'course not found', [], ['location not found'], 404);
            }
        }
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

    // END COURSE


    public function categories(Request $request)
    {
        $categories = Category::query();
        if ($request->with_sub) {
            $countries = $categories->with('sub');
        }
        $categories = $categories->get();
        return mainResponse(true, 'ok', compact('categories'), []);
    }
}
