<?php

namespace App\Livewire\Roomrate;

use Livewire\Component;

class Index extends Component
{
    public $propertyId;

    public function mount($id)
    {
        $this->propertyId = $id;
        // You can use the $id parameter to fetch data or perform actions based on the ID.
        // For example, you might want to load room rates for a specific property.
    }

    public function render()
    {
        return view('livewire.roomrate.index');
    }
}
