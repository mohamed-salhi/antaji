<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\City;
use App\Models\Course;
use App\Models\Location;
use App\Models\Order;
use App\Models\Product;
use App\Models\Service;
use App\Models\Serving;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class MainController extends Controller
{

   public function index(){
       $users=User::query()->where('type',User::USER)->count();
       $artists=User::query()->where('type',User::ARTIST)->count();
       $orders=Order::query()->count();
       $courses=Course::query()->count();
       $cities=City::query()->count();
       $services=Serving::query()->count();
       $rent=Product::query()->where('type',Product::RENT)->count();
       $sale=Product::query()->where('type',Product::SALE)->count();
       $locations=Location::query()->count();
       $usersByCity = User::query()->where('type',User::USER)->selectRaw('city_uuid, COUNT(*) as user_count')
           ->groupBy('city_uuid')
           ->get();
       $artistsByCity = User::query()->where('type',User::ARTIST)->selectRaw('city_uuid, COUNT(*) as user_count')
           ->groupBy('city_uuid')
           ->get();
       return view('admin.main',compact('users','artists','orders','courses','cities','services','rent','sale','locations','usersByCity','artistsByCity'));
   }
}
