<?php

namespace App\Admin\Properties;

use App\Models\BookableUnit;
use App\Models\Property;

class RoomRate
{
    /**
     * Get all rooms with their bookable units for a property
     *
     * @param integer $id
     * @return array
     */
    public function getByProperty(int $id): array
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

    /**
     * Get all bookable units for a property
     *
     * @param int $id
     * @return array
     */
    public function getBookableUnits(int $id): array
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
            $units[$key] = $bookableUnits;
        }

        return $units;
    }

    /**
     * Get room rates for a property within a date range
     *
     * @param int $id
     * @param \DateTime $start
     * @param \DateTime $end
     * @return array
     */

    public function getRoomRates(int $id, \DateTime $start, \DateTime $end)
    {
        $units = $this->getBookableUnits($id);
        $rates = [];

        foreach ($units as $roomId => $bookables) {

            foreach ($bookables as $bookableUnit) {

                $planId = $bookableUnit->bookable->plan_id;

                $pricing = $bookableUnit->getPriceValue($start, $end);
                // Adults
                if ($bookableUnit->bookable_type == 'App\Models\UnitRoomAdult') {
                    $rates[$planId][$roomId][] = [
                        'id'=>$bookableUnit->id,
                        'adults' => $bookableUnit->bookable->adults,
                        'type' => 'unitAdultIds',
                        'value'=> $pricing->getAmount()->getQuantity() ?? 0,
                    ];
                }
                // Children
                if ($bookableUnit->bookable_type == 'App\Models\UnitRoomChild') {
                    $rates[$planId][$roomId][] = [
                        'id'=>$bookableUnit->id,
                        'ages' => $bookableUnit->bookable->age,
                        'type' => 'unitChildIds',
                        'value' => $pricing->getAmount()->getQuantity() ?? 0,
                    ];
                }
            }
        }

        return $rates;
    }
}
