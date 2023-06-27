<?php

namespace App\Http\Controllers\Admin\Business;

use App\Http\Controllers\Controller;
use App\Models\Businessimages;
use App\Models\BusinessVideo;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Yajra\DataTables\Facades\DataTables;

class BusinessController extends Controller
{
    public function indexVideo(){
        $users=User::query()->select('name','uuid')->where('type','artist')->get();
        return view('admin.business.video',compact('users'));
    }
    public function storeVideo(Request $request)
    {
        $rules['video'] = 'required';
        $rules['title'] = 'required|string|max:100';
        $rules['image'] = 'required|image';
        $rules['user_uuid']='required|exists:users,uuid';
        $this->validate($request, $rules);
        $busines = BusinessVideo::query()->create($request->only('title', 'user_uuid'));
        if ($request->hasFile('image')) {
            UploadImage($request->image, BusinessVideo::PATH_IMAGE, BusinessVideo::class, $busines->uuid, false, null, Upload::IMAGE);
        }
        if ($request->hasFile('video')) {
            $video=  UploadImage($request->video, BusinessVideo::PATH_VIDEO, BusinessVideo::class, $busines->uuid, false, null, Upload::VIDEO);
            $getID3 = new \getID3;
            $video_file = $getID3->analyze('upload/business/video/'.$video->filename);
            $duration_string = $video_file['playtime_string'];
            $busines->time=$duration_string;
            $busines->save();
        }


        return response()->json([
            'item_added'
        ]);
    }
    public function updateVideo(Request $request)
    {
        $rules['video'] = 'nullable';
        $rules['title'] = 'required|string|max:100';
        $rules['image'] = 'nullable|image';
        $rules['user_uuid']='required|exists:users,uuid';
        $this->validate($request, $rules);
        $business=  BusinessVideo::query()->findOrFail($request->uuid);
        $business->update($request->only('title', 'user_uuid'));
        if ($request->hasFile('image')) {
            UploadImage($request->image, BusinessVideo::PATH_IMAGE, BusinessVideo::class, $business->uuid, true, null, Upload::IMAGE);
        }
        if ($request->hasFile('video')) {
            $video=  UploadImage($request->video, BusinessVideo::PATH_VIDEO, BusinessVideo::class, $business->uuid, true, null, Upload::VIDEO);
            $getID3 = new \getID3;
            $video_file = $getID3->analyze('upload/business/video/'.$video->filename);
            $duration_string = $video_file['playtime_string'];
            $business->time=$duration_string;
            $business->save();
        }
        return response()->json([
            'item_edite'
        ]);
    }
    public function destroyVideo($uuid)
    {
        $uuids=explode(',', $uuid);
        $Business_Video=  BusinessVideo::whereIn('uuid', $uuids)->get();
        foreach ($Business_Video as $item) {
            File::delete(public_path(BusinessVideo::PATH_IMAGE . @$item->imageBusiness->filename));
            File::delete(public_path(BusinessVideo::PATH_VIDEO . @$item->videoBusiness->filename));
            $item->videoBusiness()->delete();
            $item->imageBusiness()->delete();
            $item->delete();
        }
        return response()->json([
                'item_deleted'
            ]);

    }
    public function indexTableVideo(Request $request)
    {
        $Business_Video = BusinessVideo::query()->withoutGlobalScope('status')->orderByDesc('created_at');
        return Datatables::of($Business_Video)
            ->filter(function ($query) use ($request) {
                if ($request->status){
                    $query->where('status',$request->status);
                }
                if ($request->view){
                    $query->where('view',$request->view);
                }
                if ($request->time){
                    $query->where('time',$request->time);
                }
                if ($request->title) {
                    $query->where('title', $request->title);
                }
                if ($request->user_uuid) {
                    $query->where('user_uuid', $request->user_uuid);
                }
                if ($request->name) {
                    $query->whereHas('artists', function($q) use ($request){
                        $q->where('type','artist')->where('name','like', "%{$request->name}%");
                    });
                }
//
            })
            ->addColumn('checkbox',function ($que){
                return $que->uuid;
            })
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->uuid . '" ';
                $data_attr .= 'data-user_uuid="' . $que->user_uuid . '" ';
                $data_attr .= 'data-title="' . $que->title . '" ';
                $data_attr .= 'data-time="' . $que->time . '" ';
                $data_attr .= 'data-view="' . $que->view . '" ';
                $data_attr .= 'data-image="' . $que->image . '" ';

                $data_attr .= 'data-video="' . $que->video . '" ';
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
<button type="button"  data-url="' . $currentUrl . "/business/video/updateStatus/0/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('active') . '</button>
                    ';
                }else{
                    $data='
<button type="button"  data-url="' . $currentUrl . "/business/video/updateStatus/1/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-uuid="' . $que->uuid .
                        '">' . __('inactive') . '</button>
                    ';
                }
                return $data;
            })
            ->rawColumns(['action', 'status'])->toJson();
    }
    public function updateStatusVideo($status,$sup)
    {
        $uuids=explode(',', $sup);

        $Business_Video =  BusinessVideo::query()->withoutGlobalScope('status')
            ->whereIn('uuid',$uuids)
            ->update([
                'status'=>$status
            ]);
        return response()->json([
            'item_edited'
        ]);
    }


    public function indexImages(){
        $users=User::query()->select('name','uuid')->where('type','artist')->get();
       $path=Businessimages::PATH;
        return view('admin.business.images',compact('users','path'));
    }
    public function storeImages(Request $request)
    {
        $rules['images'] = 'required';
        $rules['user_uuid']='required|exists:users,uuid';
        $this->validate($request, $rules);
        $business = Businessimages::firstOrNew(
            ['user_uuid' => request('user_uuid')],
        );
        $business->save();
        if ($request->hasFile('images')) {
            foreach ($request->images as $item) {
                UploadImage($item, Businessimages::PATH, Businessimages::class, $business->uuid, false, null, Upload::IMAGE); // one يعني انو هذه الصورة تابعة لمعرض الاعمال الي من نوع الفيديوهات

            }
        }
        return response()->json([
            'item_added'
        ]);
    }
    public function updateImages(Request $request)
    {
//        $rules['images'] = 'required';
//        $rules['images.*'] = 'images';
        $rules['user_uuid']='required|exists:users,uuid';
        $this->validate($request, $rules);
        $business=  Businessimages::query()->findOrFail($request->uuid);
        $business->update($request->only('user_uuid'));
        if (isset($request->delete_images)) {
            $images = Upload::query()->where('imageable_type',Businessimages::class)->where('imageable_id',$business->uuid)->whereNotIn('uuid', $request->delete_images)->get();
            foreach ($images as $item) {
                File::delete(public_path(Businessimages::PATH . $item->filename));
                $item->delete();
            }
        }
        if ($request->hasFile('images')) {
            foreach ($request->images as $item) {
                UploadImage($item, Businessimages::PATH, Businessimages::class, $business->uuid, false, null, Upload::IMAGE);
            }
        }
        return response()->json([
            'item_edite'
        ]);
    }
    public function destroyImages($uuid)
    {
        $uuids = explode(',', $uuid);
        $business = Businessimages::whereIn('uuid', $uuids)->withoutGlobalScope('status')->get();

        foreach ($business as $item) {
            foreach ($item->imageBusiness as $image) {
                File::delete(public_path(Businessimages::PATH . $image->filename));
                $image->delete();
            }
            $item->delete();
        }
        return response()->json([
            'item_deleted'
        ]);

    }
    public function indexTableImages(Request $request)
    {
        $business = Businessimages::query()->withoutGlobalScope('status')->orderByDesc('created_at');
        return Datatables::of($business)
            ->filter(function ($query) use ($request) {
                if ($request->status){
                    ($request->status==1)?$query->where('status',$request->status):$query->where('status',0);
                }
                if ($request->name){
                    $query->whereHas('artists', function($q) use ($request){
                        $q->where('type','artist')->where('name','like', "%{$request->name}%");
                    });
                }
//
            })
            ->addColumn('checkbox',function ($que){
                return $que->uuid;
            })
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->uuid . '" ';
                $data_attr .= 'data-user_uuid="' . $que->user_uuid . '" ';
                $data_attr .= 'data-images_uuid="' . implode(',', $que->imageBusiness->pluck('uuid')->toArray()) .'" ';
                $data_attr .= 'data-images="' . implode(',', $que->imageBusiness->pluck('filename')->toArray()) .'" ';

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
<button type="button"  data-url="' . $currentUrl . "/business/images/updateStatus/0/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('active') . '</button>
                    ';
                }else{
                    $data='
<button type="button"  data-url="' . $currentUrl . "/business/images/updateStatus/1/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-uuid="' . $que->uuid .
                        '">' . __('inactive') . '</button>
                    ';
                }
                return $data;
            })
            ->rawColumns(['action', 'status'])->toJson();
    }
    public function updateStatusImages($status,$sup)
    {
        $uuids=explode(',', $sup);

        $business =  Businessimages::query()->withoutGlobalScope('status')
            ->whereIn('uuid',$uuids)
            ->update([
                'status'=>$status
            ]);
        return response()->json([
            'item_edited'
        ]);
    }
}
