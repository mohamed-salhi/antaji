<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasFactory, HasTranslations;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $appends = ['name_translate', 'icon'];
    protected $translatable = ['name'];
    protected $guarded = [];
    protected $hidden = ['name', 'iconService', 'updated_at', 'created_at',];

    const ACTIVE = 1;

    //Relations

    public function iconService()
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
        return  !is_null(@$this->iconService->path) ? asset(Storage::url(@$this->iconService->path) ):null;    }

    public function getStatusAttribute($value)
    {
        return $value == 1;
    }

    //Boot

    public static function boot()
    {
        parent::boot();
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
        });
//       static::addGlobalScope('service', function (Builder $builder) {
//           $builder->where('status', 1);//1==active
//       });

    }
}
