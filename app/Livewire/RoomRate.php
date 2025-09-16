<?php

namespace App\Livewire;

use Livewire\Component;

class RoomRate extends Component
{

    public $counter = 0;

    public function plusCounter()
    {
        $this->counter++;
    }

    public function minusCounter()
    {
        $this->counter--;
    }

    public function render()
    {
        return view('livewire.room-rate');
    }
}
