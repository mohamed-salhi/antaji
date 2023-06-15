<?php

namespace App\Http\Controllers\Admin\Specialization;

use App\Http\Controllers\Controller;
use App\Models\Specialization;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SpecializationController extends Controller
{
    public function index()
    {

        return view('admin.specializations.index');
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

        Specialization::create($data);
        return response()->json([
            'item_added'
        ]);    }

    public function update(Request $request)
    {

        $rules = [];
        foreach (locales() as $key => $language) {
            $rules['name_' . $key] = 'required|string|max:255';
        }
        $rules['country_uuid']='required|exists:countries,uuid';
        $this->validate($request, $rules);
        $data = [];
        foreach (locales() as $key => $language) {
            $data['name'][$key] = $request->get('name_' . $key);
        }
        $specializations = Specialization::query()->withoutGlobalScope('skill')->findOrFail($request->uuid);
        $specializations->update($data);
        return response()->json([
            'item_edited'
        ]);

    }

    public function destroy($uuid)
    {
        $uuids=explode(',', $uuid);
        Specialization::query()->withoutGlobalScope('skill')->whereIn('uuid', $uuids)->delete();
        return response()->json([
            'item_deleted'
        ]);
    }


    public function indexTable(Request $request)
    {
        $specializations = Specialization::query()->withoutGlobalScope('city');

        return Datatables::of($specializations)
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
                <input class="activate-row"  url="' . $currentUrl . "/specializations/updateStatus/" . $que->uuid . '" type="checkbox" id="checkbox' . $que->uuid . '" ' .
                    ($que->status ? 'checked' : '')
                    . '>
                <label for="checkbox' . $que->uuid . '"><span class="checkbox-icon"></span> </label>
            </div>';
            })
            ->rawColumns(['action', 'status'])->toJson();
    }

    public function updateStatus($uuid)
    {
//        Gate::authorize('place.update');
        $activate =  Specialization::query()->withoutGlobalScope('skill')->findOrFail($uuid);
        $activate->status = !$activate->status;
        if (isset($activate) && $activate->save()) {
            return response()->json([
                'item_edited'
            ]);
        }
    }
}
