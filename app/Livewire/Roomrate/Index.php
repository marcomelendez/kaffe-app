<?php

namespace App\Livewire\Roomrate;

use App\Models\BookableUnit;
use DateInterval;
use Livewire\Component;

class Index extends Component
{
    public $propertyId;

    public $event = [];

    public $rates;

    public $showRates = false;

    public $boards = [];

    public $rooms = [];

    public $unitAdultIds = [];

    public $unitChildIds = [];

    public $startDate;

    public $endDate;

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
        $units = array_first($units);
        foreach ($units as $bookable) {

            $pricing = $bookable->getPricingCalendar(now()->startOfMonth(), now()->endOfMonth());
            $prices = array_first($pricing);

            foreach ($prices as $price) {

                $startDate = $price->getStartDate()->format('Y-m-d');
                $endDate = $price->getEndDate();
                $endDate->add(new DateInterval('P1D'));
                if ($price->getValue() > 0) {
                    $result[] = ['title' => 'done', 'start' =>  $startDate, 'end' => $endDate->format('Y-m-d'), 'color' => 'blue'];
                }
            }
            break;
        }
        return $result;
    }

    public function dateClick($params)
    {
        $this->clear();

        $roomRate = new \App\Admin\Properties\RoomRate();
        $property = \App\Models\Property::find($this->propertyId);

        $this->boards = $property->plan;

        foreach ($property->roomsAll as $room) {

            $this->rooms[] = [
                'room_id'=> $room->id,
                'name' => $room->name,
                'unit_adults' => $room->getBookableIdsThroughUnitAdults(),
                'unit_children' => $room->getBookableIdsThroughUnitChildren()
            ];
        }

        $start = new \DateTime($params['start']);
        $end = new \DateTime($start->format('Y-m-d'));
        // $end->add(new DateInterval('P1D'));

        $this->startDate = $start;
        $this->endDate   = $end;

        $rates = $roomRate->getRoomRates($this->propertyId, $start, $end);

        foreach ($rates as $rate) {
            foreach($rate as $values) {
                foreach($values as $value) {
                    $type = $value['type'];
                    $this->$type[$value['id']] = $value['value'];
                }
            }
        }

        $this->showRates = true;
    }

    private function clear()
    {
        $this->rooms = [];
        $this->boards = [];
    }

    public function save()
    {
        //$this->validateRate();

        $bookableUnits = array_replace($this->unitAdultIds, $this->unitChildIds);
        $start = $this->startDate;
        $end  = $this->endDate;

        foreach ($bookableUnits as $key => $value) {
            BookableUnit::find($key)->saveEventDaily($start, $end, $value);
        }

        return $this->redirectRoute('room_rate.index', ['id' => $this->propertyId]);
    }

    public function render()
    {
        return view('livewire.roomrate.index');
    }
}
