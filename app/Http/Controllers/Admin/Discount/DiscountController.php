<?php

namespace App\Http\Controllers\Admin\Discount;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\DiscountContent;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DiscountController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:discount', ['only' => ['index','store','create','destroy','edit','update']]);
    }
    public function index()
    {
        return view('admin.discounts.index');
    }

    public function store(Request $request)
    {

        $rules = [
            'code' => 'required|string|max:36',
            'discount' => 'required|int',
            'discount_type' => 'required|string',
            'number_of_usage' => 'required|integer',
            'number_of_usage_for_user' => 'required|integer',
            'date_from' => 'required|date',
            'checkboxes' => 'nullable|array',
            'date_to' => 'required|date|after:' . $request->date_from,
        ];
        foreach (locales() as $key => $language) {
            $rules['name_' . $key] = 'required|string|max:255';
        }
        $this->validate($request, $rules);

        $data = [
            'code'=>$request->code,
            'discount'=>$request->discount,
            'discount_type'=>$request->discount_type,
            'number_of_usage'=>$request->number_of_usage,
            'number_of_usage_for_user'=>$request->number_of_usage_for_user,
            'date_from'=>$request->date_from,
            'date_to'=>$request->date_to,
        ];
        foreach (locales() as $key => $language) {
            $data['name'][$key] = $request->get('name_' . $key);
        }

        $discount = Discount::query()->create($data);
      foreach ($request->checkboxes as $item){
          DiscountContent::query()->create([
             'discount_uuid'=>$discount->uuid,
              'type'=>$item
          ]);
      }
        return response()->json([
            'item_added'
        ]);
    }

    public function update(Request $request)
    {
        $discount = Discount::query()->withoutGlobalScope('status')->findOrFail($request->uuid);
        $rules = [
            'code' => 'required|string|max:36',
            'discount' => 'required|int',
            'discount_type' => 'required|string',
            'number_of_usage' => 'required|integer',
            'number_of_usage_for_user' => 'required|integer',
            'checkboxes' => 'nullable|array',
            'date_from' => 'required|date',
            'date_to' => 'required|date|after:' . $request->form,
        ];
        foreach (locales() as $key => $language) {
            $rules['name_' . $key] = 'required|string|max:255';
        }
        $this->validate($request, $rules);
        $data = [
            'code'=>$request->code,
            'discount'=>$request->discount,
            'discount_type'=>$request->discount_type,
            'number_of_usage'=>$request->number_of_usage,
            'number_of_usage_for_user'=>$request->number_of_usage_for_user,
            'date_from'=>$request->date_from,
            'date_to'=>$request->date_to,

        ];

        foreach (locales() as $key => $language) {
            $data['name'][$key] = $request->get('name_' . $key);
        }
        $discount->discountContent()->delete();
        $discount->update($data);
        foreach ($request->checkboxes as $item){
            DiscountContent::query()->updateOrCreate([
                'discount_uuid'=>$discount->uuid,
                'type'=>$item
            ]);
        }
        return response()->json([
            'item_edited'
        ]);

    }

    public function destroy($uuid)
    {

//        try {
        $uuids = explode(',', $uuid);
        Discount::whereIn('uuid', $uuids)->withoutGlobalScope('status')->delete();
        return response()->json([
            'item_deleted'
        ]);

    }

    public function indexTable(Request $request)
    {
        $discount = Discount::query()->withoutGlobalScope('status')->orderByDesc('created_at');
        return Datatables::of($discount)
            ->filter(function ($query) use ($request) {
                if ($request->status) {
                    ($request->status==1)? $query->where('status', $request->status):$query->where('status',0);
                }
                if ($request->discount) {
                    $query->where('discount', $request->discount);
                }
                if ($request->code) {
                    $query->where('code', $request->code);
                }
                if ($request->name) {
                    $query->where('name->' . locale(), 'like', "%{$request->name}%");

                    foreach (locales() as $key => $value) {
                        if ($key != locale())
                            $query->orWhere('name->' . $key, 'like', "%{$request->name}%");
                    }

                }
                if ($request->discount_type) {
                    $query->where('discount_type', $request->discount_type);
                }
//                if ($request->date_to) {
//                    $query->whereData('date_to', $request->date_to);
//                }
//                if ($request->date_from) {
//                    $query->whereData('date_from', $request->date_from);
//                }
//                if ($request->from) {
//                    $query->where('from', $request->from);
//                }
//                if ($request->to) {
//                    $query->where('to', $request->to);
//                }
//                if ($request->city_uuid) {
//                    $query->where('city_uuid', $request->city_uuid);
//                }

            })
            ->addColumn('checkbox', function ($que) {
                return $que->uuid;
            })
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->uuid . '" ';
                $data_attr .= 'data-code="' . $que->code . '" ';
                $data_attr .= 'data-discount="' . $que->discount . '" ';
                $data_attr .= 'data-discount_type="' . $que->discount_type . '" ';
                $data_attr .= 'data-number_of_usage="' . $que->number_of_usage . '" ';
                $data_attr .= 'data-number_of_usage_for_user="' . $que->number_of_usage_for_user . '" ';
                $data_attr .= 'data-date_from="' . $que->date_from . '" ';
                $data_attr .= 'data-date_to="' . $que->date_to . '" ';
                $data_attr .= 'data-checkboxes="' . implode(',', $que->discountContent->pluck('type')->toArray()) .'" ';
                foreach (locales() as $key => $value) {
                    $data_attr .= 'data-name_' . $key . '="' . $que->getTranslation('name', $key) . '" ';
                }
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
<button type="button"  data-url="' . $currentUrl . "/admin/discount/updateStatus/0/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('active') . '</button>
                    ';
                } else {
                    $data = '
<button type="button"  data-url="' . $currentUrl . "/admin/discount/updateStatus/1/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-uuid="' . $que->uuid .
                        '">' . __('inactive') . '</button>
                    ';
                }
                return $data;
            })->addColumn('date', function ($que) {
                return '   <div class="date-cell">
        <span class="date1">'.__('from').'    ' . $que->date_from . '</span>
        <span class="date2">'.__('to').'   ' . $que->date_to . '</span>
      </div>';
            })
            ->rawColumns(['action', 'status', 'date'])->toJson();
    }

    public function updateStatus($status, $sup)
    {
        $uuids = explode(',', $sup);

        Discount::query()->withoutGlobalScope('status')
            ->whereIn('uuid', $uuids)
            ->update([
                'status' => $status
            ]);
        return response()->json([
            'item_edited'
        ]);
    }

}
