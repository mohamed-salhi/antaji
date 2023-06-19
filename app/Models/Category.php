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
    protected $appends = ['name_translate','image'];
    protected $guarded = [];
    const PATH_IMAGE='/upload/category/images/';

    //Relations
//    public function types()
//    {
//        return $this->belongsToMany(Type::class, 'category_type', 'category_uuid', 'type_uuid');
//    }
    public function imageCategory()
    {
        return $this->morphOne(Upload::class, 'imageable');
    }

//    public function sup_category()
//    {
//        return $this->belongsTo(SupCategory::class, 'category_uuid');
//    }
    //Attributes
    public function getNameTranslateAttribute()
    {
        return @$this->name;
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
