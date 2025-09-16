<?php

use App\Models\SetupStore as SetupStore;
use App\Models\BookableUnit as Unit;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class SetupBatDb extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      $db = \DB::connection(env('DB_CONNECTION'),'mysql');
      // Setup Availability Tables
       $db->statement(SetupStore::createDayTable(Unit::BAT_AVAILABILITY, 'event'));
       $db->statement(SetupStore::createDayTable(Unit::BAT_AVAILABILITY, 'state'));

       $db->statement(SetupStore::createHourTable(Unit::BAT_AVAILABILITY, 'event'));
       $db->statement(SetupStore::createHourTable(Unit::BAT_AVAILABILITY, 'state'));

       $db->statement(SetupStore::createMinuteTable(Unit::BAT_AVAILABILITY, 'event'));
       $db->statement(SetupStore::createMinuteTable(Unit::BAT_AVAILABILITY, 'state'));

      // Setup Price Tables
       $db->statement(SetupStore::createDayTable(Unit::BAT_PRICING, 'event'));
       $db->statement(SetupStore::createDayTable(Unit::BAT_PRICING, 'state'));

       $db->statement(SetupStore::createHourTable(Unit::BAT_PRICING, 'event'));
       $db->statement(SetupStore::createHourTable(Unit::BAT_PRICING, 'state'));

       $db->statement(SetupStore::createMinuteTable(Unit::BAT_PRICING, 'event'));
       $db->statement(SetupStore::createMinuteTable(Unit::BAT_PRICING, 'state'));
    }


    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $db = \DB::connection('mysql');

      // Drop Availability Tables
       $db->statement(SetupStore::createDayTable(Unit::BAT_AVAILABILITY, 'event', 'DROP'));
       $db->statement(SetupStore::createDayTable(Unit::BAT_AVAILABILITY, 'state', 'DROP'));
       $db->statement(SetupStore::createHourTable(Unit::BAT_AVAILABILITY, 'event', 'DROP'));
       $db->statement(SetupStore::createHourTable(Unit::BAT_AVAILABILITY, 'state', 'DROP'));
       $db->statement(SetupStore::createMinuteTable(Unit::BAT_AVAILABILITY, 'event', 'DROP'));
       $db->statement(SetupStore::createMinuteTable(Unit::BAT_AVAILABILITY, 'state', 'DROP'));

      // Drop Price Tables
       $db->statement(SetupStore::createDayTable(Unit::BAT_PRICING, 'event', 'DROP'));
       $db->statement(SetupStore::createDayTable(Unit::BAT_PRICING, 'state', 'DROP'));
       $db->statement(SetupStore::createHourTable(Unit::BAT_PRICING, 'event', 'DROP'));
       $db->statement(SetupStore::createHourTable(Unit::BAT_PRICING, 'state', 'DROP'));
       $db->statement(SetupStore::createMinuteTable(Unit::BAT_PRICING, 'event', 'DROP'));
       $db->statement(SetupStore::createMinuteTable(Unit::BAT_PRICING, 'state', 'DROP'));
    }

}
