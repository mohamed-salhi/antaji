<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class City extends Model
{
    use HasFactory, HasTranslations;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $translatable = ['name'];
    protected $guarded = [];
    protected $appends = ['name_translate', 'country_name'];

//Relations
    public function country()
    {
        return @$this->belongsTo(Country::class)->withoutGlobalScope('country');
    }

    //Attributes
    public function getCountryNameAttribute()
    {
        return @$this->country->name;
    }

    public function getNameTranslateAttribute()
    {
        return @$this->name;
    }

    //Boot

    public static function boot()
    {
        parent::boot();
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
        });
        static::addGlobalScope('city', function (Builder $builder) {
            $builder->where('status', 1);//1==active
        });

    }
}
