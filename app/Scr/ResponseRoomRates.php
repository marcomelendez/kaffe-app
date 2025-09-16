<?php

namespace App\Scr;

use App\Models\Property;

class ResponseRoomRates
{
    protected $property;
    protected  $roomRate = [];

    /**
     * @param Property $property
     */
    public function setProperty(Property $property)
    {
        $this->property = $property;
    }

    public function getProperty()
    {
        return $this->property;
    }

    public function addRoomRate(RoomRate $roomRate)
    {
        if(!$this->hasRoomRate($roomRate)) {

            $this->roomRate[] = $roomRate;
        }
    }

    public function getRoomRate()
    {
        return $this->roomRate;
    }

    public function hasRoomRate(RoomRate $rate)
    {
        /** @var RoomRate $roomRate */

        foreach($this->getRoomRate() as $inx => $roomRate){

            if($roomRate->getCode() === $rate->getCode()){

                return $this->roomRate[$inx] = $this->updateMerge($roomRate, $rate);
            }
        }
    }

    private function updateMerge(RoomRate $roomRate, RoomRate $rateCurrent): RoomRate
    {
        $occupancyParams = $rateCurrent->getRoom()->getRoomOccupancy();
        $occupancy = clone $roomRate->getRoom()->getRoomOccupancy();
        //
        $adults    = array_merge($occupancy->getAdults(), $occupancyParams->getAdults());
        $children  = array_merge($occupancy->getChildren(), $occupancyParams->getChildren());
        //
        $quantity  = $roomRate->getRoom()->getQuantity() + 1;
        $amountPVP = $roomRate->getAmountPVP()->getQuantity() + $rateCurrent->getAmountPVP()->getQuantity();
        $discount  = $roomRate->getDiscount() + $rateCurrent->getDiscount();
        $roomRate->setDiscount($discount);
        $roomRate->setAmountPVP(new Amount($amountPVP));
        //
        $occupancy->setAdults($adults);
        $occupancy->setChildren($children);
        $roomRate->getRoom()->setQuantity($quantity);

        $roomRate->getRoom()->setRoomOccupancy($occupancy);

        return $roomRate;
    }
}
