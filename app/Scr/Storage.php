<?php

namespace App\Scr;

use App\Models\Plan;
use App\Models\Producto;
use App\Scr\Interfaces\IAmount;
use App\Scr\Interfaces\IProductRate;
use App\Scr\Interfaces\IRoom;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Session;

class Storage
{
    private Request $request;

    public function initProduct(Request $request): void
    {
        $this->request = $request;
        $product = new Product($this->request, $this->request->getProducto());

        $colSession = Session::get("_quote.product", new Collection());

        if (!$colSession->contains(function ($value, $key) {
            return $value->getClassification() === $this->request->getProducto()->clasificacion_id;
        })) {
            $colSession = $colSession->push($product);
        }

        \Session()->put('_quote.product', $colSession);
    }

    public function addRate(IProductRate $rates)
    {
        if(empty($this->request)){
            throw new \Exception('Debes inicializar primero');
        }
        /** @var Collection $colSession */
        $colSession = Session::get("_quote.product", new Collection());

        $roomsSession = $colSession->map(function($item, $key) use($rates){
            if($item->getProducto()->id == $this->request->getProducto()->id){
                $item->addRates($rates);
            }
            return $item;
        });

        Session::put('_quote.product', $roomsSession);
    }


    public function delete($code)
    {
        $colSession = Session::get("_quote.product", new Collection());

        $newCollection = $colSession->map(function($item, $key) use($code){
            foreach($item->getRates() as $inx => $rates){
                if($rates->getCode() == $code){
                    $item->delete($inx);
                }
            }

            return $item;
        });

        Session::put('_quote.product', $newCollection );
    }
}
