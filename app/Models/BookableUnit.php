<?php

namespace App\Models;

use App\Scr\Valuator;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Roomify\Bat\Calendar\Calendar;
use Roomify\Bat\Constraint\CheckInDayConstraint;
use Roomify\Bat\Constraint\DateConstraint;
use Roomify\Bat\Constraint\MinMaxDaysConstraint;
use Roomify\Bat\Event\Event;
use Roomify\Bat\Store\SqlDBStore;
use Roomify\Bat\Store\SqlLiteDBStore;
use Roomify\Bat\Unit\Unit;

// use Elasticquent\ElasticquentTrait;

class BookableUnit extends Model
{
    const BAT_PRICING = 'pricing'; // event group
    const BAT_AVAILABILITY = 'availability'; // event group

    public $table = "bookable_units";

    protected $batConnection = null;
    protected $constraints = [];
    protected $min_nights = 1;


    protected $fillable = ['default_state','default_rate','active'];
    protected $dates = ['created_at', 'updated_at', 'deleted_at', 'start_date', 'end_date'];

    public function __construct(array $attributes = array())
    {
        parent::__construct($attributes);

        $this->batConnection = DB::connection(env('DB_CONNECTION','mysql'))->getPdo();
    }

    public function bookable()
    {
        return $this->morphTo();
    }

    /**
     * @param string $event
     */
    public function getBatUnit($event)
    {
        $default = 'default_'.$event;
        $default = $this->$default;

        return new Unit($this->id, $default, $this->getConstraints());
    }

    /**
     * Obtiene el contrato de acuerdo a la Unit
     */
    public function getConstraints()
    {
        $response[] = new MinMaxDaysConstraint(array(), $this->min_nights, null);

        foreach ($this->constraints as $constraint ) {

            switch ($constraint['constraint_type']) {
                case 'checkin_day':
                    $response[] = new CheckInDayConstraint(array(), $constraint->checkin_day);
                    break;
                case 'date_constraint':
                    $response[] = new DateConstraint(array(), $constraint->start_date, $constraint->end_date);
                break;

                case 'min_max_days':
                  $response[] = new MinMaxDaysConstraint(array(), $constraint->min_days, $constraint->max_days, $constraint->start_date, $constraint->end_date);
                    break;
                default:
                    $response[] = null;
                    break;
            }
        }

        return $response;
    }



    /**
     * Upcreates availability event for the event time period
     *
     * @param  Illuminate\Database\Eloquent\Model $eventType [description]
     * @param array $constraints Array of \Roomify\Bat\Constraint objects
     * @return Roomify\Bat\Calendar\CalendarResponse
     */
    public function updateAvailability($event, $startDate, $endDate, $state)
    {
        // setup stores
        $event_store =  new SqlLiteDBStore($this->batConnection, $event->event_group, 'event');
        $state_store = new SqlLiteDBStore($this->batConnection, $event->event_group, 'state');

        $event_calendar = new Calendar(array($this->getBatUnit('state')), $event_store);
        $state_calendar = new Calendar(array($this->getBatUnit('state')), $state_store);

        $event_id_event = new Event($startDate, $endDate, $this->getBatUnit('state'), $event->id);
        $state_event = new Event($startDate, $endDate, $this->getBatUnit('state'), $state);

        $event_calendar->addEvents(array($event_id_event), Event::BAT_DAILY);
        $state_calendar->addEvents(array($state_event), Event::BAT_DAILY);

        // return the calendar response
        return $event_calendar;
    }


    public function updatePricing($event, $startDate, $endDate, $state)
    {
        // setup stores
        $event_store =  new SqlLiteDBStore($this->batConnection, $event->event_group, 'event');

        $event_calendar = new Calendar(array($this->getBatUnit('event')), $event_store);

        $event_id_event = new Event($startDate, $endDate, $this->getBatUnit('state'), $event->id);
        $state_event = new Event($startDate, $endDate, $this->getBatUnit('state'), $state);

        $event_calendar->addEvents(array($event_id_event), Event::BAT_DAILY);
        //$state_calendar->addEvents(array($state_event), Event::BAT_DAILY);

        // return the calendar response
        return $event_calendar;
    }


