<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateBatConstraintsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bat_constraints', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->bigInteger('constrainable_id');
            $table->string('constrainable_type');
            $table->foreignId('custom_period_id')->nullable();
            $table->string('constraint_type');
            $table->datetime('checkin_date')->nullable();
            $table->datetime('checkout_date')->nullable();
            $table->integer('checkin_day')->nullable();
            $table->integer('min_days')->nullable();
            $table->integer('max_days')->nullable();
            $table->boolean('active');
            $table->datetime('start_date')->nullable();
            $table->datetime('end_date')->nullable();
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
        Schema::drop('bat_constraints');
    }
}
