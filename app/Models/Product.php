<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $appends = ['content_type', 'attachments', 'category_name', 'user_name', 'sub_category_name', 'image', 'is_favorite'];
    protected $hidden = ['imageProduct', 'category', 'cart', 'user', 'subCategory', 'status', 'updated_at', 'created_at'];

    protected $guarded = [];
    const PATH_PRODUCT = "/upload/product/images/";
    const TYPE = 'product';
    const SALE = 'sale';
    const RENT = 'rent';

    //Relations
    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid');
    }
//    public function multiDayDiscount()
//    {
//        return $this->belongsTo(MultiDayDiscount::class, 'multi_day_discount_uuid');
//    }
    public function delivery()
    {
        return $this->belongsTo(Delivery::class, 'delivery_uuid');
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_uuid');
    }

    public function cart()
    {
        return $this->hasMany(Cart::class, 'content_uuid');
    }

    public function favorite()
    {
        return $this->hasMany(Favorite::class, 'content_uuid');
    }

    public function subCategory()
    {
        return $this->belongsTo(SubCategory::class, 'sub_category_uuid');
    }

    public function specifications()
    {
        return $this->hasMany(Specification::class, 'product_uuid');
    }

    public function imageProduct()
    {
        return $this->morphMany(Upload::class, 'imageable');
    }

    public function oneImageProduct()
    {
        return $this->morphOne(Upload::class, 'imageable');
    }

    //Attributes
    public function getContentTypeAttribute()
    {
        return 'product';
    }

    public function getIsFavoriteAttribute()
    {
        if (@Auth::guard('sanctum')->user()->uuid) {
            return Favorite::query()->where('content_uuid', $this->uuid)->where('user_uuid', Auth::guard('sanctum')->user()->uuid)->exists();
        } else {
            return false;
        }
    }

    public function getPriceAttribute($value)
    {
        return number_format($value, 0, '.', '');
    }

    public function getLatAttribute($value)
    {
        return latLngFormat($value);
    }

    public function getLngAttribute($value)
    {
        return latLngFormat($value);
    }

    public function getCategoryNameAttribute()
    {
        return @$this->category->name;
    }

    public function getSubCategoryNameAttribute()
    {
        return @$this->subCategory->name;
    }

    public function getUserNameAttribute()
    {
        return @$this->user->name;
    }

    public function getImageAttribute()
    {
        return !is_null(@$this->oneImageProduct->path) ? asset(Storage::url(@$this->oneImageProduct->path)) : null;
    }

    public function getAttachmentsAttribute()
    {
        $attachments = [];
        foreach ($this->imageProduct as $item) {
            $attachments[] = [
                'uuid' => $item->uuid,
                'attachment' => !is_null(@$item->path) ? asset(Storage::url(@$item->path)) : null,
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
