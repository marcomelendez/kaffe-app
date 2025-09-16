<?php

namespace App\Scr\Interfaces;

use App\Scr\RoomRate;

interface IRate
{
    public function getCode();

    public function getAmountAdult();

    public function getAmountChild();

    public function addRoom(RoomRate $room);

    public function getRooms();
}    