<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class Location extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $appends = ['content_type', 'user_name', 'image', 'attachments', 'is_favorite'];
    protected $guarded = [];
    protected $hidden = ['cart', 'user', 'categories'];
    const PATH_LOCATION = "/upload/location/images/";

    //Relations
    public function imageLocation()
    {
        return $this->morphMany(Upload::class, 'imageable');
    }

    public function oneImageLocation()
    {
        return $this->morphOne(Upload::class, 'imageable');
    }

    public function cart()
    {
        return $this->hasMany(Cart::class, 'content_uuid');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid');
    }
    public function multiDayDiscount()
    {
        return $this->belongsTo(MultiDayDiscount::class, 'multi_day_discount_uuid');
    }
    public function favorite()
    {
        return $this->hasMany(Favorite::class, 'content_uuid');
    }

    public function categories()
    {
        return $this->belongsToMany(CategoryContent::class, 'category_locations', 'location_uuid', 'category_contents_uuid');
    }

    public function getContentTypeAttribute()
    {
        return 'location';
    }

    public function getLatAttribute($value)
    {
        return latLngFormat($value);
    }

    public function getLngAttribute($value)
    {
        return latLngFormat($value);
    }


    public function getAttachmentsAttribute()
    {
        $attachments = [];
        foreach ($this->imageLocation as $item) {
            $attachments[] = [
                'uuid' => $item->uuid,
                'attachment' => url('/') . self::PATH_LOCATION . $item->filename,
            ];
        }
        return $attachments;
    }
    //Attributes
//    public function getCategoryNameAttribute()
//    {
//        return @$this->category->name;
//    }
    public function getImageAttribute()
    {
        return url('/') . self::PATH_LOCATION . @$this->oneImageLocation->filename;
    }

    public function getUserNameAttribute()
    {
        return @$this->user->name;
    }

    public function getIsFavoriteAttribute()
    {
        return Favorite::query()->where('content_uuid', $this->uuid)->where('user_uuid', Auth::guard('sanctum')->user()->uuid)->exists();
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
