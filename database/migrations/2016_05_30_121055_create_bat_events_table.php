<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateBatEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bat_events', function (Blueprint $table) {
            $table->id();
            $table->string('event_group');
            $table->string('type');
            $table->string('name');
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->integer('value');
            $table->enum('price_calculation', array('pp', 'total'));
            $table->string('granularity');
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('bat_events');
    }
}
