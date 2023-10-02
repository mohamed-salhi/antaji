<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Contact extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guarded = [];
    protected $appends = ['image'];
//variables
    const WATCHED = 2;

    //Relations
    public function imageContact()
    {
        return $this->morphOne(Upload::class, 'imageable');
    }


//Attributes
    public function getImageAttribute()
    {
        return   !is_null(@$this->imageContact->path) ? asset(Storage::url(@$this->imageContact->path) ):null;

    }

    //Boot

    public static function boot()
    {
        parent::boot();
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
        });


    }
}

