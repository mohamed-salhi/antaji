<?php

namespace App\Http\Controllers\Admin\Conversation;

use App\Http\Controllers\Controller;
use App\Models\Conversation;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ConversationController extends Controller
{
   public function index(){
       return view('admin.conversations.index');
   }


    public function indexTable(Request $request)
    {
        $conversations = Conversation::query();
        return Datatables::of($conversations)
            ->filter(function ($query) use ($request) {
                if ($request->name){
                    $query->whereHas('userOne',function ($q)use ($request){
                        $q->where('name',$request->name);
                    })->orwhereHas('userTow',function ($q)use ($request){
                        $q->where('name',$request->name);
                    });
                }

            })
            ->addColumn('checkbox',function ($que){
                return $que->uuid;
            })
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->uuid . '" ';
                $data_attr .= 'data-one="' . $que->one . '" ';
                $data_attr .= 'data-tow="' . $que->tow . '" ';

                $url=route('conversations.details',$que->uuid);
                $string = '';
                $string .= '<button class="detail_btn btn btn-sm btn-outline-primary" data-toggle="modal"
                    data-target="#details_modal" data-uuid="'.$que->uuid.'" data-url="'.$url.'">' . __('details') . '</button>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger btn_delete" data-uuid="' . $que->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            })
            ->rawColumns(['action'])->toJson();
    }

    public function details($uuid)
    {
        $conversation=Conversation::query()->findOrFail($uuid);
        return  $chat=$conversation->chat;
    }
}
