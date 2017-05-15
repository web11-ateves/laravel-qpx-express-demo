<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePricesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('trip_option_id')->unsigned();
            $table->decimal('price_total_brl', 8, 2);
            $table->decimal('base_fare_brl', 8, 2);
            $table->decimal('base_fare_usd', 8, 2);
            $table->decimal('taxes_brl', 8, 2);
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
        Schema::dropIfExists('prices');
    }
}
