<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Reviews extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guarded = [];
    protected $appends = ['user_name', 'user_image', 'time_ago'];
    protected $hidden = [
        'created_at',
        'updated_at',
        'uuid',
        'user_uuid',
        'reference_uuid',
        'user',
        'content_uuid',
    ];


    //Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid');
    }

    //Attributes

    public function getUserNameAttribute()
    {
        return @$this->user->name;
    }

    public function getUserImageAttribute()
    {
        return $this->user->image;
    }

    public function getTimeAgoAttribute()
    {
        return $this->created_at->diffForHumans();
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
