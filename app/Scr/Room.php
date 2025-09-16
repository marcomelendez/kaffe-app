<?php

namespace App\Scr;

use App\Scr\Interfaces\IRoom;

/**
 * Clase que obtiene la informacion relacionada con la habitacion
 *
 */

class Room implements IRoom
{
    const DEFAULT_TYPE_RATE = 1;

    protected $id;

    protected $code;

    protected $name;

    protected $occupancyName;

    protected $roomOccupancy;

    protected $quantity = 1;

    protected $person;

    protected $maxOccupancy = 1;

    protected $minOccupancy = 1;

    protected $typeRate = self::DEFAULT_TYPE_RATE;

    protected $roomModel;

    /**
     * Undocumented function
     *
     * @param [type] $id
     * @param [type] $code
     * @param [type] $name
     * @param [type] $occupancyName
     */
    public function __construct($id,$code,$name,$occupancyName = "")
    {
        $this->id = $id;
        $this->code = $code;
        $this->name = $name;
        $this->occupancyName = $occupancyName;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getRoomModel()
    {
        if($this->roomModel == null){
            $this->roomModel = \App\Models\Room::find($this->getId());
        }
        return $this->roomModel;
    }

    public function getName()
    {
        return $this->getRoomModel()->name;
    }

    public function setOccupancyName(string $value)
    {
        $this->occupancyName = $value;
    }

    public function getOccupancyName()
    {
        return $this->occupancyName;
    }

    public function setQuantity($value)
    {
        $this->quantity = $value;
    }

    public function getQuantity()
    {
        return $this->quantity;
    }

    public function setPerson($value)
    {
        $this->person = $value;
    }

    public function getPerson()
    {
         return $this->getRoomOccupancy()->getTotalPersons();
    }

    public function setMinOccupancy($value)
    {
        $this->minOccupancy = $value;
    }

    public function getMinOccupancy()
    {
        return $this->minOccupancy;
    }

    public function setMaxOccupancy($value)
    {
        $this->maxOccupancy = $value;
    }

    public function getMaxOccupancy()
    {
        return $this->maxOccupancy;
    }

    /**
     * Nombre completo de la habitacion (Habitacion mas ocupacion ejemp: Standard DBL)
     *
     * @return string
     */
    public function getFullName()
    {
        return $this->name .' '. $this->occupancyName;
    }


    public function setRoomOccupancy(RoomOccupancy $roomOccupancy)
    {
        $this->roomOccupancy = $roomOccupancy;
    }

    public function getRoomOccupancy(): RoomOccupancy
    {
        return $this->roomOccupancy;
    }

    public function setTypeRate($value)
    {
        $this->typeRate = $value;
    }

    public function getTypeRate()
    {
        return $this->typeRate;
    }
}
