<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserThirdPartiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_third_parties', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('token');
            $table->foreignId('user_id');
            $table->longText('access_token');
            $table->longText('refresh_token');
            $table->foreignId('third_party_id');
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
        Schema::dropIfExists('user_third_parties');
    }
}
