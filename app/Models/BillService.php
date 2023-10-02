<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BillService extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guarded = [];
    protected $appends=['status_text'];
    const PENDING=1;

    const ACCEPT = 2;
    const REJECT = 3;
    //Relations
public function conversations(){
    return $this->belongsTo(OrderConversation::class,'order_conversation_uuid');
}

//Attributes
    public function getStatusTextAttribute()
    {
        if ($this->status == self::ACCEPT) {
            return __('accept');
        } elseif ($this->status == self::PENDING) {
            return __('pending');
        } elseif ($this->status == self::REJECT) {
            return __('reject');
        }
    }
    //Boot

    public static function boot()
    {
        parent::boot();
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
        });

    }
}
