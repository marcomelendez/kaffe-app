<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Astrotomic\Translatable\Contracts\Translatable as TranslatableContract;
use Astrotomic\Translatable\Translatable;

class Plan extends Model implements TranslatableContract
{
    use HasFactory;
    use Translatable;

    public $fillable = ['code'];
    public $translatedAttributes = ['name','slug','description'];


    public function properties()
    {
    	return $this->belongsToMany(Property::class);
    }
}
