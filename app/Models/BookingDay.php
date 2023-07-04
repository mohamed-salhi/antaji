<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class BookingDay extends Model
{
    use HasFactory;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guarded = [];

    //Relations
    public function product()
    {
        return $this->belongsTo(Product::class, 'content_uuid');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'content_uuid');
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid');
    }
    //Attributes
    public function getUserNameAttribute()
    {
        return $this->user->name;
    }
    //Boot
    public static function boot()
    {
        parent::boot();
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
        });
    }}
