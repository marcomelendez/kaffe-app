<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'name',
        'description',
        'type',
        'max_adult',
        'max_child',
        'max_persons',
        'status'
    ];


    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    public function unitAdults()
    {
        return $this->hasMany(UnitRoomAdult::class);
    }

    public function unitChildren()
    {
        return $this->hasMany(UnitRoomChild::class);
    }

    public function getBookableIdsThroughUnitAdults(): \Illuminate\Support\Collection
    {
        $unitAdultIds = $this->unitAdults()->pluck('id');

        return BookableUnit::whereIn('bookable_id', $unitAdultIds)
            ->where('bookable_type', UnitRoomAdult::class)
            ->get();
    }

    public function getBookableIdsThroughUnitChildren(): \Illuminate\Support\Collection
    {
        $unitChildIds = $this->unitChildren()->pluck('id');

        return BookableUnit::whereIn('bookable_id', $unitChildIds)
            ->where('bookable_type', UnitRoomChild::class)
            ->get();
    }

    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
