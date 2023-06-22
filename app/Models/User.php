<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $appends = ['image', 'cover_user', 'video_user', 'city_name', 'country_name'];
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
    ];
    const PATH_COVER = "/upload/user/cover/";
    const PATH_PERSONAL = "/upload/user/personal/";
    const PATH_VIDEO = "/upload/user/video/";
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

    public function specialization()
    {
        return $this->belongsTo(Specialization::class, 'specialization_uuid');
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

    public function imageUser()
    {
        return $this->morphOne(Upload::class, 'imageable')->where('type', '=', Upload::IMAGE)->where('name', '=', 'personal_photo');
    }

    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'skill_user', 'user_uuid', 'skill_uuid');
    }


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
        return @$this->specialization->name;
    }

    public function getCoverUserAttribute()
    {
        return url('/') . self::PATH_COVER . @$this->coverImage->filename;
    }

    public function getVideoUserAttribute()
    {
        return url('/') . self::PATH_VIDEO . @$this->videoImage->filename;
    }

    public function getImageAttribute()
    {
        if (@$this->imageUser->filename) {
            return url('/') . self::PATH_PERSONAL . @$this->imageUser->filename;
        } else {
            return url('/') . '/upload/user/fea062c5fb579ac0dc5ae2c22c6c51fb.jpg';

        }
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
