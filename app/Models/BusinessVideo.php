<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BusinessVideo extends Model
{
    use HasFactory;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $appends=['image','video'];
    protected $guarded = [];
    protected $hidden=['imageBusiness','videoBusiness','user_uuid','updated_at','created_at'];
    const PATH_VIDEO='/upload/business/video/';
    const PATH_IMAGE='/upload/business/image/';

    //Relations
    public function imageBusiness()
    {
        return $this->morphOne(Upload::class, 'imageable')->where('type',Upload::IMAGE);
    }
    public function videoBusiness()
    {
        return $this->morphOne(Upload::class, 'imageable')->where('type',Upload::VIDEO);
    }
    //Attributes
    public function getImageAttribute()
    {
        return url('/') . self::PATH_IMAGE . @$this->imageBusiness->filename;
    }
    public function getVideoAttribute()
    {
        return url('/') . self::PATH_VIDEO . @$this->videoBusiness->filename;
    }
    //Boot
    public static function boot()
    {
        parent::boot();
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
        });
    }
}
