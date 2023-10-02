<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Social extends Model
{
    use HasFactory,HasTranslations;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $translatable = ['name'];
    protected $appends=['name_translate','icon'];
    protected $hidden=['name','imageSocial','updated_at','created_at'];
    protected $guarded = [];
    const PATH='/upload/live/';
    //Relations
    public function imageSocial ()
    {
        return $this->morphOne(Upload::class, 'imageable');
    }
    //Attributes
    public function getNameTranslateAttribute()
    {
        return @$this->name;
    }
    public function getIconAttribute()
    {
        return !is_null(@$this->imageSocial->path) ? asset(Storage::url(@$this->imageSocial->path) ):null;
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
