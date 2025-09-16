<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class PropertyTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['title', 'description', 'short_description', 'owner_highlights','directions','other_conditions'];

}
