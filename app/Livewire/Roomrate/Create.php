<?php

namespace App\Livewire\Roomrate;

use App\Models\BookableUnit;
use Illuminate\Database\Eloquent\Attributes\Boot;
use Livewire\Component;

class Create extends Component
{

    public $boards;

    private $property;

    public $rooms = [];

    public $unitAdultIds = [];
    public $unitChildIds = [];

    public array $rangoFechas = [
        'start' => '',
        'end' => '',
    ];


    public function mount($id)
    {
        $property = \App\Models\Property::find($id);

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
        $bookableUnits = array_replace($this->unitAdultIds, $this->unitChildIds);
        $start = new \DateTime($this->rangoFechas['start']);
        $end  = new \DateTime($this->rangoFechas['end']);
        $end  = $end->modify( '+1 day' );

        $this->validate([
            // 'rangoFechas.start' => 'required|date',
            // 'rangoFechas.end' => 'required|date|after_or_equal:rangoFechas.start',
            'unitAdultIds' => 'required|array',
            'unitChildIds' => 'required|array'
        ]);

        dd("SI");

        foreach($bookableUnits as $key => $value){
            BookableUnit::find($key)->saveEventDaily($start, $end, $value);
        }
    }
}
