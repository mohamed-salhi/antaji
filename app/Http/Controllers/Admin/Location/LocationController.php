<?php

namespace App\Http\Controllers\Admin\Location;

use App\Http\Controllers\Controller;
use App\Models\CategoryContent;
use App\Models\Content;
use App\Models\Location;
use App\Models\Project;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class LocationController extends Controller
{
    public function index()
    {
        $category_contents = CategoryContent::query()->where('type', 'location')->select('uuid', 'name')->get();
        $users = User::query()->select('name', 'uuid')->get();
        return view('admin.locations.index', compact('category_contents', 'users'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string',
            'price' => 'required|int',
            'details' => 'required',
            'user_uuid' => 'required|exists:users,uuid',
            'images' => 'required',
            'images.*' => 'mimes:jpeg,jpg,png|max:2048'
        ];
        $this->validate($request, $rules);
        $location = Location::query()->create($request->only('name', 'user_uuid', 'price', 'details'));
        $location->categories()->sync($request->category_contents_uuid);
        Content::query()->create([
            'content_uuid' => $location->uuid,
            'user_uuid' => $request->user_uuid,
        ]);
        if ($request->hasFile('images')) {
            foreach ($request->images as $item) {
                UploadImage($item, Location::PATH_LOCATION, Location::class, $location->uuid, false, null, Upload::IMAGE);
            }
        }

        return response()->json([
            'item_added'
        ]);

    }

    public function update(Request $request)
    {

        $location = Location::query()->withoutGlobalScope('status')->findOrFail($request->uuid);

        $rules = [
            'name' => 'required|string',
            'price' => 'required|int',
            'details' => 'required',
            'user_uuid' => 'required|exists:users,uuid',
            'category_contents_uuid' => 'required|exists:category_contents,uuid',

        ];

        $this->validate($request, $rules);
        $location->update($request->only('name', 'details', 'price', 'user_uuid'));
        $location->categories()->sync($request->category_contents_uuid);

        if ($request->has('delete_images')) {

            $images = Upload::query()->where('imageable_type',Location::class)->where('imageable_id',$location->uuid)->whereNotIn('uuid', $request->delete_images)->get();
            foreach ($images as $item) {
                File::delete(public_path(Location::PATH_LOCATION . $item->filename));
                $item->delete();
            }
        }
            if ($request->hasFile('images')) {
                foreach ($request->images as $item) {
                    UploadImage($item, Location::PATH_LOCATION, Location::class, $location->uuid, false, null, Upload::IMAGE);
                }
            }
            return response()->json([
                'item_edited'
            ]);

        }

    public function destroy($uuid)
    {

//        try {
        $uuids = explode(',', $uuid);
        $location = Location::whereIn('uuid', $uuids)->withoutGlobalScope('status')->get();

        foreach ($location as $item) {
            foreach ($item->imageLocation as $image) {
                File::delete(public_path(Location::PATH_LOCATION . $image->filename));
                $image->delete();
            }
           $item->cart()->delete();
            $item->delete();
        }
        return response()->json([
            'item_deleted'
        ]);
//        }catch (\Exception $e){
//            return response()->json([
//                'err'
//            ]);
//        }
    }

    public function indexTable(Request $request)
    {
        $location = Location::query()->withoutGlobalScope('status')->orderByDesc('created_at');
        return Datatables::of($location)
            ->filter(function ($query) use ($request) {
                if ($request->status) {
                    ($request->status==1)?$query->where('status',$request->status):$query->where('status',0);
                }
                if ($request->price) {
                    $query->where('price', $request->price);
                }
                if ($request->name) {
                    $query->where('name', $request->name);
                }
                if ($request->user_name) {
                    $query->whereHas('user', function($q) use ($request){
                        $q->where('name','like', "%{$request->user_name}%");
                    });
                }
                if ($request->category_contents_uuid) {
                    $query->whereHas('categories', function($q) use ($request){
                        $q->where('uuid',$request->category_contents_uuid);
                    });
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
                $data_attr .= 'data-images_uuid="' . implode(',', $que->imageLocation->pluck('uuid')->toArray()) .'" ';
                $data_attr .= 'data-images="' . implode(',', $que->imageLocation->pluck('filename')->toArray()) .'" ';

                $data_attr .= 'data-category_contents_uuid="' . implode(',', $que->categories->pluck('uuid')->toArray()) . '," ';

                $url = url('/locations/images/' . $que->uuid);

                $string = '';
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary btn_edit" data-toggle="modal"
                    data-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';

                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger btn_delete" data-uuid="' . $que->uuid .
                    '">' . __('delete') . '</button>';
                $string .= ' <a href="' . $url . '"  class="btn btn-sm btn-outline-dark btn_image" data-uuid="' . $que->uuid .
                    '">' . __('images') . '  </a>';
                $string .= ' <a href="' . $url . '"  class="btn btn-sm btn-outline-info btn_image" data-uuid="' . $que->uuid .
                    '">' . __('details') . '  </a>';
                return $string;
            })->addColumn('status', function ($que) {
                $currentUrl = url('/');
                if ($que->status == 1) {
                    $data = '
<button type="button"  data-url="' . $currentUrl . "/admin/locations/updateStatus/0/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('active') . '</button>
                    ';
                } else {
                    $data = '
<button type="button"  data-url="' . $currentUrl . "/admin/locations/updateStatus/1/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-uuid="' . $que->uuid .
                        '">' . __('inactive') . '</button>
                    ';
                }
                return $data;
            })->addColumn('categories',function ($que){
                $string='';
                $i=1;
                foreach ($que->categories as $item) {
                $string.=       ' <span class="date'.$i.'">'.$item->name.',</span>';
                $i++;
                }
                '<div class="date-cell">'.$string.'</div>';
                return $string;
            })
            ->rawColumns(['action', 'status','categories'])->toJson();
    }

    public function updateStatus($status, $sup)
    {
        $uuids = explode(',', $sup);

        $location = Location::query()->withoutGlobalScope('status')
            ->whereIn('uuid', $uuids)
            ->update([
                'status' => $status
            ]);
        return response()->json([
            'item_edited'
        ]);
    }


    public function imageIndex($uuid)
    {
        $uuid_location = $uuid;
        return view('admin.locations.images', compact('uuid_location'));
    }

    public function imageIndexTable(Request $request, $uuid)
    {
        $images = Upload::query()->where('imageable_id', $uuid)->where('imageable_type', Location::class);
        return \Yajra\DataTables\DataTables::of($images)
            ->addColumn('checkbox', function ($que) {
                return $que->uuid;
            })
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->uuid . '" ';
                $data_attr .= 'data-imageable_id="' . $que->imageable_id . '" ';
                $data_attr .= 'data-image="' . url('/') . Location::PATH_LOCATION . $que->filename . '" ';
                $string = '';
                $url = url('/dashboard/projects/images/' . $que->imageable_id);
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary btn_edit" data-toggle="modal"
                    data-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" data-url="' . $url . '"  class="btn btn-sm btn-outline-danger btn_delete" data-uuid="' . $que->uuid .
                    '">' . __('delete') . '  </button>';

                return $string;
            })->addColumn('image', function ($que) {
                return url('/') . Location::PATH_LOCATION . $que->filename;
            })
            ->rawColumns(['action'])->toJson();
    }

    public function imageStore(Request $request)
    {
        foreach ($request->images as $item) {
            UploadImage($item, Location::PATH_LOCATION, Location::class, $request->uuid, false, null, Upload::IMAGE);
        }
        return response()->json("done");
    }

    public function imageUpdate(Request $request)
    {
        UploadImage($request->images, Location::PATH_LOCATION, Location::class, $request->imageable_id, true, $request->uuid, Upload::IMAGE);
        return response()->json([
            'item_deleted'
        ]);
    }

    public function imageDestroy($uuid, $uploade)
    {

        try {
            $uuids = explode(',', $uploade);
            $image = Upload::query()->whereIn('uuid', $uuids)->get();
            foreach ($image as $item) {
                File::delete(public_path(Location::PATH_LOCATION . $item->filename));
                $item->delete();
            }

            return response()->json("done");
        } catch (\Exception $e) {
            throw $e;
        }
    }
}
