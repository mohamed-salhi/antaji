<?php

namespace App\Http\Controllers\Admin\Serving;

use App\Http\Controllers\Controller;
use App\Models\CategoryContent;
use App\Models\City;
use App\Models\Content;
use App\Models\Serving;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ServingController extends Controller
{
    public function index()
    {
        $cities=City::query()->select('name','uuid')->get();
        $category_contents = CategoryContent::query()->where('type', 'serving')->select('uuid', 'name')->get();
        $users = User::query()->select('name', 'uuid')->get();

        return view('admin.servings.index', compact('category_contents', 'users','cities'));
    }

    public function store(Request $request){

        $rules = [
            'user_uuid' => 'required|exists:users,uuid',
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'category_contents_uuid' => 'required|exists:category_contents,uuid',
            'city_uuid' =>'required|exists:cities,uuid',
            'from' => 'required|date|after:'.date('Y/m/d'),
            'to' => 'required|date|after:'.$request->form,
            'working_condition' => 'required|in:contract,Fixed_price,hour',
        ];

        $this->validate($request, $rules);
        $serving= Serving::query()->create($request->only('name','user_uuid','price','details','category_contents_uuid','city_uuid','to','from','working_condition'));
        $content= Content::query()->create([
            'content_uuid'=>$serving->uuid,
            'user_uuid' => $request->user_uuid,
        ]);
        return response()->json([
            'item_added'
        ]);
    }

    public function update(Request $request)
    {
        $serving = Serving::query()->withoutGlobalScope('status')->findOrFail($request->uuid);
        $rules = [
            'user_uuid' => 'required|exists:users,uuid',
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'category_contents_uuid' => 'required|exists:category_contents,uuid',
            'city_uuid' =>'required|exists:cities,uuid',
            'from' => 'required|date|after:'.date('Y/m/d'),
            'to' => 'required|date|after:'.$request->form,
            'working_condition' => 'required|in:contract,Fixed_price,hour',
        ];
        $this->validate($request, $rules);
        $serving->update($request->only('name','user_uuid','price','details','category_contents_uuid','city_uuid','to','from','working_condition'));
        return response()->json([
            'item_edited'
        ]);

    }

    public function destroy($uuid)
    {

//        try {
        $uuids = explode(',', $uuid);
        Serving::whereIn('uuid', $uuids)->withoutGlobalScope('status')->delete();
        return response()->json([
            'item_deleted'
        ]);

    }

    public function indexTable(Request $request)
    {
        $serving = Serving::query()->withoutGlobalScope('status')->orderByDesc('created_at');
        return Datatables::of($serving)
            ->filter(function ($query) use ($request) {
                if ($request->status) {
                    ($request->status==1)? $query->where('status', $request->status):$query->where('status',0);
                }
                if ($request->price) {
                    $query->where('price', $request->price);
                }
                if ($request->name) {
                    $query->where('name', $request->name);
                }
                if ($request->category_contents_uuid) {
                    $query->where('category_contents_uuid', $request->category_contents_uuid);
                }
                if ($request->working_condition) {
                    $query->where('working_condition', $request->working_condition);
                }
                if ($request->from) {
                    $query->where('from', $request->from);
                }
                if ($request->to) {
                    $query->where('to', $request->to);
                }
                if ($request->city_uuid) {
                    $query->where('city_uuid', $request->city_uuid);
                }
//
            })
            ->addColumn('checkbox', function ($que) {
                return $que->uuid;
            })
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->uuid . '" ';
                $data_attr .= 'data-name="' . $que->name . '" ';
                $data_attr .= 'data-price="' . $que->price . '" ';
                $data_attr .= 'data-user_uuid="' . $que->user_uuid . '" ';
                $data_attr .= 'data-details="' . $que->details . '" ';
                $data_attr .= 'data-to="' . $que->to . '" ';
                $data_attr .= 'data-from="' . $que->from . '" ';
                $data_attr .= 'data-city_uuid="' . $que->city_uuid . '" ';
                $data_attr .= 'data-category_contents_uuid="' . $que->category_contents_uuid . '" ';
                $data_attr .= 'data-working_condition="' . $que->working_condition . '" ';

                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary btn_edit" data-toggle="modal"
                    data-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';

                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger btn_delete" data-uuid="' . $que->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })->addColumn('status', function ($que) {
                $currentUrl = url('/');
                if ($que->status == 1) {
                    $data = '
<button type="button"  data-url="' . $currentUrl . "/servings/updateStatus/0/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('active') . '</button>
                    ';
                } else {
                    $data = '
<button type="button"  data-url="' . $currentUrl . "/servings/updateStatus/1/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-uuid="' . $que->uuid .
                        '">' . __('inactive') . '</button>
                    ';
                }
                return $data;
            })->addColumn('date',function ($que){
                return '   <div class="date-cell">
        <span class="date1">from    '  .$que->from.'</span>
        <span class="date2">to   '  .$que->to.'</span>
      </div>';
            })
            ->rawColumns(['action', 'status','date'])->toJson();
    }

    public function updateStatus($status, $sup)
    {
        $uuids = explode(',', $sup);

        Serving::query()->withoutGlobalScope('status')
            ->whereIn('uuid', $uuids)
            ->update([
                'status' => $status
            ]);
        return response()->json([
            'item_edited'
        ]);
    }
}
