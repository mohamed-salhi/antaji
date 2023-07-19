<?php

namespace App\Http\Controllers\Admin\Discount;

use App\Http\Controllers\Controller;
use App\Models\Discount;
use App\Models\MultiDayDiscount;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class MultiDayDiscountController extends Controller
{
    public function index()
    {
        return view('admin.discounts.multidaydiscount');
    }

    public function update(Request $request)
    {

        $discount = MultiDayDiscount::query()->first();
        $rules = [
            'rate' => 'required|int',
            'minimal_day' => 'required',
        ];

        $this->validate($request, $rules);
        $data = [
            'rate'=>$request->rate,
            'minimal_day'=>$request->minimal_day,

        ];
        $discount->update($data);

        return response()->json([
            'item_edited'
        ]);

    }

    public function indexTable(Request $request)
    {
        $discount = MultiDayDiscount::query()->withoutGlobalScope('status')->orderByDesc('created_at');
        return Datatables::of($discount)
            ->addColumn('checkbox', function ($que) {
                return $que->id;
            })
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-id="' . $que->id . '" ';
                $data_attr .= 'data-rate="' . $que->rate . '" ';
                $data_attr .= 'data-minimal_day="' . $que->minimal_day . '" ';
                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary btn_edit" data-toggle="modal"
                    data-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                return $string;
            })->addColumn('status', function ($que) {
                $currentUrl = url('/');
                if ($que->status == 1) {
                    $data = '
<button type="button"  data-url="' . $currentUrl . "/admin/multidaydiscount/updateStatus/0/" . $que->id . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-id="' . $que->id .
                        '">' . __('active') . '</button>
                    ';
                } else {
                    $data = '
<button type="button"  data-url="' . $currentUrl . "/admin/multidaydiscount/updateStatus/1/" . $que->id . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-id="' . $que->id .
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

        MultiDayDiscount::query()->withoutGlobalScope('status')
            ->whereIn('id', $ids)
            ->update([
                'status' => $status
            ]);
        return response()->json([
            'item_edited'
        ]);
    }



}
