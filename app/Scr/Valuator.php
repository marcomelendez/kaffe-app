<?php

namespace App\Scr;

use App\Models\ProductDiscount;
use App\Models\Producto;
use Illuminate\Support\Collection;
use phpDocumentor\Reflection\Types\Boolean;
use Roomify\Bat\Calendar\Calendar;
use Roomify\Bat\Event\EventInterval;
use Roomify\Bat\Store\Store;
use Roomify\Bat\Unit\UnitInterface;
use Roomify\Bat\Valuator\IntervalValuator;

class Valuator extends IntervalValuator
{

    public function __construct(\DateTime     $start_date,
                                \DateTime     $end_date,
                                UnitInterface $unit,
                                Store         $store,
                                \DateInterval $duration_unit)
    {
        parent::__construct($start_date, $end_date, $unit, $store, $duration_unit);
        $this->duration_unit = $duration_unit;
    }

    /**
     * @return float
     */
    public function determineValue(): TotalPVP|Int
    {
        $totalValue = 0;
        // Instantiate a calendar
        $calendar = new Calendar(array($this->unit), $this->store);
        $events = $calendar->getEvents($this->start_date, $this->end_date);

        $totalDiscount = 0;

        foreach ($events as $unit => $unit_events) {

            if ($unit == $this->unit->getUnitId()) {

                foreach ($unit_events as $event) {
                    $percentage = (int) EventInterval::divide($event->getStartDate(), $event->getEndDate(), $this->duration_unit);
                    $duration = $percentage > 0 ? $percentage : 1;

                    if (empty($event->getValue()) && $this->end_date > $event->getStartDate()) {
                        return new TotalPVP(new Amount($totalValue), new Amount($totalDiscount));
                    }

                    $discount = 1;//$this->discount($event->getStartDate(), $event->getEndDate());
                    /* Percentage (cantidad de noches o horas o minutos) */
                    $value = $event->getValue() * $duration;
                    $totalValue += $value;
                    $totalDiscount += $value * $discount;

                }
            }
        }

        $totalValue = round($totalValue, 2);
        $totalDiscount = round($totalDiscount, 2);

        return new TotalPVP(new Amount($totalValue), new Amount($totalDiscount));
    }

    // private function getDiscountByProduct(): Collection
    // {
    //     return ProductDiscount::where('producto_id',$this->getProduct()->id)->get();
    // }

    // private function discount(\DateTime $startDate, \DateTime $endDate): float
    // {
    //     $discounts = $this->getDiscountByProduct(); // Obtienes los descuentos

    //     $intervalo = new \DateInterval('P1D'); // Intervalo de un día

    //     $startDateInverval = clone $startDate; // Hacemos una copia para no modificar la fecha de inicio
    //     $i = 0;
    //     $discountAmount = 0;

    //     while ($startDateInverval <= $endDate) {
    //         $date = $startDateInverval->format('Y-m-d');

    //         $discountAmount += $discounts->reduce(function($curry, $item) use ($date){
    //             if($item->date_start <= $date && $item->date_end >= $date){
    //                 return $curry + $item->amount;
    //             }
    //         },0);

    //         $startDateInverval->add($intervalo); // Agregar un día a la fecha actual
    //         $i++;
    //     }

    //     $total = $discountAmount / $i;
    //     return $total / 100;
    // }

    // public function setProduct(Producto $producto)
    // {
    //     $this->product = $producto;
    // }

    // private function getProduct()
    // {
    //     return $this->product;
    // }
}
