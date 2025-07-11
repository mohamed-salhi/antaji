<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BusinessVideo extends Model
{
    use HasFactory;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $appends=['image','video','artist_name','artist_image'];
    protected $guarded = [];
    protected $hidden=['imageBusiness','videoBusiness','user_uuid','updated_at','artists','created_at','status','artist_image'];
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
    public function artists()
    {
        return $this->belongsTo(User::class, 'user_uuid');
    }
    //Attributes
    public function getImageAttribute()
    {
        return !is_null(@$this->imageBusiness->path) ? asset(Storage::url(@$this->imageBusiness->path) ): '';
    }
    public function getVideoAttribute()
    {
        return !is_null(@$this->videoBusiness->path) ? asset(Storage::url(@$this->videoBusiness->path) ): '';

    }
    public function getArtistNameAttribute()
    {
        return $this->artists->name;
    }
    public function getArtistImageAttribute()
    {
        return $this->artists->image;
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
