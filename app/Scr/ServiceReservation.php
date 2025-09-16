<?php

namespace App\Scr;

use App\Models\BookableUnit;
use App\Models\Property;
use DateTime;
use Exception;
use Illuminate\Support\Str;

class ServiceReservation
{
    protected $geoLocation = 1;

    protected $roomOccupancy;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function setGeoLocation($location)
    {
        $this->geoLocation = $location;
    }
    

    public function getBookables()
    {
        $properties = Property::all();

        $propertiesRate = [];

        foreach($properties as $property){

            $rates = [];
            $rateCode = null;
            $roomCode = null;

            if($property->multi_unit == 1){
                /* Obtenemos las unicadades (BookableUnits) */

                foreach($this->getRoomOccupancy()->getOccupancy() as $rooms){

                    $propertiesUnits = $property->getBookableUnit($rooms->getTotalAdults());

                    foreach($propertiesUnits as $propertiesUnit){

                        if($rooms->getTotalPersons() > $propertiesUnit->room_limit){

                            continue;
                        }

                        $bookableUnit = BookableUnit::find($propertiesUnit->BookableId);

                        if($pricings = $this->getRates($bookableUnit,$this->getRoomOccupancy()->getStartDate(),$this->getRoomOccupancy()->getEndDate())){

                            if($roomCode != $propertiesUnit->room_code){

                                $rateCode = Str::random(32);
                            }

                            $rates[$rateCode][] = new Rate($bookableUnit,
                                                $propertiesUnit->room_code,
                                                $propertiesUnit->room_name .' '.$propertiesUnit->name,
                                                $propertiesUnit->meal_plan,
                                                $rooms->getTotalAdults(),
                                                $rooms->getTotalChilds(),
                                                $pricings);

                            $roomCode = $propertiesUnit->room_code;
                        }
                    }
                }
            }

            if($rates){
                $property->addRates($this->orderByRooms($rates));
                $propertiesRate[] = $property;
            }
        }

        return $propertiesRate;
    }

    public function getRoomOccupancy()
    {
        return $this->request->getRoomsOccupancy();
    }

    public function getRates($bookable, $startDate, $endDate)
    {
        $rates = [];

        if($pricing = $bookable->getPriceValue($startDate, $endDate)){

            $rates[] = $pricing;

        }

        return $rates;
    }

    public function orderByRooms(array $rates): array
    {
        $result = [];

        foreach($rates as $rate){

            /*@var Rate */
            $code = $rate->getCode().'-'.$rate->getRoomName();

            if(array_key_exists($code,$result)){

                $quantity = $rate->getQuantity() + 1;
                $rate->setQuantity($quantity);
            }

            $result[$code] = $rate;
        }

        return array_values($result);
    }
}
