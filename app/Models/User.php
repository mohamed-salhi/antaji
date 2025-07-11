<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $primaryKey = 'uuid';
    public $incrementing = false;
        protected $appends = ['image', 'cover_user', 'video_user', 'city_name', 'country_name', 'products_count', 'reviews', 'response', 'specialization_name', 'commission', 'is_favorite','id_image_user','is_verified'];
    protected $fillable = [
        'name',
        'email',
        'city_uuid',
        'country_uuid',
        'mobile',
        'type',
        'password',
        'specialization_uuid',
        'brief',
        'lat',
        'lng',
        'address',
        'documentation',

        'package_uuid'
    ];
    const USER = "user";
    const ARTIST = "artist";
    const PATH_COVER = "/upload/user/cover/";
    const PATH_PERSONAL = "/upload/user/personal/";
    const PATH_VIDEO = "/upload/user/video/";
    const PATH_ID = "/upload/user/id/";
    const PENDING = 0;
    const ACCEPT = 1;
    const REJECT = 2;
    const ANDROID = 'android';
    const IOS = 'ios';
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'country_uuid',
        'city_uuid',
        'city',
        'country',
        'coverImage',
        'videoImage',
        'imageUser'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Relations
     */
    public function country()
    {
        return $this->belongsTo(Country::class, 'country_uuid');
    }
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_uuid');
    }
    public function specialization()
    {
        return @$this->belongsTo(Specialization::class, 'specialization_uuid');
    }

    public function city()
    {
        return $this->belongsTo(City::class, 'city_uuid');
    }

    public function fcm_tokens()
    {
        return $this->hasMany(FcmToken::class, 'user_uuid');
    }

    public function coverImage()
    {
        return $this->morphOne(Upload::class, 'imageable')->where('type', '=', Upload::IMAGE)->where('name', '=', 'cover_photo');
    }

    public function videoImage()
    {
        return $this->morphOne(Upload::class, 'imageable')->where('type', '=', Upload::VIDEO);
    }
    public function message(){
        return $this->hasMany(Message::class,'user_uuid')->orderByDesc('created_at');
    }
    public function imageUser()
    {
        return $this->morphOne(Upload::class, 'imageable')->where('type', '=', Upload::IMAGE)->where('name', '=', 'personal_photo');
    }
    public function idUserImage()
    {
        return $this->morphOne(Upload::class, 'imageable')->where('type', '=', Upload::IMAGE)->where('name', '=', 'id_image');
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'skill_user', 'user_uuid', 'skill_uuid');
    }
    public function businessVideo()
    {
        return $this->hasMany(BusinessVideo::class, 'user_uuid');
    }
    public function businessImage()
    {
        return $this->belongsTo(Businessimages::class, 'user_uuid');
    }
    public function products()
    {
        return $this->hasMany(Product::class, 'user_uuid');
    }
    public function favorite()
    {
        return $this->hasMany(Favorite::class, 'content_uuid');
    }
    public function hasAbility($id)
    {
        $check= Conversation::query()->where('uuid',$id)->where(function ($q){
            $q->where('one',$this->uuid)->orWhere('tow',$this->uuid);
        })->exists();
        if ($check) {
            return true;
        }
        return false;
    }
//    public function deliveryAddresses()
//    {
//        return $this->hasMany(DeliveryAddresses::class, 'user_uuid')->where('default',1);
//    }
    /**
     * Attribute
     */

    public function getCountryNameAttribute()
    {
        return @$this->country->name;
    }

    public function getCityNameAttribute()
    {
        return @$this->city->name;
    }

    public function getSpecializationNameAttribute()
    {
        if ($this->type == self::USER){
            return __('user');
        }
        return @$this->specialization->name;
    }

    public function getCoverUserAttribute()
    {
        return !is_null(@$this->coverImage->path) ? asset(Storage::url(@$this->coverImage->path) ) : null;
    }

    public function getVideoUserAttribute()
    {
        return !is_null(@$this->videoImage->path) ? asset(Storage::url(@$this->videoImage->path) ): null;
    }
    public function getIdImageUserAttribute()
    {

            return !is_null(@$this->idUserImage->path) ? asset(Storage::url(@$this->idUserImage->path) ): url('/') . '/dashboard/app-assets/images/4367.jpg';
    }
    public function getIsFavoriteAttribute()
    {
        return $this->favorite()->where('user_uuid', auth('sanctum')->id())->exists();
    }
    public function getIsVerifiedAttribute()
    {
        $is_verified=false;
        if ($this->package->type==Package::VIP&&$this->documentation==User::ACCEPT){
            $is_verified=true;
        }
        return $is_verified;
    }
    public function getImageAttribute()
    {
     return !is_null(@$this->imageUser->path) ? asset(Storage::url(@$this->imageUser->path) ): url('/') . '/dashboard/app-assets/images/4367.jpg';
    }

    public function getLatAttribute($value)
    {
        return latLngFormat($value);
    }

    public function getLngAttribute($value)
    {
        return latLngFormat($value);
    }

    public function getProductsCountAttribute()
    {
        return $this->products->count();
    }

    public function getResponseAttribute()
    {
        return '3 ' . __('hours');
    }

    public function getReviewsAttribute()
    {
        return number_format(2, 1, '.', '') . '%';
    }
    public function getCommissionAttribute()
    {
        return ((@$this->package->percentage_of_sale ?? 0) / 100);
    }

    /**
     * Boot
     */
    public static function boot()
    {
        parent::boot();
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
        });
        static::addGlobalScope('user', function (Builder $builder) {
            $builder->where('status', 1);//1==active
        });

    }
}
