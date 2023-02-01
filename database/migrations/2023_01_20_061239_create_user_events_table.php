<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_events', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('duration');
            $table->dateTime('expire_at');
            $table->string('third_party_name');
            $table->string('notes')->nullable();
            $table->string('agenda')->nullable();
            $table->string('password')->nullable();
            $table->dateTime('subscribed_on')->nullable();
            $table->boolean('is_subscribed')->default(false);
            $table->string('calendly_link')->nullable()->index();
            $table->string('third_party_link')->nullable()->index();
            $table->boolean('is_notified')->default(false)->index();
            $table->unsignedInteger('user_event_status_id')->index();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('user_event_status_id')->references('id')->on('user_event_statuses');
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
        Schema::dropIfExists('user_events');
    }
}
