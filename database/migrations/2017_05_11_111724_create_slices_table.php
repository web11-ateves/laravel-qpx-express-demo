<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSlicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('slices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trip_option_id')->unsigned();
            $table->string('code');
            $table->integer('duration');
            $table->string('origin_airport')->nullable();
            $table->string('destination_airport')->nullable();
            $table->dateTime('departure_time')->nullable();
            $table->dateTime('arrival_time')->nullable();
            $table->integer('connection_duration')->nullable();
            $table->integer('stops')->nullable();
            $table->timestamps();
            $table->foreign('trip_option_id')->references('id')->on('trip_options')->onDelete('cascade');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('slices');
    }
}
