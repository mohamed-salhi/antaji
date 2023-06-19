<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Location extends Model
{
    use HasFactory;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $appends=['category_name','user_name'];
    protected $guarded = [];
    const PATH_LOCATION="/upload/location/images/";
    //Relations
    public function imageLocation()
    {
        return $this->morphMany(Upload::class, 'imageable');
    }
    public function category()
    {
        return $this->belongsTo(CategoryContent::class, 'category_contents_uuid');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid');
    }
    //Attributes
    public function getCategoryNameAttribute()
    {
        return @$this->category->name;
    }
    public function getUserNameAttribute()
    {
        return @$this->user->name;
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
