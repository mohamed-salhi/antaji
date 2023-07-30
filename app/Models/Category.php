<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Category extends Model
{

    use HasFactory, HasTranslations;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $translatable = ['name'];
    protected $appends = ['name_translate','image','product_count','sub_count'];
    protected $guarded = [];
    protected $hidden=['imageCategory','name','created_at','updated_at','status','products'];
    const PATH_IMAGE='/upload/category/images/';

    //Relations
    public function imageCategory()
    {
        return $this->morphOne(Upload::class, 'imageable');
    }
    public function products()
    {
        return $this->hasMany(Product::class, 'category_uuid');
    }
    public function sub()
    {
        return $this->hasMany(SubCategory::class, 'category_uuid');
    }
    //Attributes
    public function getNameTranslateAttribute()
    {
        return @$this->name;
    }
    public function getProductCountAttribute()
    {
        return $this->products()->count();
    }
    public function getSubCountAttribute()
    {
        return $this->sub()->count();
    }
    public function getImageAttribute()
    {
        return url('/') .self::PATH_IMAGE . @$this->imageCategory->filename;
    }
    //boot
    public static function boot()
    {
        parent::boot();
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
        });
        static::addGlobalScope('category', function (Builder $builder) {
            $builder->where('status', 1);//1==active
        });
    }
}
