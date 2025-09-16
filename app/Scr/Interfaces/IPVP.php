<?php

namespace App\Scr\Interfaces;

interface IPVP
{
    public function getAmount():IAmount;

    public function getDiscount():IAmount;
}