    /**
     * Retrieves Availability for given time period
     *
     * @param  DateTaime $start_date   [description]
     * @param  DateTaime $end_date     [description]
     * @param  array  $valid_states [description]
     *
     * @return CalendarResponse  [description]
     */
    public function getBatAvailability($startDate, $endDate, $validStates = array() )
    {
        // setup state store
        $state_store = new SqlLiteDBStore($this->batConnection, self::BAT_AVAILABILITY, SqlLiteDBStore::BAT_STATE);
        $state_calendar = new Calendar(array($this->getBatUnit('state')), $state_store);
            // dd($end_date);
        return $state_calendar->getMatchingUnits($startDate, $endDate, $validStates);
    }

    /**
     * Returns itemized prices
     *
     * @param datetime $start_date
     * @param datetime $end_date
     * @param integer $pax Total amount of people staying
     *
     * @return array
     */
    public function calculateAmountsItemized($start_date, $end_date, $pax) {

        // $total = 0;

        // $duration = $start_date->diff($end_date)->days; // We use this to calculate extra pax + additional services

        // $adtl_pax = $pax - $this->min_occupancy;

        // // Get normal rate per person as returned by BAT framework
        // $basePerPerson = $this->getPriceValue($start_date, $end_date);

        // // Base price per person is multiplied by min_occupancy to calculate total base price
        // $total = $basePerPerson * $this->min_occupancy;

        // $adtlPerPerson = $this->adtl_pax_price * $duration;

        // $total = $total + ($adtlPerPerson * $adtl_pax);

        // $prepayment = $this->confirmation_percentage > 0 ? $total*$this->confirmation_percentage/100 : 0;

        // return ['total' => $total, 'prepayment' => $prepayment];
    }

    /**
     * Check that the unit is available for booking and that it can accomodate given number of pax
     *
     * @return array returns the status (true or false) and any error messages
     */
    public function canBook($startDate, $endDate, $pax )
    {
        $can_accommodate = true;
        $errors = [];

        // Check if this unit can accommodate pax
        $maximum_pax = $this->max_occupancy + $this->adtl_pax;

        if( $pax > $maximum_pax ) {
            $can_accommodate = false;
            $errors[] = 'La casa no puede alojar a tantos huéspedes.';
        }

        $availability = $this->getBatAvailability($startDate, $endDate, [\Config::get('eres.availability_states_status.available')['id']]);
        $is_available = in_array($this->id, array_keys($availability->getIncluded()));

        if(!$is_available && in_array($this->id, array_keys($availability->getExcluded()))) {

            $excluded = $availability->getExcluded()[$this->id];
            if($excluded['reason'] == 'constraint') {
                $errors[] = strtr($excluded['constraint']->toString()['text'], $excluded['constraint']->toString()['args']);
            }
            else {
                $razon = $availability->getExcluded()[$this->id]['reason'];
                if($razon == "invalid_state") $errors[] = 'Casa no disponible para esas fechas';
                else $errors[] = $razon;
            }
        }

        return ['status' => ($can_accommodate && $is_available), 'errors' => $errors];
    }

    /**
     * Returns the total price value for the length of occupation, calculated based on date interval unit (P1M - Monthly, P1D - Daily, PT15M - 15 min blocks, PT1M - 1 minute blocks)
     *
     * @param  [type] $start_date    [description]
     * @param  [type] $end_date      [description]
     * @param  string $duration_unit [description]
     * @return [type]                [description]
     */
    public function getPriceValue($startDate, $endDate, $durationUnit = 'P1D')
    {
        $valuator = $this->getPriceValuator($startDate, $endDate, $durationUnit);
        return $valuator->determineValue();
    }

