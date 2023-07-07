<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Page extends Model
{
    use HasFactory, HasTranslations;

    protected $translatable = ['title'];
    protected $appends = ['description_translate'];
    protected $hidden = ['title', 'created_at', 'updated_at', 'id'];
    protected $guarded = [];
    const delete_my_account = 1;
    const policies_privacy = 2;
    const about_application = 3;
    const terms_conditions = 4;

    //Attributes
    public function getDescriptionTranslateAttribute()
    {
        return @$this->title;
    }

}
