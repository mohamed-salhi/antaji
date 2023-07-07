<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class CategoryContent extends Model
{
    use HasFactory, HasTranslations;

    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $translatable = ['name'];
    protected $guarded = [];
    protected $appends = ['name_translate','content_count'];
    protected $hidden=['name','status','updated_at','created_at','pivot'];

    //Relations
    public function content(){
        if ($this->type=='product'){
            return @$this->hasMany(Product::class,'category_contents_uuid');
        }
        if ($this->type=='serving'){
            return @$this->hasMany(Serving::class,'category_contents_uuid');
        }
        if ($this->type=='location'){
            return $this->belongsToMany(Location::class,'category_locations','category_contents_uuid','location_uuid');
        }
    }
    //Attributes
    public function getNameTranslateAttribute()
    {
        return @$this->name;
    }
    public function getContentCountAttribute()
    {
        return @$this->content()->count();
    }

    //Boot

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
