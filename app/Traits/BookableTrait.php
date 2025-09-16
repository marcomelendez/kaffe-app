<?php

namespace App\Traits;

use App\Booking;
use App\Models\BookableUnit;
use Roomify\Bat\Unit\Unit;
use Roomify\Bat\Store\SqlLiteDBStore;
use Roomify\Bat\Calendar\Calendar;
use App\Models\Reservation;
trait BookableTrait
{

    /**
     * Implementation of Search for Hotel rooms
     * 
     * @param  [type] $start_date     [description]
     * @param  [type] $end_date       [description]
     * @param  array  $valid_states   [description]
     * @param  [type] $bookable_units [description]
     * @return [type]                 [description]
     */
    // public function search($start_date, $end_date, $valid_states = array(), $bookable_units = NULL)
    // {
    //     $bat_units = array();

    // 	if($bookable_units === NULL) { // No array of units provided so get all
    //         $bookable_units = $this->units; 
    //     	foreach($bookable_units as $bookable) {	
    // 	        $bat_units[] = $bookable->getBatUnit();
    //         }
    //     }  else {
    //         $bookable_units = $this->findMany($bookable_units);
    //         foreach($bookable_units as $bookable) { 
    //             $bat_units[] = $bookable->getBatUnit();
    //         }            
    //     }

    //     $bookable = new BookableUnit;
        
    //     $global_constraints = $bookable->getConstraints();

    //     // setup state store
    //     $store = new SqlLiteDBStore(\DB::connection()->getPdo(), $bookable::BAT_AVAILABILITY, 'state');         

    //     $state_calendar = new Calendar($bat_units, $store);

    //     $response = $state_calendar->getMatchingUnits($start_date, $end_date, $valid_states, $global_constraints, TRUE);

    //     return $response;
    // }


    /**
     * Polymorphic relationship to BookableUnit
     * 
     * @return [type] [description]
     */
    // public function unit()
    // {
    //     return $this->morphMany('App\Models\BookableUnit', 'bookable');
    // }


    // public function allotments()
    // {
    //     return $this->morphOne('App\Models\Allotment', 'allotable');
    // }

    public function constraints()
    {
        return $this->morphMany('App\Models\Constraint', 'constrainable');
    }

    public function book($parameters)
    {   
        // $resevation = new Reservation;
        // $reservatio
    }

    public function reservations()
    {
        return $this->hasMany('App\Models\Reservation', 'unit_id');
    }
}
