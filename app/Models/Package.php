<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Package extends Model implements TranslatableContract, HasMedia
{
    use Translatable;
    use InteractsWithMedia;

    public $translatedAttributes = ['name', 'description', 'short_description', 'other_conditions'];


    public function scopeActive($query)
    {
        return $query->where('is_active', 1);
    }
}
