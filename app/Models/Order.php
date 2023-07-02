<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use function Termwind\render;

class Order extends Model
{
    use HasFactory;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guarded=[];
const ACTIVE='active';

    public static function boot()
    {
        parent::boot();
       Carbon::now()->format('Y');
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
//            $item->order_number=Carbon::now()->format('Y')+rand(1000, 9999);
        });

        static::addGlobalScope('status', function (Builder $builder) {
            $builder->where('status', 1);//1==active
        });
    }
}
