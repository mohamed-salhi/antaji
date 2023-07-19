<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class OrderStatus extends Model
{
    use HasFactory;
    protected $primaryKey = 'uuid';
    public $incrementing = false;

    protected $guarded = [];
    const COURSE = 'course';
    const PENDING = 'pending';
    const PENDING1 = 'pending1';
    const COMPLETE = 'complete';
    const INACTIVE = 'inactive';
    const BUGING_SUCCEEDED = 'buying_succeeded';
    //Relations

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_uuid_uuid');
    }

    //Attributes



    public static function boot()
    {
        parent::boot();
        Carbon::now()->format('Y');
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
        });

        static::addGlobalScope('status', function (Builder $builder) {
            $builder->where('status', '!=', self::INACTIVE);
        });
    }
}
