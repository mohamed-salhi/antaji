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
                    $query->where('status',$request->status);
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
            }) ->addColumn('status', function ($que) {
                $currentUrl = url('/');
                return '<div class="checkbox">
                <input class="activate-row"  url="' . $currentUrl . "/services/updateStatus/" . $que->uuid . '" type="checkbox" id="checkbox' . $que->id . '" ' .
                    ($que->status ? 'checked' : '')
                    . '>
                <label for="checkbox' . $que->uuid . '"><span class="checkbox-icon"></span> </label>
            </div>';
            })
            ->rawColumns(['action', 'status'])->toJson();
    }

    public function updateStatus($uuid)
    {

        $activate =  Service::query()->withoutGlobalScope('service')->findOrFail($uuid);
        $activate->status = !$activate->status;
        if (isset($activate) && $activate->save()) {
            return  response()->json([
                'item_edited'
            ]);
        }
    }
}

