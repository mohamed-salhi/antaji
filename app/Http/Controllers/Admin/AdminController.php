<?php

namespace App\Http\Controllers\admin;

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

    public function index(){
//        $roles = Role::pluck('name','name')->all();
        return view('admin.index');
    }
    public function store(Request $request){
        $request->validate([
            'name'=>'required',
            'email'=>'required|email|unique:admins',
            'password'=>'required|min:6',
        ]);
        $data=$request->only([
            'name',
            'email',
        ]);
        $data['password']=Hash::make($request->password);

        $admin= Admin::create($data);
//        $admin->assignRole($request->input('role'));
        return response()->json([
            'item_edited'
        ]);    }
    public function edit($id){
        $admin=Admin::findOrFail($id);
//        $roles = Role::pluck('name','name')->all();
//        $userRole = $admin->roles->pluck('name','name')->all();

        return view('admin.edit');
    }
    public function update(Request $request){
        $request->validate([
            'name'=>'required',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
            ],
            'password'=>'nullable|min:6',
            'role' => 'required'
        ]);

        $admin=Admin::query()->find($request->id);
        $data=$request->only([
            'name',
            'email',
        ]);
        $data['password']=Hash::make($request->password);
        $admin->update($data);
        DB::table('model_has_roles')->where('model_id',$admin->id)->delete();

//        $admin->assignRole($request->input('role'));

        return redirect()->route('admin.index',);

    }
    public function destroy($id){

        $id_admin=explode(',', $id);
        Admin::whereIn('id',$id_admin)->delete();
        return $this->sendResponse(null, __('item_deleted'));

    }
    public function indexTable(Request $request)
    {


        $user = Admin::query();
        return Datatables::of($user)

            ->addColumn('checkbox',function ($que){
                return $que->id;
            })
            ->addColumn('action', function ($que) {
                $data_attr = 'data-id="' . $que->id . '" ';
                $data_attr .= 'data-name="' .$que->name . '" ';
                $data_attr .= 'data-email="' .$que->email . '" ';
                $string = '';
                    $route=url('/admins/edit/'.$que->id);
                    $string .= '<a class="edit_btn btn btn-sm btn-outline-primary" href="'.$route.'" >' . __('edit') . '</a>';

                    $string .= ' <button type="button" class="btn btn-sm btn-outline-danger btn_delete" data-id="' . $que->id .
                        '">' . __('delete') . '</button>';


                return $string;
            })
            ->addColumn('status', function ($que) {
                $currentUrl = url('/');
                return '<div class="checkbox">
                <input class="activate-row"  url="' . $currentUrl . "/admins/updateStatus/" . $que->id . '" type="checkbox" id="checkbox' . $que->id . '" ' .
                    ($que->status ? 'checked' : '')
                    . '>
                <label for="checkbox' . $que->id . '"><span class="checkbox-icon"></span> </label>
            </div>';
            })
            ->rawColumns(['status','Type','action'])
            ->make(true);
    }

    public function updateStatus($id)
    {
        $activate =  Admin::findOrFail($id);
        $activate->status = !$activate->status;
        if (isset($activate) && $activate->save()) {
            return $this->sendResponse(null, __('item_edited'));
        }
    }
}
