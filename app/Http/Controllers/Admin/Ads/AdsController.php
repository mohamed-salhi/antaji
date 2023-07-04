<?php

namespace App\Http\Controllers\Admin\Ads;

use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\Upload;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;

class AdsController extends Controller
{
    public function index(Request $request)
    {
        return view('admin.ads.index');
    }

    public function store(Request $request)
    {
        $rules = [
            'image'=>'required|image',
            'link'=>'required',
        ];
        $this->validate($request, $rules);
        $data = [
            'link'=>$request->link
        ];
        $ads=  Ads::query()->create($data);
        if ($request->hasFile('image')) {
            UploadImage($request->image, "upload/ads/", Ads::class, $ads->uuid, false ,null,Upload::IMAGE,'home_page');
        }
        return response()->json([
            'item_added'
        ]);

    }


    public function update(Request $request)
    {
        $ads=  Ads::findOrFail($request->uuid);

        $rules = [
            'image'=>'required|image',
            'link'=>'required',
        ];
        $this->validate($request, $rules);
        $data = [
            'link'=>$request->link
        ];
        $ads=  Ads::query()->create($data);
        $ads->update($data);
        if ($request->hasFile('image')) {
            UploadImage($request->image, "upload/ads/", Ads::class, $ads->uuid, true ,null,Upload::IMAGE,'home_page');
        }
        return response()->json([
            'item_edited'
        ]);

    }

    public function destroy($uuid)
    {

        try {
            $uuids=explode(',', $uuid);
            $ads=  Ads::whereIn('uuid', $uuids)->get();

            foreach ($ads as $item){
                File::delete(public_path('/upload/ads/'.$item->imageAds->filename));
                $item->imageAds()->delete();
                $item->delete();
            }
            return response()->json([
                'item_deleted'
            ]);
        }catch (\Exception $e){
            return response()->json([
                'err'
            ]);
        }
    }

    public function indexTable(Request $request)
    {
        $ads = Ads::query()->withoutGlobalScope('status');
        return Datatables::of($ads)
            ->filter(function ($query) use ($request) {
                if ($request->status){
                    ($request->status==1)?$query->where('status',$request->status):$query->where('status',0);
                }

            })
            ->addColumn('checkbox',function ($que){
                return $que->uuid;
            })
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->uuid . '" ';
                $data_attr .= 'data-link="' . $que->link . '" ';
                $data_attr .= 'data-image="' . $que->image . '" ';

                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary btn_edit" data-toggle="modal"
                    data-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger btn_delete" data-uuid="' . $que->uuid .
                    '">' . __('delete') . '</button>';
                return $string;
            }) ->addColumn('status', function ($que)  {
                $currentUrl = url('/');
                if ($que->status==1){
                    $data='
<button type="button"  data-url="' . $currentUrl . "/admin/ads/updateStatus/0/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('active') . '</button>
                    ';
                }else{
                    $data='
<button type="button"  data-url="' . $currentUrl . "/admin/ads/updateStatus/1/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-uuid="' . $que->uuid .
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

        $activate =  Ads::query()->withoutGlobalScope('status')
            ->whereIn('uuid',$uuids)
            ->update([
                'status'=>$status
            ]);
        return response()->json([
            'item_edited'
        ]);
    }


}
