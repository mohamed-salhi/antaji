<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class OrderConversation extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guarded = [];
    //Relations
    public function service()
    {
       return $this->belongsTo(Serving::class, 'service_uuid');
    }
    public function chat(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(ChatOrder::class, 'order_conversation_uuid');
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
