<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExcursionTranslation extends Model
{
    public $timestamps = false;
    protected $fillable = ['name', 'description', 'short_description'];
}
