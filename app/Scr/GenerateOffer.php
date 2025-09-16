<?php

namespace App\Scr;

use App\Models\BookableUnit;
use App\Models\Property;
use App\Models\Room;
use App\Models\RoomUnit;
use App\Scr\RequestAccommodation as RequestSCR;
use App\Scr\Room as RoomSCR;
use Illuminate\Support\Collection;

class GenerateOffer
{
    const TYPE_RATE_PERSONS = 1;

    private $request;

    public function __construct(RequestAccommodation $request)
    {
        $this->request = $request;
    }

    /**
     * @param Collection $properties
     * @return ResponseAccommodation
     */
    public function listProperty(Collection $properties): ResponseAccommodation
    {
        $responseAccommodation = new ResponseAccommodation();
        $responseAccommodation->setRequest($this->request);

        /** @var Property $property */
        foreach ($properties as $property) {

            $this->request->setProducto($property->productable);
            $responseRoomRates = $this->roomRates($property);

            if (count($responseRoomRates->getRoomRate()) === 0) {

                continue;
            }

            $responseRoomRates->setProperty($property);
            $responseAccommodation->addRoomRates($responseRoomRates);
        }

        return $responseAccommodation;
    }

    private function comprobateRooms(Room $room, RequestAccommodation $requestAccommodation)
    {
        $arrRooms = [];

        /** @var RoomOccupancy $roomOccupancy */
        foreach($requestAccommodation->getRoomsOccupancy() as $inx => $roomOccupancy) {
            /** Si la cantidad de personas supera la capacidad de la habitacion segmentarla */

            if($roomOccupancy->getTotalPersons() > $room->max_persons){

                if ($roomOccupancy->getTotalAdults() == 4) {
                    $adults = generarteArray(($roomOccupancy->getTotalAdults() / 2),34);
                    $arrRooms[] = new RoomOccupancy($adults, $roomOccupancy->getChildren());
                    $arrRooms[] = new RoomOccupancy($adults);
                }

                if ($roomOccupancy->getTotalAdults() == 3) {

                    $arrRooms[] = new RoomOccupancy([34], $roomOccupancy->getChildren());
                    $arrRooms[] = new RoomOccupancy([34, 34]);
                }

                if ($roomOccupancy->getTotalAdults() == 2) {

                    $arrRooms[] = new RoomOccupancy([34], $roomOccupancy->getChildren());
                    $arrRooms[] = new RoomOccupancy([34]);
                }

            }else{
                $arrRooms[] = $roomOccupancy;
            }
        }

        $requestAccommodation->setRoomsOccupancy($arrRooms);
        return $requestAccommodation;
    }

    public function loadProperty(Property $property): ResponseAccommodation
    {
        $responseAccommodation = new ResponseAccommodation();
        $responseAccommodation->setRequest($this->request);

        $this->request->setProducto($property->productable);

        $responseRoomRates = $this->roomRates($property);

        $responseRoomRates->setProperty($property);

        $responseAccommodation->addRoomRates($responseRoomRates);

        return $responseAccommodation;

    }

    /**
     * @param RequestAccommodation $roomOccupancy
     * @param Room $room
     * @param ResponseRoomRates $responseRoomRates
     * @return ResponseRoomRates|null
     */
    private function getResponseRoomRate(RequestSCR $roomOccupancy, Room $room, ResponseRoomRates $responseRoomRates): ?ResponseRoomRates
    {
        /** @var RoomUnit $item */
        foreach ($room->unit as $item) {

            /** @var RoomOccupancy $occupancy */
            foreach ($roomOccupancy->getRoomsOccupancy() as $occupancy) {

                $pricingAdults    = 0;
                $pricingChildren  = 0;
                $discountAdult    = 0;
                $discountChildren = 0;


                /** @var BookableUnit $bookables */
                foreach ($item->bookables as $bookables) {

                    /** Children */
                    if ($bookables->name == config('options.room_child') && $occupancy->getTotalChildren() > 0) {
                        $pricingChildren = $bookables->getPriceWithDiscount($roomOccupancy->getStartDate(), $roomOccupancy->getEndDate(),$item->product);
                    }
                    /** Adults */
                    if ($bookables->min_occupancy >= $occupancy->getTotalAdults() &&
                        $bookables->max_occupancy <= $occupancy->getTotalAdults()) {
                        $code = $bookables->id;

                        $nameRoom = comparative($occupancy->getTotalAdults());
                        $pricingAdults = $bookables->getPriceWithDiscount($roomOccupancy->getStartDate(), $roomOccupancy->getEndDate(), $item->product);
                    }
                }

                if ($room->type == self::TYPE_RATE_PERSONS) {

                    if ($pricingAdults instanceof TotalPVP && $pricingAdults->getAmount()->getQuantity() > 0) {
                        $discountAdult = $pricingAdults->getDiscount()->getQuantity() * $occupancy->getTotalAdults();
                        $pricingAdults = $pricingAdults->getAmount()->getQuantity() * $occupancy->getTotalAdults();
                    }

                    if ($pricingChildren instanceof TotalPVP && $pricingChildren->getAmount()->getQuantity() > 0) {
                        $discountChildren = $pricingChildren->getDiscount()->getQuantity() * $occupancy->getTotalChildren();
                        $pricingChildren = $pricingChildren->getAmount()->getQuantity() * $occupancy->getTotalChildren();
                    }
                }

                $totalRoom     = $pricingAdults + $pricingChildren;
                $totalDiscount = $discountAdult + $discountChildren;

                if ($totalRoom > 0) {

                     $roomSRC  = new RoomSCR($room->id, $room->code, $room->name, $nameRoom);
                     $roomSRC->setRoomOccupancy($occupancy);
                     $roomRate = new RoomRate($roomSRC, $item->plan, new Amount($totalRoom), $totalDiscount);
                     $roomRate->setCode($code);
                     $roomRate->setAmountAdults(new Amount($pricingAdults));
                     $roomRate->setAmountChildren(new Amount($pricingChildren));

                     $responseRoomRates->addRoomRate($roomRate);
                }

            }
        }

        return $responseRoomRates;
    }

    /**
     * @param Property $property
     * @return ResponseRoomRates
     */
    private function roomRates(Property $property): ResponseRoomRates
    {
        $responseRoomRates = new ResponseRoomRates();

        /** @var Room $room */
        foreach ($property->roomsAll as $room) {

            $requestAccommodationProperty = clone $this->request;

            /** @var RequestAccommodation $roomOccupancy */
            $roomOccupancy = $this->comprobateRooms($room, $requestAccommodationProperty);

            $this->getResponseRoomRate($roomOccupancy, $room, $responseRoomRates);
        }

        return $responseRoomRates;
    }
}
