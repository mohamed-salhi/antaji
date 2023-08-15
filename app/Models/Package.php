<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Package extends Model
{
    use HasFactory, HasTranslations;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $translatable = ['name', 'details'];
    protected $appends = ['name_translate', 'details_translate', 'is_subscriber'];
    protected $hidden = ['details', 'name'];
    protected $guarded = [];

    const VIP = 'vip';
    const PROFESSIONAL = 'professionals';
    const BASIC = 'basic';

    //Relations

    //Attributes
    public function getPriceAttribute($value)
    {
        return ($value) ? $value : __('Complimentary');
    }

    public function getNameTranslateAttribute()
    {
        return @$this->name;
    }
//    public function getQualityAttribute()
//    {
//        return '4k';
//    }
    public function getDetailsTranslateAttribute()
    {
        return @$this->details;
    }

    public function getIsSubscriberAttribute()
    {
        return User::query()->where('uuid', auth('sanctum')->id())->where('package_uuid', $this->uuid)->exists();
    }

    //boot
    public static function boot()
    {
        parent::boot();
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
        });
    }
}
