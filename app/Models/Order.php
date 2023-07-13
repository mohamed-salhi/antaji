<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use function Termwind\render;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $appends=['content'];
    protected $guarded = [];
    const COURSE = 'course';
    const PENDING = 'pending';
    const INACTIVE = 'inactive';
    const BUGING_SUCCEEDED = 'buying_succeeded';
    //Relations

    public function product()
    {
        return $this->belongsTo(Product::class, 'content_uuid');
    }

    public function serving()
    {
        return $this->belongsTo(Serving::class, 'content_uuid');
    }

    public function location()
    {
        return $this->belongsTo(Location::class, 'content_uuid');
    }
    public function course()
    {
        return $this->belongsTo(Course::class, 'content_uuid');
    }
    //Attributes

    public function getContentAttribute()
    {
        if ($this->content_type == 'product') {
            return $this->product;
        }
        if ($this->content_type == 'serving') {
            return $this->serving;
        }
        if ($this->content_type == 'location') {
            return $this->location;
        }
        if ($this->content_type == 'course') {
            return $this->course;
        }
    }

    public static function boot()
    {
        parent::boot();
        Carbon::now()->format('Y');
        self::creating(function ($item) {
            $item->uuid = Str::uuid();
        });

        static::addGlobalScope('status', function (Builder $builder) {
            $builder->where('status', '!=', self::INACTIVE);
        });
    }
}
