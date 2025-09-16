<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateAmenitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('amenities', function(Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('icon')->nullable();
            $table->timestamps();
        });

        Schema::create('amenity_translations', function(Blueprint $table)
        {
            $table->id();
            $table->foreignId('amenity_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->text('description');
            $table->string('locale')->index();
            $table->unique(['amenity_id', 'locale']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('amenity_translations');
        Schema::drop('amenities');
    }
}
