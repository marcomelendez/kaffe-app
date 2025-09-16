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


        // foreach ($rooms as $room) {
        //     foreach ($room['unit_adults'] as $unitAdult) {

        //         $bookable = BookableUnit::find($unitAdult);
        //         // $valuate = $bookable->getPriceValue(new \DateTime('now'), new \DateTime('+ 3  days'));

        //         // dd($valuate);

        //         $bookable->saveEventDaily(new \DateTime('now'), new \DateTime('+ 10  days'), 100);

        //         exit;
        //     }
        // }
    }
}