    public function getPriceWithDiscount($startDate, $endDate, $durationUnit = 'P1D')
    {

        $valuator = $this->getPriceValuator($startDate, $endDate, $durationUnit);
        return $valuator->determineValue();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @param string $durationUnit
     * @return Valuator
     * @throws Exception
     */
    private function getPriceValuator($startDate, $endDate, string $durationUnit = 'P1D'): Valuator
    {
        $store = new SqlLiteDBStore($this->batConnection, self::BAT_PRICING, SqlLiteDBStore::BAT_STATE);
        $valuator = new Valuator($startDate, $endDate, $this->getBatUnit('pricing'), $store, new \DateInterval($durationUnit));

        return $valuator;
    }

    /**
     * Returns the total price and prepayment amount
     *
     * @param datetime $start_date
     * @param datetime $end_date
     * @param integer $pax Total amount of people staying
     *
     * @return array
     */
//    public function calculateAmounts($start_date, $end_date, $pax)
//    {
//
//        $total = 0;
//        if(empty($start_date) || empty($end_date)) return null;
//        $duration = $start_date->diff($end_date)->days; // We use this to calculate extra pax + additional services
//
//        // Get normal rate as returned by BAT framework
//        $basePrice = $this->getPriceValue($start_date, $end_date);
//
//        $adtlPaxAmount = 0;
//
//        if($pax > $this->max_occupancy) {
//            $adtl_pax = $pax - $this->max_occupancy;
//            $adtlPaxAmount = $adtl_pax * $this->adtl_pax_price * $duration;
//        }
//
//        $total = $basePrice + $adtlPaxAmount;
//
//        $prepayment = $this->confirmation_percentage > 0 ? $total*$this->confirmation_percentage/100 : 0;
//
//        return ['base_price' => $basePrice, 'total' => $total, 'prepayment' => $prepayment];
//    }

    /**
     * Returns the availability / pricing events for a given time period
     *
     * @param datetime $startDate
     * @param datetime $endDate
     *
     *
     * @return array Event
     */
    public function getEvents(\DateTime $startDate, \DateTime $endDate, $units = null, $group = self::BAT_AVAILABILITY, $type = SqlLiteDBStore::BAT_STATE, $reset = TRUE)
    {

        $selected_units[] = $this->getBatUnit('rate');

        $store = new SqlLiteDBStore($this->batConnection, $group, $type);

        $calendar = new Calendar($selected_units, $store);

        return $calendar->getEvents($startDate, $endDate);
    }

    /**
     *  Returns the availability / pricing events for a given
     * @param datetime $start_date
     * @param datetime $start_date, $end_date
     */
    public function getAvailabilityCalendar($startDate, $endDate, $units = null, $reset = TRUE)
    {
        return $this->stateStorage($startDate,$endDate,'availability', 'state',$units);
    }


    public function getPricingCalendar($startDate, $endDate, $reset = TRUE)
    {
        return $this->stateStorage($startDate,$endDate,'pricing', 'event');
    }

    /**
     *
     */
    private function stateStorage($startDate, $endDate, $eventType,$evenData)
    {
        try {
            $units = $this->getBatUnit('rate');

            $state_store = new SqlLiteDBStore($this->batConnection, $eventType,$evenData);

            $stateCalendar = new Calendar([$units], $state_store);

            return $stateCalendar->getEvents($startDate, $endDate);

        }catch(Exception $e){

            return false;
        }

    }

    /**
     * @param $startDate
     * @param $endDate
     *
     * @return
     */
    public function saveEventDaily(\DateTime $startDate,\DateTime $endDate,$value = 0)
    {
        $unit = $this->getBatUnit('rate');

        $state_store = new SqlLiteDBStore($this->batConnection, 'pricing', SqlDBStore::BAT_EVENT);

        $event = new Event($startDate, $endDate, $unit, $value);

        //$itemized = $event->itemize(new EventItemizer($event));

        $calendar = new Calendar([$unit], $state_store);
        $calendar->addEvents([$event], Event::BAT_DAILY);

    }
}
