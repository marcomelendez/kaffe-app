<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('excursions', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();
            $table->enum('type', ['historical', 'cultural', 'adventure', 'nature','family', 'other']);
            $table->string('image_url')->nullable();
            $table->decimal('price_default', 8, 2);
            $table->date('available_from');
            $table->date('available_to');
            $table->integer('location_id')->unsigned();
            $table->boolean('is_active')->default(false);
            $table->timestamps();
        });

        Schema::create('excursion_translations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('excursion_id')->constrained('excursions')->onDelete('cascade');
            $table->string('name');
            $table->text('description');
            $table->text('short_description');
            $table->string('locale')->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('excursion_translations');
        Schema::dropIfExists('excursions');
    }
};
