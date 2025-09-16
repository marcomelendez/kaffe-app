<?php

namespace App\Scr;

use App\Scr\Interfaces\IAmount;
use App\Scr\Interfaces\IPVP;

class TotalPVP implements IPVP
{
    protected $amount;

    protected $discount;
    public function __construct(IAmount $amount, IAmount $discount)
    {
        $this->amount = $amount;
        $this->discount = $discount;
    }

    public function getAmount(): IAmount
    {
        return $this->amount;
    }

    public function getDiscount(): IAmount
    {
        return $this->discount;
    }
}
