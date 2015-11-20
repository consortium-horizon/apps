<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateForeignStuffs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('stuffs', function ($table) {
            $table->foreign('userID')->references('id')->on('users');   
            $table->foreign('headID')->references('id')->on('items'); 
            $table->foreign('bodyID')->references('id')->on('items'); 
            $table->foreign('bootsID')->references('id')->on('items'); 
            $table->foreign('weaponID')->references('id')->on('items'); 
            $table->foreign('offhandID')->references('id')->on('items'); 
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('stuffs', function ($table) {
            $table->dropForeign('stuffs_userid_foreign');
            $table->dropForeign('stuffs_headid_foreign');
            $table->dropForeign('stuffs_bodyid_foreign');
            $table->dropForeign('stuffs_bootsid_foreign');
            $table->dropForeign('stuffs_weaponid_foreign');
            $table->dropForeign('stuffs_offhandid_foreign');
        });
    }
}
