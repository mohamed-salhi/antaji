<?php

namespace App\Http\Controllers\Admin\places;

use App\Http\Controllers\Admin\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Country;
use App\Models\Image;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class countryController extends Controller
{
//    use ResponseTrait;
//    function __construct()
//    {
//        $this->middleware('permission:place-list|place-edit|place-delete', ['only' => ['index','store','activate']]);
//        $this->middleware('permission:place-create', ['only' => ['store']]);
//        $this->middleware('permission:place-edit', ['only' => ['update','activate']]);
//        $this->middleware('permission:place-delete', ['only' => ['destroy']]);
//    }

    public function index(Request $request)
    {
        return view('admin.places.country.index');
    }

    public function store(Request $request)
    {
//        Gate::authorize('place.create');
        $rules = [];
        foreach (locales() as $key => $language) {
            $rules['name_' . $key] = 'required|string|max:255';
        }
        $rules['key']='required|unique:countries,key';
        $rules['image']='required|image';
        $this->validate($request, $rules);
        $data = [];
        foreach (locales() as $key => $language) {
            $data['name'][$key] = $request->get('name_' . $key);
        }
        $data['key']=$request->key;
      $country=  Country::query()->create($data);
        if ($request->hasFile('image')) {
            UploadImage($request->image, "upload/country/", 'App\Models\Country', $country->uuid, false ,null,Upload::IMAGE);
        }

        return response()->json([
           'item_added'
        ]);

    }


    public function update(Request $request)
    {
        $country = Country::findOrFail($request->uuid);

//        Gate::authorize('place.update');
        $rules = [];
        foreach (locales() as $key => $language) {
            $rules['name_' . $key] = 'required|string|max:255';
        }
        $rules['key']='required|unique:countries,key,'.$request->uuid.',uuid';

        $rules['image']='nullable|image';
        $this->validate($request, $rules);
        $data = [];
        foreach (locales() as $key => $language) {
            $data['name'][$key] = $request->get('name_' . $key);
        }
        $data['key']=$request->key;
        $country->update($data);
        if ($request->hasFile('image')) {
            UploadImage($request->image, "upload/country/", 'App\Models\Country', $country->uuid, true,null,Upload::IMAGE);
        }

              return response()->json([
                  'item_edited'
              ]);

    }

    public function destroy($uuid)
    {
//        Gate::authorize('place.delete');

        try {
            $uuids=explode(',', $uuid);
            $country=  Country::whereIn('uuid', $uuids)->get();

            foreach ($country as $item){
                File::delete(public_path('upload/country/'.$item->imageCountry->filename));
                $item->imageCountry()->delete();
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
        $countrys = Country::query()->withoutGlobalScope('country');
        return Datatables::of($countrys)
            ->filter(function ($query) use ($request) {
                if ($request->status){
                    $query->where('status',$request->status);
                }
//                if ($request->get('search')) {
//                    $query->orWhere('key','like', "%{$request->search['value']}%");
//                    $query->where('name->' . locale(), 'like', "%{$request->search['value']}%");
//
//                    foreach (locales() as $key => $value) {
//                        if ($key != locale())
//                            $query->orWhere('name->' . $key, 'like', "%{$request->search['value']}%");
//                    }
//
//                }
            })
            ->addColumn('checkbox',function ($que){
                return $que->uuid;
            })
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->uuid . '" ';
                $data_attr .= 'data-key="' . $que->key . '" ';
                $data_attr .= 'data-image="' . $que->image . '" ';
                foreach (locales() as $key => $value) {
                    $data_attr .= 'data-name_' . $key . '="' . $que->getTranslation('name', $key) . '" ';
                }
                $user = Auth()->user();

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
<button type="button"  data-url="' . $currentUrl . "/countries/updateStatus/0/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('active') . '</button>
                    ';
                }else{
                    $data='
<button type="button"  data-url="' . $currentUrl . "/countries/updateStatus/1/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-uuid="' . $que->uuid .
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

        $activate =  Country::query()->withoutGlobalScope('country')
            ->whereIn('uuid',$uuids)
            ->update([
                'status'=>$status
            ]);
        return response()->json([
            'item_edited'
        ]);
    }


}
