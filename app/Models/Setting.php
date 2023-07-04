<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Setting extends Model
{
    use HasFactory, HasTranslations;

    protected $appends = ['title_page_translate','image','delete_translate', 'terms_translate', 'policies_translate', 'about_translate'];
    protected $translatable = ['title_page','delete_my_account', 'terms_conditions', 'policies_privacy', 'about_application'];
    protected $guarded = [];

    public function getImageAttribute()
    {
        return url('/') . '/upload/setting/' . @$this->imageSetting->filename;
    }
    public function imageSetting()
    {
        return $this->morphOne(Upload::class, 'imageable')->where('name','home_page_title');
    }

    public function getDeleteTranslateAttribute()
    {
        return @$this->delete_my_account;
    }

    public function getTermsTranslateAttribute()
    {
        return @$this->terms_conditions;
    }

    public function getPoliciesTranslateAttribute()
    {
        return @$this->name;
    }
    public function getTitlePageTranslateAttribute()
    {
        return @$this->title_page;
    }
    public function getAboutTranslateAttribute()
    {
        return @$this->name;
    }

}
