    <?php

    use Illuminate\Database\Schema\Blueprint;
    use Illuminate\Database\Migrations\Migration;
    use Illuminate\Support\Facades\Schema;

    class CreatePropertiesTable extends Migration
    {

        /**
         * Run the migrations.
         *
         * @return void
         */
        public function up()
        {
            Schema::create('property_types', function (Blueprint $table) {
                $table->increments('id');
                $table->softDeletes();
                $table->timestamps();
            });

            Schema::create('property_type_translations', function (Blueprint $table) {
                $table->increments('id')->constrained()->cascadeOnDelete();
                $table->integer('property_type_id')->unsigned();
                $table->string('name', 100);

                $table->string('locale')->index();
                $table->unique(['property_type_id', 'locale']);

                $table->foreign('property_type_id')->references('id')->on('property_types')->onDelete('cascade');
            });


            Schema::create('categories', function (Blueprint $table) {
                $table->id();
                $table->timestamps();
                $table->softDeletes();
            });

            Schema::create('category_translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('category_id')->constrained()->cascadeOnDelete();;
                $table->string('name');
                $table->string('slug');
                $table->text('description');
                $table->string('locale')->index();
                $table->unique(['category_id', 'locale']);
            });

            Schema::create('providers', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('dni', 60);
                $table->string('address')->nullable();
                $table->string('phone_number', 100)->nullable();
                $table->string('email', 80)->nullable();
                $table->string('email_secundary', 80)->nullable();
                $table->string('province_code', 4)->nullable();
                $table->string('contact_person')->nullable();
                $table->text('highlights')->nullable();
                $table->timestamps();
            });

            Schema::create('properties', function (Blueprint $table) {
                $table->id();
                $table->boolean('multi_unit')->default(false);
                $table->foreignId('provider_id')->constrained()->cascadeOnDelete();;
                $table->foreignId('category_id')->constrained()->cascadeOnDelete();;
                $table->foreignId('property_type_id');
                $table->integer('location_id')->unsigned();
                $table->string('name');
                $table->string('real_name')->nullable();
                $table->string('slug')->unique()->index();
                $table->text('videos')->nullable();
                $table->string('postal_code')->nullable();
                $table->string('latlng')->nullable();
                $table->integer('owner_id')->nullable();
                $table->datetime('last_calendar_update')->default(null);
                $table->time('checkin_from')->nullable();
                $table->time('checkin_to')->nullable();
                $table->time('checkout_from')->nullable();
                $table->time('checkout_to')->nullable();
                $table->integer('confirmation_percentage')->nullable();
                $table->smallInteger('rooms');
                $table->smallInteger('bathrooms');
                $table->smallInteger('adtl_beds');
                $table->smallInteger('min_occupancy');
                $table->smallInteger('max_occupancy');
                $table->smallInteger('adtl_pax');
                $table->smallInteger('adtl_pax_price');
                $table->smallInteger('min_nights')->default(1);
                $table->smallInteger('days_in_advance')->default(2);
                $table->boolean('instant_booking')->default(false);
                $table->float('default_state')->default(1);
                $table->float('default_rate')->nullable();
                $table->float('deposit')->nullable();
                $table->float('commission');
                $table->boolean('published')->default(false);
                $table->boolean('recommended')->default(false);
                $table->boolean('active')->default(false);
                $table->softDeletes();
                $table->timestamps();
            });

            Schema::create('property_translations', function (Blueprint $table) {
                $table->id();
                $table->foreignId('property_id')->constrained()->cascadeOnDelete();;
                $table->text('description')->nullable();
                $table->text('short_description')->nullable();
                $table->text('owner_highlights')->nullable();
                $table->string('directions')->nullable();
                $table->text('other_conditions')->nullable();

                $table->string('locale')->index();
                $table->unique(['property_id', 'locale']);
            });
        }

        /**
         * Reverse the migrations.
         *
         * @return void
         */
        public function down()
        {
            Schema::dropIfExists('providers');
            Schema::dropIfExists('category_translations');
            Schema::dropIfExists('categories');
            Schema::dropIfExists('property_type_translations');
            Schema::dropIfExists('property_types');
            Schema::dropIfExists('property_translations');
            Schema::dropIfExists('properties');
        }
    }
