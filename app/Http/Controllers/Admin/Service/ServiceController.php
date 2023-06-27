<?php

namespace App\Http\Controllers\Admin\Service;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Service;
use App\Models\Image;
use App\Models\Upload;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;
class ServiceController extends Controller
{
    public function index(){
        return view('admin.services.index');
    }

    public function store(Request $request)
    {
        $rules = [];
        foreach (locales() as $key => $language) {
            $rules['name_' . $key] = 'required|string|max:255';
        }
        $rules['image']='required|image';
        $this->validate($request, $rules);
        $data = [];
        foreach (locales() as $key => $language) {
            $data['name'][$key] = $request->get('name_' . $key);
        }
      $service=Service::query()->create($data);
        if ($request->hasFile('image')) {
            UploadImage($request->image, "upload/service/", Service::class, $service->uuid, false ,null,Upload::IMAGE);
        }

        return response()->json([
           'item_added'
        ]);

    }


    public function update(Request $request)
    {
        $service=  Service::findOrFail($request->uuid);
        $rules = [];
        foreach (locales() as $key => $language) {
            $rules['name_' . $key] = 'required|string|max:255';
        }
        $rules['image']='nullable|image';
        $this->validate($request, $rules);
        $data = [];
        foreach (locales() as $key => $language) {
            $data['name'][$key] = $request->get('name_' . $key);
        }
        $service->update($data);
        if ($request->hasFile('image')) {
            UploadImage($request->image, "upload/service/", Service::class, $service->uuid, true,null,Upload::IMAGE);
        }

              return response()->json([
                  'item_edited'
              ]);

    }

    public function destroy($uuid)
    {
        try {
            $uuids=explode(',', $uuid);
            $service=  Service::whereIn('uuid', $uuids)->withoutGlobalScope('service')->get();

            foreach ($service as $item){
                File::delete(public_path('upload/service/'.$item->iconService->filename));
                $item->iconService()->delete();
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
        $service=Service::query()->withoutGlobalScope('service');
        return Datatables::of($service)
            ->filter(function ($query) use ($request) {
                if ($request->status){
                    ($request->status==1)?$query->where('status',$request->status):$query->where('status',0);
                }
                if ($request->name){
                    $query->where('name->' . locale(), 'like', "%{$request->name}%");

                                       foreach (locales() as $key => $value) {
                                           if ($key != locale())
                                               $query->orWhere('name->' . $key, 'like', "%{$request->name}%");
                                       }
                }
            })
            ->addColumn('checkbox',function ($que){
                return $que->uuid;
            })
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->uuid . '" ';
                $data_attr .= 'data-image="' . $que->icon . '" ';
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
            })->addColumn('status', function ($que)  {
                $currentUrl = url('/');
                if ($que->status==1){
                    $data='
<button type="button"  data-url="' . $currentUrl . "/services/updateStatus/0/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('active') . '</button>
                    ';
                }else{
                    $data='
<button type="button"  data-url="' . $currentUrl . "/services/updateStatus/1/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-uuid="' . $que->uuid .
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

        $activate =  Service::query()->withoutGlobalScope('service')
            ->whereIn('uuid',$uuids)
            ->update([
                'status'=>$status
            ]);
        return response()->json([
            'item_edited'
        ]);
    }
}

