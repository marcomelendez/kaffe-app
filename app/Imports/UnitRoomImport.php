<?php

namespace App\Imports;

use App\Models\BookableUnit;
use App\Models\Property;
use App\Models\UnitRoomAdult;
use App\Models\UnitRoomChildren;

class UnitRoomImport
{

    public function run()
    {

        $properties = Property::all();

        foreach($properties as $property) {


            foreach($property->roomsAll as $room) {

                $init = 1;

                foreach ($property->plan as $plan) {

                    do {
                        // echo "PLAN: " . $plan->id." ";
                        // echo "ROOM: " . $room->id." ";
                        // echo "ADULTS: " . $init. " ";
                        // echo "\n";

                        UnitRoomAdult::create([
                            'room_id' => $room->id,
                            'plan_id' => $plan->id,
                            'adults'  => $init
                        ]);


                        $init++;
                    } while ($init <= $room->total_capacity);
                }

            }

        }
    }

    public function runChild()
    {

        $properties = Property::all();

        foreach ($properties as $property) {

            foreach ($property->roomsAll as $room) {

                $init = 1;

                foreach ($property->plan as $plan) {

                    //do {
                        // echo "PLAN: " . $plan->id." ";
                        // echo "ROOM: " . $room->id." ";
                        // echo "ADULTS: " . $init. " ";
                        // echo "\n";

                        UnitRoomChildren::create([
                            'room_id' => $room->id,
                            'plan_id' => $plan->id,
                            'age'  => 10
                        ]);


                        $init++;
                    //} while ($init <= $room->total_capacity);
                }
            }
        }
    }

    public function runBookableUnit()
    {
        $unitRoomAdults = UnitRoomAdult::all();

        foreach($unitRoomAdults as $unitRoomAdult) {

            $unitRoomAdult->bookables()->create([
                'active'=>1
            ]);

            // BookableUnit::create([
            //     'bookable_id'=>$unitRoomAdult->id,
            //     'bookable_type'=>
            // ]);

        }

    }

    public function runBookableUnitChd()
    {
        $unitRoomChildren = UnitRoomChildren::all();

        foreach ($unitRoomChildren as $unitRoomChild) {

            $unitRoomChild->bookables()->create([
                'active' => 1
            ]);

            // BookableUnit::create([
            //     'bookable_id'=>$unitRoomAdult->id,
            //     'bookable_type'=>
            // ]);

        }
    }

}

