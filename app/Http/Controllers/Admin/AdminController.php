<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class AdminController extends Controller
{
    function __construct()
    {
        $this->middleware('permission:admin', ['only' => ['index','show']]);
//        $this->middleware('permission:product-create', ['only' => ['create','store']]);
//        $this->middleware('permission:product-edit', ['only' => ['edit','update']]);
//        $this->middleware('permission:product-delete', ['only' => ['destroy']]);
    }

    public function index()
    {
        $roles = Role::all();
        return view('admin.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:admins',
            'password' => 'required|min:6',
            'roles' => 'required|array',

        ]);
        $data = $request->only([
            'name',
            'email',
        ]);
        $data['password'] = Hash::make($request->password);

        $admin = Admin::create($data);
        $admin->assignRole($request->input('role'));
        return response()->json([
            'item_edited'
        ]);
    }
//    public function edit($id){
//        $manager=Admin::findOrFail($id);
//        $roles = Role::pluck('name','name')->all();
//        $userRole = $manager->roles->pluck('name','name')->all();
//
//        return view('admin.edit',compact('manager','roles','userRole'));
//    }
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],
            'password' => 'nullable|min:6',
            'roles' => 'required'
        ]);

        $admin = Admin::query()->find($request->id);
        $data = $request->only([
            'name',
            'email',
        ]);
        if ($request->has('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);
        DB::table('model_has_roles')->where('model_id', $admin->id)->delete();

        $admin->assignRole($request->input('roles'));

        return 'done';

    }

    public function destroy($id)
    {
        $id_admin = explode(',', $id);
        Admin::whereIn('id', $id_admin)->delete();
        return 'done';

    }

    public function indexTable(Request $request)
    {


        $admin = Admin::query()->orderBy('created_at');
        return Datatables::of($admin)
            ->addColumn('checkbox', function ($que) {
                return $que->id;
            })
            ->addColumn('action', function ($que) {
                $data_attr = 'data-id="' . $que->id . '" ';
                $data_attr .= 'data-name="' . $que->name . '" ';
                $data_attr .= 'data-email="' . $que->email . '" ';
                $data_attr .= 'data-roles="' . implode(',', $que->roles->pluck('name')->toArray()) . '," ';

                $string = '';
                $route = url('/admin/managers/edit/' . $que->id);
                $string .= '<button class="edit_btn btn btn-sm btn-outline-primary btn_edit" data-toggle="modal"
                    data-target="#edit_modal" ' . $data_attr . '>' . __('edit') . '</button>';
                $string .= ' <button type="button" class="btn btn-sm btn-outline-danger btn_delete" data-uuid="' . $que->id .
                    '">' . __('delete') . '</button>';


                return $string;
            })
            ->addColumn('status', function ($que) {
                $currentUrl = url('/');
                if ($que->status == 1) {
                    $data = '
<button type="button"  data-url="' . $currentUrl . "/admin/managers/updateStatus/0/" . $que->id . '" id="btn_update" class=" btn btn-sm btn-outline-success " data-uuid="' . $que->uuid .
                        '">' . __('active') . '</button>
                    ';
                } else {
                    $data = '
<button type="button"  data-url="' . $currentUrl . "/admin/managers/updateStatus/1/" . $que->id . '" id="btn_update" class=" btn btn-sm btn-outline-danger " data-uuid="' . $que->uuid .
                        '">' . __('inactive') . '</button>
                    ';
                }
                return $data;
            })
            ->rawColumns(['status', 'action'])
            ->make(true);
    }

    public function updateStatus($status, $sup)
    {
        $uuids = explode(',', $sup);

        $activate = Admin::query()
            ->whereIn('id', $uuids)
            ->update([
                'status' => $status
            ]);
        return response()->json([
            'item_edited'
        ]);
    }

}
