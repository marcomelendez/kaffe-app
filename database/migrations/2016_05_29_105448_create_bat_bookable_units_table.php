<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateBatBookableUnitsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bookable_units', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('bookable_id');
            $table->string('bookable_type');
            $table->integer('default_state')->default(1);
            $table->integer('default_rate')->default(0);
            $table->boolean('active')->default(false);
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
        Schema::drop('bookable_units');
    }
}
