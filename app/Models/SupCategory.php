<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class SupCategory extends Model
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
        return Product::query()->where('sup_category_uuid',$this->uuid)->count();
    }
    public function getCategoryNameAttribute()
    {
        return @$this->category->name;
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
        static::addGlobalScope('sup', function (Builder $builder) {
            $builder->where('status', 1);//1==active
        });
    }}
