<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Content;
use App\Models\Product;
use App\Models\Specification;
use App\Models\SubCategory;
use App\Models\Upload;
use App\Models\User;
use App\Models\ViewNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Psy\Util\Str;
use Yajra\DataTables\Facades\DataTables;

class ProductSaleController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:product', ['only' => ['index','store','create','destroy','edit','update']]);
    }
    public function index(Request $request){
        if ($request->has('uuid')){

            ViewNotification::query()->updateOrCreate([
                'admin_id'=>Auth::id(),
                'notification_uuid'=>$request->uuid
            ]);
        }
        $categories=Category::query()->select('uuid','name')->get();
        $users=User::query()->select('uuid','name')->get();
        return view('admin.products.sale',compact('categories','users'));
    }
    public function store(Request $request)
    {
        $rules = [
            'images' => 'required',
            'images.*' => 'required|mimes:jpeg,jpg,png,gif,csv,txt,pdf|max:2048',
            'name' => 'required|string',
            'price' => 'required|int',
            'details' => 'required|string',
            'address' => 'required|string',
            'fname' => 'required',
            'fname.*' => 'string',
            'fvalue' => 'required',
            'fvalue.*' => 'string',
            'category_uuid' => 'required|exists:categories,uuid',
            'sub_category_uuid' => 'required|exists:sub_categories,uuid',
            'user_uuid'=>'required|exists:users,uuid',
            'lat' => 'required|string',
            'lng' => 'required|string',
        ];
        $this->validate($request, $rules);
        $request->merge([
            'type'=>'sale',
        ]);
        $product= Product::query()
            ->create($request->only('sale','user_uuid','name','price','details','sub_category_uuid','category_uuid','type','address','lat','lng'));


        for ($i = 0; $i < count($request->fname); $i++) {
            Specification::query()->create([
                'key' => $request->fname[$i],
                'value' => $request->fvalue[$i],
                'product_uuid' => $product->uuid
            ]);
        }
        Content::query()->create([
            'content_uuid'=>$product->uuid,
            'user_uuid'=>$request->user_uuid,
        ]);
        if ($request->hasFile('images')) {
            foreach ($request->images as $item) {
                UploadImage($item, Product::PATH_PRODUCT, Product::class, $product->uuid, false, null, Upload::IMAGE); // one يعني انو هذه الصورة تابعة لمعرض الاعمال الي من نوع الفيديوهات

            }
        }
        return response()->json([
            'item_added'
        ]);
    }

    public function update(Request $request)
    {

        $rules = [
            'name' => 'required|string',
            'price' => 'required|int',
            'details' => 'required|string',
            'address' => 'required|string',
            'fname' => 'required',
            'fname.*' => 'string',
            'fvalue' => 'required',
            'fvalue.*' => 'string',
            'category_uuid' => 'required|exists:categories,uuid',
            'sub_category_uuid' => 'required|exists:sub_categories,uuid',
            'user_uuid'=>'required|exists:users,uuid',
            'lat' => 'required|string',
            'lng' => 'required|string',
        ];
        $product = Product::query()->withoutGlobalScope('status')->findOrFail($request->uuid);
        $product->specifications()->delete();
        $this->validate($request, $rules);
        $product->update($request->only('name','details','price','user_uuid','category_content_uuid','address','lat','lng'));
        if (isset($request->delete_images)) {
            $images = Upload::query()->where('imageable_type',Product::class)->where('imageable_id',$product->uuid)->whereNotIn('uuid', $request->delete_images)->get();

            foreach ($images as $item) {
                File::delete(public_path(Product::PATH_PRODUCT . $item->filename));
                $item->delete();
            }
        }
        for ($i = 0; $i < count($request->fname); $i++) {
            Specification::query()->create([
                'key' => $request->fname[$i],
                'value' => $request->fvalue[$i],
                'product_uuid' => $product->uuid
            ]);
        }
        if ($request->hasFile('images')) {
            foreach ($request->images as $item) {
                UploadImage($item, Product::PATH_PRODUCT, Product::class, $product->uuid, false, null, Upload::IMAGE);
            }
        }
        return response()->json([
            'item_edited'
        ]);

    }

    public function destroy($uuid)
    {

        try {
            $uuids=explode(',', $uuid);
            $product=  Product::query()->withoutGlobalScope('status')->whereIn('uuid', $uuids)->get();

            foreach ($product as $item){
                foreach ($item->imageProduct as $image){
                    Storage::delete('public/' . @$image->path);
                    $image->delete();
                }
                $item->delete();
            }
            return response()->json([
                'item_deleted'
            ]);
        }catch (\Exception $e){
            return response()->json([
                'err'
            ]);
        }
    }

    public function indexTable(Request $request)
    {
        $product = Product::query()->withoutGlobalScope('status')->where('type','sale')->orderByDesc('created_at');
        return Datatables::of($product)
            ->filter(function ($query) use ($request) {
                if ($request->status){
                    ($request->status==1)?$query->where('status',$request->status):$query->where('status',0);
                }
                if ($request->price){
                    $query->where('price',$request->price);
                }
                if ($request->name){
                    $query->where('name',$request->name);
                }
                if ($request->user_name) {
                    $query->whereHas('user', function($q) use ($request){
                        $q->where('name','like', "%{$request->user_name}%");
                    });
                }
                if ($request->category_uuid) {
                    $query->where('category_uuid', $request->category_uuid);
                }
                if ($request->sub_category_uuid) {
                    $query->where('sub_category_uuid', $request->sub_category_uuid);
                }
//
            })
            ->addColumn('checkbox',function ($que){
                return $que->uuid;
            })
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->uuid . '" ';
                $data_attr .= 'data-name="' . $que->name . '" ';
                $data_attr .= 'data-price="' . $que->price . '" ';
                $data_attr .= 'data-address="' . $que->address . '" ';
                $data_attr .= 'data-lng="' . $que->lng . '" ';
                $data_attr .= 'data-lat="' . $que->lat . '" ';
                $data_attr .= 'data-details="' . $que->details . '" ';
                $data_attr .= 'data-user_uuid="' . $que->user_uuid . '" ';
                $data_attr .= 'data-category_uuid="' . $que->category_uuid . '" ';
                $data_attr .= 'data-sub_category_uuid="' . $que->sub_category_uuid . '" ';
                $data_attr .= 'data-images_uuid="' . implode(',', $que->imageProduct->pluck('uuid')->toArray()) .'" ';
                $data_attr .= 'data-images="' . implode(',', $que->imageProduct->pluck('path')->toArray()) .'" ';
                $data_attr .= 'data-key="' . implode(',', $que->specifications->pluck('key')->toArray()) .'" ';
                $data_attr .= 'data-value="' . implode(',', $que->specifications->pluck('value')->toArray()) .'" ';

                $url = url('/products/sale/images/'.$que->uuid);

                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary btn_edit" data-toggle="modal"
                    data-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';

                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger btn_delete" data-uuid="' . $que->uuid .
                    '">' . __('delete') . '</button>';
//                $string .= ' <a href="'.$url.'"  class="btn btn-sm btn-outline-dark btn_image" data-uuid="' . $que->uuid .
//                    '">' . __('images') . '  </a>';
//                $string .= ' <a href="'.$url.'"  class="btn btn-sm btn-outline-info btn_image" data-uuid="' . $que->uuid .
//                    '">' . __('details') . '  </a>';
                return $string;
            }) ->addColumn('status', function ($que)  {
                $currentUrl = url('/');
                if ($que->status==1){
                    $data='
<button type="button"  data-url="' . $currentUrl . "/admin/products/sale/updateStatus/0/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('active') . '</button>
                    ';
                }else{
                    $data='
<button type="button"  data-url="' . $currentUrl . "/admin/products/sale/updateStatus/1/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-uuid="' . $que->uuid .
                        '">' . __('inactive') . '</button>
                    ';
                }
                return $data;
            })
            ->rawColumns(['action', 'status'])->toJson();
    }

    public function updateStatus($status,$sub)
    {
        $uuids=explode(',', $sub);

        $product =  Product::query()->withoutGlobalScope('status')->orderByDesc('created_at')
            ->whereIn('uuid',$uuids)
            ->update([
                'status'=>$status
            ]);
        return response()->json([
            'item_edited'
        ]);
    }
    public function imageIndex($uuid){
        return view('admin.products.images', compact('uuid'));
    }
    public function imageIndexTable(Request $request,$uuid)
    {
        $images = Upload::query()->where('imageable_id',$uuid)->where('imageable_type',Product::class);
        return \Yajra\DataTables\DataTables::of($images)
            ->addColumn('checkbox', function ($que) {
                return $que->uuid;
            })
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->uuid . '" ';
                $data_attr .= 'data-imageable_id="' . $que->imageable_id . '" ';
                $data_attr .= 'data-image="' .url('/').Product::PATH_PRODUCT.$que->filename . '" ';

                $string = '';
                $url = url('/products/sale/images/'.$que->imageable_id);
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary btn_edit" data-toggle="modal"
                    data-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" data-url="'.$url.'"  class="btn btn-sm btn-outline-danger btn_delete" data-uuid="' . $que->uuid .
                    '">'.__('delete') .'  </button>';

                return $string;
            })->addColumn('image',function ($que){
                return url('/').Product::PATH_PRODUCT.$que->filename;
            })

            ->rawColumns(['action'])->toJson();
    }
    public function imageStore(Request $request){
        foreach ($request->images as $item){
            UploadImage($item, Product::PATH_PRODUCT, Product::class, $request->uuid, false, null, Upload::IMAGE);
        }
        return response()->json("done");
    }
    public function imageUpdate(Request $request){
        UploadImage($request->images, Product::PATH_PRODUCT, Product::class, $request->imageable_id, true, $request->uuid, Upload::IMAGE);
        return response()->json([
            'item_deleted'
        ]);
    }
    public function imageDestroy($uuid,$uploade){

        try {
            $uuids=explode(',', $uploade);
            $image=Upload::query()->whereIn('uuid',$uuids)->get();
            foreach ($image as $item){
                File::delete(public_path(Product::PATH_PRODUCT.$item->filename));
                $item->delete();
            }

            return response()->json("done");
        }catch (\Exception $e){
            throw $e;
        }
    }
    public function category($uuid)
    {
        $category = SubCategory::where("category_uuid", $uuid)->pluck("name", "uuid");
        return $category;
    }
}
