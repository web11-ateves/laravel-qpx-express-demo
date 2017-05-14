<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLegsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('legs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('segment_id')->unsigned();
            $table->string('code');
            $table->string('aircraft');
            $table->string('origin_airport');
            $table->string('origin_airport_name');
            $table->string('origin_city');
            $table->string('destination_airport');
            $table->string('destination_airport_name');
            $table->string('destination_city');
            $table->dateTime('departure_time');
            $table->dateTime('arrival_time');
            $table->integer('duration');
            $table->integer('connection_duration');
            $table->boolean('change_plane')->default(false);
            $table->integer('mileage');
            $table->text('operating_disclosure')->nullable();
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
        Schema::dropIfExists('legs');
    }
}
