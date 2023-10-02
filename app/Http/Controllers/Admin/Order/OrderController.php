<?php

namespace App\Http\Controllers\Admin\Order;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentGateway;
use App\Models\Upload;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class OrderController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:order', ['only' => ['index','store','create','destroy','edit','update']]);
    }
    public function index(){
        $method=PaymentGateway::all();

        return view('admin.orders.index',compact('method'));
    }



    public function indexTable(Request $request)
    {
        $items = Order::query()->orderByDesc('created_at');
        return Datatables::of($items)
            ->filter(function ($query) use ($request) {
                if ($request->user_name){
                    $query->whereHas('user',function ($query)use ($request){
                        $query->where('name','like',"%{$request->user_name}%");
                    });
                }
                if ($request->order_number){
                        $query->where('order_number', 'like', "%{$request->order_number}%");
                }
                if ($request->price) {
                    $query->where('price', $request->price);
                }
                if ($request->payment_method_id) {
                    $query->where('payment_method_id', $request->payment_method_id);
                }

            })->addColumn('status',function ($q){
               return $string='<h3 class="btn  btn-sm" style="color:white;  background:'.$q->status_color.' ">'.$q->status_text.'</h3>';
            }) ->addColumn('details', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->uuid . '" ';
                $data_attr .= 'data-price="' . $que->price . '" ';
                $data_attr .= 'data-discount="' . $que->discount_uuid . '" ';
                $data_attr .= 'data-commission="' . $que->commission . '" ';
                $data_attr .= 'data-delivery="' . $que->delivery . '" ';
                $data_attr .= 'data-days_count="' . $que->days_count . '" ';

                $data_attr .= 'data-multi_day_discounts="' . $que->multi_day_discounts . '" ';
               $days= ($que->days_count!=0)?$que->days_count:1;
                $data_attr .= 'data-all="' .($que->price *$days)- $que->discount_uuid +$que->commission +$que->delivery+$que->multi_day_discounts . '" ';


                $string = '';
                $string .= '<button class=" btn btn-sm btn-outline-primary btn_details" data-toggle="modal"
                    data-target="#btn_details" ' . $data_attr . '>' . __('details') . '</button>';

                return $string;
            })

            ->rawColumns(['status','details'])->toJson();
    }

}
