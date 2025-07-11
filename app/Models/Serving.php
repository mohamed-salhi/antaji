<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Serving extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';
    protected $appends = ['category_name', 'user_name', 'city_name','daysDifference'];
    protected $hidden = ['category', 'user'];
    public $incrementing = false;
    protected $guarded = [];
    const PERCEN='percent';
    const FIXED_PRICE='fixed price';
    const HOUR='hour';

    //boot
    //Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid');
    }

    public function category()
    {
        return $this->belongsTo(CategoryContent::class, 'category_contents_uuid');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_uuid');
    }

    //Attributes
    public function getCityNameAttribute()
    {
        return @$this->city->name;
    }

    public function getCategoryNameAttribute()
    {
        return @$this->category->name;
    }

    public function getUserNameAttribute()
    {
        return @$this->user->name;
    }
    public function getLatAttribute($value)
    {
        return latLngFormat($value);
    }
    public function getPriceAttribute($value)
    {
        return number_format($value, 0, '.', '');
    }

    public function getLngAttribute($value)
    {
        return latLngFormat($value);
    }
    public function getDaysDifferenceAttribute()
    {
        $startDate = Carbon::parse($this->start);
        $endDate = Carbon::parse($this->end);
        $daysDifference = $startDate->diffInDays($endDate);
       return $daysDifference = ($daysDifference != 0) ? $daysDifference : 1;
    }
   //boot
    public static function boot()
    {
        parent::boot();
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
        });
        static::addGlobalScope('status', function (Builder $builder) {
            $builder->where('status', 1);//1==active
        });

    }
}
