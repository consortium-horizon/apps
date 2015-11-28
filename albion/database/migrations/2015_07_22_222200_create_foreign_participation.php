<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForeignParticipation extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('participation', function ($table) {
            $table->foreign('eventID')->references('id')->on('events');
            $table->foreign('userID')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('participation', function ($table) {
            $table->dropForeign('participation_eventID_foreign');
            $table->dropForeign('participation_userID_foreign');
        });
    }
}
