<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Upload extends Model
{
    use HasFactory;
    protected $primaryKey = 'uuid';
    public $incrementing = false;
    protected $guarded=[];
    protected $hidden=['type','created_at','updated_at'];
    protected $appends=[
        'type_attachment',
//        'images'
    ];

    //Variables

    const IMAGE = 1;
    const VIDEO = 2;
    const VOICE = 3;
    const LOCATION = 4;
    const ATTACHMENT = 4;
    //Attributes
    public function getTypeAttachmentAttribute()
    {
        if ($this->type==2){
            return  'video';
        }else{
            return  'image';

        }
    }
//    public function getImagesAttribute(){
//        return url('/').$this->imageable.$this->filename;
//    }

    //Relations
    public function imageable()
    {
        return $this->morphTo();
    }

    //Boot

    public static function boot()
    {
        parent::boot();
        self::creating(function ($image) {
            $image->uuid = Str::uuid();
        });

    }
}
