<?php

namespace App\Http\Controllers\Admin\Category;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\SupCategory;
use App\Models\Type;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
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
                File::delete(public_path(Category::PATH_IMAGE.$item->imageCategory->filename));
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
            ->addColumn('sup-category', function ($que) {
                $currentUrl = url('/');
                return '   <a class="btn btn-gradient-success " href="'.route('categories.sup',$que->uuid).'" type="button"                                                                                         ><span><i
                                                    class="fa fa-plus"></i>'.__('show').'</span>
                                        </button>';
            })
            ->rawColumns(['action', 'status','sup-category'])->toJson();
    }

    public function UpdateStatus($status,$sup)
    {
        $uuids=explode(',', $sup);

        $activate =  Category::query()->withoutGlobalScope('category')
            ->whereIn('uuid',$uuids)
            ->update([
                'status'=>$status
            ]);
        return response()->json([
            'item_edited'
        ]);
    }

    public function supIndex($uuid){
        return view('admin.categories.sup',compact('uuid'));
    }
    public function supIndexTable(Request $request ,$uuid)
    {
        $sup= SupCategory::query()->withoutGlobalScope('sup')->where('category_uuid',$uuid)->orderBy('created_at');

        return Datatables::of($sup)
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
<button type="button"  data-url="' . $currentUrl . "/categories/sup/'.$que->category_uuid.'/updateStatus/0/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('active') . '</button>
                    ';
                }else{
                    $data='
<button type="button"  data-url="' . $currentUrl . "/categories/sup/'.$que->category_uuid.'/updateStatus/1/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-uuid="' . $que->uuid .
                        '">' . __('inactive') . '</button>
                    ';
                }
               return $data;
            })
            ->rawColumns(['action', 'status'])->toJson();
    }


    public function supStore(Request $request)
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
        $sup= SupCategory::create($data);
        if ($request->has('image')) {
            UploadImage($request->image, SupCategory::PATH_IMAGE, SupCategory::class, $sup->uuid, true, null, Upload::IMAGE);
        }
        return response()->json([
            'item_addedd'
        ]);
    }
    public function supUpdate(Request $request)
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
        $sup = SupCategory::query()->withoutGlobalScope('category')->findOrFail($request->uuid);
        $sup->update($data);
        if ($request->has('image')) {
            UploadImage($request->image, SupCategory::PATH_IMAGE, SupCategory::class, $sup->uuid, true, null, Upload::IMAGE);
        }
        return response()->json([
            'item_edited'
        ]);

    }
    public function supUpdateStatus($uuid,$status,$sup)
    {
        $uuids=explode(',', $sup);

        $activate =  SupCategory::query()->withoutGlobalScope('sup')
            ->whereIn('uuid',$uuids)
            ->update([
           'status'=>$status
       ]);
        return response()->json([
            'item_edited'
        ]);
    }
    public function supDestroy($uuid,$delete)
    {

        try {
            $uuids=explode(',', $delete);
            $Sup=  SupCategory::whereIn('uuid', $uuids)->get();

            foreach ($Sup as $item){
                File::delete(public_path(SupCategory::PATH_IMAGE.$item->imageCategory->filename));
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
