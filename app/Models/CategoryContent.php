<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class CategoryContent extends Model
{
    use HasFactory, HasTranslations;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $translatable = ['name'];
    protected $guarded = [];
    protected $appends = ['name_translate', 'content_count', 'content'];
    protected $hidden = ['name', 'status', 'updated_at', 'created_at', 'pivot'];

    public function products()
    {
        return $this->hasMany(Product::class, 'category_contents_uuid');
    }

    public function servings()
    {
        return $this->hasMany(Serving::class, 'category_contents_uuid');
    }

    public function locations()
    {
        return $this->belongsToMany(Location::class, 'category_locations', 'category_contents_uuid', 'location_uuid');
    }

    public function getContentAttribute()
    {
        if ($this->type == 'product') {
            return $this->products;
        }
        if ($this->type == 'serving') {
            return $this->servings;
        }
        if ($this->type == 'location') {
            return $this->locations;
        }
    }

    //Attributes
    public function getNameTranslateAttribute()
    {
        return @$this->name;
    }

    public function getContentCountAttribute()
    {
        if ($this->content) {
            return $this->content->count();
        }
        return 0;
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
