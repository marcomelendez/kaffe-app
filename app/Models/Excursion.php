<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Excursion extends Model implements TranslatableContract, HasMedia
{
    use Translatable;
    use InteractsWithMedia;

    public $translatedAttributes = ['name', 'description', 'short_description'];

    protected $fillable = [
        'slug',
        'type',
        'image_url',
        'price_default',
        'available_from',
        'available_to',
        'location_id',
        'is_active',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
