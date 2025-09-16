<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UnitRoomChild extends Model
{
    protected $fillable = ['id', 'room_id', 'plan_id', 'age'];

    public $table = "unit_room_children";

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function bookables()
    {
        return $this->morphMany(BookableUnit::class, 'bookable');
    }
}
