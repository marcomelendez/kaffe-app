<?php
namespace App\Scr\Interfaces;

interface IProductRate
{
    public function getCode(): String;
    public function getAmountPVP(): IAmount;

    public function getAmountPVD(): float;

    public function getDiscount(): float;

    public function getCommission();

    public function getAmountDiscount(): float;
}
