<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Ads extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $appends = ['image'];
    protected $hidden = ['imageAds', 'created_at', 'updated_at', 'status'];
    protected $guarded = [];

    //Relations
    public function imageAds()
    {
        return $this->morphOne(Upload::class, 'imageable');
    }

//Attributes
    public function getImageAttribute()
    {
        return !is_null(@$this->imageAds->path) ? asset(Storage::url(@$this->imageAds->path) ): '';
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
