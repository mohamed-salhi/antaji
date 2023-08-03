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
    protected $appends = ['pay_geteway', 'user_name', 'when', 'phone'];
    protected $hidden = ['name', 'updated_at','getway','user'];

    //variables
    const PENDING = 'pending';
    const COMPLETE = 'complete';
    const FAILED = 'failed';



    //relations

    public function getway()
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_method_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid');
    }


    //Attributes
    public function getWhenAttribute()
    {
        return date($this->created_at);;
    }
    public function getStatusAttribute($value)
    {
        return __($value);
    }

    public function getPayGetewayAttribute()
    {
        return @$this->getway->name;
    }

    public function getUserNameAttribute()
    {
        return @$this->user->name;
    }

    public function getPhoneAttribute()
    {
        return @$this->user->mobile;
    }

    //boot
    public static function boot()
    {
        parent::boot();
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
        });
    }
}
