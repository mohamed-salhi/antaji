<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Notification extends Model
{
    use HasFactory, HasTranslations;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guarded = [];
    protected $translatable = ['content', 'title'];
    protected $appends = ['content_translate', 'title_translate','link','show'];
    const ADD_PRODUCT_SALE = 'add_product_sale';
    const ADD_PRODUCT_RENT = 'add_product_rent';
    const REVIEW_ORDER = 'review_order';

    const NEW_OFFER = 'new_offer';
    const ACCEPT_ORDER = 'accept_order';
    const REJECT_ORDER = 'reject_order';

    const RECEIVE_ORDER = 'receive_order';
    const RECEIVE_DOCUMENT = 'receive_document';
    const ACCEPT_DOCUMENT = 'accept_document';
    const NEW_USER = 'new_user';
    const NEW_ARTIST = 'new_artist';
    const ADD_LOCATION = 'add_location';
    const ADD_SERVING = 'add_serving';
    const ADD_COURSE = 'add_course';
    const COMPLETE_ORDER = 'complete_order';

    const REQUEST_JOIN_TRIP = "request_join_trip"; //send to driver
    const ACCEPT_JOIN_TRIP = "accept_join_trip"; //send to user
    const REJECT_JOIN_TRIP = "reject_join_trip"; //send to user
    const ADD_OFFER = "add_offer"; //send to user
    const ACCEPT_OFFER = "accept_offer"; //send to driver
    const CANCEL_ORDER = "cancel_order"; //send to driver
    const CANCEL_TRIP = "cancel_trip"; //send to user
    const START_TRIP = "start_trip"; //send to user
    const START_ORDER = "start_order"; //send to user
    const ARRIVED_ORDER = "arrived_order"; //send to user
    const SKIP_ORDER = "skip_order"; //send to user
    const END_TRIP = "end_trip"; //send to user
    const NEW_CHAT_MESSAGE = "new_chat_message"; //send to user,driver
    const NEW_TECHNICAL_SUPPORT_MESSAGE = "new_technical_support_message"; //send to user,driver
    const ACCEPT_ACCOUNT = "accept_account";//send to driver
    const REJECT_ACCOUNT = "reject_account";//send to driver
    const ACCEPT_WALLET_TRANSACTION = "accept_wallet_transaction";//send to driver
    const REJECT_WALLET_TRANSACTION = "reject_wallet_transaction";//send to driver
    const ACTIVATE = "activate";//send to driver,user
    const DEACTIVATE = "deactivate";//send to driver,user
    const RECHARGE_CODE = "recharge_code";//send to driver,user
    /*** ***/
    const GENERAL_NOTIFICATION = "general_notification";
    const NEW_PROMO_CODE = "new_promo_code";
    const DELETE_DRIVER = "delete_driver";//send to user
    const SCHEDULE_TRIP = "schedule_trip";
    const SCHEDULE_ORDER = "schedule_order";
//    public function users(){
//        return $this->belongsToMany(User::class, 'notification_users', 'notification_uuid', 'user_uuid', 'uuid', 'uuid');
//    }
//    public function admins(){
//        return $this->belongsToMany(User::class, 'notification_users', 'notification_uuid', 'user_uuid', 'uuid', 'uuid');
//    }
    public function receiver()
    {
        return $this->hasMany(NotificationUser::class, 'notification_uuid');
    }

    //Attributes
    public function getContentTranslateAttribute()
    {
        return @$this->content;
    }

    public function getTitleTranslateAttribute()
    {
        return @$this->title;
    }

    public function getIconAttribute($value)
    {
        if ($value) {
            return $value;
        } else {
            return url('/') . '/dashboard/app-assets/images/4367.jpg';

        }
    }
    public function getShowAttribute()
    {
        return ViewNotification::query()->where('admin_id',Auth::id())->where('notification_uuid',$this->uuid)->exists();
    }
    public function getLinkAttribute()
    {
        if ($this->type == self::GENERAL_NOTIFICATION) {
            return null;
        }elseif ($this->type==self::ADD_PRODUCT_SALE) {
            return route('products.sales.index') . "?uuid=" . $this->uuid;
        }elseif ($this->type==self::ADD_PRODUCT_RENT) {
            return route('products.rent.index') . "?uuid=" . $this->uuid;
        }
        elseif ($this->type==self::NEW_USER) {
            return route('users.index') . "?uuid=" . $this->uuid;
        }elseif ($this->type==self::NEW_ARTIST) {
            return route('artists.index') . "?uuid=" . $this->uuid;
        }elseif ($this->type==self::ADD_LOCATION) {
            return route('locations.index') . "?uuid=" . $this->uuid;
        }elseif ($this->type==self::ADD_SERVING) {
            return route('servings.index') . "?uuid=" . $this->uuid;
        }elseif ($this->type==self::ADD_COURSE) {
            return route('courses.index') . "?uuid=" . $this->uuid;
        }
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
