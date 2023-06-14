<?php

namespace App\Http\Controllers\Admin\places;

use App\Http\Controllers\Admin\ResponseTrait;
use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Yajra\DataTables\Facades\DataTables;

class CityController extends Controller
{
//    use ResponseTrait;
//    function __construct()
//    {
//        $this->middleware('permission:place-list|place-edit|place-delete', ['only' => ['index','store','activate']]);
//        $this->middleware('permission:place-create', ['only' => ['store']]);
//        $this->middleware('permission:place-edit', ['only' => ['update','activate']]);
//        $this->middleware('permission:place-delete', ['only' => ['destroy']]);
//    }
    public function index()
    {

//        Gate::authorize('place.view');
        $country=Country::select(['name','uuid'])->get();

        return view('admin.places.city.index',compact('country'));
    }


    public function store(Request $request)
    {
//        Gate::authorize('place.create');
        $rules = [];
        foreach (locales() as $key => $language) {
            $rules['name_' . $key] = 'required|string|max:45';
        }
        $rules['country_uuid']='required|exists:countries,uuid';
        $this->validate($request, $rules);
        $data = [];
        foreach (locales() as $key => $language) {
            $data['name'][$key] = $request->get('name_' . $key);
        }
        $data['country_uuid']=$request->country_uuid;
        City::create($data);
        return response()->json([
            'item_added'
        ]);    }

    public function update(Request $request)
    {

//        Gate::authorize('place.update');
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
        $data['country_uuid']=$request->country_uuid;
        $brands = City::query()->withoutGlobalScope('country')->findOrFail($request->uuid);
        $brands->update($data);
        return response()->json([
            'item_edited'
        ]);

    }

    public function destroy($uuid)
    {
//        Gate::authorize('place.delete');
        $uuids=explode(',', $uuid);
        City::query()->withoutGlobalScope('city')->whereIn('uuid', $uuids)->delete();
        return response()->json([
            'item_deleted'
        ]);
    }


    public function indexTable(Request $request)
    {
        $city = City::query()->withoutGlobalScope('city');

        return Datatables::of($city)
            ->filter(function ($query) use ($request) {
                if ($request->get('search')) {
                    $locale = app()->getLocale();
                    $query->where('name->'.locale(), 'like', "%{$request->search['value']}%");
                }
                if ($request->get("country_uuid")){
                    $query->where('country_uuid', $request->get("country_uuid"));

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
                $data_attr .= 'data-country_name="' . $que->country->name . '" ';
                $data_attr .= 'data-country_uuid="' . $que->country->uuid . '" ';
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
                <input class="activate-row"  url="' . $currentUrl . "/cities/updateStatus/" . $que->uuid . '" type="checkbox" id="checkbox' . $que->uuid . '" ' .
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
        $activate =  City::query()->withoutGlobalScope('city')->findOrFail($uuid);
        $activate->status = !$activate->status;
        if (isset($activate) && $activate->save()) {
            return response()->json([
                'item_edited'
            ]);
        }
    }
}
