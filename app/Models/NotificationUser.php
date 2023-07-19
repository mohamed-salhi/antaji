<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class NotificationUser extends Model
{
    use HasFactory, HasTranslations;
    protected $guarded;
    protected $translatable = ['content'];
    protected $appends = ['content_translate'];
    //Attributes
    public function getContentTranslateAttribute()
    {
        return @$this->content;
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
