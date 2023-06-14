<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Verification extends Model
{
    use HasFactory;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $fillable=['code','mobile'];

    public static function boot()
    {
        parent::boot();
        self::creating(function ($admin) {
            $admin->uuid = Str::uuid();
        });

    }

}
