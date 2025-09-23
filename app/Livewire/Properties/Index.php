<?php

namespace App\Livewire\Properties;

use App\Models\Property;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public function render()
    {
        return view('livewire.properties.index', [
            'properties' => Property::paginate(10),
        ]);
    }
}
