<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use App\Traits\LoggableTrait;
use App\Traits\FormTrait;

class BatEvent extends Model
{
    use LoggableTrait;
    use FormTrait;
    
    protected $table = 'bat_events';

    public function editable()
    {
        return [
             [
                'name' => 'event_group', 
                'type' => 'text', 
                'value' => $this->event_group,
                'label' => 'Event Group',
                'rules' => 'required' 
            ],[
                'name' => 'type', 
                'type' => 'text', 
                'value' => $this->type,
                'label' => 'Event type',
                'rules' => 'required'
            ],[
                'name' => 'name', 
                'type' => 'text', 
                'value' => $this->name,
                'label' => 'Name',
                'rules' => ''
            ],[
                'name' => 'start_date', 
                'type' => 'datepicker', 
                'value' => $this->start_date,
                'label' => 'Start date',
                'rules' => ''
            ],[
                'name' => 'end_date', 
                'type' => 'datepicker', 
                'value' => $this->end_date,
                'label' => 'End date',
                'rules' => ''
            ],[
                'name' => 'value', 
                'type' => 'text', 
                'value' => $this->value,
                'label' => 'Value',
                'rules' => ''
            ],[
                'name' => 'price_calculation', 
                'type' => 'text', 
                'value' => $this->price_calculation,
                'label' => 'Price calculation',
                'rules' => ''
            ],[
                'name' => 'granularity', 
                'type' => 'text', 
                'value' => $this->granularity,
                'label' => 'Granularity',
                'rules' => ''
            ]
        ];
    }
}
