<?php
namespace App\Scr;

use App\Models\Plan;
use App\Models\Producto;

class RequestAccommodation extends Request
{
    private Plan $plan;

    public function setPlan(Plan $plan)
    {
        $this->plan = $plan;
    }

    /**
     * @param \App\Scr\RoomOccupancy $roomOccupancy
     * @return void
     */
    public function addRoomsOccupancy(RoomOccupancy $roomOccupancy)
    {
        $this->roomsOccupancy[] = $roomOccupancy;
    }

    public function setRoomsOccupancy(array $roomOccupacy)
    {
        $this->roomsOccupancy = $roomOccupacy;
    }

    /**
     * @return array|mixed
     */
    public function getRoomsOccupancy(): array
    {
        return $this->roomsOccupancy;
    }

    /**
     * @return int
     */
    public function totalRooms()
    {
        return count($this->roomsOccupancy);
    }

    public function getPlan(): Plan
    {
        return $this->plan;
    }

    public function totalAdults()
    {
        $adults = 0;
        /** @var \App\Scr\RoomOccupancy $roomsOcupancy */
        foreach($this->roomsOccupancy as $roomsOcupancy){
            $adults += $roomsOcupancy->getTotalAdults();
        }
        return $adults;
    }

    public function totalChilds()
    {
        $childs = 0;
        /** @var \App\Scr\RoomOccupancy $roomsOcupancy */
        foreach($this->roomsOccupancy as $roomsOcupancy){
            $childs += $roomsOcupancy->getTotalChildren();
        }
        return $childs;
    }
}
