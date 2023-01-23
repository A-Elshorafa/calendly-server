<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserEventAvailableTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_event_available_times', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('time');
            $table->unsignedBigInteger('user_event_available_date_id')->index();
            $table->foreign('user_event_available_date_id')->on('user_event_available_dates')->references('id');
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
        Schema::dropIfExists('user_event_available_times');
    }
}
