<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\Type;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:product', ['only' => ['index','store','create','destroy','edit','update']]);
    }
    public function index()
    {
        $types=Type::all();
        return view('admin.categories.index',compact('types'));
    }


    public function store(Request $request)
    {
        $rules = [];
        foreach (locales() as $key => $language) {
            $rules['name_' . $key] = 'required|string|max:45';
        }
        $rules['image'] = 'required|image';

        $this->validate($request, $rules);
        $data = [];
        foreach (locales() as $key => $language) {
            $data['name'][$key] = $request->get('name_' . $key);
        }
        $category= Category::create([
            'name'=>['en'=>$request->name_en,'ar'=>$request->name_ar]
        ]);
        if ($request->has('image')) {
            UploadImage($request->image, Category::PATH_IMAGE, Category::class, $category->uuid, true, null, Upload::IMAGE);
        }
//        $category->types()->sync($request->types);
        return response()->json([
            'item_added'
        ]);
    }

    public function update(Request $request)
    {

        $rules = [];
        foreach (locales() as $key => $language) {
            $rules['name_' . $key] = 'required|string|max:255';
        }
        $this->validate($request, $rules);
        $data = [];
        foreach (locales() as $key => $language) {
            $data['name'][$key] = $request->get('name_' . $key);
        }
        $category = Category::query()->withoutGlobalScope('category')->findOrFail($request->uuid);
        $category->update($data);
        if ($request->has('image')) {
            UploadImage($request->image, Category::PATH_IMAGE, Category::class, $category->uuid, true, null, Upload::IMAGE);
        }
//        $category->types()->sync($request->types);
        return response()->json([
            'item_edited'
        ]);

    }

    public function destroy($uuid)
    {

        try {
            $uuids=explode(',', $uuid);
            $Category=  Category::whereIn('uuid', $uuids)->get();

            foreach ($Category as $item){
                Storage::delete('public/' . @$item->imageCategory->path);

//                File::delete(public_path(Category::PATH_IMAGE.$item->imageCategory->filename));
                $item->imageCategory()->delete();
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
        $category= Category::query()->withoutGlobalScope('category')->orderByDesc('created_at');

        return Datatables::of($category)
            ->filter(function ($query) use ($request) {
                if ($request->get('name')) {
                    $locale = app()->getLocale();
                    $query->where('name->'.locale(), 'like', "%{$request->get('name')}%");
                }
                if ($request->status){
                    ($request->status==1)?$query->where('status',$request->status):$query->where('status',0);
                }

            })
            ->addColumn('checkbox',function ($que){
                return $que->uuid;
            })
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->uuid . '" ';
                $data_attr .= 'data-image="' . $que->image . '" ';

                foreach (locales() as $key => $value) {
                    $data_attr .= 'data-name_' . $key . '="' . $que->getTranslation('name', $key) . '" ';
                }
                $string = '';
//                if ($user->can('competitions-edit')){
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary btn_edit" data-toggle="modal"
                    data-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
//                }
//                if ($user->can('competitions-delete')){
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger btn_delete" data-uuid="' . $que->uuid .
                    '">' . __('delete') . '</button>';
//                }
                return $string;
            }) ->addColumn('status', function ($que)  {
                $currentUrl = url('/');
                if ($que->status==1){
                    $data='
<button type="button"  data-url="' . $currentUrl . "/admin/categories/updateStatus/0/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('active') . '</button>
                    ';
                }else{
                    $data='
<button type="button"  data-url="' . $currentUrl . "/admin/categories/updateStatus/1/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-uuid="' . $que->uuid .
                        '">' . __('inactive') . '</button>
                    ';
                }
                return $data;
            })
            ->addColumn('sub-category', function ($que) {
                $currentUrl = url('/');
                return '   <a class="btn btn-gradient-success " href="'.route('categories.sub',$que->uuid).'" type="button"                                                                                         ><span><i
                                                    class="fa fa-plus"></i>'.__('show').'</span>
                                        </button>';
            })
            ->rawColumns(['action', 'status','sub-category'])->toJson();
    }

    public function UpdateStatus($status,$sub)
    {
        $uuids=explode(',', $sub);

        $activate =  Category::query()->withoutGlobalScope('category')
            ->whereIn('uuid',$uuids)
            ->update([
                'status'=>$status
            ]);
        return response()->json([
            'item_edited'
        ]);
    }

    public function subIndex($uuid){
        return view('admin.categories.sub',compact('uuid'));
    }
    public function subIndexTable(Request $request ,$uuid)
    {
        $sub= SubCategory::query()->withoutGlobalScope('sub')->where('category_uuid',$uuid)->orderByDesc('created_at');

        return Datatables::of($sub)
            ->filter(function ($query) use ($request) {
                if ($request->get('name')) {
                    $locale = app()->getLocale();
                    $query->where('name->'.locale(), 'like', "%{$request->get('name')}%");
                }
                if ($request->status){
                    $query->where('status',$request->status);
                }

            })
            ->addColumn('checkbox',function ($que){
                return $que->uuid;
            })
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->uuid . '" ';
                $data_attr .= 'data-category_uuid="' . $que->category_uuid . '" ';
                $data_attr .= 'data-image="' . $que->image . '" ';

                foreach (locales() as $key => $value) {
                    $data_attr .= 'data-name_' . $key . '="' . $que->getTranslation('name', $key) . '" ';
                }
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary btn_edit" data-toggle="modal"
                    data-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';

                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger btn_delete" data-uuid="' . $que->uuid .
                    '">' . __('delete') . '</button>';

                return $string;
            }) ->addColumn('status', function ($que)  {
                $currentUrl = url('/');
                if ($que->status==1){
                    $data='
<button type="button"  data-url="' . $currentUrl . "/categories/sub/'.$que->category_uuid.'/updateStatus/0/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('active') . '</button>
                    ';
                }else{
                    $data='
<button type="button"  data-url="' . $currentUrl . "/categories/sub/'.$que->category_uuid.'/updateStatus/1/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-uuid="' . $que->uuid .
                        '">' . __('inactive') . '</button>
                    ';
                }
               return $data;
            })
            ->rawColumns(['action', 'status'])->toJson();
    }


    public function subStore(Request $request)
    {

        $rules = [];
        foreach (locales() as $key => $language) {
            $rules['name_' . $key] = 'required|string|max:45';
        }
        $rules['category_uuid']='required|exists:categories,uuid';
        $this->validate($request, $rules);
        $data = [];
        foreach (locales() as $key => $language) {
            $data['name'][$key] = $request->get('name_' . $key);
        }
        $data['category_uuid']=$request->category_uuid;
        $sub= SubCategory::create($data);
        if ($request->has('image')) {
            UploadImage($request->image, SubCategory::PATH_IMAGE, SubCategory::class, $sub->uuid, true, null, Upload::IMAGE);
        }
        return response()->json([
            'item_addedd'
        ]);
    }
    public function subUpdate(Request $request)
    {

        $rules = [];
        foreach (locales() as $key => $language) {
            $rules['name_' . $key] = 'required|string|max:255';
        }
        $rules['category_uuid']='required|exists:categories,uuid';

        $this->validate($request, $rules);
        $data = [];
        foreach (locales() as $key => $language) {
            $data['name'][$key] = $request->get('name_' . $key);
        }
        $sub = SubCategory::query()->withoutGlobalScope('category')->findOrFail($request->uuid);
        $sub->update($data);
        if ($request->has('image')) {
            UploadImage($request->image, SubCategory::PATH_IMAGE, SubCategory::class, $sub->uuid, true, null, Upload::IMAGE);
        }
        return response()->json([
            'item_edited'
        ]);

    }
    public function subUpdateStatus($uuid,$status,$sub)
    {
        $uuids=explode(',', $sub);

        $activate =  SubCategory::query()->withoutGlobalScope('sub')
            ->whereIn('uuid',$uuids)
            ->update([
           'status'=>$status
       ]);
        return response()->json([
            'item_edited'
        ]);
    }
    public function subDestroy($uuid,$delete)
    {

        try {
            $uuids=explode(',', $delete);
            $sub=  SubCategory::whereIn('uuid', $uuids)->get();

            foreach ($sub as $item){
                File::delete(public_path(SubCategory::PATH_IMAGE.$item->imageCategory->filename));
                $item->imageCategory()->delete();
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


}
