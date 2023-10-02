<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Chat extends Model
{
    use HasFactory;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guarded=[];
    protected $appends = ['type_text','content'];

    const PATH_IMAGE = "/upload/chats/images/";
    const PATH_VOICE = "/upload/chats/voices/";
    const PATH_ATTACHMENT = "/upload/chats/attachments/";
    const PATH_BILL = "/upload/chats/bill/";

    const TEXT = 1;
    const ATTACHMENT = 2;
    const VOICE = 3;
    const LOCATION = 4;
    const IMAGE = 5;

//    public function user()
//    {
//        return $this->belongsTo(User::class,'user_uuid');
//    }
    public function voice()
    {
        return $this->morphOne(Upload::class, 'imageable')->where('type', '=', Upload::VOICE);
    }

    public function images()
    {
        return $this->morphOne(Upload::class, 'imageable')->where('type', Upload::IMAGE);
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid');
    }
    public function attachment()
    {
        return $this->morphOne(Upload::class, 'imageable')->where('type', '=', Upload::ATTACHMENT);
    }

    public function getTypeTextAttribute()
    {
        if ($this->type == self::TEXT) {
            return 'text';
        } elseif (@$this->type == self::IMAGE) {
            return 'image';
        } elseif (@$this->type == self::VOICE) {
            return 'voice';
        } elseif (@$this->type == self::LOCATION) {
            return 'location';
        } elseif (@$this->type == self::ATTACHMENT) {
            return 'attachment';
        }
    }

    //Attributes
    public function getContentAttribute()
    {
        if (@$this->type == self::TEXT) {
            return $this->msg;
        } elseif (@$this->type == self::IMAGE) {
            if (@$this->images->filename) {
                return !is_null(@$this->images->path) ? asset(Storage::url(@$this->images->path) ): '';
            } else {
                return 'nulll';
            }
        } elseif (@$this->type == self::VOICE) {
            if (@$this->voice->filename) {
                return !is_null(@$this->voice->path) ? asset(Storage::url(@$this->voice->path) ): '';
            } else {
                return null;
            }
        } elseif (@$this->type == self::LOCATION) {
            return $this->lat_lng;
        } elseif (@$this->type == self::ATTACHMENT) {
            if (@$this->attachment->filename) {
                return !is_null(@$this->attachment->path) ? asset(Storage::url(@$this->attachment->path) ): '';
            } else {
                return null;
            }
        }
    }
    public static function boot()
    {
        parent::boot();
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
        });


    }
}
