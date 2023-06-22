<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;

class Setting extends Model
{
    use HasFactory,HasTranslations;
//    protected $appends = ['delete_translate','terms_translate','policies_translate','about_translate'];
    protected $translatable = ['delete_my_account','terms_conditions','policies_privacy','about_application'];
    protected $guarded=[];
//    public function getDeleteTranslateAttribute()
//    {
//        return @$this->delete_my_account;
//    }
//    public function getTermsTranslateAttribute()
//    {
//        return @$this->terms_conditions;
//    }
//    public function getPoliciesTranslateAttribute()
//    {
//        return @$this->name;
//    }
//    public function getAboutTranslateAttribute()
//    {
//        return @$this->name;
//    }

}
