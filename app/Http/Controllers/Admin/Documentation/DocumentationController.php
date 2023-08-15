<?php

namespace App\Http\Controllers\Admin\Documentation;

use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;

class DocumentationController extends Controller
{
    public function index(Request $request)
    {

        return view('admin.documentations.index');
    }

    public function indexTable(Request $request)
    {
        $users = User::query()->orderByDesc('updated_at')->whereNotNull('documentation');
        return Datatables::of($users)
            ->filter(function ($query) use ($request) {
                if ($request->documentation){
                    ($request->documentation==1)?$query->where('documentation',$request->documentation):$query->where('documentation',0);
                }
                if ($request->name){
                    $query->where('name','like', "%{$request->name}%");
                }if ($request->mobile){
                    $query->where('mobile','like', "%{$request->mobile}%");
                }
            })
            ->addColumn('status', function ($que)  {
                $currentUrl = url('/');
                if ($que->documentation==User::PENDING){
                    $data='
<button type="button"  data-url="' . $currentUrl . "/admin/documentations/updateStatus/1/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('accept') . '</button>
<button type="button"  data-url="' . $currentUrl . "/admin/documentations/updateStatus/2/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-uuid="' . $que->uuid .
                        '">' . __('reject') . '</button>
                    ';

                }elseif ($que->documentation==User::ACCEPT){
                    $data='
<button type="button" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('acceptable') . '</button>';
                }elseif ($que->documentation==User::REJECT){
                    $data='
<button type="button" class=" btn btn-sm btn-outline-danger" data-uuid="' . $que->uuid .
                        '">' . __('unacceptable') . '</button>';
                }
                return $data;


            })
            ->rawColumns(['status'])->toJson();
    }

    public function updateStatus($status,$uuid)
    {

       $user= User::query()->find($uuid);

           $user->update([
            'documentation'=>$status
        ]);
//        if ($status==User::REJECT){
//            File::delete(public_path(User::PATH_ID . @$user->idUserImage->filename));
//            $user->idUserImage()->delete();
//        }

        return response()->json([
            'item_edited'
        ]);
    }


}
