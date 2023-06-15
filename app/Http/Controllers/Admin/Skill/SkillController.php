<?php

namespace App\Http\Controllers\Admin\Skill;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class SkillController extends Controller
{
    public function index()
    {

        return view('admin.skills.index');
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

        Skill::create($data);
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
        $skills = Skill::query()->withoutGlobalScope('skill')->findOrFail($request->uuid);
        $skills->update($data);
        return response()->json([
            'item_edited'
        ]);

    }

    public function destroy($uuid)
    {
        $uuids=explode(',', $uuid);
        Skill::query()->withoutGlobalScope('skill')->whereIn('uuid', $uuids)->delete();
        return response()->json([
            'item_deleted'
        ]);
    }


    public function indexTable(Request $request)
    {
        $skills = Skill::query()->withoutGlobalScope('city');

        return Datatables::of($skills)
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
                <input class="activate-row"  url="' . $currentUrl . "/skills/updateStatus/" . $que->uuid . '" type="checkbox" id="checkbox' . $que->uuid . '" ' .
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
        $activate =  Skill::query()->withoutGlobalScope('skill')->findOrFail($uuid);
        $activate->status = !$activate->status;
        if (isset($activate) && $activate->save()) {
            return response()->json([
                'item_edited'
            ]);
        }
    }
}
