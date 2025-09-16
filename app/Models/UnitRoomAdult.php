<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitRoomAdult extends Model
{
    protected $fillable = ['id', 'room_id','plan_id','adults'];

    public $table = "unit_room_adults";

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function bookable()
    {
        return $this->morphOne(BookableUnit::class,'bookable');
    }
}
