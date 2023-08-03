<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ChatOrder extends Model
{
    use HasFactory;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guarded = [];
    protected $appends = ['type_text','content'];
    const PATH_IMAGE = "/upload/order/images/";
    const PATH_VOICE = "/upload/order/voices/";
    const PATH_ATTACHMENT = "/upload/order/attachments/";
    const PATH_BILL = "/upload/order/bill/";

    public function conversation(){
        return  $this->belongsTo(OrderConversation::class,'order_conversation_uuid');
    }
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

    const TEXT = 1;
    const ATTACHMENT = 2;
    const VOICE = 3;
    const LOCATION = 4;
    const IMAGE = 5;
    const OFFER = 6;
//Attribute
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
            return $this->message;
        } elseif (@$this->type == self::IMAGE) {
            if (@$this->images->filename) {
                return url('/') . self::PATH_IMAGE . @$this->images->filename;
            } else {
                return 'nulll';
            }
        } elseif (@$this->type == self::VOICE) {
            if (@$this->voice->filename) {
                return url('/') . self::PATH_VOICE . @$this->voice->filename;
            } else {
                return null;
            }
        } elseif (@$this->type == self::LOCATION) {
            return $this->lat_lng;
        } elseif (@$this->type == self::ATTACHMENT) {
            if (@$this->attachment->filename) {
                return url('/') . self::PATH_ATTACHMENT . @$this->attachment->filename;
            } else {
                return null;
            }
        }elseif (@$this->type == self::OFFER) {
          return  $item = [
                'name' => $this->conversation->service->name,
                'start' => $this->conversation->service->from,
                'end' => $this->conversation->service->to,
                'price' => $this->conversation->service->price,
                'count' => $this->conversation->service->daysDifference,
                'currency' => __('sr')
            ];
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
