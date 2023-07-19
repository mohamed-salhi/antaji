<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class PackageUser extends Model
{
    use HasFactory;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guarded = [];

    //Relations
    public function package()
    {
        return $this->belongsTo(Package::class,'package_uuid');
    }

    public function user()
    {
        return $this->belongsTo(User::class,'user_uuid');
    }

    //Attributes


    //boot
    public static function boot()
    {
        parent::boot();
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
        });
    }
}
