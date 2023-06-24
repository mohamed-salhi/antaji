<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DeliveryAddresses extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guarded = [];
    protected $appends = ['city_name', 'country_name'];
    protected $hidden = ['city_uuid', 'country_uuid', 'status', 'updated_at', 'created_at'];

//Relations
    public function country()
    {
        return @$this->belongsTo(Country::class, 'country_uuid');
    }

    public function city()
    {
        return @$this->belongsTo(City::class, 'city_uuid');
    }

    //Attributes
    public function getCityNameAttribute()
    {
        return @$this->city->name;
    }

    public function getCountryNameAttribute()
    {
        return @$this->country->name;
    }

    //Boot
    public static function boot()
    {
        parent::boot();
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
        });
        static::addGlobalScope('city', function (Builder $builder) {
            $builder->where('status', 1);//1==active
        });

    }
}
