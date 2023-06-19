<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Country extends Model
{
    use HasFactory, HasTranslations;

    protected $primaryKey = 'uuid';
    public $incrementing = false;

    protected $translatable = ['name'];
    protected $guarded = [];
    protected $appends = ['name_translate', 'image'];
    protected $hidden = ['name', 'imageCountry', 'updated_at', 'created_at', 'status'];

    //Relations

    public function imageCountry()
    {
        return $this->morphOne(Upload::class, 'imageable');
    }

    //Attributes
    public function getNameTranslateAttribute()
    {
        return @$this->name;
    }

    public function getImageAttribute()
    {
        return url('/') . '/upload/country/' . @$this->imageCountry->filename;
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
