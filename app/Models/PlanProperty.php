<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanProperty extends Model
{
    protected $table = "plan_property";

    protected $fillable = ['property_id','plan_id'];
}
