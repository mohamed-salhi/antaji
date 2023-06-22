<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Serving extends Model
{
    use HasFactory;
    protected $primaryKey = 'uuid';
    protected $appends=['category_name','user_name','city_name'];
    public $incrementing = false;
    protected $guarded = [];
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
