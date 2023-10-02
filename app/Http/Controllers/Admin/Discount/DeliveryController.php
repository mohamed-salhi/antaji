<?php

namespace App\Http\Controllers\Admin\Discount;

use App\Http\Controllers\Controller;
use App\Models\Delivery;
use App\Models\Discount;
use App\Models\MultiDayDiscount;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class DeliveryController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:discount', ['only' => ['index','store','create','destroy','edit','update']]);
    }
    public function index()
    {
        return view('admin.discounts.delivery');
    }

    public function update(Request $request)
    {

        $discount = MultiDayDiscount::query()->first();
        $rules = [
            'delivery' => 'required|int',
        ];

        $this->validate($request, $rules);
        $data = [
            'delivery'=>$request->rate,
        ];
        $discount->update($data);

        return response()->json([
            'item_edited'
        ]);

    }

    public function indexTable(Request $request)
    {
        $discount = Delivery::query()->withoutGlobalScope('status')->orderByDesc('created_at');
        return Datatables::of($discount)
            ->addColumn('checkbox', function ($que) {
                return $que->id;
            })
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-id="' . $que->id . '" ';
                $data_attr .= 'data-delivery="' . $que->delivery . '" ';
               $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary btn_edit" data-toggle="modal"
                    data-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                return $string;
            })->addColumn('status', function ($que) {
                $currentUrl = url('/');
                if ($que->status == 1) {
                    $data = '
<button type="button"  data-url="' . $currentUrl . "/admin/delivery/updateStatus/0/" . $que->id . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-id="' . $que->id .
                        '">' . __('active') . '</button>
                    ';
                } else {
                    $data = '
<button type="button"  data-url="' . $currentUrl . "/admin/delivery/updateStatus/1/" . $que->id . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-id="' . $que->id .
                        '">' . __('inactive') . '</button>
                    ';
                }
                return $data;
            })
            ->rawColumns(['action', 'status'])->toJson();
    }

    public function updateStatus($status, $sup)
    {
        $ids = explode(',', $sup);

        Delivery::query()->withoutGlobalScope('status')
            ->whereIn('id', $ids)
            ->update([
                'status' => $status
            ]);
        return response()->json([
            'item_edited'
        ]);
    }



}
