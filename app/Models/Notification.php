<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Notification extends Model
{
    use HasFactory, HasTranslations;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guarded=[];
    protected $translatable = ['content','title'];
    protected $appends = ['content_translate','title_translate'];
    const NEWORDER='There_is_a_new_order';
    const NEWOFFER='There_is_a_new_offer';

    //Attributes
    public function getContentTranslateAttribute()
    {
        return @$this->content;
    }
    public function getTitleTranslateAttribute()
    {
        return @$this->title;
    }
    //Boot

    public static function boot()
    {
        parent::boot();
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
        });
    }

}
