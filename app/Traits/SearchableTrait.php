<?php namespace App\Traits;

use DB;
use App\Booking;
use App\Models\BookableUnit;
use App\Models\Property;
use App\Models\Province;
use App\Models\Zone;
use Roomify\Bat\Unit\Unit;
use Roomify\Bat\Store\SqlLiteDBStore;
use Roomify\Bat\Calendar\Calendar;
use Roomify\Bat\Constraint\MinMaxDaysConstraint;
use Roomify\Bat\Constraint\CheckInDayConstraint;
use Roomify\Bat\Constraint\DateConstraint;
use Roomify\Bat\Event\EventItemizer;

use App\Models\Municipality;
use App\Models\Region;


trait SearchableTrait
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
    public function getAvailability($start_date, $end_date, $valid_states)
    {
        $bat_units = [$this->getBatUnit('state')];

        // setup state store
        $store = new SqlLiteDBStore(\DB::connection()->getPdo(), BookableUnit::BAT_AVAILABILITY, 'state');
        $state_calendar = new Calendar($bat_units, $store);


        $response = $state_calendar->getMatchingUnits($start_date, $end_date, [1,2,3,4]);


        $included_units = $response->getIncluded();
        $excluded_units = $response->getExcluded();

        $price_events = $this->getPricingCalendar($start_date, $end_date);

        $cheapest = null;

        foreach ($price_events[$this->id] as $event) {
            if($event->getValue() < $cheapest || $cheapest == null) {
                $cheapest = $event->getValue();
            }
        }

        $results['cheapest'] = $cheapest;
        $results['availability'] = $response;

        return $results;
    }


    public function search($start_date = null, $end_date = null, $location, $type, $adults, $valid_states, $filters , $category = 'all' , $pagination = array(), $options = array())
    {

        $results = [];
        $In = [];
        $bindings = [];
        $bindings2 = [];
        $key = 0;


        $properties_in_area = $this->where('active', 1)
            ->where('published', 1)
            ->orderBy('properties.instant_booking','DESC')
            ->orderBy(DB::raw('(properties.max_occupancy + properties.adtl_pax)'),'ASC')
            ->orderBy('properties.id','DESC');
            //->limit(10);

            //dd($properties_in_area);

        if($category !== 'all') {

            if( $category = \App\Models\Category::whereTranslation('slug', $category)->first()) {
                $properties_in_area->where('category_id', $category->id);

                foreach ($properties_in_area as $property) {

                    $results[] = ['property' => $property, 'cheapest' => null];


                }

            }
        }
        if($location && $type) {
            switch ($type) {
                case config('eres.type_search.0'):
                    $location = Zone::where('id', $location)->first()->municipalities->pluck('id');
                    $properties_in_area->whereIn('location_id', $location);
                    break;
                case config('eres.type_search.1'):
                    $location = Region::where('id', $location)->first()->municipalities->pluck('id');
                    $properties_in_area->whereIn('location_id', $location);
                    break;
                case config('eres.type_search.2'):
                    $location = Province::where('id', $location)->first()->municipalities->pluck('id');
                    $properties_in_area->whereIn('location_id', $location);
                    break;
                case config('eres.type_search.3'):
                    $properties_in_area->where('location_id', $location);
                    break;
            };
        }

        if(isset($adults) && !empty($adults) && $adults > 1) {
            $properties_in_area
                ->where('min_occupancy', '<=', $adults)
                ->whereRaw('max_occupancy + adtl_pax >= ?', [$adults])
                ->with([
                    'photos',
                    'municipality.province',
                    'municipality.region',
                    'amenities.translations',
                    'category.translations',
                    'constraints',
                    'translations',
                    'region'
                ])
            ;
        }


        if($filters) {
            if (!is_null($filters['instant'])){
                if( $filters['instant'] <>0 ) { // Zero means All Properties so if its checked there's no need to query
                    $properties_in_area->where('instant_booking', $filters['instant']);
                    //dd($properties_in_area->get());
                }
            }
            
            if (!is_null($filters['type'])){
                    if( !in_array("0", $filters['type']) ) { // Zero means All Properties so if its checked there's 
                       $properties_in_area->whereIn('category_id', $filters['type']);
                       //dd($properties_in_area->get());
                    }
            }
           
            if (!is_null($filters['range'])){
                //dd($filters['range']);
                $cont=count($filters['range']);

                $filtermin=explode("-",$filters['range'][0]);
                $min_price=$filtermin[0];
                //lo llevo a precio default rate no a precio por persona
                $min_price =$min_price * $adults;

                $filtermax=explode("-",$filters['range'][$cont-1]);
                $contmax=count($filtermax);

                if ($contmax ==1) {
                    $max_price= substr($filtermax[0], 0, -1);
                }else{
                    $max_price= $filtermax[1];

                    if ($max_price =='') {
                        $max_price= $filtermin[0];
                    }
                }
                //lo llevo a precio default rate no a precio por persona
                $max_price = $max_price * $adults;
                //$properties_in_area->whereBetween('default_rate', [$min_price, $max_price]);
                 $properties_in_area->where(function ($query) use ($min_price,$max_price) {
                                          $query->where('default_rate', '>=', $min_price);
                                          $query->where('default_rate', '<=', $max_price);
                                      });
                  //dd($properties_in_area->get());

                
            }
            if (!is_null($filters['amenities'])){
                 foreach ($filters['amenities'] as $key => $value) {
                         //$In[]= $value;
                         $properties_in_area->whereRaw("EXISTS (SELECT * FROM amenities INNER JOIN amenables ON amenities.id = amenables.amenity_id WHERE amenables.amenable_id=`properties`.`id` AND amenables.amenable_type LIKE 'App\\\\\\\\Models\\\\\\\\Property' AND amenity_id = $value ) "); 

                    }
               //dd($properties_in_area->get());
               //dd($properties_in_area->getBindings());
               //dd($properties_in_area->toSql());
            }
            //dd($properties_in_area);            
            if (!is_null($filters['tags'])){
                 //dd($filters['tags']);
                    foreach ($filters['tags'] as $key => $value) {
                         //$In[]= $value;
                        $properties_in_area->whereRaw("EXISTS (SELECT * FROM `tags` INNER JOIN `taggables` ON `tags`.`id` = `taggables`.`tag_id` WHERE `taggables`.`taggable_id` = `properties`.`id` AND `taggables`.`taggable_type` LIKE 'App\\\\\\\\Models\\\\\\\\Property' AND `tag_id` =  $value) "); 

                    }
                //dd($properties_in_area->getBindings());
                //dd($properties_in_area->get());
                //dd($properties_in_area->toSql());
            }

        }

        //dd($properties_in_area->get());
        // $properties_in_area->paginate(15);
        //dd($pagination);
        if(isset($pagination) && isset($pagination['results_per_page'])) {
            if($pagination['page'] == '' || $pagination['page'] < 1) $pagination['page'] = 1;
            $limite = $pagination['page'] * $pagination['results_per_page'];
            //$properties_in_area->limit($limite);
        }


        if(($start_date == null || $end_date == null)) {
            foreach ($properties_in_area->get() as $property) {
                # Extracción de thumbnail de la casa
                if(count($property->photos) > 0) $property->photo = $property->photos->sortByDesc('cover')->first()->thumb(400, 230);

                $results[] = ['property' => $property, 'cheapest' => $property->default_rate];

            }
        } else {
            $incluidas = 0;
            foreach ($properties_in_area->get() as $property) {

                //if($incluidas >= $limite) break;
                # Extracción de thumbnail de la casa
                if(count($property->photos) > 0) $property->photo = $property->photos->sortByDesc('cover')->first()->thumb(400, 230);

                    $response = $property->getAvailability($start_date, $end_date, $valid_states);

                    foreach($response['availability']->getIncluded() as $included  ) {
                        # Si vienen fechas y adultos, calculo el precio real para meterlo en el cálculo
                        if(isset($adults) && !empty($adults) && $adults > 1) {
                            $amounts = $property->calculateAmounts($start_date, $end_date, $adults );
                            //dd($amounts);
                            if ($amounts['total'] > 0) {
                                $incluidas ++;
                                $results[] = [
                                    'property' => $property,
                                    'cheapest' => $amounts['total']/($end_date->diffInDays($start_date)),
                                    'valid' => true
                                ];
                            }

                        } else {
                            #si no vienen adultos, me centro en el valor de 'cheapest' devuelvo por $property->getAvailability
                            if ($response['cheapest'] > 0) {
                                $results[] = [
                                    'property' => $property,
                                    'cheapest' => $response['cheapest'],
                                    'valid' => true
                                ];
                            }
                        }
                    }
                    foreach($response['availability']->getExcluded() as $excluded  ) {
                        if($response['cheapest'] > 0) {

                            if($excluded['reason'] == 'constraint') {
                                $constraintReason = '';
                                $constraintReason.= strtr("Min @minimum_stay", $excluded['constraint']->toString()['args']);

                                /*
				if($excluded['constraint']->getMaxDays()) {
                                    $constraintReason.= strtr(", max @maximum_stay", $excluded['constraint']->toString()['args']);
                                }
				*/

                                $results[] = [
                                    'property' => $property,
                                    'cheapest' => $response['cheapest'],
                                    'valid' => false,
                                    'constraints' => $constraintReason,
                                ];
                            }
                        }
                    }
            }
        }

        $results = collect($results);
        if(isset($options['only_count']) && $options['only_count']) {
            return count($results);
        }

        return $results;
    }



    public function filter($filters, $results)
    {
        return $results;
    }

    // public function getCheapestAttribute()
    // {
    //     return $this->cheapest;
    // }

    // public function setCheapestAttribute($cheapest)
    // {
    //     $this->cheapest = $cheapest;
    // }

    // public function getValid()
    // {
    //     return $this->cheapest;
    // }

    // public function setValid($valid)
    // {
    //     return $this->valid = $valid;
    // }

    // public function getAffectingConstraints()
    // {
    //     return $htis->affecting_constraints;
    // }

    // public function setAffectingConstraints($affecting_constraints)
    // {
    //     return $this->affecting_constraints = $affecting_constraints;
    // }
}
