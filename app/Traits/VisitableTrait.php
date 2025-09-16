<?php namespace App\Traits;

use App\Models\Visit;

trait VisitableTrait
{
    public function hit() {
        $visit = new Visit;
        $reflection = new \ReflectionClass($this);        
        $visit->visitable_id = $this->id;
        $visit->visitable_type = $reflection->getName();
        $visit->ip = app('request')->ip();
        $visit->user_agent = app('request')->header('User-Agent');
        if(empty($visit->user_agent)) $visit->user_agent = 'unknown';
        $visit->save();  
    }

    public function visits()
    {
        return $this->morphMany('App\Models\Visit', 'visitable');
    }   
}