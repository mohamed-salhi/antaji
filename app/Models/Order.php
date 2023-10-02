<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use function Termwind\render;

class Order extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $appends = ['content', 'pay_geteway', 'days_count', 'status_color', 'status_bg_color', 'status_text', 'user_name', 'type_text'];
    protected $guarded = [];
    const PRODUCT = 'product';
    const SALE = 'sale';

    const SERVICE = 'service';
    const COURSE = 'course';
    const LOCATION = 'location';

    const PENDING = 'pending';
    const ACCEPT = 'accept';
    const REJECT = 'reject';
    const COMPLETE = 'complete';
    const INACTIVE = 'inactive';
    const BUGING_SUCCEEDED = 'buying_succeeded';

    //Relations

    public function product()
    {
        return $this->belongsTo(Product::class, 'content_uuid');
    }

    public function user()
    {
        return @$this->belongsTo(User::class, 'user_uuid');
    }

    public function getway()
    {
        return $this->belongsTo(PaymentGateway::class, 'payment_method_id', 'id');
    }

    public function service()
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

    public function deliveryAddress()
    {
        return $this->belongsTo(DeliveryAddresses::class, 'delivery_address_uuid');
    }

    public function paymentMethod()
    {
        return @$this->belongsTo(PaymentGateway::class, 'payment_method_id');
    }

    //Attributes
    public function getDaysCountAttribute()
    {
        $startDate = Carbon::parse($this->start);
        $endDate = Carbon::parse($this->end);
        $daysDifference = $endDate->diffInDays($startDate);
        return $daysDifference;
    }

    public function getContentAttribute()
    {
        if ($this->content_type == self::PRODUCT) {
            return $this->product;
        }
        if ($this->content_type == self::SERVICE) {
            return $this->service;
        }
        if ($this->content_type == self::LOCATION) {
            return $this->location;
        }
        if ($this->content_type == self::COURSE) {
            return $this->course;
        }

    }

    public function getTypeTextAttribute()
    {
        if ($this->content_type == self::PRODUCT) {
            return $this->type;
        }
        if ($this->content_type == self::SERVICE) {
            return 'rent';
        }
        if ($this->content_type == self::LOCATION) {
            return 'rent';
        }
        if ($this->content_type == self::COURSE) {
            return 'sale';
        }

    }

    public function getPayGetewayAttribute()
    {
        return @$this->getway->name;
    }

    public function getStatusColorAttribute()
    {
        if ($this->status == self::PENDING && @$this->user_uuid == auth('sanctum')->id()) {
            return '#FFF3EA';
        } elseif ($this->status == self::PENDING) {
            return '#F78831';
        } elseif ($this->status == self::COMPLETE) {
            return '#028C59';
        } elseif ($this->status == self::BUGING_SUCCEEDED) {
            return '#028C59';
        } elseif ($this->status == self::ACCEPT) {
            return '#FEEDED';
        } elseif ($this->status == self::INACTIVE) {
            return '#FEEDED';
        }
    }

    public function getStatusTextAttribute()
    {
        if ($this->status == self::ACCEPT) {
            return __('Underway');
        } elseif ($this->status == self::PENDING && Auth::guard('sanctum')->id() == @$this->content->user_uuid) {
            return __('new');
        } elseif ($this->status == self::BUGING_SUCCEEDED) {
            return __('Purchased');
        } else {
            return __($this->status);

        }
    }

    public function getUserNameAttribute()
    {
        return @$this->user->name;
    }

    public function getStatusBgColorAttribute()
    {
        if ($this->status == self::PENDING && $this->user_uuid == auth('sanctum')) {
            return '#FFF3EA';
        }
        if ($this->status == self::PENDING) {
            return '#E9F1FD';
        }
        if ($this->status == self::COMPLETE) {
            return '#FEEDED';
        }
        if ($this->status == self::ACCEPT) {
            return '#FEEDED';
        }  if ($this->status == self::INACTIVE) {
        return '#FEEDED';
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
