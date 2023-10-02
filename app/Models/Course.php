<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Course extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guarded = [];
    protected $appends = ['image', 'user_name', 'video', 'course_count', 'attachments', 'is_purchased'];
    protected $hidden = ['user', 'videosCourse', 'imageCourse', 'videosCourse'];

    const PATH_COURSE = "/upload/course/images/";
    const PATH_COURSE_VIDEO = "/upload/course/video/";

    //Relations
    public function imageCourse()
    {
        return @$this->morphOne(Upload::class, 'imageable')->where('type', Upload::IMAGE);
    }

    public function videoCourse()
    {
        return @$this->morphOne(Upload::class, 'imageable')->where('type', Upload::VIDEO)->where('name', 'demonstration video');
    }

    public function videosCourse()
    {
        return @$this->morphMany(Upload::class, 'imageable')->where('type', Upload::VIDEO)->whereNull('name');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid');
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'content_uuid')->where('content_type', Order::COURSE);
    }

    //Attributes
    public function getImageAttribute()
    {
        return   !is_null(@$this->imageCourse->path) ? asset(Storage::url(@$this->imageCourse->path) ):null;

    }

    public function getVideoAttribute()
    {
        return  !is_null(@$this->videoCourse->path) ? asset(Storage::url($this->videoCourse->path) ):null;
//        return url('/') . self::PATH_COURSE_VIDEO . @$this->videoCourse->filename;
    }

    public function getUserNameAttribute()
    {
        return @$this->user->name;
    }

    public function getCourseCountAttribute()
    {
        return @$this->videosCourse()->count();
    }

    public function getIsPurchasedAttribute()
    {
        return $this->orders()->where('user_uuid', auth('sanctum')->id())->exists();
    }

    public function getAttachmentsAttribute()
    {
        $attachments = [];
        foreach ($this->videosCourse as $item) {
            $attachments[] = [
                'uuid' => $item->uuid,
                'attachment' =>!is_null(@$item->path) ? asset(Storage::url(@$item->path) ):null,
                'duration' =>@$item->duration,
            ];
        }
        return $attachments;
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
