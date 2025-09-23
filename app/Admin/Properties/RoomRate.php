<?php

namespace App\Admin\Properties;

use App\Models\BookableUnit;
use App\Models\Property;

class RoomRate
{
    public function getByProperty(int $id)
    {
        $property = Property::find($id);

        foreach ($property->roomsAll as $room) {
            // habitaciones
            $rooms[$room->id] = [
                'unit_adults' => $room->getBookableIdsThroughUnitAdults(),
                'unit_children' => $room->getBookableIdsThroughUnitChildren()
            ];
        }

        return $rooms;
    }

    public function getBookableUnits(int $id)
    {
        $roomUnits = $this->getByProperty($id);

        foreach ($roomUnits as $key => $value) {

            $unitAdults = $value['unit_adults']->map(function ($item) {
                return $item->id;
            })->toArray();

            $unitChildren = $value['unit_children']->map(function ($item) {
                return $item->id;
            })->toArray();

            $bookableUnits = BookableUnit::whereIn('id', array_merge($unitAdults,$unitChildren))->get();
            $units[] = $bookableUnits;
        }

        return $units;

    }
}
