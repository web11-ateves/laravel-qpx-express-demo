<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSegmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('segments', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('slice_id')->unsigned();
            $table->string('code');
            $table->integer('duration');
            $table->string('carrier_code');
            $table->string('carrier_name');
            $table->string('flight_number');
            $table->string('cabin');
            $table->integer('seats_available');
            $table->integer('connection_duration');
            $table->string('origin_airport')->nullable();
            $table->string('destination_airport')->nullable();
            $table->dateTime('departure_time')->nullable();
            $table->dateTime('arrival_time')->nullable();
            $table->timestamps();
            $table->foreign('slice_id')->references('id')->on('slices')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('segments');
    }
}
