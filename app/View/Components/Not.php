<?php

namespace App\View\Components;

use App\Models\Notification;
use App\Models\ViewNotification;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class Not extends Component
{
    public $not;
    public $count;

    public function __construct()
    {
        $view = ViewNotification::query()->where('admin_id', Auth::id())->count();
//        $roles = [];
//        if (Auth::user()->can('help-list')){
//            $help = 'help-list';
//            array_push($roles,$help);
//        }
//
//        if (Auth::user()->can('user-list')){
//            $user = 'user-list';
//            array_push($roles,$user);
//        }
//        if (Auth::user()->can('competition-list')){
//            $competition = 'competition-list';
//            array_push($roles,$competition);
//        }
//        if (Auth::user()->can('reward-list')){
//            $reward = 'reward-list';
//            array_push($roles,$reward);
//        }
//        $not = Notification::query()->whereIn('type',$roles)->count();
        $not = Notification::query()->whereHas('receiver',function ($q){
            $q->where('receiver_uuid',Auth::id());
        })->count();
        $this->not = Notification::query()->orderByDesc('created_at')->whereHas('receiver',function ($q){
        $q->where('receiver_uuid',Auth::id());
    })->limit(5)->get();

//        $this->not = Notification::query()->orderByDesc('created_at')->whereIn('type',$roles)->limit(5)->get();
        $this->count = $not - $view;

    }


    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.not');
    }
}
