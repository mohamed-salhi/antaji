<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\Admin;
use App\Models\PaymentGateway;
use App\Models\Upload;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin=Admin::create([
            'name' => 'Super Admin',
            'email' => 'super@admin.com',
            'password'=>Hash::make('123456')
        ]);

//        $role = Role::create(['name' => 'Admin']);
//
//        $permissions = Permission::pluck('id','id')->all();
//
//        $role->syncPermissions($permissions);
//
//        $admin->assignRole([$role->id]);
        $made= PaymentGateway::create([
            'name'=>['ar' => "المدى", 'en' => "mada"]
        ]);
        $pay= PaymentGateway::create([
            'name'=>['ar' => "ابل باي", 'en' => "pay"]
        ]);
        Upload::create([
            'filename'=>'mada.png',
            'imageable_id'=>$made->id,
            'imageable_type'=>PaymentGateway::class,
            'type'=>Upload::IMAGE

        ]);
        Upload::create([
            'filename'=>'pay.png',
            'imageable_id'=>$pay->id,
            'imageable_type'=>PaymentGateway::class,
            'type'=>Upload::IMAGE
        ]);
    }
}
