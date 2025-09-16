<?php

namespace App\Scr\Interfaces;

interface IRoom
{
    public function getName();

    public function getCode();

    public function getOccupancyName();

    public function getMinOccupancy();

    public function getMaxOccupancy();
}