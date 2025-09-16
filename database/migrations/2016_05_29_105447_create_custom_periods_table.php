<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateCustomPeriodsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_periods', function(Blueprint $table){
            $table->id();
            $table->foreignId('property_id')->constrained()->cascadeOnDelete();;
            $table->datetime('start_date');
            $table->datetime('end_date');
            $table->integer('price')->nullable();
            $table->boolean('free_adtl_pax')->nullable();
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
        Schema::drop('custom_periods');
    }
}
