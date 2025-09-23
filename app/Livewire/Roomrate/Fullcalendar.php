<?php

namespace App\Livewire\Roomrate;

use DateInterval;
use Livewire\Component;

class Fullcalendar extends Component
{

    public $event = [];

    private $propertyId;

    public function mount($id)
    {
        $this->propertyId = $id;


        $this->event = $this->loadEvent();
        $this->dispatch('init-calendar', ['events' => $this->event]);
    }

    public function loadEvent()
    {

        $roomRate = new \App\Admin\Properties\RoomRate();
        $units = $roomRate->getBookableUnits($this->propertyId);

        $result = [];

        foreach ($units[0] as $bookable) {

            $pricing = $bookable->getPricingCalendar(now()->startOfMonth(), now()->endOfMonth());
            $prices = array_first($pricing);

            foreach ($prices as $price) {

                $startDate = $price->getStartDate()->format('Y-m-d');
                $endDate = $price->getEndDate();
                $endDate->add(new DateInterval('P1D'));
                if($price->getValue() > 0){
                    $result[] = ['title' => $price->getValue(), 'start' =>  $startDate, 'end' => $endDate->format('Y-m-d'), 'color' => 'blue'];
                }
            }

            break;
        }
        return $result;
    }

    public function eventClick()
    {
        dd('evento clickeado');
    }

    public function render()
    {
        return view('livewire.roomrate.fullcalendar');
    }
}
