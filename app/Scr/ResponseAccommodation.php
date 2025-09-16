<?php

namespace App\Scr;

class ResponseAccommodation
{
    protected $roomRates = [];

    protected Request $request;

    public function setRequest(RequestAccommodation $request)
    {
        $this->request = $request;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function addRoomRates(ResponseRoomRates $rate)
    {
        $this->roomRates[] = $rate;
    }

    public function getRoomRates()
    {
        return $this->roomRates;
    }

}
