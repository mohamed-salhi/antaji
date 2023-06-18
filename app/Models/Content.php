<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Content extends Model
{
    use HasFactory;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guarded = [];
    const PATH_LOCATION="/upload/location/images/";
    const PATH_COURSE="/upload/course/images/";
    const PATH_COURSE_VIDEO="/upload/course/video/";
    const PATH_PRODUCT="/upload/product/images/";

    //boot
    public static function boot()
    {
        parent::boot();
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
        });

    }}
