<?php

namespace App\Http\Controllers\Admin\Skill;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Yajra\DataTables\Facades\DataTables;

class SkillController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:artisan', ['only' => ['index','store','create','destroy','edit','update']]);
    }
    public function index()
    {

        return view('admin.skills.index');
    }


    public function store(Request $request)
    {


        if ($request->hasFile('excel')){

            $file = request()->file('excel');
            Excel::import(new \App\Imports\skills(), $file);
            return response()->json([
                'item_added'
            ]);
        }
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
        $skills = Skill::query()->withoutGlobalScope('skill')->orderByDesc('created_at');

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
            })
            ->addColumn('status', function ($que)  {
                $currentUrl = url('/');
                if ($que->status==1){
                    $data='
<button type="button"  data-url="' . $currentUrl . "/admin/skills/updateStatus/0/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('active') . '</button>
                    ';
                }else{
                    $data='
<button type="button"  data-url="' . $currentUrl . "/admin/skills/updateStatus/1/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-uuid="' . $que->uuid .
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

        $activate =  Skill::query()->withoutGlobalScope('skill')
            ->whereIn('uuid',$uuids)
            ->update([
                'status'=>$status
            ]);
        return response()->json([
            'item_edited'
        ]);
    }
}
