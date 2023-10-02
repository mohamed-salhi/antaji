<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Discount extends Model
{
    use HasFactory, HasTranslations;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $translatable = ['name'];
    protected $appends = ['name_translate','number_uses','type_text'];
    protected $guarded = [];

    const PERCENT = 'percent';
    const FIXED_PRICE = 'fixed_price';


//Relations
    public function discountContent()
    {
        return @$this->hasMany(DiscountContent::class);
    }
    public function discountUser()
    {
        return $this->hasMany(DiscountUser::class,'discount_uuid');
    }
    //Attributes
    public function getTypeTextAttribute()
    {
        return __($this->discount_type);
    }
    public function getNumberUsesAttribute()
    {
        return $this->discountUser()->count();
    }
    public function getNameTranslateAttribute()
    {
        return @$this->name;
    }

    //boot
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
