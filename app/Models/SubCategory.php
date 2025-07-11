<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class SubCategory extends Model
{
    use HasFactory, HasTranslations;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $translatable = ['name'];
    protected $appends = ['name_translate','category_name','image','product_count'];
    protected $guarded = [];
    protected $hidden=['updated_at','created_at','status','name','category','imageCategory','status'];
    const PATH_IMAGE='/upload/subcategory/images/';

    //Relations
//    public function types()
//    {
//        return $this->belongsToMany(Type::class, 'category_type', 'category_uuid', 'type_uuid');
//    }
    public function imageCategory()
    {
        return $this->morphOne(Upload::class, 'imageable');
    }
    public function products()
    {
        return $this->hasMany(Product::class, 'sub_category_uuid');
    }
    public function category()
    {
        return $this->belongsTo(Category::class, 'category_uuid');
    }
    //Attributes
    public function getNameTranslateAttribute()
    {
        return @$this->name;
    }
    public function getProductCountAttribute()
    {
     return @$this->products()->count();
    }
    public function getCategoryNameAttribute()
    {
        return @$this->category->name;
    }

    public function getImageAttribute()
    {
        return !is_null(@$this->imageCategory->path) ? asset(Storage::url(@$this->imageCategory->path) ):null;
    }
    //boot
    public static function boot()
    {
        parent::boot();
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
        });
        static::addGlobalScope('sub', function (Builder $builder) {
            $builder->where('status', 1);//1==active
        });
    }}
