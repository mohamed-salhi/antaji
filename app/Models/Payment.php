<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payment extends Model
{
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guarded = [];
//    protected $appends = ['pay_geteway', 'user_name', 'when', 'phone','progress_name'];
    protected $hidden = ['name', 'updated_at', 'type'];

    //variables
    const PENDING = 'pending';
    const COMPLETE = 'complete';


    //relations

//    public function getway()
//    {
//        return $this->belongsTo(PaymentGateway::class, 'payment_method_uuid', 'uuid');
//    }
//
//    public function user()
//    {
//        return $this->belongsTo(User::class, 'user_uuid');
//    }


    //Attributes
//    public function getWhenAttribute()
//    {
//        return date($this->created_at);;
//    }


//    public function getProgressNameAttribute()
//    {
//
//        if ($this->reference_type == Competition::class && $this->status == "complete") {
//            return __('Recharge to participate in the competition');
//        } elseif ($this->reference_type == Movement::class && $this->status == "complete") {
//            return __('Charge to wallet');
//        }
//    }

//    public function getPayGetewayAttribute()
//    {
//        return @$this->getway->name;
//    }
//
//    public function getUserNameAttribute()
//    {
//        return @$this->user->name;
//    }
//
//    public function getPhoneAttribute()
//    {
//        return @$this->user->phone;
//    }

    //boot
    public static function boot()
    {
        parent::boot();
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
        });
    }
}
