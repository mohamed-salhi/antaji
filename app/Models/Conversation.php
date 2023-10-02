<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Conversation extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guarded = [];
    protected $appends = ['last_msg', 'count_msg','user','user_name_one','user_name_tow'];
    protected $hidden=['chat'];

    //Relations


    public function userTow()
    {
        return $this->belongsTo(User::class, 'tow');
    }
    public function userOne()
    {
        return $this->belongsTo(User::class, 'one');
    }
    public function chat()
    {
        return $this->hasMany(Chat::class, 'conversation_uuid') ->orderByDesc('created_at');
    }

    //Attributes

    public function getLastMsgAttribute()
    {
        $last = $this->chat()->latest()->first();
        if (@$last->type == Chat::TEXT) {
            return $last->value('msg');
        } elseif (@$last->type == Chat::IMAGE) {
            return 'image';
        } elseif (@$last->type == Chat::VOICE) {
            return 'voice';
        } elseif (@$last->type == Chat::LOCATION) {
            return 'location';
        } elseif ($this->type == Chat::ATTACHMENT) {
            return 'attachment';
        }

    }

    public function getCountMsgAttribute()
    {
        if (auth('sanctum')->id() == $this->one) {
          return  $this->chat()->whereNot('user_uuid', auth('sanctum')->id())->whereNull('view_one')->count();
        } else {
            return  $this->chat()->whereNot('user_uuid', auth('sanctum')->id())->whereNull('view_tow')->count();
        }

    }
    public function getUserAttribute()
    {
        if (auth('sanctum')->id()==$this->one){
           return $this->userTow;
        }else{
           return $this->userOne;
        }

    }
    public function getUserNameOneAttribute()
    {
        return @$this->userOne->name;
    }
    public function getUserNameTowAttribute()
    {
        return @$this->userTow->name;
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
        });


    }
}
