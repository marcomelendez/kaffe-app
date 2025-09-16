<?php
namespace App\Scr\Interfaces;

interface IProduct
{
    public function getName();

    public function addRates(IProductRate $productRate);

    public function getRates(): array;

}
