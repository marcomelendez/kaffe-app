<?php 

namespace App\Scr;

use App\Models\BookableUnit;
use App\Models\Property;
use App\Scr\Interfaces\IRate;
use DateTime;


class Rate implements IRate
{    
    protected $code;

    protected $room;

    protected $mealPlan;

    protected $pricing;


    public function __construct()
    {

    }

    public function setCode($value)
    {
        $this->code = $value;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getAmountAdult()
    {

    }

    public function getAmountChild()
    {

    }

    public function addRoom(RoomRate $roomRate)
    {
        $this->room[] = $roomRate;
    }

    public function getRooms()
    {
        return $this->room;
    }

    public function getPricing()
    {
        return $this->pricing;
    }
}