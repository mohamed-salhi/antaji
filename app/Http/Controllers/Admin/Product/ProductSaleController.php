<?php

namespace App\Http\Controllers\Admin\Product;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Content;
use App\Models\Product;
use App\Models\SupCategory;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;

class ProductSaleController extends Controller
{
    public function index(){
        $categories=Category::query()->select('uuid','name')->get();
        $users=User::query()->select('uuid','name')->get();
        return view('admin.products.sale',compact('categories','users'));
    }
    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'category_uuid' => 'required|exists:categories,uuid',
            'sup_category_uuid' => 'required|exists:sup_categories,uuid',
            'user_uuid'=>'required|exists:users,uuid',
        ];

        $this->validate($request, $rules);
        $request->merge([
            'type'=>'sale'
        ]);
        $product= Product::query()->create($request->only('sale','user_uuid','name','price','details','sup_category_uuid','category_uuid','type'));
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
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'category_uuid' => 'required|exists:categories,uuid',
            'sup_category_uuid' => 'required|exists:sup_categories,uuid',
            'user_uuid'=>'required|exists:users,uuid',
        ];
        $product = Product::findOrFail($request->uuid);

        $this->validate($request, $rules);
        $product->update($request->only('name','details','price','user_uuid','category_content_uuid'));
        return response()->json([
            'item_edited'
        ]);

    }

    public function destroy($uuid)
    {

        try {
            $uuids=explode(',', $uuid);
            $product=  Product::whereIn('uuid', $uuids)->get();

            foreach ($product as $item){
                File::delete(public_path(Product::PATH_PRODUCT.$item->imageProduct->filename));
                $item->imageProduct()->delete();
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
        $product = Product::query()->withoutGlobalScope('status')->where('type','sale')->orderBy('created_at');
        return Datatables::of($product)
            ->filter(function ($query) use ($request) {
                if ($request->status){
                    $query->where('status',$request->status);
                }
                if ($request->price){
                    $query->where('price',$request->price);
                }
                if ($request->name){
                    $query->where('name',$request->name);
                }
                if ($request->category_uuid) {
                    $query->where('category_uuid', $request->category_uuid);
                }
                if ($request->sup_category_uuid) {
                    $query->where('sup_category_uuid', $request->sup_category_uuid);
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
                $data_attr .= 'data-details="' . $que->details . '" ';
                $data_attr .= 'data-user_uuid="' . $que->user_uuid . '" ';
                $data_attr .= 'data-category_uuid="' . $que->category_uuid . '" ';
                $data_attr .= 'data-sup_category_uuid="' . $que->sup_category_uuid . '" ';

                $url = url('/products/sale/images/'.$que->uuid);

                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary btn_edit" data-toggle="modal"
                    data-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';

                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger btn_delete" data-uuid="' . $que->uuid .
                    '">' . __('delete') . '</button>';
                $string .= ' <a href="'.$url.'"  class="btn btn-sm btn-outline-dark btn_image" data-uuid="' . $que->uuid .
                    '">' . __('images') . '  </a>';
                $string .= ' <a href="'.$url.'"  class="btn btn-sm btn-outline-info btn_image" data-uuid="' . $que->uuid .
                    '">' . __('details') . '  </a>';
                return $string;
            }) ->addColumn('status', function ($que)  {
                $currentUrl = url('/');
                if ($que->status==1){
                    $data='
<button type="button"  data-url="' . $currentUrl . "/products/sale/updateStatus/0/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('active') . '</button>
                    ';
                }else{
                    $data='
<button type="button"  data-url="' . $currentUrl . "/products/sale/updateStatus/1/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-uuid="' . $que->uuid .
                        '">' . __('inactive') . '</button>
                    ';
                }
                return $data;
            })
            ->rawColumns(['action', 'status'])->toJson();
    }

    public function updateStatus($status,$sup)
    {
        $uuids=explode(',', $sup);

        $product =  Product::query()->withoutGlobalScope('status')
            ->whereIn('uuid',$uuids)
            ->update([
                'status'=>$status
            ]);
        return response()->json([
            'item_edited'
        ]);
    }
    public function imageIndex($uuid){
        return view('admin.products.sale.images', compact('uuid'));
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
        $category = SupCategory::where("category_uuid", $uuid)->pluck("name", "uuid");
        return $category;
    }
}
