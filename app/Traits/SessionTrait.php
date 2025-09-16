<?php

namespace App\Traits;

use App\Scr\Product;
use App\Scr\RoomRate;

trait SessionTrait
{
    /**
     * @return array
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public function getSessionContent(): array
    {
        $productSession = [];
        $roomsSession = [];

        if(\Session()->has('_quote')){
            /** @var Product $product */
            foreach (\Session()->get('_quote.product') as $product){

                foreach($product->getRates() as $rates){
                    /** La tarifa pertenece a habitaciones  */
                    if($rates instanceof RoomRate){
                        $roomsSession[] = [
                            'code'=>$rates->getCode(),
                            'room_name' => $rates->getRoom()->getName(),
                            'adults'    => $rates->getRoom()->getRoomOccupancy()->getTotalAdults(),
                            'children'  =>$rates->getRoom()->getRoomOccupancy()->getTotalChildren(),
                            'discount'  =>$rates->getDiscount(),
                            'amount'    =>$rates->getAmountPVP()->getQuantity(),
                            'agesChildren'=>$rates->getRoom()->getRoomOccupancy()->getChildren()
                        ];
                    }
                }

                $productSession[] = [
                    'product'=>$product->getName(),
                    'checkIn' =>$product->getRequest()->getStartDate()->format('Y-m-d'),
                    'checkOut'=>$product->getRequest()->getEndDate()->format('Y-m-d'),
                    'plan'=>$product->getRequest()->getPlan()->name,
                    'rates'=>$roomsSession
                ];
            }
        }

        return $productSession;
    }
}
