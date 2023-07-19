<?php

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class Cart extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';
    public $incrementing = false;

    protected $appends = ['content_owner_name', 'content_owner_uuid', 'content_count', 'days_count', 'content_owner_image'];
    protected $hidden = ['content'];
    protected $guarded = [];

    //Relations
    public function content()
    {

        if ($this->type == 'product') {
            return $this->belongsTo(Product::class, 'content_uuid');
        }
        if ($this->type == 'location') {
            return $this->belongsTo(Location::class, 'content_uuid');
        }
    }

    public function products()
    {
        return $this->belongsTo(Product::class, 'content_uuid');
    }
    public function discount()
    {
        return $this->belongsTo(Discount::class, 'discount_uuid');
    }
    public function locations()
    {
        return $this->belongsTo(Location::class, 'content_uuid');
    }

    public function days()
    {
        return $this->belongsTo(BookingDay::class, 'content_uuid');
    }

//    Attributes
    public function getImageAttribute()
    {
        return @$this->content->oneImageProduct->filename;
    }

    public function getContentOwnerNameAttribute()
    {
        return @$this->content->user->name;
    }

    public function getContentOwnerImageAttribute()
    {
        return @$this->content->user->image;
    }

    public function getContentOwnerUuidAttribute()
    {
        return @$this->content->user->uuid;
    }

    public function getContentCountAttribute()
    {
        return @$this->content()->count();
    }

    public function getDaysCountAttribute()
    {
        $startDate = Carbon::parse($this->start);
        $endDate = Carbon::parse($this->end);
        $daysDifference = $endDate->diffInDays($startDate);
        return ($daysDifference!=0)?$daysDifference:1;
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
