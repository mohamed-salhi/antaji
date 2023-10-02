<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Payment extends Model
{


    /* LIVE */
//    const PAYMENT_BASE_URL = '';
//    const PAYMENT_TOKEN = '';
//    const PAYMENT_ENTITY_ID_DEFAULT = '';
//    const PAYMENT_ENTITY_ID_MADA = '';
//    const PAYMENT_ENTITY_ID_APPLE_PAY = '';
//    const PAYMENT_IS_LIVE = true;

    /* TEST */
    const PAYMENT_BASE_URL = 'https://eu-test.oppwa.com';
    const PAYMENT_TOKEN = 'OGE4Mjk0MTc0YjdlY2IyODAxNGI5Njk5MjIwMDE1Y2N8c3k2S0pzVDg=';
    const PAYMENT_ENTITY_ID_DEFAULT = '8a8294174b7ecb28014b9699220015ca';
    const PAYMENT_ENTITY_ID_MADA = '8a8294174b7ecb28014b9699220015ca';
    const PAYMENT_ENTITY_ID_APPLE_PAY = '8a8294174b7ecb28014b9699220015ca';
    const PAYMENT_IS_LIVE = false;


    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guarded = [];
    protected $appends = ['pay_geteway', 'user_name', 'when', 'phone','status_text'];
    protected $hidden = ['name', 'updated_at', 'getway', 'user'];

    //variables
    const PENDING = 'pending';
    const COMPLETE = 'success';
    const FAILED = 'failed';
    const UN_COMPLETED = 'un_completed';


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
        return date($this->created_at);
    }

    public function getStatusTextAttribute()
    {
        return __($this->status);
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
