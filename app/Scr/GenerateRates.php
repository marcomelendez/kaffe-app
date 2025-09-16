<?php

namespace App\Scr;

use App\Models\BookableUnit;
use App\Models\Plan;
use App\Models\Property;
use App\Scr\Interfaces\IRate;
use Exception;

class GenerateRates
{
    const UNIT_CHILD = 'CHILDREN';

    protected $colRooms = [];

    /**
     * Undocumented function
     *
     * @param [type] $propertyId
     * @param integer $occupancy
     * @return IRate
     */
    private function getPropertyRoomsBookable($propertyId, $occupancy = 1, Request $request): IRate
    {
        $property = Property::find($propertyId);

        $arrRooms = [];

        $rate = new Rate();

        foreach ($property->getBookableUnit($occupancy) as $roomProperty) {

            $pricing = $this->getPriceValue($roomProperty->BookableId, $request->getStartDate(), $request->getEndDate());

            $room = new Room($roomProperty->BookableId,
                            $roomProperty->room_code,
                            $roomProperty->room_name,
                            $roomProperty->name);

            $room->setPerson($roomProperty->room_limit);
            $room->setMinOccupancy($roomProperty->min_occupancy);
            $room->setMinOccupancy($roomProperty->max_occupancy);

            $plan = Plan::find($roomProperty->meal_plan);

            $amount = new Amount($pricing);

            $codeRate = $roomProperty->room_code.'_'.$plan->code;

            $rate->setCode($codeRate);
            $rate->addRoom(new RoomRate($room,$plan,$amount));
        }

        return $rate;
    }


    /**
     * Undocumented function
     *
     * @param [type] $propertyId
     * @param Request $request
     * @return void
     */
    public function getRoomsType($propertyId, Request $request)
    {
        foreach ($request->getRoomsOccupancy() as $inx => $occupancy) {

            $adults = $occupancy->getTotalAdults();

            $bookableUnits = $this->getPropertyRoomsBookable($propertyId, $adults, $request);

            if(!$bookableUnits){

                throw new Exception('Habitacion no encontrada');
            }

            foreach ($bookableUnits->getRooms() as $unitInx => $bookableUnitRow) {

                $code = $bookableUnitRow->getRoom()->getCode().'_'.$bookableUnitRow->getPlan()->code;
                $id   = $bookableUnitRow->getRoom()->getId();
                $i = 0;

                if (!$this->hasElement($code, $id)) {

                    if ($bookableUnitRow->getRoom()->getOccupancyName() !== self::UNIT_CHILD) {

                        $rates = $this->getPriceValue($id, $request->getStartDate(), $request->getEndDate());

                        $this->colRooms[$code][] = [
                            'id' => $id,
                            'code' => $code . $bookableUnitRow->getRoom()->getOccupancyName(),
                            'name' => $bookableUnitRow->getRoom()->getFullName(),
                            'meal_plan' => $bookableUnitRow->getMealPlan(),
                            'code_plan'=>$bookableUnitRow->getPlan()->code,
                            'cant' => 1,
                            'mount'=>$bookableUnitRow->getAmount()->getQuantity()
                        ];

                        $i++;
                    }
                }
            }
        }

        return $this->colRooms;
    }

    /**
     * Undocumented function
     *
     * @param [type] $code
     * @param [type] $id
     * @return boolean
     */
    private function hasElement($code, $id)
    {
        $result = false;

        if (isset($this->colRooms[$code])) {

            foreach ($this->colRooms[$code] as $inx => $rooms) {

                if ($rooms['id'] == $id) {

                    $result = true;
                    $this->colRooms[$code][$inx]['cant']++;
                    break;
                }
            }
        }

        return $result;
    }

    public function getPriceValue($bookableId, $startDate, $endDate)
    {
        $bookableUnit = BookableUnit::find($bookableId);

        $rates = 0;

        if($pricing = $bookableUnit->getPriceValue($startDate, $endDate)){

            $rates += $pricing;

        }

        return $rates;
    }
}
