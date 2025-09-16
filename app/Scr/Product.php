<?php

namespace App\Scr;

use App\Models\Producto;
use App\Scr\Interfaces\IProduct;
use App\Scr\Interfaces\IProductRate;

class Product implements IProduct
{
    private Request $request;
    private array $productRate = [];

    private Producto $producto;
    public function __construct(Request $request, Producto $producto)
    {
        $this->request = $request;
        $this->producto = $producto;
    }

    public function getId()
    {
        return $this->producto->id;
    }

    public function getName()
    {
        return $this->producto->producto;
    }

    public function addRates(IProductRate $productRate)
    {
        $this->productRate[] = $productRate;
    }

    public function getRates(): array
    {
        return $this->productRate;
    }

    public function getProducto(): Producto
    {
        return $this->producto;
    }

    public function getClassification()
    {
        return $this->getProducto()->clasificacion_id;
    }

    public function delete($inx)
    {
        unset($this->productRate[$inx]);
    }

    public function getRequest(): Request
    {
        return $this->request;
    }
}
