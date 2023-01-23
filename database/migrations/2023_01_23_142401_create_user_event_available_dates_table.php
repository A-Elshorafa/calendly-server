<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserEventAvailableDatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_event_available_dates', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->datetime('date');
            $table->unsignedBigInteger('user_event_id')->index();
            $table->foreign('user_event_id')->on('user_events')->references('id');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_event_available_dates');
    }
}
