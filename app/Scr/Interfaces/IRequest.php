<?php

namespace App\Scr\Interfaces;

use App\Models\Producto;

interface IRequest
{
    public function getProducto(): Producto;

    public function getStartDate(): \DateTime;

    public function getEndDate(): \DateTime;

    public function totalAdults();

    public function totalChilds();
}
