<?php

namespace App\Http\Controllers\Admin\Artist;

use App\Http\Controllers\Controller;
use App\Models\City;
use App\Models\Country;
use App\Models\Skill;
use App\Models\Specialization;
use App\Models\Upload;
use App\Models\User;
use App\Models\ViewNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Yajra\DataTables\Facades\DataTables;

class ArtistController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:artisan', ['only' => ['index','store','create','destroy','edit','update']]);
    }
    public function index(Request $request)
    {
        if ($request->has('uuid')){

            ViewNotification::query()->updateOrCreate([
                'admin_id'=>Auth::id(),
                'notification_uuid'=>$request->uuid
            ]);
        }
        $countries = Country::query()->select(['name', 'uuid'])->get();
        $specializations = Specialization::query()->select(['name', 'uuid'])->get();
        $skills = Skill::query()->select(['name', 'uuid'])->get();

        return view('admin.artists.index', compact('countries', 'specializations', 'skills'));
    }

    public function store(Request $request)
    {
        $rules = [
            'prefix' => 'required|int',
            'mobile' => 'required|unique:users,mobile',
            'name' => 'required',
            'email' => 'required|unique:users,email',
            'country_uuid' => 'required|exists:countries,uuid',
            'city_uuid' => ['required',
                Rule::exists(City::class, 'uuid')->where(function ($query) use ($request) {
                    $query->where('country_uuid', $request->country_uuid);
                }),
            ],
//            'type' => 'required|in:artist,user',
            'personal_photo' => 'nullable',
            'cover_Photo' => 'nullable',
            'video' => 'nullable',
            'skills' => 'nullable',
            'brief' => 'nullable',
            'lat' => 'nullable',
            'lng' => 'nullable',
            'address' => 'nullable',
            'specialization_uuid' => 'required|exists:specializations,uuid',
        ];
        $this->validate($request, $rules);
        $request->merge([
            'type'=> 'artist',
            'mobile'=>$request->prefix.'-'.$request->mobile
        ]);
        $user = User::query()->create($request->only('mobile', 'name', 'email', 'country_uuid', 'city_uuid', 'type', 'brief', 'lat', 'lng', 'address', 'specialization_uuid'));
        if ($request->has('personal_photo')) {
            UploadImage($request->personal_photo, User::PATH_PERSONAL, User::class, $user->uuid, false, null, Upload::IMAGE, 'personal_photo');
        }
        if ($request->has('cover_Photo')) {
            UploadImage($request->cover_Photo, User::PATH_COVER, User::class, $user->uuid, false, null, Upload::IMAGE, 'cover_photo');
        }
        if ($request->has('video')) {
            UploadImage($request->video, User::PATH_VIDEO, User::class, $user->uuid, false, null, Upload::VIDEO);
        }
        $user->skills()->sync($request->skills);
        return response()->json([
            'item_edited'
        ]);
    }


    public function update(Request $request)
    {
        $user = User::query()->withoutGlobalScope('user')->findOrFail($request->uuid);
        $rules = [
            'prefix' => 'required|int',

            'name' => 'required',
            'mobile' => [
                'required',
                'max:12',
                Rule::unique('users', 'mobile')->ignore($user->uuid, 'uuid')
            ],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($user->uuid, 'uuid')
            ],
            'country_uuid' => 'required|exists:countries,uuid',
            'city_uuid' => ['required',
                Rule::exists(City::class, 'uuid')->where(function ($query) use ($request) {
                    $query->where('country_uuid', $request->country_uuid);
                }),
            ],
//            'type' => 'required|in:artist,user',
            'personal_photo' => 'nullable',
            'cover_Photo' => 'nullable',
            'video' => 'nullable',
            'skills' => 'nullable',
            'brief' => 'nullable',
            'lat' => 'nullable',
            'lng' => 'nullable',
            'address' => 'nullable',
            'specialization_uuid' => 'required|exists:specializations,uuid',
        ];

        $this->validate($request, $rules);
        $request->merge([
            'mobile'=>$request->prefix.'-'.$request->mobile
        ]);
        $user->update($request->only('mobile', 'name', 'email', 'country_uuid', 'city_uuid', 'brief', 'lat', 'lng', 'address', 'specialization_uuid'));
        if ($request->hasFile('cover_Photo')) {
            UploadImage($request->cover_Photo, User::PATH_COVER, User::class, $user->uuid, true, null, Upload::IMAGE, 'cover_photo');
        }
        if ($request->hasFile('personal_photo')) {
            UploadImage($request->personal_photo, User::PATH_PERSONAL, User::class, $user->uuid, true, null, Upload::IMAGE, 'personal_photo');
        }
        if ($request->hasFile('video')) {
            UploadImage($request->video, User::PATH_VIDEO, User::class, $user->uuid, true, null, Upload::VIDEO);
        }
        $user->skills()->sync($request->skills);

        return response()->json([
            'item_added'
        ]);
    }

    public function destroy($uuid)
    {
//        try {
        $uuids = explode(',', $uuid);
        $user = User::query()->withoutGlobalScope('user')->whereIn('uuid', $uuids)->get();
        foreach ($user as $item) {
            Storage::delete('public/' . @$item->imageUser->path);
            Storage::delete('public/' . @$item->videoImage->path);
            Storage::delete('public/' . @$item->coverImage->path);
            foreach ($item->businessVideo as $video){
                Storage::delete('public/' . @$video->videoBusiness->path);
                Storage::delete('public/' . @$video->imageBusiness->path);
            }
//            foreach ($item->businessImage->imageBusiness as $image){
//                Storage::delete('public/' . @$image->path);
//            }

            $item->businessVideo()->delete();
            $item->businessImage()->delete();

//            File::delete(public_path(User::PATH_PERSONAL . @$item->imageUser->filename));
//            File::delete(public_path(User::PATH_VIDEO . @$item->videoImage->filename));
//            File::delete(public_path(User::PATH_COVER . @$item->coverImage->filename));
            $item->coverImage()->delete();
            $item->videoImage()->delete();
            $item->imageUser()->delete();
            $item->skills()->detach();
            $item->delete();
        }
        return response()->json([
            'done'
        ]);
//        } catch (\Exception $e) {
//            return response()->json([
//                'err'
//            ]);
//        }
    }

    public function indexTable(Request $request)
    {
        $user = User::query()->withoutGlobalScope('user')->where('type', 'artist')->orderByDesc('created_at');
        return Datatables::of($user)
            ->filter(function ($query) use ($request) {
                if ($request->status) {
                    ($request->status == 1) ? $query->where('status', 1) : $query->where('status', 0);
                }
                if ($request->name) {
                    $query->where('name', 'like', "%{$request->name}%");
                }
                if ($request->email) {
                    $query->where('email', 'like', "%{$request->email}%");
                }
                if ($request->mobile) {
                    $query->where('mobile', 'like', "%{$request->mobile}%");
                }
                if ($request->country_uuid) {
                    $query->where('country_uuid', $request->country_uuid);
                }
                if ($request->city_uuid) {
                    $query->where('city_uuid', $request->city_uuid);
                }
            })
            ->addColumn('checkbox', function ($que) {
                return $que->uuid;
            })
            ->addColumn('action', function ($que) {
                $mobile=explode("-", $que->mobile);
                $into=(count($mobile) == 2)?$mobile[0]:null;
                $mobile=(count($mobile) == 2)?$mobile[1]:$que->mobile;
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->uuid . '" ';
                $data_attr .= 'data-city_uuid="' . $que->city_uuid . '" ';
                $data_attr .= 'data-country_uuid="' . $que->country_uuid . '" ';
                $data_attr .= 'data-mobile="' .$mobile . '" ';
                $data_attr .= 'data-intro="' .$into . '" ';
                $data_attr .= 'data-email="' . $que->email . '" ';
                $data_attr .= 'data-brief="' . $que->brief . '" ';
                $data_attr .= 'data-lat="' . $que->lat . '" ';
                $data_attr .= 'data-lng="' . $que->lng . '" ';
                $data_attr .= 'data-address="' . $que->address . '" ';
                $data_attr .= 'data-specialization_uuid="' . $que->specialization_uuid . '" ';
                $data_attr .= 'data-type="' . $que->type . '" ';
                $data_attr .= 'data-video="' . $que->video_user . '" ';
                $data_attr .= 'data-address="' . $que->address . '" ';
                $data_attr .= 'data-skills="' . implode(',', $que->skills->pluck('uuid')->toArray()) . '," ';
                $data_attr .= 'data-cover_user="' . $que->cover_user . '" ';
                $data_attr .= 'data-personal_photo="' . $que->image . '" ';
                $data_attr .= 'data-name="' . $que->name . '" ';
//                $user = Auth()->user();


                $string = '';
//                if ($user->can('user-edit')) {
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary btn_edit" data-toggle="modal"
                    data-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
//                }
//                if ($user->can('user-delete')) {
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger btn_delete" data-uuid="' . $que->uuid .
                    '">' . __('delete') . '</button>';
                $url_videos = route('business.video.index') . '?user_uuid=' . $que->uuid;
                $url_images = route('business.images.index') . '?user_uuid=' . $que->uuid;


//                }

//                $string .= '<button  class="btn-outline-danger" href="' . $url_videos . ' " data-uuid="' . $que->uuid .
//                    '">' . __('Business Gallery Video') . '</button>';
//                $string .= '<button  class="btn-outline-danger" href="'.$url_images.'
//                " data-uuid="' . $que->uuid .
//                    '">'.__('Business photo gallery').'</button>';


                $string=' <div class="btn-group">
<button type="button"  class=" btn btn-sm btn-outline-danger ropdown-toggle" data-toggle="dropdown" data-uuid="' . $que->uuid .
                    '">' . __('Business Gallery') . '</button>
 <button class="edit_btn btn btn-sm btn-outline-primary btn_edit" data-toggle="modal"
                    data-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>


 <div class="dropdown-menu">

                                                                        <a  class=" dropdown-item" href="' . $url_videos . ' " data-uuid="' . $que->uuid . '">
                                                                            <i data-feather="edit-2" class="mr-50"></i>
                                                                            <span>' . __('videos') . '</span>
                                                                        </a>
                                                                        <a  class=" dropdown-item" href="'.$url_images.'
                " data-uuid="' . $que->uuid .
                    '">
                                                                            <i data-feather="edit-2" class="mr-50"></i>
                                                                            <span>' . __('images') . '</span>
                                                                        </a>
                                                                        </div>
                                                                </div>
                                                          </div>';





                $string .= ' <a href=" ' . route('conversations.index',$que->uuid) . '" type="button" class="btn btn-sm btn-outline-danger btn_conversation" data-uuid="' . $que->uuid .
                    '">' . __('Conversations') . '</button>';
                return $string;
            })
            ->addColumn('status', function ($que) {
                $currentUrl = url('/');
                if ($que->status == 1) {
                    $data = '
<button type="button"  data-url="' . $currentUrl . "/admin/users/updateStatus/0/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('active') . '</button>
                    ';
                } else {
                    $data = '
<button type="button"  data-url="' . $currentUrl . "/admin/users/updateStatus/1/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-uuid="' . $que->uuid .
                        '">' . __('inactive') . '</button>
                    ';
                }
                return $data;
            })
            ->rawColumns(['action', 'status'])->toJson();
    }

    public function updateStatus($status, $sup)
    {
        $uuids = explode(',', $sup);

        $activate = User::query()->withoutGlobalScope('user')->orderByDesc('created_at')
            ->whereIn('uuid', $uuids)
            ->update([
                'status' => $status
            ]);
        return response()->json([
            'item_edited'
        ]);
    }

    public function country($uuid)
    {
        $City = City::where("country_uuid", $uuid)->pluck("name", "uuid");
        return $City;
    }
}
