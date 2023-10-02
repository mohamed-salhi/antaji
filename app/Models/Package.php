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
    protected $appends = ['name_translate', 'details_translate', 'is_subscriber','bg_color', 'btn_bg_color', 'btn_color', 'currency'];
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
    public function getCurrencyAttribute()
    {
        return __('sr');
    }
    public function getIsSubscriberAttribute()
    {
        return User::query()->where('uuid', auth('sanctum')->id())->where('package_uuid', $this->uuid)->exists();
    }
    public function getBtnBgColorAttribute()
    {
        if ($this->type == self::VIP) {
            return '#3A8BC4';
        } elseif ($this->type == self::PROFESSIONAL) {
            return '#10A580';
        } elseif ($this->type == self::BASIC) {
            return '#E2E6EA';
        }
    }
    public function getBtnColorAttribute()
    {
        if ($this->type == self::VIP) {
            return '#000000';
        } elseif ($this->type == self::PROFESSIONAL) {
            return '#FFFFFF';
        } elseif ($this->type == self::BASIC) {
            return '#3A8BC4';
        }
    }
    public function getBgColorAttribute()
    {
        if ($this->type == self::VIP) {
            return '#E2E6EA';
        } elseif ($this->type == self::PROFESSIONAL) {
            return '#EEF2F6';
        } elseif ($this->type == self::BASIC) {
            return '#000000';
        }
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
