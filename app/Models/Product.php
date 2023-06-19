<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $appends=['category_name','user_name','sup_category_name',];

    protected $guarded = [];
    const PATH_PRODUCT="/upload/product/images/";
    //Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_uuid');
    }
    public function supCategory()
    {
        return $this->belongsTo(SupCategory::class, 'sup_category_uuid');
    }
    public function imageProduct()
    {
        return $this->morphMany(Upload::class, 'imageable');
    }
    //Attributes
    public function getCategoryNameAttribute()
    {
        return @$this->category->name;
    }
    public function getSupCategoryNameAttribute()
    {
        return @$this->supCategory->name;
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
