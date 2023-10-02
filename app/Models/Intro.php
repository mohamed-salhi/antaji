<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Intro extends Model
{
    use HasFactory,HasTranslations;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $translatable = ['title','sup_title'];
    protected $appends=['image','title_translate','sup_title_translate'];
    protected $hidden=['sup_title','title','imageIntro','updated_at','created_at'];
    protected $guarded = [];
    //Relations
    public function imageIntro()
    {
        return $this->morphOne(Upload::class, 'imageable');
    }
    //Attributes
    public function getTitleTranslateAttribute()
    {
        return @$this->title;
    }
    public function getSupTitleTranslateAttribute()
    {
        return @$this->sup_title;
    }
    public function getImageAttribute()
    {
        return!is_null(@$this->imageIntro->path) ? asset(Storage::url(@$this->imageIntro->path) ):null;
    }

    //Boot
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
