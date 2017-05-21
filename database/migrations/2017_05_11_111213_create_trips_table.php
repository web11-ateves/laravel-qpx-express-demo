<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTripsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->string('description');
            $table->string('origin');
            $table->string('destination');
            $table->boolean('roundtrip');
            $table->date('departure_date');
            $table->date('return_date')->nullable();
            $table->date('departure_date_end')->nullable();
            $table->boolean('nonstop');
            $table->string('permitted_carriers')->nullable();
            $table->string('prohibited_carriers')->nullable();
            $table->integer('max_connection_time')->nullable();
            $table->integer('max_stops')->nullable();
            $table->string('max_price')->nullable();
            $table->string('earliest_departure_time')->nullable();
            $table->string('latest_departure_time')->nullable();
            $table->integer('adults')->nullable();
            $table->integer('infants')->nullable();
            $table->integer('children')->nullable();
            $table->integer('seniors')->nullable();
            $table->date('end_date');
            $table->decimal('min_price', 8, 2);
            $table->boolean('alert')->default(true);
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
        Schema::dropIfExists('trips');
    }
}
