<?php

namespace App\Livewire\Roomrate;

use App\Models\BookableUnit;
use Illuminate\Database\Eloquent\Attributes\Boot;
use Livewire\Component;
use Illuminate\Support\Facades\Validator;

class Create extends Component
{

    public $boards;

    private $property;

    public $propertyId;

    public $rooms = [];

    public $unitAdultIds = [];
    public $unitChildIds = [];

    public $rangeValid = true;


    public array $dateRange = [
        'start' => '',
        'end' => '',
    ];


    public function mount($id)
    {
        $this->dispatch('init-daterange');
        $property = \App\Models\Property::find($id);
        $this->propertyId = $id;

        $this->boards = $property->plan;

        foreach($property->roomsAll as $room){

            $this->rooms[] = [
                'name' => $room->name,
                'unit_adults' => $room->getBookableIdsThroughUnitAdults(),
                'unit_children' => $room->getBookableIdsThroughUnitChildren()
            ];

        }
    }


    public function render()
    {
        return view('livewire.roomrate.create');
    }

    public function save()
    {
        $this->validateRate();

        $bookableUnits = array_replace($this->unitAdultIds, $this->unitChildIds);
        $start = new \DateTime($this->dateRange['start']);
        $end  = new \DateTime($this->dateRange['end']);
        $end  = $end->modify( '+1 day' );

        foreach($bookableUnits as $key => $value){
            BookableUnit::find($key)->saveEventDaily($start, $end, $value);
        }

        return $this->redirectRoute('room_rate.index', ['id' => $this->propertyId]);
    }


    public function validateRate(): array
    {
        $validateData = [];
        $validateRules = [];

        $this->rangeValid = empty($this->dateRange['start']) || empty($this->dateRange['end']) ? false : true;

        $rooms = $this->rooms;

        foreach($rooms as $room) {
            foreach($room['unit_adults'] as $unitAdultId) {

                if (!isset($this->unitAdultIds[$unitAdultId->id]) || $this->unitAdultIds[$unitAdultId->id] === null) {
                    $validateData['unitAdultIds.' . $unitAdultId->id] = $this->unitAdultIds[$unitAdultId->id] ?? null;
                    $validateRules['unitAdultIds.' . $unitAdultId->id] = 'number|required';
                }
            }

            foreach ($room['unit_children'] as $unitChildId) {

                if (!isset($this->unitChildIds[$unitChildId->id]) || $this->unitChildIds[$unitChildId->id] === null) {
                    $validateData['unitChildIds.' . $unitChildId->id] = $this->unitChildIds[$unitChildId->id] ?? null;
                    $validateRules['unitChildIds.' . $unitChildId->id] = 'number|required';
                }
            }
        };


        $validator =  Validator::make($validateData, $validateRules)->validate();
        return $validator;
    }


}
