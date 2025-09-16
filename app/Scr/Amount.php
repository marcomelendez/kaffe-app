<?php

namespace App\Scr;

use App\Scr\Interfaces\IAmount;

class Amount implements IAmount
{
    private $quantity;

    private $currency;

    public function __construct($quantity, $currency = 'USD')
    {
        $this->quantity = $quantity;
        $this->currency = $currency;
    }

    /**
     *
     * @param [float] $value
     * @return void
     */
    public function setQuantity($value)
    {
        $this->quantity = $value;
    }

    /**
     * Devuelve la cantidad 
     *
     * @return void
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * Devuelve el codigo de moneda
     *
     * @return string
     */
    public function getCurrency()
    {
        return $this->currency;
    }
}
    