<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Businessimages extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $appends = ['images', 'user_name'];
    protected $guarded = [];
    protected $hidden = ['imageBusiness', 'videoBusiness', 'user_uuid', 'updated_at', 'created_at'];
    const PATH = "/upload/business/images/";

    //Relations
    public function imageBusiness()
    {
        return $this->morphMany(Upload::class, 'imageable')->where('type', Upload::IMAGE);
    }

    public function artists()
    {
        return $this->belongsTo(User::class, 'user_uuid');
    }

    //Attributes
    public function getImagesAttribute()
    {
        $attachments = [];
        foreach ($this->imageBusiness as $item) {
            $attachments[] = [
                "uuid" => $item->uuid,
                'image' => url('/') . self::PATH . $item->filename,
            ];
        }
        return $attachments;
    }

    public function getUserNameAttribute()
    {
        return $this->artists->name;
    }

    //Boot
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
