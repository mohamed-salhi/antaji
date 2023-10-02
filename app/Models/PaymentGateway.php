<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class PaymentGateway extends Model
{
    use HasFactory, HasTranslations;

    protected $translatable = ['name'];
    protected $guarded = [];
    protected $appends = ['name_translate', 'image'];
    protected $hidden = ['name', 'imagePayment', 'updated_at', 'created_at', 'status'];


    //Variables
    const MADA = 1;
    const APPLE_PAY = 2;
    const VISA = 3;

    const ACTIVE = 1;

    //Attributes
    public function getNameTranslateAttribute()
    {
        return @$this->name;
    }

    public function getImageAttribute()
    {
       return !is_null(@$this->imagePayment->path) ? asset(Storage::url(@$this->imagePayment->path) ):null;    }

    //Relations
    public function imagePayment()
    {
        return $this->morphOne(Upload::class, 'imageable');
    }

    //Boot
    public static function boot()
    {
        parent::boot();
        static::addGlobalScope('status', function (Builder $builder) {
            $builder->where('status', 1);//1==active
        });

    }
}




