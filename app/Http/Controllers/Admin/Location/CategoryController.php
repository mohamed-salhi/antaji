<?php

namespace App\Http\Controllers\Admin\Location;

use App\Http\Controllers\Controller;
use App\Models\CategoryContent;
use App\Models\User;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    public function index(){
        return view('admin.locations.category');
    }
    public function store(Request $request)
    {
        $rules = [];
        foreach (locales() as $key => $language) {
            $rules['name_' . $key] = 'required|string|max:45';
        }
        $this->validate($request, $rules);
        $data = [];
        foreach (locales() as $key => $language) {
            $data['name'][$key] = $request->get('name_' . $key);
        }
        $data['type']='location';
        $CategoryLocaton= CategoryContent::create($data);
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
        $data['type']='location';
        $CategoryLocaton= CategoryContent::query()->withoutGlobalScope('status')->findOrFail($request->uuid);
        $CategoryLocaton->update($data);

//        $category->types()->sync($request->types);
        return response()->json([
            'item_edited'
        ]);

    }

    public function destroy($uuid)
    {

        try {
            $uuids=explode(',', $uuid);
            $CategoryLocaton= CategoryContent::query()->whereIn('uuid', $uuids)->delete();
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
        $CategoryLocaton= CategoryContent::query()->where('type','location')->withoutGlobalScope('status')->orderBy('created_at');

        return Datatables::of($CategoryLocaton)
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
<button type="button"  data-url="' . $currentUrl . "/admin/locations/categories/updateStatus/0/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('active') . '</button>
                    ';
                }else{
                    $data='
<button type="button"  data-url="' . $currentUrl . "/admin/locations/categories/updateStatus/1/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-uuid="' . $que->uuid .
                        '">' . __('inactive') . '</button>
                    ';
                }
                return $data;
            })
            ->rawColumns(['action', 'status'])->toJson();
    }

    public function UpdateStatus($status,$sup)
    {
        $uuids=explode(',', $sup);

        $CategoryLocaton= CategoryContent::query()->withoutGlobalScope('status')
            ->whereIn('uuid',$uuids)
            ->update([
                'status'=>$status
            ]);
        return response()->json([
            'item_edited'
        ]);
    }
}
