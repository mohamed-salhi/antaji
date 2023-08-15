<?php

namespace App\Http\Controllers\Admin\Course;

use App\Http\Controllers\Controller;
use App\Models\Content;
use App\Models\Course;
use App\Models\Upload;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class CourseController extends Controller
{
    public function index()
    {
        $users = User::query()->select('name', 'uuid')->get();
        return view('admin.courses.index', compact('users'));
    }

    public function store(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'image' => 'required|image',
            'user_uuid' => 'required|exists:users,uuid',
        ];
        $this->validate($request, $rules);
        $course = Course::query()->create($request->only('name', 'price', 'details', 'user_uuid'));
        Content::query()->create([
            'content_uuid' => $course->uuid,
            'user_uuid' => $request->user_uuid,
        ]);
        if ($request->hasFile('image')) {
            UploadImage($request->image, Course::PATH_COURSE, Course::class, $course->uuid, false, null, Upload::IMAGE);
        }
//        if ($request->hasFile('video')) {
//            UploadImage($request->video, Course::PATH_COURSE_VIDEO, Course::class, $course->uuid, false, null, Upload::VIDEO);
//        }
        return response()->json([
            'item_added'
        ]);
    }

    public function update(Request $request)
    {
        $rules = [
            'name' => 'required|string|max:36',
            'price' => 'required|int',
            'details' => 'required',
            'image' => 'required|image',
            'user_uuid' => 'required|exists:users,uuid',
        ];
        $this->validate($request, $rules);
        $course = Course::findOrFail($request->uuid);
        $course->update($request->only('name', 'details', 'price', 'user_uuid'));
        if ($request->hasFile('image')) {
            UploadImage($request->image, Course::PATH_COURSE, Course::class, $course->uuid, true, null, Upload::IMAGE);
        }
//        if ($request->hasFile('video')) {
//            UploadImage($request->video, Course::PATH_COURSE_VIDEO, Course::class, $course->uuid, true, null, Upload::VIDEO);
//        }
        return response()->json([
            'item_edited'
        ]);
    }

    public function destroy($uuid)
    {
        $uuids = explode(',', $uuid);
        $courses = Course::query()->withoutGlobalScope('status')->whereIn('uuid', $uuids)->get();
        foreach ($courses as $item) {
            File::delete(public_path(Course::PATH_COURSE . $item->imageCourse->filename));
            File::delete(public_path(Course::PATH_COURSE_VIDEO . $item->videoCourse->filename));
            $item->imageCourse()->delete();
            $item->videoCourse()->delete();
            $item->delete();
        }
        return response()->json([
            'item_deleted'
        ]);
    }

    public function indexTable(Request $request)
    {
        $course = Course::query()->withoutGlobalScope('status')->orderByDesc('created_at');
        return Datatables::of($course)
            ->filter(function ($query) use ($request) {
                if ($request->status) {
                    $query->where('status', $request->status);
                }
                if ($request->price) {
                    $query->where('price', $request->price);
                }
                if ($request->name) {
                    $query->where('name', $request->name);
                }
            })
            ->addColumn('checkbox', function ($que) {
                return $que->uuid;
            })
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->uuid . '" ';
                $data_attr .= 'data-name="' . $que->name . '" ';
                $data_attr .= 'data-price="' . $que->price . '" ';
                $data_attr .= 'data-details="' . $que->details . '" ';
                $data_attr .= 'data-details="' . $que->details . '" ';
                $data_attr .= 'data-user_uuid="' . $que->user_uuid . '" ';
                $data_attr .= 'data-user_uuid="' . $que->user_uuid . '" ';
                $data_attr .= 'data-image="' . $que->image . '" ';
                $string = '';
                $url = route('courses.videos.index',$que->uuid);
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary btn_edit" data-toggle="modal"
                    data-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger btn_delete" data-uuid="' . $que->uuid .
                    '">' . __('delete') . '</button>';

                $string .= ' <a href="' . $url . '"  class="btn btn-sm btn-outline-info btn_image" data-uuid="' . $que->uuid .
                    '">' . __('Videos') . '  </a>';
                return $string;
            })->addColumn('status', function ($que) {
                $currentUrl = url('/');
                if ($que->status == 1) {
                    $data = '
<button type="button"  data-url="' . $currentUrl . "/admin/courses/updateStatus/0/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('active') . '</button>
                    ';
                } else {
                    $data = '
<button type="button"  data-url="' . $currentUrl . "/admin/courses/updateStatus/1/" . $que->uuid . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-uuid="' . $que->uuid .
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
        $course = Course::query()->withoutGlobalScope('status')
            ->whereIn('uuid', $uuids)
            ->update([
                'status' => $status
            ]);
        return response()->json([
            'item_edited'
        ]);
    }

    public function videoIndex($uuid)
    {
        $uuid_course = $uuid;
        return view('admin.courses.videos', compact('uuid_course'));
    }

    public function videoIndexTable(Request $request, $uuid)
    {
        $images = Upload::query()->where('imageable_id', $uuid)->where('imageable_type', Course::class)->where('type',Upload::VIDEO)->orderByDesc('created_at');
        return \Yajra\DataTables\DataTables::of($images)
            ->addColumn('checkbox', function ($que) {
                return $que->uuid;
            })
            ->addColumn('action', function ($que) {
                $data_attr = '';
                $data_attr .= 'data-uuid="' . $que->uuid . '" ';
                $data_attr .= 'data-imageable_id="' . $que->imageable_id . '" ';
                $data_attr .= 'data-image="' . url('/') . Course::PATH_COURSE_VIDEO . $que->filename . '" ';
                $string = '';
                $url = url('/dashboard/projects/images/' . $que->imageable_id);
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary btn_edit" data-toggle="modal"
                    data-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" data-url="' . $url . '"  class="btn btn-sm btn-outline-danger btn_delete" data-uuid="' . $que->uuid .
                    '">' . __('delete') . '  </button>';

                return $string;
            })->addColumn('video', function ($que) {
                return url('/') . Course::PATH_COURSE_VIDEO . $que->filename;
            })
            ->rawColumns(['action'])->toJson();
    }

    public function videoStore(Request $request)
    {
        foreach ($request->videos as $item) {
            $video = UploadImage($item, Course::PATH_COURSE_VIDEO, Course::class, $request->uuid, false, null, Upload::VIDEO);
            $getID3 = new \getID3;
            $video_file = $getID3->analyze('upload/course/video/' . $video->filename);
            $duration_string = $video_file['playtime_string'];
            $video->duration = $duration_string;
            $video->save();        }
        return response()->json("done");
    }

    public function videoUpdate(Request $request)
    {
        UploadImage($request->video, Course::PATH_COURSE_VIDEO, Course::class, $request->imageable_id, true, $request->uuid, Upload::VIDEO);
        return response()->json([
            'item_deleted'
        ]);
    }

    public function videoDestroy($uuid, $uploade)
    {

        try {
            $uuids = explode(',', $uploade);
            $video = Upload::query()->whereIn('uuid', $uuids)->get();
            foreach ($video as $item) {
                File::delete(public_path(Course::PATH_COURSE_VIDEO . $item->filename));
                $item->delete();
            }

            return response()->json("done");
        } catch (\Exception $e) {
            throw $e;
        }
    }


}
