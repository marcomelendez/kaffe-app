<?php

namespace App\Scr;

use App\Models\Plan;
use App\Models\Producto;
use App\Scr\Interfaces\IAmount;
use App\Scr\Interfaces\IProductRate;
use App\Scr\Interfaces\IRoom;
use Carbon\Carbon;

class RoomRate implements IProductRate
{

    private String $code;

    private Room $room;

    private Plan $plan;

    private IAmount $amount;

    private IAmount $amountAdults;

    private IAmount $amountChildren;

    private mixed $commission;

    private float $discount;


    public function __construct(IRoom $room, Plan $plan, IAmount $amount, $discount = 0, $commission = 10)
    {
        $this->room       = $room;
        $this->plan       = $plan;
        $this->amount     = $amount;
        $this->commission = $commission;
        $this->discount   = $discount;
        $this->code = md5(Carbon::now()->getTimestamp());
    }

    public function setCode($value)
    {
        $this->code = $value;
    }
    public function getCode(): String
    {
        return $this->code;
    }

    public function getRoom(): IRoom|Room
    {
        return $this->room;
    }

    public function getPlan(): Plan
    {
        return $this->plan;
    }

    public function setAmountPVP(IAmount $amount)
    {
        $this->amount = $amount;
    }

    public function getAmountPVP(): IAmount
    {
        return $this->amount;
    }

    public function getAmountPVD(): float
    {
        $commission = $this->amount->getQuantity() * ($this->commission / 100);
        return $this->amount->getQuantity() - $commission;
    }

    public function setDiscount(float $value)
    {
        $this->discount = $value;
    }
    public function getDiscount(): float
    {
        return $this->discount;
    }

    public function getCommission()
    {
        return $this->commission;
    }

    public function getMealPlan()
    {
        return $this->plan->name;
    }

    /**
     * @param IAmount $amount
     * @return void
     */
    public function setAmountChildren(IAmount $amount): void
    {
        $this->amountChildren = $amount;
    }

    public function getAmountChildren(): IAmount
    {
        return $this->amountChildren;
    }

    /**
     * @param IAmount $amount
     * @return void
     */
    public function setAmountAdults(IAmount $amount): void
    {
        $this->amountAdults = $amount;
    }

    public function getAmountAdults(): IAmount
    {
        return $this->amountAdults;
    }

    public function getAmountDiscount(): float
    {
        return $this->discount;
    }
}
