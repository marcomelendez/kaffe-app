<?php

namespace App\Livewire;

use Livewire\Component;

class Fullcalendar extends Component
{

    public $event = [];

    public function mount()
    {
        $this->event = $this->loadEvent();
    }

    public function loadEvent()
    {
        return [
            ['title' => 'Evento 1', 'start' => '2025-09-14'],
            ['title' => 'Evento 2', 'start' => '2025-09-15'],
        ];
    }

    public function eventClick()
    {

    }

    public function render()
    {
        return view('livewire.fullcalendar');
    }
}
