<?php
namespace App\Scr;
use App\Models\Producto;
use App\Models\Room;
use App\Scr\Interfaces\IRequest;
use App\Scr\RoomOccupancy;
use App\Models\BookableUnit;
use App\Models\Property;
use DateTime;

class Request implements IRequest
{
    public Producto $producto;

    public \DateTime $startDate;

    public \DateTime $endDate;

    public $roomsOccupancy = [];

    /**
     * @param DateTime $startDate
     * @param DateTime $endDate
     */
    public function __construct(\DateTime $startDate,\DateTime $endDate = null)
    {
        $this->startDate  = $startDate;
        $this->endDate    = $endDate;
    }

    public function setProducto(Producto $producto)
    {
        $this->producto = $producto;
    }


    public function getProducto(): Producto
    {
        return $this->producto;
    }

    /**
     * @return DateTime
     */
    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    /**
     * @return DateTime
     */
    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    /**
     * @return array
     */
    public function getAgesChildren()
    {
        $ages = [];
        /** @var \App\Scr\RoomOccupancy $roomsOcupancy */
        foreach($this->roomsOccupancy as $roomsOcupancy){
            $ages[]  = implode(",",$roomsOcupancy->getChildren());
        }

        return $ages;
    }

    public function totalAdults()
    {

    }

    public function totalChilds()
    {

    }
}
